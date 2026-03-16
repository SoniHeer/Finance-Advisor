<?php

namespace App\Services;

use App\Models\Family;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class FamilyDashboardService
{
    public function build(Family $family): array
    {
        return Cache::remember(
            "family_dashboard_{$family->id}",
            now()->addMinutes(5),
            fn () => $this->generate($family)
        );
    }

    private function generate(Family $family): array
    {
        $familyId = $family->id;

        /* ======================================
           TOTALS
        ====================================== */

        $totalIncome = (float) DB::table('incomes')
            ->where('family_id', $familyId)
            ->sum('amount');

        $totalExpense = (float) DB::table('expenses')
            ->where('family_id', $familyId)
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        /* ======================================
           MONTHLY TREND (SAFE VERSION)
        ====================================== */

        $incomeMonthly = DB::table('incomes')
            ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, SUM(amount) as total')
            ->where('family_id', $familyId)
            ->groupBy('y', 'm')
            ->get()
            ->keyBy(fn ($r) => sprintf('%04d-%02d', $r->y, $r->m));

        $expenseMonthly = DB::table('expenses')
            ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, SUM(amount) as total')
            ->where('family_id', $familyId)
            ->groupBy('y', 'm')
            ->get()
            ->keyBy(fn ($r) => sprintf('%04d-%02d', $r->y, $r->m));

        $months = [];
        $incomeTrend = [];
        $expenseTrend = [];

        for ($i = 5; $i >= 0; $i--) {

            $date = Carbon::now()->subMonths($i);
            $key  = $date->format('Y-m');

            $months[] = $date->format('M');

            $incomeTrend[]  = (float) ($incomeMonthly[$key]->total ?? 0);
            $expenseTrend[] = (float) ($expenseMonthly[$key]->total ?? 0);
        }

        /* ======================================
           GROWTH
        ====================================== */

        $thisMonthKey = now()->format('Y-m');
        $lastMonthKey = now()->subMonth()->format('Y-m');

        $thisIncome  = (float) ($incomeMonthly[$thisMonthKey]->total ?? 0);
        $lastIncome  = (float) ($incomeMonthly[$lastMonthKey]->total ?? 0);

        $thisExpense = (float) ($expenseMonthly[$thisMonthKey]->total ?? 0);
        $lastExpense = (float) ($expenseMonthly[$lastMonthKey]->total ?? 0);

        $incomeGrowth  = $this->growth($thisIncome, $lastIncome);
        $expenseGrowth = $this->growth($thisExpense, $lastExpense);

        $savingRate = $thisIncome > 0
            ? (($thisIncome - $thisExpense) / $thisIncome) * 100
            : 0;

        /* ======================================
           CATEGORY BREAKDOWN
        ====================================== */

        $categories = DB::table('expenses')
            ->selectRaw('category, SUM(amount) as total')
            ->where('family_id', $familyId)
            ->groupBy('category')
            ->pluck('total', 'category')
            ->map(fn ($v) => (float) $v)
            ->toArray();

        return [
            'family' => $family,

            'metrics' => [
                'total_income'   => $totalIncome,
                'total_expense'  => $totalExpense,
                'balance'        => $balance,
                'income_growth'  => round($incomeGrowth, 2),
                'expense_growth' => round($expenseGrowth, 2),
                'saving_rate'    => round($savingRate, 2),
            ],

            'trend' => [
                'months'  => $months,
                'income'  => $incomeTrend,
                'expense' => $expenseTrend,
            ],

            'categories' => $categories,

            'recentIncomes' => $family->incomes()
                ->latest()
                ->limit(5)
                ->get(),

            'recentExpenses' => $family->expenses()
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }

    private function growth(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }
}
