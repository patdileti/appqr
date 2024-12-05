<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaiterCall extends Model
{
    use HasFactory;

    public $table = 'waiter_call';
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * Relationships
     */
    public function restaurant()
    {
        return $this->belongsTo(Post::class, 'restaurant_id');
    }
}
