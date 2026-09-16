<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Request;

class SmsLogController extends Controller
{
    public function index(Request $request)
    {
        abort_if(! auth()->user()->isAdministrator(), 403);

        $q = NotificationLog::with(['user', 'application'])->where('channel', 'sms')->latest();

        if ($request->filled('status'))  $q->where('status', $request->status);
        if ($request->filled('search')) {
            $term = $request->search;
            $q->where(function ($sub) use ($term) {
                $sub->where('recipient', 'like', '%'.$term.'%')
                    ->orWhere('template', 'like', '%'.$term.'%')
                    ->orWhere('subject', 'like', '%'.$term.'%');
            });
        }

        return view('admin.sms-logs.index', ['logs' => $q->paginate(20)->withQueryString()]);
    }
}