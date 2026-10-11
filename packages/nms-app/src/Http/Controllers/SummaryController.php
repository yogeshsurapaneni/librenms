<?php

namespace NmsApp\Http\Controllers;

use App\Models\Alert;
use App\Models\Device;
use Illuminate\Routing\Controller;

class SummaryController extends Controller
{
    public function show()
    {
        $list = Device::query()->get(['device_id', 'status']);
        $up = $list->filter(fn ($d) => (int) $d->status === 1)->count();
        $alerts = Alert::query()->where('state', '!=', 0)->count();

        return response()->json([
            'deviceCount' => $list->count(),
            'up' => $up,
            'down' => $list->count() - $up,
            'alertCount' => $alerts,
        ]);
    }
}
