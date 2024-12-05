<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/* New User Account Details Email */
class RestaurantOrder extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = (object) $data;
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
            '{RESTAURANT_NAME}' => $this->data->restaurant_name,
            '{CUSTOMER_NAME}' => $this->data->customer_name,
            '{TABLE_NUMBER}' => $this->data->table_number,
            '{PHONE_NUMBER}' => $this->data->phone_number,
            '{ADDRESS}' => $this->data->address,
            '{ORDER_TYPE}' => $this->data->order_type,
            '{ORDER}' => $this->data->order,
            '{MESSAGE}' => $this->data->message,
        ];

        $this->subject(str_replace(array_keys($short_codes), array_values($short_codes), config('settings.email_sub_new_order')));

        return $this->markdown('emails.default', [
            'body' => config('settings.email_message_new_order'),
            'short_codes' => $short_codes,
        ]);
    }
}
