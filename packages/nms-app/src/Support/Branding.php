<?php

namespace NmsApp\Support;

class Branding
{
    public static function title(): ?string
    {
        $raw = self::osEnv('NMS_APP_TITLE');
        if ($raw === null || trim($raw) === '') {
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
        return self::hex(self::osEnv('NMS_BRAND_PRIMARY') ?: '', '#4f46e5');
    }

    public static function accent(): string
    {
        return self::hex(self::osEnv('NMS_BRAND_ACCENT') ?: '', '#06b6d4');
    }

    /**
     * php-fpm in the LibreNMS image sets clear_env=yes, so getenv() is empty
     * for HTTP requests. Docker still exposes the variables on PID 1.
     */
    public static function osEnv(string $key): ?string
    {
        foreach ([getenv($key), $_SERVER[$key] ?? null, $_ENV[$key] ?? null] as $v) {
            if (is_string($v) && $v !== '') {
                return $v;
            }
        }
        static $pid1 = null;
        if ($pid1 === null) {
            $pid1 = [];
            $raw = @file_get_contents('/proc/1/environ') ?: '';
            foreach (explode("\0", $raw) as $pair) {
                if (str_contains($pair, '=')) {
                    [$k, $val] = explode('=', $pair, 2);
                    $pid1[$k] = $val;
                }
            }
        }

        return $pid1[$key] ?? null;
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
