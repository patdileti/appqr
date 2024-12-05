<?php

namespace App\Mails;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

/* New User Confirmation Email */
class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $this->user->getKey(),
                'hash' => sha1($this->user->getEmailForVerification()),
            ]
        );

        $short_codes = [
            '{SITE_TITLE}' => config('settings.site_title'),
            '{SITE_URL}' => route('home'),
            '{USER_ID}' => $this->user->id,
            '{USERNAME}' => $this->user->username,
            '{USER_FULLNAME}' => $this->user->name,
            '{EMAIL}' => $this->user->email,
            '{CONFIRMATION_LINK}' => $verificationUrl,
        ];

        $this->subject(str_replace(array_keys($short_codes), array_values($short_codes), config('settings.email_sub_signup_confirm')));

        return $this->markdown('emails.default', [
            'body' => config('settings.email_message_signup_confirm'),
            'short_codes' => $short_codes,
        ]);
    }
}
