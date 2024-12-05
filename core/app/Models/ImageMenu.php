<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageMenu extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($menu) {
            /* Delete the related images before deleting the post */

            if (!empty($menu->image) && $menu->image != 'default.png') {
                remove_file('storage/menu/'.$menu->image);
            }
        });
    }
}
