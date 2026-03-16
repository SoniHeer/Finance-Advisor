<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class FinancialAIService
{
    public function generateDashboardData(int $userId): array
    {
        $currentYear = Carbon::now()->year;

        /* =====================================================
         | TOTALS (FAST AGGREGATES)
        ====================================================== */

        $income = (float) Income::where('user_id', $userId)->sum('amount');
        $expense = (float) Expense::where('user_id', $userId)->sum('amount');

        $savings = $income - $expense;

        $savingRate = $income > 0
            ? round(($savings / $income) * 100, 2)
            : 0.0;

        /* =====================================================
         | MONTHLY GROUPING (YEAR FILTER + STRICT SAFE)
        ====================================================== */

        $incomeRows = Income::where('user_id', $userId)
            ->whereYear('created_at', $currentYear)
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month_key,
                SUM(amount) as total
            ")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month_key')
            ->get();

        $expenseRows = Expense::where('user_id', $userId)
            ->whereYear('created_at', $currentYear)
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month_key,
                SUM(amount) as total
            ")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month_key')
            ->get();

        /* =====================================================
         | BUILD MONTH MAP
        ====================================================== */

        $monthMap = [];

        foreach ($incomeRows as $row) {
            $monthMap[$row->month_key]['income'] = (float) $row->total;
            $monthMap[$row->month_key]['expense'] = 0.0;
        }

        foreach ($expenseRows as $row) {
            if (!isset($monthMap[$row->month_key])) {
                $monthMap[$row->month_key] = [
                    'income' => 0.0,
                    'expense' => 0.0
                ];
            }
            $monthMap[$row->month_key]['expense'] = (float) $row->total;
        }

        ksort($monthMap);

        /* =====================================================
         | BUILD SERIES
        ====================================================== */

        $labels = [];
        $incomeSeries = [];
        $expenseSeries = [];
        $netWorthSeries = [];

        $cumulative = 0.0;

        foreach ($monthMap as $month => $data) {

            $inc = $data['income'];
            $exp = $data['expense'];

            $cumulative += ($inc - $exp);

            $labels[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
            $incomeSeries[] = $inc;
            $expenseSeries[] = $exp;
            $netWorthSeries[] = $cumulative;
        }

        /* =====================================================
         | CATEGORY BREAKDOWN (FOR PIE CHART)
        ====================================================== */

        $categoryData = Expense::where('user_id', $userId)
            ->whereYear('created_at', $currentYear)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $categoryLabels = $categoryData->pluck('category')->toArray();
        $categorySeries = $categoryData
            ->pluck('total')
            ->map(fn($v) => (float)$v)
            ->toArray();

        /* =====================================================
         | BURN RATE (SMART VERSION)
        ====================================================== */

        $monthsCount = max(count($expenseSeries), 1);

        $avgMonthlyExpense = array_sum($expenseSeries) / $monthsCount;

        $liquidReserve = $savings > 0 ? $savings : 0;

        $runway = $avgMonthlyExpense > 0
            ? round($liquidReserve / $avgMonthlyExpense, 1)
            : 0.0;

        /* =====================================================
         | STABILITY SCORE ENGINE (BALANCED)
        ====================================================== */

        $scoreBase =
            ($savingRate * 1.2) +
            ($runway * 4);

        if ($avgMonthlyExpense > ($income / max($monthsCount, 1))) {
            $scoreBase -= 10;
        }

        $score = (int) min(100, max(0, round($scoreBase)));

        $risk = match (true) {
            $score >= 90 => 'God Tier Stable',
            $score >= 75 => 'Ultra Stable',
            $score >= 60 => 'Stable',
            $score >= 45 => 'Moderate Risk',
            default      => 'High Risk',
        };

        /* =====================================================
         | RETURN ENTERPRISE PACKAGE
        ====================================================== */

        return [
            'totalIncome'     => $income,
            'totalExpense'    => $expense,
            'savings'         => $savings,
            'savingRate'      => $savingRate,
            'score'           => $score,
            'riskLevel'       => $risk,
            'runway'          => $runway,

            'labels'          => $labels,
            'incomeSeries'    => $incomeSeries,
            'expenseSeries'   => $expenseSeries,
            'netWorthSeries'  => $netWorthSeries,

            'categoryLabels'  => $categoryLabels,
            'categorySeries'  => $categorySeries,
        ];
    }
}
