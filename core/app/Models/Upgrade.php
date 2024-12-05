<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upgrade extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'Active';
    
    public $timestamps = false;
    public $primaryKey = 'upgrade_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'sub_id',
        'user_id',
        'pay_mode',
        'interval',
        'upgrade_lasttime',
        'upgrade_expires',
        'unique_id',
        'status',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
