<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $q = AuditLog::with('user')->latest();

        if ($request->filled('q')) {
            $term = $request->string('q')->trim();
            $q->where(function ($query) use ($term) {
                $query->where('action', 'like', '%'.$term.'%')
                    ->orWhere('url', 'like', '%'.$term.'%')
                    ->orWhere('model_type', 'like', '%'.$term.'%')
                    ->orWhere('ip_address', 'like', '%'.$term.'%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$term.'%'));
            });
        }

        if ($request->filled('method')) {
            $q->where('method', strtoupper($request->string('method')));
        }

        return view('admin.audit-logs.index', ['logs'=>$q->paginate(30)->withQueryString()]);
    }
}