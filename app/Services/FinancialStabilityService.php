<?php

namespace App\Services;

use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;

class FinancialStabilityService
{
    public function calculate(int $userId): array
    {
        $year = now()->year;

        $totalIncome = Income::where('user_id', $userId)
            ->where('is_personal', true)
            ->sum('amount');

        $totalExpense = Expense::where('user_id', $userId)
            ->where('is_personal', true)
            ->sum('amount');

        $netSavings = max($totalIncome - $totalExpense, 0);

        $savingsRate = $totalIncome > 0
            ? ($netSavings / $totalIncome) * 100
            : 0;

        /* ================= Expense Ratio ================= */
        $expenseRatio = $totalIncome > 0
            ? ($totalExpense / $totalIncome) * 100
            : 100;

        /* ================= Income Consistency ================= */
        $monthlyIncome = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthlyIncome[] = Income::where('user_id', $userId)
                ->where('is_personal', true)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('amount');
        }

        $averageIncome = collect($monthlyIncome)->avg();
        $variance = collect($monthlyIncome)->map(function ($value) use ($averageIncome) {
            return pow($value - $averageIncome, 2);
        })->avg();

        $consistencyScore = $variance == 0
            ? 100
            : max(0, 100 - ($variance / 100000));

        /* ================= Emergency Cushion ================= */
        $avgMonthlyExpense = Expense::where('user_id', $userId)
            ->where('is_personal', true)
            ->whereYear('expense_date', $year)
            ->avg('amount');

        $emergencyMonths = $avgMonthlyExpense > 0
            ? $netSavings / $avgMonthlyExpense
            : 0;

        $emergencyScore = min(100, ($emergencyMonths / 6) * 100);

        /* ================= FINAL WEIGHTED SCORE ================= */
        $score =
            ($savingsRate * 0.4) +
            ((100 - $expenseRatio) * 0.3) +
            ($consistencyScore * 0.2) +
            ($emergencyScore * 0.1);

        $score = round(max(min($score, 100), 0), 1);

        return [
            'score' => $score,
            'savings_rate' => round($savingsRate, 1),
            'expense_ratio' => round($expenseRatio, 1),
            'consistency_score' => round($consistencyScore, 1),
            'emergency_score' => round($emergencyScore, 1),
        ];
    }
}
