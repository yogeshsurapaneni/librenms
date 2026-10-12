<?php

use Illuminate\Support\Facades\Route;
use NmsApp\Http\Controllers\AlertController;
use NmsApp\Http\Controllers\AssetController;
use NmsApp\Http\Controllers\AuthMeController;
use NmsApp\Http\Controllers\BrandController;
use NmsApp\Http\Controllers\DeviceController;
use NmsApp\Http\Controllers\EventlogController;
use NmsApp\Http\Controllers\ReportController;
use NmsApp\Http\Controllers\SpaController;
use NmsApp\Http\Controllers\StatusController;
use NmsApp\Http\Controllers\SummaryController;
use NmsApp\Http\Controllers\TopologyController;

Route::middleware(['web'])->prefix('nms')->group(function () {
    Route::get('brand/logo', [BrandController::class, 'logo']);
    Route::get('brand/theme.css', [BrandController::class, 'theme']);
});

Route::middleware(['web', 'nms-auth'])->prefix('nms')->group(function () {
    Route::get('api/me', [AuthMeController::class, 'show']);
    Route::get('auth/me', [AuthMeController::class, 'show']);
    Route::get('api/assets', [AssetController::class, 'index']);
    Route::get('api/summary', [SummaryController::class, 'show']);
    Route::get('api/status/stream', [StatusController::class, 'stream']);
    Route::get('api/topology', [TopologyController::class, 'show']);
    Route::get('api/devices', [DeviceController::class, 'index']);
    Route::get('api/devices/{hostname}', [DeviceController::class, 'show'])->where('hostname', '.*');
    Route::get('api/alerts', [AlertController::class, 'index']);
    Route::put('api/alerts/{id}', [AlertController::class, 'ack']);
    Route::get('api/logs/eventlog', [EventlogController::class, 'index']);
    Route::get('api/reports/query', [ReportController::class, 'query']);
    Route::get('api/reports/failures.csv', [ReportController::class, 'failuresCsv']);
    Route::get('api/reports/availability.csv', [ReportController::class, 'availabilityCsv']);
    Route::get('api/reports/query.pdf', [ReportController::class, 'pdf']);
    Route::get('api/reports/availability', [ReportController::class, 'availability']);
    Route::get('{path?}', [SpaController::class, 'index'])->where('path', '.*');
});
