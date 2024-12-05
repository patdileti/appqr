<?php

namespace App\Mails;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/* New User Account Details Email */
class UserDetails extends Mailable
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
        $short_codes = [
            '{SITE_TITLE}' => config('settings.site_title'),
            '{SITE_URL}' => route('home'),
            '{USER_ID}' => $this->user->id,
            '{USERNAME}' => $this->user->username,
            '{USER_FULLNAME}' => $this->user->name,
            '{EMAIL}' => $this->user->email,
        ];

        $this->subject(str_replace(array_keys($short_codes), array_values($short_codes), config('settings.email_sub_signup_details')));

        return $this->markdown('emails.default', [
            'body' => config('settings.email_message_signup_details'),
            'short_codes' => $short_codes,
        ]);
    }
}
