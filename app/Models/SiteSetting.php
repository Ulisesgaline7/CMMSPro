<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    /**
     * Get a setting value by key, with an optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    /**
     * Get all settings as a flat key→value array.
     */
    public static function allKeyed(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }

    /**
     * Get all settings for a group as a flat key→value array.
     */
    public static function group(string $group): array
    {
        return static::where('group', $group)->get()->pluck('value', 'key')->toArray();
    }
}
