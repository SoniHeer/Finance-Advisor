<?php

namespace App\Services;

use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;

class FinancialMetricsService
{
    /*
    |--------------------------------------------------------------------------
    | TOTAL INCOME
    |--------------------------------------------------------------------------
    */
    public function totalIncome($userId)
    {
        return Income::where('user_id', $userId)->sum('amount');
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL EXPENSE
    |--------------------------------------------------------------------------
    */
    public function totalExpense($userId)
    {
        return Expense::where('user_id', $userId)->sum('amount');
    }

    /*
    |--------------------------------------------------------------------------
    | NET SAVINGS
    |--------------------------------------------------------------------------
    */
    public function netSavings($userId)
    {
        return $this->totalIncome($userId) - $this->totalExpense($userId);
    }

    /*
    |--------------------------------------------------------------------------
    | SAVINGS RATE (%)
    |--------------------------------------------------------------------------
    */
    public function savingsRate($userId)
    {
        $income = $this->totalIncome($userId);

        if ($income == 0) {
            return 0;
        }

        return round(($this->netSavings($userId) / $income) * 100, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | CURRENT MONTH TREND
    |--------------------------------------------------------------------------
    */
    public function currentMonthSummary($userId)
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        $income = Income::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $expense = Expense::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'net' => $income - $expense,
        ];
    }
}
