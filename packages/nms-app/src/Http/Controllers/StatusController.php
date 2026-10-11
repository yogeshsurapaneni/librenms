<?php

namespace NmsApp\Http\Controllers;

use App\Models\Alert;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StatusController extends Controller
{
    public function stream(Request $request): StreamedResponse
    {
        return response()->stream(function () {
            $n = 0;
            while ($n < 3600) {
                $list = Device::query()->get(['hostname', 'status', 'status_reason']);
                $payload = [
                    'ts' => (int) (microtime(true) * 1000),
                    'deviceCount' => $list->count(),
                    'up' => $list->filter(fn ($d) => (int) $d->status === 1)->count(),
                    'devices' => $list->map(fn ($d) => [
                        'hostname' => $d->hostname,
                        'status' => (bool) $d->status,
                        'status_reason' => $d->status_reason,
                    ])->values(),
                    'alertCount' => Alert::query()->where('state', '!=', 0)->count(),
                ];
                echo 'data: ' . json_encode($payload) . "\n\n";
                if (function_exists('ob_flush')) {
                    @ob_flush();
                }
                flush();
                $n++;
                sleep(2);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }
}
