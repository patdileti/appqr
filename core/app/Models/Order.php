<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
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
        'created_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class)->with(['menu', 'itemExtras', 'variant']);
    }

    public function restaurant()
    {
        return $this->belongsTo(Post::class, 'restaurant_id');
    }
}
