<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : Carbon::now()->endOfMonth();

        // Income (Paid Invoices)
        $income = Invoice::whereHas('visit', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Expenses (OPEX & CAPEX)
        $expenses = Expense::where('user_id', Auth::id())
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        $opex = $expenses->where('type', 'OPEX')->sum('amount');
        $capex = $expenses->where('type', 'CAPEX')->sum('amount');
        $totalExpenses = $opex + $capex;

        $netProfit = $income - $totalExpenses;

        // Recent Transactions
        $recentExpenses = Expense::where('user_id', Auth::id())
            ->orderByDesc('transaction_date')
            ->limit(5)
            ->get();

        return view('finance.index', compact(
            'income', 
            'opex', 
            'capex', 
            'totalExpenses', 
            'netProfit', 
            'startDate', 
            'endDate',
            'recentExpenses'
        ));
    }
}
