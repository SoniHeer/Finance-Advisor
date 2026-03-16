<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
    |--------------------------------------------------------------------------
    | MAIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index(Request $request, ReportService $service)
    {
        $user = Auth::user();

        $from = $request->input('from');
        $to   = $request->input('to');

        $data = $service->summary($user, $from, $to);

        return view('user.reports.index', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORY REPORT
    |--------------------------------------------------------------------------
    */

    public function categories(Request $request, ReportService $service)
    {
        $user = Auth::user();

        $from = $request->input('from');
        $to   = $request->input('to');

        $categories = $service->categoryBreakdown($user, $from, $to);

        return view('user.reports.categories', compact('categories'));
    }
}
