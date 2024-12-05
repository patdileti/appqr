<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory, Sluggable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'blog';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'author',
        'title',
        'slug',
        'description',
        'image',
        'tags',
        'status',
    ];

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
        return $this->belongsTo(User::class, 'author', 'id');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_cat_relation', 'blog_id', 'category_id');
    }
}
