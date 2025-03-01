<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SettingOutlet1 extends Model
{
    use HasFactory;

    protected $connection = 'mysql_1';
    protected $table = 'settings';

    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $cacheKey = 'setting_'.$key;
        $result = null;
        if (! Cache::get('setting_'.$key)) {
            $setting = self::where('key', $key)->first();

            $result = $setting ? $setting->value : $default;

            Cache::put($cacheKey, $result, now()->addMinutes(3 * 60));

            return $result;
        }

        return Cache::get($cacheKey);
    }

    public static function set($key, $value)
    {
        // Update the value in the database
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Update the value in cache
        $cacheKey = 'setting_'.$key;
        Cache::put($cacheKey, $value, now()->addMinutes(3 * 60));
    }
}
