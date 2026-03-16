<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Alert;
use App\Services\FinancialStabilityService;
use Carbon\Carbon;

class ChartController extends Controller
{
    /**
     * =========================
     * DASHBOARD PAGE
     * =========================
     */
    public function dashboard(FinancialStabilityService $stability)
    {
        $data = $this->getDashboardData();

        // 🔥 Inject score engine safely
        $scoreData = $stability->calculate(Auth::id());

        return view('dashboard.index', array_merge($data, [
            'financialHealthScore' => $scoreData['score'],
            'riskLevel'            => $this->classifyRisk($scoreData['score']),
        ]));
    }

    /**
     * =========================
     * LIVE DASHBOARD DATA (AJAX)
     * =========================
     */
    public function live()
    {
        return response()->json($this->getDashboardData());
    }

    /**
     * =========================
     * CORE DASHBOARD DATA
     * PERSONAL ONLY
     * =========================
     */
    private function getDashboardData(): array
    {
        $userId = Auth::id();
        $year   = now()->year;

        if (!$userId) {
            return [];
        }

        /* ================= TOTALS ================= */
        $totalIncome = Income::where('user_id', $userId)
            ->where('is_personal', true)
            ->sum('amount');

        $totalExpense = Expense::where('user_id', $userId)
            ->where('is_personal', true)
            ->sum('amount');

        $savings = max($totalIncome - $totalExpense, 0);

        $savingRate = $totalIncome > 0
            ? round(($savings / $totalIncome) * 100, 1)
            : 0;

        /* ================= RECENT RECORDS ================= */
        $recentIncomes = Income::where('user_id', $userId)
            ->where('is_personal', true)
            ->latest()
            ->take(5)
            ->get();

        $recentExpenses = Expense::where('user_id', $userId)
            ->where('is_personal', true)
            ->orderByDesc('expense_date')
            ->take(5)
            ->get();

        /* ================= ALERTS ================= */
        $alerts = Alert::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        /* ================= MONTHLY EXPENSE CHART ================= */
        $monthlyExpenseLabels = [];
        $monthlyExpenseTotals = [];

        for ($month = 1; $month <= 12; $month++) {

            $monthlyExpenseLabels[] = Carbon::create($year, $month, 1)->format('M');

            $total = Expense::where('user_id', $userId)
                ->where('is_personal', true)
                ->whereYear('expense_date', $year)
                ->whereMonth('expense_date', $month)
                ->sum('amount');

            $monthlyExpenseTotals[] = (float) $total;
        }

        /* ================= CATEGORY EXPENSE CHART ================= */
        $categoryData = Expense::where('user_id', $userId)
            ->where('is_personal', true)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $categoryExpenseLabels = $categoryData->pluck('category')->values();
        $categoryExpenseTotals = $categoryData->pluck('total')
            ->map(fn ($value) => (float) $value)
            ->values();

        return [
            'totalIncome'           => $totalIncome,
            'totalExpense'          => $totalExpense,
            'savings'               => $savings,
            'savingRate'            => $savingRate,
            'recentIncomes'         => $recentIncomes,
            'recentExpenses'        => $recentExpenses,
            'alerts'                => $alerts,
            'monthlyExpenseLabels'  => $monthlyExpenseLabels,
            'monthlyExpenseTotals'  => $monthlyExpenseTotals,
            'categoryExpenseLabels' => $categoryExpenseLabels,
            'categoryExpenseTotals' => $categoryExpenseTotals,
        ];
    }

    /**
     * =========================
     * RISK CLASSIFICATION
     * =========================
     */
    private function classifyRisk(float $score): string
    {
        if ($score >= 80) {
            return 'Excellent';
        }

        if ($score >= 60) {
            return 'Stable';
        }

        if ($score >= 40) {
            return 'Warning';
        }

        return 'Critical';
    }
}
