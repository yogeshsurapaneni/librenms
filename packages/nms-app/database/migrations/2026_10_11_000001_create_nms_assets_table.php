<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('nms_assets')) {
            Schema::create('nms_assets', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('device_id')->nullable()->index();
                $table->string('asset_id')->unique();
                $table->string('hostname')->index();
                $table->string('device_type')->nullable();
                $table->string('make')->nullable();
                $table->string('model')->nullable();
                $table->string('firmware')->nullable();
                $table->string('install_date')->nullable();
                $table->string('station')->nullable();
                $table->double('lat')->nullable();
                $table->double('lng')->nullable();
                $table->string('role')->nullable();
                $table->string('vendor')->nullable();
                $table->timestamps();
            });
        }
        $file = getenv('NMS_TEST_DATA') ? rtrim(getenv('NMS_TEST_DATA'), '/') . '/nms_assets.json' : '/opt/nms-data/test-data/nms_assets.json';
        if (is_file($file) && DB::table('nms_assets')->count() === 0) {
            $rows = json_decode((string) file_get_contents($file), true) ?: [];
            foreach ($rows as $r) {
                DB::table('nms_assets')->updateOrInsert(
                    ['asset_id' => $r['asset_id']],
                    [
                        'hostname' => $r['hostname'] ?? '',
                        'device_type' => $r['device_type'] ?? null,
                        'make' => $r['make'] ?? null,
                        'model' => $r['model'] ?? null,
                        'firmware' => $r['firmware'] ?? null,
                        'install_date' => $r['install_date'] ?? null,
                        'station' => $r['station'] ?? null,
                        'lat' => $r['lat'] ?? null,
                        'lng' => $r['lng'] ?? null,
                        'role' => $r['role'] ?? null,
                        'vendor' => $r['vendor'] ?? null,
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('nms_assets');
    }
};
