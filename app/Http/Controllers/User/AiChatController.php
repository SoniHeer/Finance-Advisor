<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\AiChat;
use App\Models\Income;
use App\Models\Expense;

class AiChatController extends Controller
{
    /**
     * SHOW AI CHAT INTERFACE
     */
    public function index()
    {
        $userId = Auth::id();

        $chats = AiChat::where('user_id', $userId)
            ->orderBy('created_at')
            ->limit(50)
            ->get();

        // If first time user
        if ($chats->isEmpty()) {

            $welcomeMessage = $this->welcomeMessage();

            $chat = AiChat::create([
                'user_id' => $userId,
                'sender'  => 'ai',
                'message' => $welcomeMessage,
            ]);

            $chats = collect([$chat]);
        }

        return view('user.ai-chat', compact('chats'));
    }

    /**
     * HANDLE USER MESSAGE (AJAX)
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userId  = Auth::id();
        $message = trim($request->message);

        try {

            DB::beginTransaction();

            // Save user message
            AiChat::create([
                'user_id' => $userId,
                'sender'  => 'user',
                'message' => e($message),
            ]);

            // Generate intelligent reply
            $reply = $this->generateReply($userId, $message);

            // Save AI message
            AiChat::create([
                'user_id' => $userId,
                'sender'  => 'ai',
                'message' => $reply,
            ]);

            DB::commit();

            return response()->json([
                'reply' => strip_tags($reply),
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'reply' => "⚠️ AI service temporarily unavailable. Please try again.",
            ], 500);
        }
    }

    /**
     * MAIN AI RESPONSE ENGINE
     */
    private function generateReply($userId, $input)
    {
        $input = strtolower($input);

        // Financial aggregates
        $income  = Income::where('user_id', $userId)->sum('amount');
        $expense = Expense::where('user_id', $userId)->sum('amount');

        $saving      = $income - $expense;
        $savingRate  = $income > 0 ? round(($saving / $income) * 100, 1) : 0;
        $idealSaving = $income * 0.2;

        /* ================= ANALYSIS ================= */

        if (Str::contains($input, ['analysis', 'analy'])) {

            return "
📊 Financial Overview

• Total Income: ₹" . number_format($income, 2) . "
• Total Expense: ₹" . number_format($expense, 2) . "
• Net Savings: ₹" . number_format($saving, 2) . "
• Saving Rate: {$savingRate}%

" . ($savingRate < 20
                ? "⚠️ Your saving rate is below recommended 20%."
                : "✅ Your saving rate is healthy and sustainable.");
        }

        /* ================= SAVINGS ================= */

        if (Str::contains($input, ['save', 'saving'])) {

            return "
💡 Savings Strategy

• Recommended saving: 20%
• Target amount: ₹" . number_format($idealSaving, 2) . "

" . ($saving < $idealSaving
                ? "⚠️ You need to reduce discretionary spending."
                : "✅ Excellent discipline! Keep it up.");
        }

        /* ================= RISK ================= */

        if (Str::contains($input, ['risk', 'alert', 'danger'])) {

            if ($expense > $income) {
                return "
🚨 Financial Risk Alert

Your expenses exceed your income.
Immediate spending review required.";
            }

            if ($savingRate < 10) {
                return "
⚠️ Moderate Risk

Low saving buffer detected.
Increase emergency fund.";
            }

            return "
🛡️ No major financial risks detected.
Your financial balance is stable.";
        }

        /* ================= INVESTMENT ================= */

        if (Str::contains($input, ['invest', 'investment'])) {

            return "
📈 Investment Allocation Model

• 50% Index Funds (Growth)
• 30% Debt Instruments (Stability)
• 20% Emergency Reserve (Liquidity)

Always align investments with your risk profile.";
        }

        /* ================= GREETING ================= */

        if (Str::contains($input, ['hi', 'hello', 'hey'])) {

            return "
👋 Hello!

I'm your AI Finance Assistant.
Ask me about:

• Financial analysis
• Saving strategies
• Risk alerts
• Investment planning";
        }

        /* ================= DEFAULT ================= */

        return "
🤖 I can help you with:

• Financial analysis
• Savings planning
• Risk detection
• Investment ideas

Try asking:
“Analyze my finances”";
    }

    /**
     * WELCOME MESSAGE
     */
    private function welcomeMessage()
    {
        return "
🤖 FinanceAI Online

I am connected to your financial data.

You can ask about:
• Financial analysis
• Savings advice
• Risk alerts
• Investment ideas";
    }
}
