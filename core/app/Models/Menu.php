<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    public $table = 'menu';
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'translations' => 'object',
    ];

    /**
     * Override the name attribute
     *
     * @param $value
     * @return mixed
     */
    public function getNameAttribute($value)
    {
        if (!empty($_COOKIE['Quick_user_lang_code'])
            && !empty($this->translations->{$_COOKIE['Quick_user_lang_code']}->title)) {
            return $this->translations->{$_COOKIE['Quick_user_lang_code']}->title;
        } else {
            return $value;
        }
    }

    /**
     * Override the description attribute
     *
     * @param $value
     * @return mixed
     */
    public function getDescriptionAttribute($value)
    {
        if (!empty($_COOKIE['Quick_user_lang_code'])
            && !empty($this->translations->{$_COOKIE['Quick_user_lang_code']}->description)) {
            return $this->translations->{$_COOKIE['Quick_user_lang_code']}->description;
        } else {
            return $value;
        }
    }

    /**
     * Relationships
     */
    public function variantOptions()
    {
        return $this->hasMany(MenuVariantOption::class)->orderBy('position');
    }

    public function variants()
    {
        return $this->hasMany(MenuVariant::class)->orderBy('position');
    }

    public function extras()
    {
        return $this->hasMany(MenuExtra::class)->orderBy('position');
    }

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
