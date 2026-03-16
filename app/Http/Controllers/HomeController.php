<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $now = Carbon::now();
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | BASE USER FILTER
        |--------------------------------------------------------------------------
        */

        $incomeQuery = Income::query();
        $expenseQuery = Expense::query();

        if ($user) {
            $incomeQuery->where('user_id', $user->id);
            $expenseQuery->where('user_id', $user->id);
        }

        /*
        |--------------------------------------------------------------------------
        | TOTALS
        |--------------------------------------------------------------------------
        */

        $totalIncome = (float) $incomeQuery->sum('amount');
        $totalExpense = (float) $expenseQuery->sum('amount');
        $savings = $totalIncome - $totalExpense;

        $savingRate = $totalIncome > 0
            ? round(($savings / $totalIncome) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | RECENT RECORDS (No clone freeze issue)
        |--------------------------------------------------------------------------
        */

        $recentIncomes = Income::when($user, fn ($q) =>
                $q->where('user_id', $user->id)
            )
            ->latest()
            ->limit(5)
            ->get();

        $recentExpenses = Expense::when($user, fn ($q) =>
                $q->where('user_id', $user->id)
            )
            ->latest()
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | MONTHLY EXPENSE TREND (SAFE VERSION)
        |--------------------------------------------------------------------------
        */

        $monthlyExpenseLabels = [];
        $monthlyExpenseTotals = [];

        for ($i = 5; $i >= 0; $i--) {

            $date = $now->copy()->subMonths($i);

            $total = Expense::when($user, fn ($q) =>
                    $q->where('user_id', $user->id)
                )
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $monthlyExpenseLabels[] = $date->format('M');
            $monthlyExpenseTotals[] = (float) $total;
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY BREAKDOWN (SAFE CHECK)
        |--------------------------------------------------------------------------
        */

        $categoryExpenseLabels = [];
        $categoryExpenseTotals = [];

        if (Expense::count() > 0) {

            $categoryData = Expense::when($user, fn ($q) =>
                    $q->where('user_id', $user->id)
                )
                ->selectRaw('category, SUM(amount) as total')
                ->groupBy('category')
                ->orderByDesc('total')
                ->limit(6)
                ->get();

            $categoryExpenseLabels = $categoryData->pluck('category');
            $categoryExpenseTotals = $categoryData->pluck('total');
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('home', [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'savings' => $savings,
            'savingRate' => $savingRate,
            'recentIncomes' => $recentIncomes,
            'recentExpenses' => $recentExpenses,
            'monthlyExpenseLabels' => $monthlyExpenseLabels,
            'monthlyExpenseTotals' => $monthlyExpenseTotals,
            'categoryExpenseLabels' => $categoryExpenseLabels,
            'categoryExpenseTotals' => $categoryExpenseTotals,
        ]);
    }
}