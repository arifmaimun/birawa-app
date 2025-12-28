<?php

namespace App\Http\Controllers;

use App\Models\DoctorInventory;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Stats
        $totalPatients = Patient::count(); // Global patients for now (or scope to doctor's patients if we had that relation clearly defined)
        // Actually, patients are owned by owners, and owners are created by users.
        // But for now, let's keep it global or scope it if we can.
        // Looking at Patient model: belongsTo Owner. Owner belongsTo User?
        // Check Owner model.
        
        // Let's stick to global for stats similar to previous dashboard, 
        // but for specific doctor features, we use Auth::id().

        $activeVets = User::count();

        // Doctor Specific Stats
        $myInventoryCount = DoctorInventory::where('user_id', $user->id)->count();
        
        // Low Stock Alerts
        $lowStockItems = DoctorInventory::where('user_id', $user->id)
            ->whereColumn('stock_qty', '<=', 'alert_threshold')
            ->get();

        // Upcoming Visits (Today)
        $upcomingVisits = Visit::where('user_id', $user->id)
            ->where('status', 'scheduled')
            ->whereDate('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->take(5)
            ->with(['patient', 'patient.owners'])
            ->get();

        // Recent Patients (Global for now, as per original dashboard)
        $recentPatients = Patient::with('owners')->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalPatients', 
            'activeVets', 
            'myInventoryCount', 
            'lowStockItems', 
            'upcomingVisits',
            'recentPatients'
        ));
    }
}
