<?php

namespace App\Mails;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/* Forgot Password Email */
class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $user;
    public $token;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param $token
     */
    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = url(url('/') . route('password.reset', ['token' => $this->token, 'email' => $this->user->getEmailForPasswordReset()], false));

        $expire = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        $short_codes = [
            '{SITE_TITLE}' => config('settings.site_title'),
            '{SITE_URL}' => route('home'),
            '{USER_ID}' => $this->user->id,
            '{USERNAME}' => $this->user->username,
            '{USER_FULLNAME}' => $this->user->name,
            '{EMAIL}' => $this->user->email,
            '{FORGET_PASSWORD_LINK}' => $url,
            '{EXPIRY_TIME}' => $expire,
        ];

        $this->subject(str_replace(array_keys($short_codes), array_values($short_codes), config('settings.email_sub_forgot_pass')));

        return $this->markdown('emails.default', [
            'body' => config('settings.email_message_forgot_pass'),
            'short_codes' => $short_codes,
        ]);
    }
}
