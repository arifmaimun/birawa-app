<?php

namespace App\Http\Controllers\Manual\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($search = $request->input('search')) {
            $query->where('action', 'like', "%{$search}%")
                ->orWhere('model_type', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        if ($action = $request->input('action_filter')) {
            $query->where('action', $action);
        }

        $logs = $query->latest()->paginate(20);

        return view('manual.settings.audit-logs.index', compact('logs'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');

        return view('manual.settings.audit-logs.show', compact('auditLog'));
    }
}
