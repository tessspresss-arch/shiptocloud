<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'label',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = Cache::rememberForever("setting_{$key}", function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string'): bool
    {
        $castedValue = static::prepareValue($value, $type);

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $castedValue,
                'type' => $type,
            ]
        );

        // Clear cache
        Cache::forget("setting_{$key}");

        return $setting ? true : false;
    }

    /**
     * Get all settings as array
     */
    public static function getAll(): array
    {
        return Cache::rememberForever('all_settings', function () {
            $settings = [];
            static::all()->each(function ($setting) use (&$settings) {
                $settings[$setting->key] = static::castValue($setting->value, $setting->type);
            });
            return $settings;
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('all_settings');
        // Also clear individual setting caches
        static::all()->each(function ($setting) {
            Cache::forget("setting_{$setting->key}");
        });
    }

    /**
     * Cast value based on type
     */
    private static function castValue($value, string $type)
    {
        switch ($type) {
            case 'integer':
            case 'int':
                return (int) $value;
            case 'boolean':
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true);
            case 'float':
            case 'double':
                return (float) $value;
            default:
                return $value;
        }
    }

    /**
     * Prepare value for storage based on type
     */
    private static function prepareValue($value, string $type): string
    {
        switch ($type) {
            case 'boolean':
            case 'bool':
                return $value ? '1' : '0';
            case 'json':
                return json_encode($value);
            default:
                return (string) $value;
        }
    }

    /**
     * Get settings for a specific category with metadata
     */
    
    /**
     * Get all settings as array
     */
    public static function getAllAsArray(): array
    {
        return static::all()->mapWithKeys(function ($setting) {
            return [$setting->key => [
                'value' => static::castValue($setting->value, $setting->type),
                'type' => $setting->type,
                'label' => $setting->label,
                'description' => $setting->description,
            ]];
        })->toArray();
    }
}
