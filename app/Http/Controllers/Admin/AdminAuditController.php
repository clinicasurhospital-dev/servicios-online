<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuditController extends Controller
{
    public function index(Request $request): View
    {
        $action = $request->query('action');
        $userSearch = $request->query('user');

        $logs = AuditLog::query()
            ->with(['user'])
            ->when($action, fn ($q) => $q->where('action', 'like', "%{$action}%"))
            ->when($userSearch, fn ($q) => $q->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$userSearch}%")->orWhere('email', 'like', "%{$userSearch}%")))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.audit', compact('logs', 'action', 'userSearch'));
    }
}
