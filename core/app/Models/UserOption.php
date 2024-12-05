<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOption extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get UserOption by key
     */
    public static function getUserOption($userId, $key, $default = null)
    {
        $option = UserOption::where([['user_id', $userId], ['option_name', $key]])->first();
        if ($option) {
            return $option->option_value;
        }
        return (!empty($default)) ? $default : false;
    }

    /**
     * Update UserOption from table.
     */
    public static function updateUserOption($userId, $key, $value)
    {
        $option = UserOption::where([['user_id', $userId], ['option_name', $key]])->first();
        if ($option) {
            $option->option_value = $value;
            return $option->save();
        } else {
            $option = new UserOption();
            $option->user_id = $userId;
            $option->option_name = $key;
            $option->option_value = $value;
            return $option->save();
        }
    }

}
