<?php

namespace App\Models;

use App\Http\Controllers\User\PaymentMethods\PaypalController;
use App\Mails\ResetPassword;
use App\Mails\VerifyEmail;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Sluggable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'user';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'lastactive' => 'datetime',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'username' => [
                'source' => 'name',
            ],
        ];
    }

    /**
     * Send Password Reset Notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->sendMail(new ResetPassword($this, $token));
    }

    /**
     * Send email to the user
     *
     * @param Mailable $mailable
     * @return bool|string
     */
    public function sendMail(Mailable $mailable)
    {
        try {
            Mail::to($this->email)->send($mailable);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Send Email Verification Notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->sendMail(new VerifyEmail($this));
    }

    /**
     * Get user's plan
     *
     * @return mixed
     */
    public function plan()
    {
        if(is_numeric($this->group_id)){
            if($plan = Plan::find($this->group_id)){
                return $plan;
            } else {
                return config('settings.free_membership_plan');
            }
        } else if ($this->group_id == 'trial'){
            return config('settings.trial_membership_plan');
        } else {
            return config('settings.free_membership_plan');
        }
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->user_type == 'admin';
    }

    /**
     * Cancel recurring subscription if available
     */
    public function cancelRecurringSubscription() {
        if(!$this->upgrade) {
            return;
        }

        if(!$this->upgrade->unique_id) {
            return;
        }

        $data = explode('###', $this->upgrade->unique_id);
        $type = strtolower($data[0]);
        $subscription_id = $data[1];

        if($type == 'stripe'){
            /* Initiate Stripe */
            \Stripe\Stripe::setApiKey(config('settings.stripe_secret_key'));

            /* Cancel the Stripe Subscription */
            $subscription = \Stripe\Subscription::retrieve($subscription_id);
            $subscription->cancel();
        } else if($type == 'paypal'){
            $provider = PaypalController::getPaypalProvider();
            $provider->cancelSubscription($subscription_id, ___('Cancelled'));
        }

        /* reset the data */
        $this->upgrade->unique_id = null;
        $this->upgrade->pay_mode = 'one_time';
        $this->upgrade->save();
    }

    /**
     * Relationships
     */
    public function posts()
    {
        return $this->hasMany(Post::class)->latest();
    }

    public function upgrade()
    {
        return $this->hasOne(Upgrade::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
