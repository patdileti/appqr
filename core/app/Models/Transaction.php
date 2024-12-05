<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public const STATUS_SUCCESS = 'success';
    public const STATUS_PENDING = 'pending';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'transaction';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'product_name',
        'product_id',
        'user_id',
        'status',
        'amount',
        'base_amount',
        'currency_code',
        'payment_id',
        'transaction_gatway',
        'transaction_method',
        'transaction_description',
        'transaction_ip',
        'frequency',
        'billing',
        'taxes_ids',
        'details',
        'coupon',
        'featured',
        'urgent',
        'highlight',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'coupon' => 'object',
        'billing' => 'object',
        'details' => 'object',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
