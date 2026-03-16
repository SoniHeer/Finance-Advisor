<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema; // ✅ FIXED
use Illuminate\Support\Carbon;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * User Notification Intelligence Dashboard
     */
    public function index(): View
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $now = Carbon::now();

        /* ========================================
           DATE DEFINITIONS
        ======================================== */

        $startOfMonth   = $now->copy()->startOfMonth();
        $endOfMonth     = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd   = $now->copy()->subMonth()->endOfMonth();

        /* ========================================
           SAFE DATE COLUMN DETECTION
        ======================================== */

        $incomeDateColumn = $this->hasColumn('incomes', 'income_date')
            ? 'income_date'
            : 'created_at';

        $expenseDateColumn = $this->hasColumn('expenses', 'expense_date')
            ? 'expense_date'
            : 'created_at';

        /* ========================================
           FINANCIAL METRICS
        ======================================== */

        $totalIncome = (float) Income::where('user_id', $user->id)
            ->sum('amount');

        $totalExpense = (float) Expense::where('user_id', $user->id)
            ->sum('amount');

        $thisMonthIncome = (float) Income::where('user_id', $user->id)
            ->whereBetween($incomeDateColumn, [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $thisMonthExpense = (float) Expense::where('user_id', $user->id)
            ->whereBetween($expenseDateColumn, [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $lastMonthIncome = (float) Income::where('user_id', $user->id)
            ->whereBetween($incomeDateColumn, [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        /* ========================================
           SMART METRICS ENGINE
        ======================================== */

        $savingRate = $thisMonthIncome > 0
            ? (($thisMonthIncome - $thisMonthExpense) / $thisMonthIncome) * 100
            : 0;

        $incomeGrowth = $lastMonthIncome > 0
            ? (($thisMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100
            : 0;

        $notifications = collect();

        /* ========================================
           INTELLIGENT ALERTS
        ======================================== */

        if ($totalIncome <= 0) {
            $notifications->push(
                $this->makeAlert(
                    'info',
                    'No income recorded yet. Add your first income.',
                    'low'
                )
            );
        }

        if ($balance < 0) {
            $notifications->push(
                $this->makeAlert(
                    'danger',
                    'Your total expenses exceed your income.',
                    'high'
                )
            );
        }

        if ($savingRate > 0 && $savingRate < 10) {
            $notifications->push(
                $this->makeAlert(
                    'warning',
                    'Savings rate below 10% this month.',
                    'medium'
                )
            );
        }

        if ($thisMonthIncome > 0) {
            $expenseRatio = ($thisMonthExpense / $thisMonthIncome) * 100;

            if ($expenseRatio > 80) {
                $notifications->push(
                    $this->makeAlert(
                        'danger',
                        'You spent more than 80% of this month income.',
                        'high'
                    )
                );
            }
        }

        if ($incomeGrowth < -20) {
            $notifications->push(
                $this->makeAlert(
                    'warning',
                    'Income dropped more than 20% compared to last month.',
                    'medium'
                )
            );
        }

        if ($savingRate >= 30) {
            $notifications->push(
                $this->makeAlert(
                    'success',
                    'Excellent! You are saving more than 30% this month.',
                    'low'
                )
            );
        }

        return view('user.notifications.index', [
            'notifications' => $notifications,
            'totalIncome'   => $totalIncome,
            'totalExpense'  => $totalExpense,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Alert Builder
    |--------------------------------------------------------------------------
    */

    private function makeAlert(string $type, string $message, string $priority): array
    {
        return [
            'type'       => $type,
            'message'    => $message,
            'priority'   => $priority,
            'is_read'    => false,
            'created_at' => now(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SAFE COLUMN CHECKER (IDE SAFE)
    |--------------------------------------------------------------------------
    */

    private function hasColumn(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column); // ✅ FIXED PROPER IMPORT
    }
}
