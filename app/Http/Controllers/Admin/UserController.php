<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /*
    |--------------------------------------------------------------------------
    | USER LIST
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $query = User::query();

        // 🔎 Search
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 🚦 Status filter
        if ($request->status === 'active') {
            $query->where('is_blocked', false);
        }

        if ($request->status === 'blocked') {
            $query->where('is_blocked', true);
        }

        $users = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // 📊 Cached Stats (Performance optimized)
        $stats = Cache::remember('admin_user_stats', 60, function () {
            return [
                'active'  => User::where('is_blocked', false)->count(),
                'blocked' => User::where('is_blocked', true)->count(),
                'admins'  => User::where('role', User::ROLE_ADMIN)->count(),
                'total'   => User::count(),
            ];
        });

        return view('admin.users.index', compact('users', 'stats'));
    }

    /*
    |--------------------------------------------------------------------------
    | BLOCK / UNBLOCK USER
    |--------------------------------------------------------------------------
    */
    public function block(User $user): RedirectResponse
    {
        // ❌ Prevent blocking self
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot block yourself.');
        }

        // ❌ Prevent blocking last admin
        if ($user->isAdmin()) {

            $adminCount = User::where('role', User::ROLE_ADMIN)->count();

            if ($adminCount <= 1) {
                return back()->with('error', 'Cannot block the last admin.');
            }
        }

        DB::transaction(function () use ($user) {

            $user->update([
                'is_blocked' => ! $user->is_blocked,
            ]);

            Activity::create([
                'user_id'     => Auth::id(),
                'description' => $user->is_blocked
                    ? "Blocked user: {$user->email}"
                    : "Unblocked user: {$user->email}",
            ]);

            Cache::forget('admin_user_stats');
        });

        return back()->with('success', 'User status updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */
    public function destroy(User $user): RedirectResponse
    {
        // ❌ Prevent deleting self
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        // ❌ Prevent deleting last admin
        if ($user->isAdmin()) {

            $adminCount = User::where('role', User::ROLE_ADMIN)->count();

            if ($adminCount <= 1) {
                return back()->with('error', 'Cannot delete the last admin.');
            }
        }

        DB::transaction(function () use ($user) {

            Activity::create([
                'user_id'     => Auth::id(),
                'description' => "Deleted user: {$user->email}",
            ]);

            $user->delete();

            Cache::forget('admin_user_stats');
        });

        return back()->with('success', 'User deleted successfully.');
    }
}
