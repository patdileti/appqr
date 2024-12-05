<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, Sluggable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function views()
    {
        return $this->hasMany(PostView::class);
    }

    public function options()
    {
        return $this->hasMany(PostOption::class);
    }

    public function menu_categories()
    {
        return $this->hasMany(MenuCategory::class, 'restaurant_id')
            ->with(['subcategories', 'menus'])
            ->whereNull('parent')
            ->orderBy('position');
    }

    public function image_menus()
    {
        return $this->hasMany(ImageMenu::class, 'restaurant_id')
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

        self::deleting(function ($restaurant) {
            /* Delete the related images before deleting the post */

            if (!empty($restaurant->cover_image) && $restaurant->cover_image != 'default.png') {
                remove_file('storage/restaurant/cover/'.$restaurant->cover_image);
            }
            if (!empty($restaurant->main_image) && $restaurant->main_image != 'default.png') {
                remove_file('storage/restaurant/logo/'.$restaurant->main_image);
            }

            /* Delete categories */
            $restaurant->menu_categories->each(function ($item) {
                $item->delete();
            });

            /* Delete image menus */
            $restaurant->image_menus->each(function ($item) {
                $item->delete();
            });
        });
    }
}
