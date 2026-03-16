<?php

namespace App\Services;

use App\Models\User;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportService
{
    /*
    |--------------------------------------------------------------------------
    | MAIN FINANCIAL SUMMARY (REAL LIVE DATA - NO STALE CACHE)
    |--------------------------------------------------------------------------
    */

    public function summary(User $user, ?string $from = null, ?string $to = null): array
    {
        $incomeQuery  = Income::query();
        $expenseQuery = Expense::query();

        /*
        |--------------------------------------------------------------------------
        | MULTI-TENANT PROTECTION
        |--------------------------------------------------------------------------
        */

        if (method_exists($user, 'isAdmin') && ! $user->isAdmin()) {

            $incomeQuery->where('user_id', $user->id);
            $expenseQuery->where('user_id', $user->id);

            // Apply is_personal filter ONLY if column exists
            if (Schema::hasColumn('incomes', 'is_personal')) {
                $incomeQuery->where('is_personal', true);
            }

            if (Schema::hasColumn('expenses', 'is_personal')) {
                $expenseQuery->where('is_personal', true);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DATE FILTER (SAFE)
        |--------------------------------------------------------------------------
        */

        if ($from && $to) {

            $incomeDateColumn = Schema::hasColumn('incomes', 'income_date')
                ? 'income_date'
                : 'created_at';

            $expenseDateColumn = Schema::hasColumn('expenses', 'expense_date')
                ? 'expense_date'
                : 'created_at';

            $incomeQuery->whereBetween($incomeDateColumn, [$from, $to]);
            $expenseQuery->whereBetween($expenseDateColumn, [$from, $to]);
        }

        /*
        |--------------------------------------------------------------------------
        | AGGREGATES
        |--------------------------------------------------------------------------
        */

        $totalIncome  = (float) $incomeQuery->sum('amount');
        $totalExpense = (float) $expenseQuery->sum('amount');

        return [
            'totalIncome'  => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance'      => $totalIncome - $totalExpense,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORY BREAKDOWN
    |--------------------------------------------------------------------------
    */

    public function categoryBreakdown(User $user, ?string $from = null, ?string $to = null)
    {
        $query = Expense::select(
            'category',
            DB::raw('SUM(amount) as total')
        );

        if (method_exists($user, 'isAdmin') && ! $user->isAdmin()) {

            $query->where('user_id', $user->id);

            if (Schema::hasColumn('expenses', 'is_personal')) {
                $query->where('is_personal', true);
            }
        }

        if ($from && $to) {

            $expenseDateColumn = Schema::hasColumn('expenses', 'expense_date')
                ? 'expense_date'
                : 'created_at';

            $query->whereBetween($expenseDateColumn, [$from, $to]);
        }

        return $query
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                $row->total = (float) $row->total;
                return $row;
            });
    }
}
