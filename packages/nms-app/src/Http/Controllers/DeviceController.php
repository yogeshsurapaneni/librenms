<?php

namespace NmsApp\Http\Controllers;

use App\Models\Device;
use Illuminate\Routing\Controller;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::query()->orderBy('hostname')->get()->map(fn ($d) => $this->payload($d))->values();

        return response()->json(['status' => 'ok', 'count' => $devices->count(), 'devices' => $devices]);
    }

    public function show(string $hostname)
    {
        $hostname = urldecode($hostname);
        $hostname = preg_replace('#^devices/#', '', $hostname);
        if (str_contains($hostname, '/')) {
            $hostname = explode('/', $hostname)[0];
        }
        $device = Device::query()
            ->where('hostname', $hostname)
            ->orWhere('hostname', 'like', $hostname . '%')
            ->orWhere('sysName', $hostname)
            ->first();
        if (! $device) {
            return response()->json(['status' => 'error', 'message' => 'device not found'], 404);
        }

        return response()->json(['status' => 'ok', 'devices' => [$this->payload($device)]]);
    }

    private function payload(Device $d): array
    {
        return [
            'device_id' => $d->device_id,
            'hostname' => $d->hostname,
            'sysName' => $d->sysName,
            'ip' => $d->ip,
            'os' => $d->os,
            'hardware' => $d->hardware,
            'version' => $d->version,
            'location' => $d->location,
            'status' => (bool) $d->status,
            'status_reason' => $d->status_reason,
            'uptime' => $d->uptime,
        ];
    }
}
