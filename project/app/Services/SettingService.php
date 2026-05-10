<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    private static array $cache = [];

    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key];
        }

        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        $value = match ($setting->type->value) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            default   => $setting->value,
        };

        static::$cache[$key] = $value;

        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        Setting::where('key', $key)->update(['value' => $value]);
        static::$cache[$key] = $value;
    }

    public function all(): array
    {
        return Setting::all()->pluck('value', 'key')->toArray();
    }
}
