<?php

namespace App\Http\Controllers\Manual;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_patients' => Patient::count(), // Patients are shared
            'today_visits' => Visit::where('user_id', Auth::id())->whereDate('scheduled_at', today())->count(),
            'pending_invoices' => Invoice::where('user_id', Auth::id())->where('payment_status', '!=', 'paid')->count(),
            'products_count' => Product::count(), // Products are shared
        ];

        // Recent Activity
        $recent_visits = Visit::with('patient')->where('user_id', Auth::id())->latest()->take(5)->get();
        $recent_invoices = Invoice::with('patient.client')->where('user_id', Auth::id())->latest()->take(5)->get();

        return view('manual.dashboard', compact('stats', 'recent_visits', 'recent_invoices'));
    }
}
