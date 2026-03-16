<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class IncomeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        /** @var User|null $user */
        $user = auth()->user();
        abort_unless($user, 403);

        $query = $user->incomes()->personal();

        $incomes = (clone $query)->latest()->paginate(10);

        $total = (float) (clone $query)->sum('amount');

        $currentMonth = (float) (clone $query)
            ->forMonth(now()->month, now()->year)
            ->sum('amount');

        $count = (int) (clone $query)->count();

        $average = $count > 0 ? round($total / $count, 2) : 0.0;

        return view('user.income.index', [
            'incomes' => $incomes,
            'stats' => [
                'total'        => $total,
                'currentMonth' => $currentMonth,
                'average'      => $average,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        $user = auth()->user();
        abort_unless($user, 403);

        return view('user.income.create', [
            'families' => $user->families()->get(),
            'recentIncome' => $user->incomes()
                ->personal()
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $data = request()->validate([
            'income_type' => 'required|in:personal,family',
            'family_id'   => 'nullable|exists:families,id',
            'amount'      => 'required|numeric|min:0.01',
            'source'      => 'required|string|max:255',
            'income_date' => 'required|date',
        ]);

        DB::transaction(function () use ($user, $data) {

            $isPersonal = $data['income_type'] === 'personal';
            $familyId = null;

            if (!$isPersonal) {

                abort_unless(
                    !empty($data['family_id']) &&
                    $user->families()
                        ->where('families.id', $data['family_id'])
                        ->exists(),
                    403,
                    'Unauthorized family access'
                );

                $familyId = $data['family_id'];
            }

            $income = Income::create([
                'user_id'     => $user->id,
                'family_id'   => $familyId,
                'is_personal' => $isPersonal,
                'amount'      => (float) $data['amount'],
                'source'      => $data['source'],
                'income_date' => $data['income_date'],
            ]);

            Activity::create([
                'user_id'     => $user->id,
                'description' =>
                    ($isPersonal ? 'Personal' : 'Family') .
                    " income added: {$income->source} (₹" .
                    number_format((float) $income->amount, 2) . ")",
            ]);
        });

        return redirect()
            ->route('user.income.index')
            ->with('success', 'Income added successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Income $income): View
    {
        abort_unless($income->user_id === auth()->id(), 403);

        return view('user.income.edit', [
            'income' => $income,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Income $income): RedirectResponse
    {
        abort_unless($income->user_id === auth()->id(), 403);

        $data = request()->validate([
            'amount'      => 'required|numeric|min:0.01',
            'source'      => 'required|string|max:255',
            'income_date' => 'required|date',
        ]);

        DB::transaction(function () use ($income, $data) {

            $income->update([
                'amount'      => (float) $data['amount'],
                'source'      => $data['source'],
                'income_date' => $data['income_date'],
            ]);

            Activity::create([
                'user_id'     => auth()->id(),
                'description' => "Income updated: {$income->source} (₹" .
                    number_format((float) $income->amount, 2) . ")",
            ]);
        });

        return redirect()
            ->route('user.income.index')
            ->with('success', 'Income updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(Income $income): RedirectResponse
    {
        abort_unless($income->user_id === auth()->id(), 403);

        DB::transaction(function () use ($income) {

            Activity::create([
                'user_id'     => auth()->id(),
                'description' => 'Income deleted: ' . $income->source,
            ]);

            $income->delete();
        });

        return redirect()
            ->route('user.income.index')
            ->with('success', 'Income deleted successfully.');
    }
}
