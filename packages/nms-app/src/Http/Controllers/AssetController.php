<?php

namespace NmsApp\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use NmsApp\Support\LabData;

class AssetController extends Controller
{
    public function index()
    {
        $rows = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('nms_assets')) {
                $rows = DB::table('nms_assets')->get()->map(fn ($r) => (array) $r)->all();
            }
        } catch (\Throwable $e) {
            $rows = [];
        }
        if (! $rows) {
            $rows = LabData::assets();
        }

        return response()->json(['assets' => $rows]);
    }
}
