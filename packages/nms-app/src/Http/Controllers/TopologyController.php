<?php

namespace NmsApp\Http\Controllers;

use Illuminate\Routing\Controller;
use NmsApp\Support\LabData;
use Symfony\Component\Yaml\Yaml;

class TopologyController extends Controller
{
    public function show()
    {
        $dir = LabData::topologyDir();
        $devices = [];
        $links = [];
        $devFile = $dir . '/devices.yaml';
        $linkFile = $dir . '/links.yaml';
        if (is_file($devFile)) {
            $parsed = Yaml::parseFile($devFile);
            $devices = $parsed['devices'] ?? [];
        }
        if (is_file($linkFile)) {
            $parsed = Yaml::parseFile($linkFile);
            $links = array_values(array_filter(
                $parsed['links'] ?? [],
                fn ($l) => ($l['kind'] ?? '') !== 'duplicate-fixture'
            ));
        }

        return response()->json([
            'devices' => $devices,
            'links' => $links,
            'inferredNote' => 'LLDP/xDP-inferred links are labelled separately when LibreNMS returns them',
        ]);
    }
}
