<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFamilyRequest;
use App\Http\Requests\SendFamilyInviteRequest;
use App\Models\Family;
use App\Models\FamilyInvite;
use App\Models\FamilyMember;
use App\Models\User;
use App\Services\FamilyDashboardService;
use App\Services\FamilyInviteService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FamilyController extends Controller
{
    public function __construct()
    {
        // Accept invite must be public
        $this->middleware('auth')
             ->except(['acceptInvite']);
    }

    /*
    |--------------------------------------------------------------------------
    | LIST USER FAMILIES
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($user, 403);

        $families = $user->families()
            ->withPivot('role')
            ->latest('families.created_at')
            ->paginate(9);

        return view('user.family.index', compact('families'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE FAMILY
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        return view('user.family.create');
    }

    public function store(StoreFamilyRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($user, 403);

        DB::transaction(function () use ($request, $user) {

            $family = Family::create([
                'name'       => $request->name,
                'created_by' => $user->id,
            ]);

            FamilyMember::addOwner(
                $family->id,
                $user->id
            );
        });

        return redirect()
            ->route('user.families.index')
            ->with('success', 'Family created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    public function show(
        Family $family,
        FamilyDashboardService $dashboard
    ): View
    {
        $this->authorize('view', $family);

        return view(
            'user.family.show',
            $dashboard->build($family)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SEND INVITE
    |--------------------------------------------------------------------------
    */
    public function invite(
        SendFamilyInviteRequest $request,
        Family $family,
        FamilyInviteService $inviteService
    ): RedirectResponse
    {
        $this->authorize('invite', $family);

        $inviteService->sendEmailInvite(
            $family,
            $request->email
        );

        return back()->with('success', 'Invitation sent.');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCEPT INVITE
    |--------------------------------------------------------------------------
    */
    public function acceptInvite(string $token): RedirectResponse
    {
        $invite = FamilyInvite::where('token', $token)->firstOrFail();

        // Check expiry manually for better UX
        if ($invite->isExpired()) {
            return redirect()
                ->route('login')
                ->withErrors('This invitation link has expired.');
        }

        // Guest flow
        if (! auth()->check()) {
            session(['invite_token' => $token]);
            return redirect()->route('register');
        }

        /** @var User $user */
        $user = auth()->user();

        DB::transaction(function () use ($invite, $user) {

            FamilyMember::firstOrCreate(
                [
                    'family_id' => $invite->family_id,
                    'user_id'   => $user->id,
                ],
                [
                    'role' => FamilyMember::ROLE_MEMBER,
                ]
            );

            $invite->accept($user->id);
        });

        return redirect()
            ->route('user.families.show', $invite->family_id)
            ->with('success', 'You joined the family.');
    }
}
