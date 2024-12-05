<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
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
     * Relationships
     */
    public function subcategories()
    {
        return $this->hasMany(MenuCategory::class, 'parent')
            ->with('menus')
            ->orderBy('position');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'category_id')
            ->orderBy('position');
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($category) {

            /* Delete category's menus */
            $category->menus->each(function ($menu) {
                $menu->delete();
            });
        });
    }
}
