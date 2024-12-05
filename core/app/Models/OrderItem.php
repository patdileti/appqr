<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
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
     * Relationships
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'item_id');
    }

    public function variant()
    {
        return $this->belongsTo(MenuVariant::class, 'variation');
    }

    public function itemExtras()
    {
        return $this->hasMany(OrderItemExtra::class)->with('extra');
    }
}
