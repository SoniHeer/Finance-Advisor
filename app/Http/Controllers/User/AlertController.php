<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Alert;

class AlertController extends Controller
{
    /**
     * AI ALERTS DASHBOARD
     */
    public function index()
    {
        $userId = Auth::id();

        /* ================= TOTALS ================= */

        $income  = (float) Income::where('user_id', $userId)->sum('amount');
        $expense = (float) Expense::where('user_id', $userId)->sum('amount');

        /* ================= AI LOGIC ================= */

        $this->generateAlerts($userId, $income, $expense);

        /* ================= FETCH ALERTS ================= */

        $alerts = Alert::where('user_id', $userId)
            ->latest()
            ->paginate(10);

        return view('user.alerts.index', compact(
            'alerts',
            'income',
            'expense'
        ));
    }

    /**
     * Generate AI Alerts (stored in DB, no duplicates)
     */
    protected function generateAlerts(int $userId, float $income, float $expense): void
    {
        // No data case
        if ($income == 0 && $expense == 0) {
            $this->storeAlertOnce(
                $userId,
                'info',
                'Add income and expenses to activate AI insights.'
            );
            return;
        }

        // Expense exceeds income
        if ($income > 0 && $expense > $income) {
            $this->storeAlertOnce(
                $userId,
                'danger',
                'Your expenses have exceeded your income. Immediate action is recommended.'
            );
        }

        // Spending more than 70%
        if ($income > 0 && $expense > ($income * 0.7)) {
            $this->storeAlertOnce(
                $userId,
                'warning',
                'You are spending more than 70% of your income. Try reducing unnecessary costs.'
            );
        }

        // Healthy savings
        if ($income > 0 && $expense < ($income * 0.4)) {
            $this->storeAlertOnce(
                $userId,
                'success',
                'Great job! You are saving a healthy portion of your income.'
            );
        }
    }

    /**
     * Store alert only once (prevents duplicates)
     */
    protected function storeAlertOnce(int $userId, string $type, string $message): void
    {
        Alert::firstOrCreate([
            'user_id' => $userId,
            'type'    => $type,
            'message' => $message,
        ]);
    }
}
