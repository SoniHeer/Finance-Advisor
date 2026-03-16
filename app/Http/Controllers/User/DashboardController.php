<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Expense;
use App\Services\Ai\FinancialAIService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Enterprise FinanceAI Dashboard v6
     */
    public function index(FinancialAIService $ai)
    {
        $userId = Auth::id();

        if (!$userId) {
            abort(403, 'Unauthorized');
        }

        $currentYear = Carbon::now()->year;

        /*
        |--------------------------------------------------------------------------
        | MONTHLY INCOME (STRICT SAFE)
        |--------------------------------------------------------------------------
        */

        $incomeData = Income::where('user_id', $userId)
            ->whereYear('created_at', $currentYear)
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month_key,
                SUM(amount) as total
            ")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        /*
        |--------------------------------------------------------------------------
        | MONTHLY EXPENSE
        |--------------------------------------------------------------------------
        */

        $expenseData = Expense::where('user_id', $userId)
            ->whereYear('created_at', $currentYear)
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month_key,
                SUM(amount) as total
            ")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        /*
        |--------------------------------------------------------------------------
        | BUILD MONTH LIST
        |--------------------------------------------------------------------------
        */

        $months = collect($incomeData->keys())
            ->merge($expenseData->keys())
            ->unique()
            ->sort()
            ->values();

        $monthlyLabels = $months->toArray();

        $monthlyIncomeTotals = $months->map(function ($month) use ($incomeData) {
            return isset($incomeData[$month])
                ? (float) $incomeData[$month]->total
                : 0.0;
        })->toArray();

        $monthlyExpenseTotals = $months->map(function ($month) use ($expenseData) {
            return isset($expenseData[$month])
                ? (float) $expenseData[$month]->total
                : 0.0;
        })->toArray();

        /*
        |--------------------------------------------------------------------------
        | NET WORTH SERIES (RUNNING TOTAL)
        |--------------------------------------------------------------------------
        */

        $runningTotal = 0;
        $netWorthSeries = [];

        foreach ($monthlyLabels as $index => $month) {
            $runningTotal += ($monthlyIncomeTotals[$index] - $monthlyExpenseTotals[$index]);
            $netWorthSeries[] = $runningTotal;
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY BREAKDOWN (THIS FIXES YOUR BLANK CHART)
        |--------------------------------------------------------------------------
        */

        $categoryData = Expense::where('user_id', $userId)
            ->whereYear('created_at', $currentYear)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $categoryLabels = $categoryData->pluck('category')->toArray();
        $categorySeries = $categoryData->pluck('total')->map(fn($v) => (float)$v)->toArray();

        /*
        |--------------------------------------------------------------------------
        | AI DASHBOARD ANALYSIS
        |--------------------------------------------------------------------------
        */

        $analysis = $ai->generateDashboardData($userId);

        /*
        |--------------------------------------------------------------------------
        | OVERRIDE SERIES INTO ANALYSIS (SINGLE SOURCE OF TRUTH)
        |--------------------------------------------------------------------------
        */

        $analysis['labels'] = $monthlyLabels;
        $analysis['incomeSeries'] = $monthlyIncomeTotals;
        $analysis['expenseSeries'] = $monthlyExpenseTotals;
        $analysis['netWorthSeries'] = $netWorthSeries;
        $analysis['categoryLabels'] = $categoryLabels;
        $analysis['categorySeries'] = $categorySeries;

        /*
        |--------------------------------------------------------------------------
        | CURRENT MONTH METRICS
        |--------------------------------------------------------------------------
        */

        $currentMonthIncome = end($monthlyIncomeTotals) ?: 0;
        $currentMonthExpense = end($monthlyExpenseTotals) ?: 0;
        $currentMonthSaving = $currentMonthIncome - $currentMonthExpense;

        return view('user.dashboard.index', [
            'analysis' => $analysis,
            'currentMonthIncome' => $currentMonthIncome,
            'currentMonthExpense' => $currentMonthExpense,
            'currentMonthSaving' => $currentMonthSaving,
        ]);
    }
}
