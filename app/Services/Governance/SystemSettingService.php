<?php

namespace App\Services\Governance;

use App\Models\Setting;

class SystemSettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    public function set(string $key, mixed $value, string $type = 'string'): bool
    {
        return Setting::set($key, $value, $type);
    }

    public function setBulk(array $entries): void
    {
        foreach ($entries as $entry) {
            $key = (string) ($entry['key'] ?? '');
            if ($key === '') {
                continue;
            }

            $value = $entry['value'] ?? null;
            $type = (string) ($entry['type'] ?? 'string');
            $this->set($key, $value, $type);
        }

        Setting::clearCache();
    }

    public function getByPrefix(string $prefix): array
    {
        return Setting::query()
            ->where('key', 'like', $prefix . '%')
            ->get()
            ->mapWithKeys(function (Setting $setting) {
                return [$setting->key => Setting::get($setting->key)];
            })
            ->toArray();
    }
}
