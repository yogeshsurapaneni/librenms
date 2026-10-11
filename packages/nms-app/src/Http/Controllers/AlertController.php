<?php

namespace NmsApp\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::query()->orderByDesc('id')->limit(200)->get()->map(function ($a) {
            return [
                'id' => $a->id,
                'device_id' => $a->device_id,
                'rule_id' => $a->rule_id,
                'state' => $a->state,
                'severity' => $a->severity ?? null,
                'timestamp' => $a->timestamp ?? $a->time_logged ?? null,
                'device' => optional($a->device)->hostname,
                'hostname' => optional($a->device)->hostname,
                'name' => $a->name ?? optional($a->rule)->name,
            ];
        });

        return response()->json(['status' => 'ok', 'count' => $alerts->count(), 'alerts' => $alerts]);
    }

    public function ack(Request $request, $id)
    {
        if (! $request->user() || ! $request->user()->can('admin')) {
            return response()->json(['status' => 'error', 'message' => 'admin role required'], 403);
        }
        $alert = Alert::query()->find($id);
        if (! $alert) {
            return response()->json(['status' => 'error', 'message' => 'not found'], 404);
        }
        $alert->state = 2;
        $alert->save();

        return response()->json(['status' => 'ok', 'message' => 'Alert Acknowledged.']);
    }
}
