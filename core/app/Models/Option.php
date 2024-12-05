<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $primaryKey = 'option_id';

    public $timestamps = false;

    /**
     * Get settings
     */
    public static function selectOptions($key)
    {
        $setting = Option::where('option_name', $key)->first();
        if ($setting) {
            return $setting->option_value;
        }
        return false;
    }

    /**
     * Update settings
     */
    public static function updateOptions($key, $value)
    {
        if(is_array($value)){
            $value = json_encode($value);
        }
        $setting = Option::where('option_name', $key)->first();
        if ($setting) {
            $setting->option_value = $value;
            return $setting->save();
        } else {
            $setting = new Option();
            $setting->option_name = $key;
            $setting->option_value = $value;
            return $setting->save();
        }
    }
}
