<?php

namespace NmsApp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NmsApp\Support\LabData;
use NmsApp\Support\Reports;

class ReportController extends Controller
{
    public function query(Request $request)
    {
        return response()->json(Reports::compute($request->query('from'), $request->query('to')));
    }

    public function failuresCsv(Request $request)
    {
        $r = Reports::compute($request->query('from'), $request->query('to'));
        $csv = Reports::csv($r['failures'], ['station', 'oem', 'device_type', 'hostname', 'timestamp', 'duration_minutes']);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=failures.csv',
        ]);
    }

    public function availabilityCsv(Request $request)
    {
        $r = Reports::compute($request->query('from'), $request->query('to'));
        $csv = Reports::csv($r['availability'], ['hostname', 'outage_minutes', 'availability_pct', 'mttr_minutes', 'outage_count']);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=availability.csv',
        ]);
    }

    public function pdf(Request $request)
    {
        $r = Reports::compute($request->query('from'), $request->query('to'));
        $buf = Reports::pdf('NMS report', json_encode($r['alarm_stats'] ?? $r));

        return response($buf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=report.pdf',
        ]);
    }

    public function availability()
    {
        $fixture = LabData::json('report-expected.json') ?: LabData::json('outages-fixture.json');

        return response()->json(['source' => 'fixture'] + $fixture);
    }
}
