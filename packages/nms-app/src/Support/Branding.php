<?php

namespace NmsApp\Support;

class Branding
{
    public static function title(): ?string
    {
        $raw = getenv('NMS_APP_TITLE');
        if ($raw === false || trim($raw) === '') {
            return null;
        }
        $title = trim(strip_tags($raw));
        if ($title === '') {
            return null;
        }

        return mb_substr($title, 0, 64);
    }

    public static function primary(): string
    {
        return self::hex(getenv('NMS_BRAND_PRIMARY') ?: '', '#4f46e5');
    }

    public static function accent(): string
    {
        return self::hex(getenv('NMS_BRAND_ACCENT') ?: '', '#06b6d4');
    }

    public static function payload(): array
    {
        return [
            'title' => self::title() ?: 'LibreNMS',
            'primary' => self::primary(),
            'accent' => self::accent(),
        ];
    }

    public static function hex(string $value, string $fallback): string
    {
        $value = trim($value);
        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
            return strtolower($value);
        }

        return $fallback;
    }
}
