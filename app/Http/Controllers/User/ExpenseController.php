<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenseController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX (WITH FILTERS + SAFE KPI)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        /** @var User|null $user */
        $user = auth()->user();

        abort_unless($user, 403);

        $baseQuery = $user->expenses()
            ->latest('expense_date');

        // ===== FILTERS =====

        if ($request->filled('search')) {
            $baseQuery->where('title', 'like', '%' . trim($request->search) . '%');
        }

        if ($request->filled('category')) {
            $baseQuery->where('category', $request->category);
        }

        if ($request->filled('from')) {
            $baseQuery->whereDate('expense_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $baseQuery->whereDate('expense_date', '<=', $request->to);
        }

        // Clone safely
        $expenses = (clone $baseQuery)
            ->paginate(10)
            ->withQueryString();

        $total = (float) (clone $baseQuery)->sum('amount');

        $topCategory = (clone $baseQuery)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->value('category');

        $latest = (clone $baseQuery)->value('updated_at');

        return view('user.expenses.index', compact(
            'expenses',
            'total',
            'topCategory',
            'latest'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        /** @var User|null $user */
        $user = auth()->user();

        abort_unless($user, 403);

        return view('user.expenses.create', [
            'families' => $user->families()->get(),
            'recentExpenses' => $user->expenses()
                ->latest('expense_date')
                ->limit(5)
                ->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = auth()->user();

        abort_unless($user, 403);

        $validated = $request->validate([
            'family_id'    => 'nullable|exists:families,id',
            'title'        => 'required|string|max:150',
            'category'     => 'required|string|max:50',
            'amount'       => 'required|numeric|min:0.01|max:999999999',
            'expense_date' => 'required|date|before_or_equal:today',
        ]);

        DB::transaction(function () use ($validated, $user) {

            $expense = Expense::create([
                'user_id'      => $user->id,
                'family_id'    => $validated['family_id'] ?? null,
                'is_personal'  => empty($validated['family_id']),
                'title'        => trim($validated['title']),
                'category'     => $validated['category'],
                'amount'       => (float) $validated['amount'],
                'expense_date' => $validated['expense_date'],
            ]);

            Activity::create([
                'user_id'     => $user->id,
                'description' => 'Added expense: ' . $expense->title .
                                 ' (₹' . number_format($expense->amount, 2) . ')',
            ]);
        });

        return redirect()
            ->route('user.expenses.index')
            ->with('success', 'Expense added successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Expense $expense): View
    {
        abort_unless(
            $expense->user_id === auth()->id(),
            403,
            'Unauthorized access.'
        );

        return view('user.expenses.edit', compact('expense'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        abort_unless(
            $expense->user_id === auth()->id(),
            403,
            'Unauthorized update attempt.'
        );

        $validated = $request->validate([
            'title'        => 'required|string|max:150',
            'category'     => 'required|string|max:50',
            'amount'       => 'required|numeric|min:0.01|max:999999999',
            'expense_date' => 'required|date|before_or_equal:today',
        ]);

        DB::transaction(function () use ($expense, $validated) {

            $expense->update([
                'title'        => trim($validated['title']),
                'category'     => $validated['category'],
                'amount'       => (float) $validated['amount'],
                'expense_date' => $validated['expense_date'],
            ]);

            Activity::create([
                'user_id'     => auth()->id(),
                'description' => 'Updated expense: ' . $expense->title,
            ]);
        });

        return redirect()
            ->route('user.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(Expense $expense): RedirectResponse
    {
        abort_unless(
            $expense->user_id === auth()->id(),
            403,
            'Unauthorized delete attempt.'
        );

        DB::transaction(function () use ($expense) {

            Activity::create([
                'user_id'     => auth()->id(),
                'description' => 'Deleted expense: ' . $expense->title,
            ]);

            $expense->delete();
        });

        return redirect()
            ->route('user.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    */
    public function exportPdf()
    {
        /** @var User|null $user */
        $user = auth()->user();

        abort_unless($user, 403);

        $expenses = $user->expenses()
            ->latest('expense_date')
            ->get();

        $summary = [
            'total'       => (float) $expenses->sum('amount'),
            'count'       => $expenses->count(),
            'average'     => $expenses->count()
                                ? (float) $expenses->avg('amount')
                                : 0.0,
            'highest'     => $expenses->sortByDesc('amount')->first(),
            'topCategory' => $expenses
                                ->groupBy('category')
                                ->map(fn($g) => $g->sum('amount'))
                                ->sortDesc()
                                ->keys()
                                ->first(),
        ];

        $reportId = 'FA-' . now()->format('YmdHis');

        $pdf = Pdf::loadView(
            'user.expenses.pdf',
            compact('expenses', 'summary', 'reportId')
        )->setPaper('a4', 'portrait');

        return $pdf->download(
            'expense-report-' . now()->format('d-m-Y') . '.pdf'
        );
    }
}
