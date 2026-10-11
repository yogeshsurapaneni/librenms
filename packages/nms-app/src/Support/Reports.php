<?php

namespace NmsApp\Support;

class Reports
{
    public static function compute(?string $from, ?string $to): array
    {
        $fx = LabData::json('outages-fixture.json');
        $assets = LabData::assets();
        $fromIso = $from ?: ($fx['window']['from'] ?? '');
        $toIso = $to ?: ($fx['window']['to'] ?? '');
        $fromMs = strtotime($fromIso);
        $toMs = strtotime($toIso);
        $windowMin = ($fromMs && $toMs) ? ($toMs - $fromMs) / 60 : 0;
        if ($windowMin <= 0) {
            return [
                'from' => $fromIso,
                'to' => $toIso,
                'empty' => true,
                'failures' => [],
                'most_frequent' => [],
                'mttr' => [],
                'availability' => [],
                'alarm_stats' => ['count' => 0],
            ];
        }
        $rows = [];
        foreach ($fx['outages'] ?? [] as $o) {
            $start = strtotime($o['going_down'] ?? '');
            if ($start === false || $start < $fromMs || $start >= $toMs) {
                continue;
            }
            $rows[] = $o;
        }
        $byHost = [];
        foreach ($rows as $o) {
            $byHost[$o['hostname']][] = $o;
        }
        $failures = array_map(fn ($o) => [
            'station' => $o['station'] ?? '',
            'oem' => $o['oem'] ?? '',
            'device_type' => $o['device_type'] ?? '',
            'hostname' => $o['hostname'] ?? '',
            'timestamp' => $o['going_down'] ?? '',
            'duration_minutes' => $o['duration_minutes'] ?? 0,
        ], $rows);
        $most = [];
        foreach ($byHost as $hostname => $list) {
            $most[] = ['hostname' => $hostname, 'outage_count' => count($list)];
        }
        usort($most, fn ($a, $b) => $b['outage_count'] <=> $a['outage_count']);
        $mttr = [];
        foreach ($byHost as $hostname => $list) {
            $sum = array_sum(array_column($list, 'duration_minutes'));
            $mttr[] = ['hostname' => $hostname, 'mttr_minutes' => $sum / max(count($list), 1)];
        }
        $hosts = [];
        foreach ($assets as $a) {
            if (! empty($a['hostname'])) {
                $hosts[$a['hostname']] = true;
            }
        }
        foreach (array_keys($byHost) as $h) {
            $hosts[$h] = true;
        }
        $hostList = array_keys($hosts);
        sort($hostList);
        $availability = [];
        foreach ($hostList as $hostname) {
            $list = $byHost[$hostname] ?? [];
            $down = array_sum(array_column($list, 'duration_minutes'));
            $availability[] = [
                'hostname' => $hostname,
                'outage_count' => count($list),
                'outage_minutes' => $down,
                'availability_pct' => round(100 * (1 - $down / $windowMin), 4),
                'mttr_minutes' => $list ? $down / count($list) : null,
            ];
        }

        return [
            'from' => $fromIso,
            'to' => $toIso,
            'empty' => false,
            'timezone' => $fx['timezone'] ?? null,
            'failures' => $failures,
            'most_frequent' => $most,
            'mttr' => $mttr,
            'availability' => $availability,
            'alarm_stats' => [
                'count' => count($rows),
                'unique_devices' => count($byHost),
            ],
        ];
    }

    public static function csv(array $rows, array $headers): string
    {
        $lines = [implode(',', $headers)];
        foreach ($rows as $r) {
            $lines[] = implode(',', array_map(fn ($h) => $r[$h] ?? '', $headers));
        }

        return implode("\n", $lines) . "\n";
    }

    public static function pdf(string $title, string $body): string
    {
        $text = str_replace(['(', ')', '\\'], ' ', $title . "\n" . $body);
        $text = substr($text, 0, 800);
        $stream = "BT /F1 12 Tf 50 750 Td ($text) Tj ET";
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length ' . strlen($stream) . " >>\nstream\n$stream\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];
        $pdf = "%PDF-1.1\n";
        $offsets = [0];
        foreach ($objects as $i => $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n$obj\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref\n0 ' . (count($objects) + 1) . "\n0000000000 65535 f \n";
        for ($i = 1; $i < count($offsets); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }
        $pdf .= 'trailer << /Size ' . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n$xref\n%%EOF\n";

        return $pdf;
    }
}
