<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory, Sluggable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'content',
        'slug',
        'type',
        'translation_lang',
        'active',
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
    public function language()
    {
        return $this->belongsTo(Language::class, 'translation_lang', 'code');
    }
}
