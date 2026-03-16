<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        $now = Carbon::now();

        /* ================= CACHED CORE METRICS ================= */
        $metrics = Cache::remember('admin_dashboard_metrics', 60, function () {
            return [
                'totalUsers'    => User::count(),
                'totalIncome'   => Income::sum('amount'),
                'totalExpenses' => Expense::sum('amount'),
            ];
        });

        /* ================= 6 MONTH ANALYTICS ================= */
        $months = [];
        $monthlyIncome = [];
        $monthlyExpenses = [];

        for ($i = 5; $i >= 0; $i--) {

            $date = $now->copy()->subMonths($i);
            $months[] = $date->format('M Y');

            $monthlyIncome[] = Income::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $monthlyExpenses[] = Expense::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
        }

        /* ================= RECENT ACTIVITIES ================= */
        $activities = Activity::with('user')
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard.index', [
            'totalUsers'     => $metrics['totalUsers'],
            'totalIncome'    => $metrics['totalIncome'],
            'totalExpenses'  => $metrics['totalExpenses'],
            'months'         => $months,
            'monthlyIncome'  => $monthlyIncome,
            'monthlyExpenses'=> $monthlyExpenses,
            'activities'     => $activities,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BLOCK / UNBLOCK USER
    |--------------------------------------------------------------------------
    */
    public function toggleBlock(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot block your own account.');
        }

        // Prevent blocking last admin
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
                    ? "Blocked user {$user->email}"
                    : "Unblocked user {$user->email}",
            ]);

            Cache::forget('admin_dashboard_metrics');
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
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting last admin
        if ($user->isAdmin()) {
            $adminCount = User::where('role', User::ROLE_ADMIN)->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Cannot delete the last admin.');
            }
        }

        DB::transaction(function () use ($user) {

            Activity::create([
                'user_id'     => Auth::id(),
                'description' => "Deleted user {$user->email}",
            ]);

            $user->delete();

            Cache::forget('admin_dashboard_metrics');
        });

        return back()->with('success', 'User deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVITY LOGS
    |--------------------------------------------------------------------------
    */
    public function activities(): View
    {
        $activities = Activity::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.activities.index', compact('activities'));
    }
}
