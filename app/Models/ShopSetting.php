<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'int' => (int) $setting->value,
            'decimal' => (float) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            default => $setting->value,
        };
    }
}
