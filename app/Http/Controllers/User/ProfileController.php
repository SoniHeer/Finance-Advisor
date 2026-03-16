<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
    |--------------------------------------------------------------------------
    | PROFILE DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index(): View
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $totalIncome  = (float) $user->incomes()->sum('amount');
        $totalExpense = (float) $user->expenses()->sum('amount');
        $savings      = $totalIncome - $totalExpense;

        $activities = method_exists($user, 'activities')
            ? $user->activities()->latest()->limit(5)->get()
            : collect();

        return view('user.profile.index', compact(
            'totalIncome',
            'totalExpense',
            'savings',
            'activities'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT PROFILE
    |--------------------------------------------------------------------------
    */

    public function edit(): View
    {
        return view('user.profile.edit');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PROFILE
    |--------------------------------------------------------------------------
    */

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user, 403);

        DB::transaction(function () use ($request, $user) {

            $validated = $request->validated();
            $emailChanged = $user->email !== $validated['email'];

            $user->update($validated);

            if ($emailChanged) {
                $user->forceFill([
                    'email_verified_at' => null,
                ])->save();
            }

            if (class_exists(Activity::class)) {
                Activity::create([
                    'user_id'     => $user->id,
                    'description' => 'Profile updated',
                ]);
            }
        });

        return redirect()
            ->route('user.profile.index')
            ->with('success', 'Profile updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | PASSWORD FORM
    |--------------------------------------------------------------------------
    */

    public function passwordForm(): View
    {
        return view('user.profile.password');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PASSWORD
    |--------------------------------------------------------------------------
    */

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user, 403);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        DB::transaction(function () use ($request, $user) {

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            if (class_exists(Activity::class)) {
                Activity::create([
                    'user_id'     => $user->id,
                    'description' => 'Password changed',
                ]);
            }
        });

        request()->session()->regenerate();

        return redirect()
            ->route('user.profile.index')
            ->with('success', 'Password changed successfully.');
    }
}
