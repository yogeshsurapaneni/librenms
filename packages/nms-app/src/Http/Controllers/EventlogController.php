<?php

namespace NmsApp\Http\Controllers;

use App\Models\Eventlog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EventlogController extends Controller
{
    public function index(Request $request)
    {
        $limit = min((int) $request->query('limit', 100), 500);
        $q = Eventlog::query()->orderByDesc('event_id');
        if ($from = $request->query('from')) {
            $q->where('datetime', '>=', date('Y-m-d H:i:s', strtotime($from)));
        }
        if ($to = $request->query('to')) {
            $q->where('datetime', '<=', date('Y-m-d H:i:s', strtotime($to)));
        }
        $logs = $q->limit($limit)->get();

        return response()->json(['status' => 'ok', 'count' => $logs->count(), 'logs' => $logs]);
    }
}
