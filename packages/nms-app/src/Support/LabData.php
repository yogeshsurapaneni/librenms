<?php

namespace NmsApp\Support;

class LabData
{
    public static function testDataDir(): string
    {
        return rtrim(getenv('NMS_TEST_DATA') ?: '/opt/nms-data/test-data', '/');
    }

    public static function topologyDir(): string
    {
        return rtrim(getenv('NMS_TOPOLOGY') ?: '/opt/nms-data/topology', '/');
    }

    public static function json(string $name): array
    {
        $path = self::testDataDir() . '/' . $name;
        if (! is_file($path)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($path), true);

        return is_array($data) ? $data : [];
    }

    public static function assets(): array
    {
        $data = self::json('nms_assets.json');

        return array_is_list($data) ? $data : [];
    }
}
