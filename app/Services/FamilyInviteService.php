<?php

namespace App\Services;

use App\Models\Family;
use App\Models\FamilyInvite;
use App\Models\FamilyMember;
use App\Models\User;
use App\Mail\FamilyInviteMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FamilyInviteService
{
    /*
    |--------------------------------------------------------------------------
    | SEND EMAIL INVITE
    |--------------------------------------------------------------------------
    */
    public function sendEmailInvite(Family $family, string $email): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless($user, 403);

        $this->authorize($family, $user);

        $email = strtolower(trim($email));

        /*
        |--------------------------------------------------------------------------
        | Already Member Check
        |--------------------------------------------------------------------------
        */
        $existingUser = User::where('email', $email)->first();

        if ($existingUser &&
            $family->users()->whereKey($existingUser->id)->exists()
        ) {
            throw ValidationException::withMessages([
                'email' => 'User is already a member of this family.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Rate Limit (5 invites/hour per family)
        |--------------------------------------------------------------------------
        */
        $recentCount = FamilyInvite::where('family_id', $family->id)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentCount >= 5) {
            throw ValidationException::withMessages([
                'email' => 'Invite limit reached. Try again later.',
            ]);
        }

        DB::transaction(function () use ($family, $email) {

            /*
            |--------------------------------------------------------------------------
            | Reuse existing ACTIVE invite if exists
            |--------------------------------------------------------------------------
            */
            $invite = FamilyInvite::active()
                ->where('family_id', $family->id)
                ->where('email', $email)
                ->first();

            if (! $invite) {
                $invite = FamilyInvite::create([
                    'family_id' => $family->id,
                    'email'     => $email,
                    'token'     => Str::random(64),
                    'expires_at'=> now()->addHours(48),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Queue Mail
            |--------------------------------------------------------------------------
            */
            Mail::to($email)->queue(
                new FamilyInviteMail($invite)
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Cache Invalidation
        |--------------------------------------------------------------------------
        */
        Cache::forget("family_dashboard_{$family->id}");

        /*
        |--------------------------------------------------------------------------
        | Optional Activity Log
        |--------------------------------------------------------------------------
        */
        if (function_exists('activity')) {
            activity()
                ->causedBy($user)
                ->performedOn($family)
                ->log("Sent family invite to {$email}");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization (Owner / Admin Only)
    |--------------------------------------------------------------------------
    */
    private function authorize(Family $family, User $user): void
    {
        $member = $family->members()
            ->where('user_id', $user->id)
            ->first();

        if (! $member || ! in_array(
            $member->role,
            [
                FamilyMember::ROLE_OWNER,
                FamilyMember::ROLE_ADMIN
            ],
            true
        )) {
            abort(403, 'Unauthorized action.');
        }
    }
}
