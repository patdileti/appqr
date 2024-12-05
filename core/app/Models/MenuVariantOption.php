<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuVariantOption extends Model
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
        'translations' => 'object',
        'options' => 'object',
    ];

    /**
     * Override the default value
     *
     * @param $value
     * @return mixed
     */
    public function getTitleAttribute($value)
    {
        if (!empty($_COOKIE['Quick_user_lang_code'])
            && !empty($this->translations->{$_COOKIE['Quick_user_lang_code']}->title)) {
            return $this->translations->{$_COOKIE['Quick_user_lang_code']}->title;
        } else {
            return $value;
        }
    }

    /**
     * Override the default value
     *
     * @param $value
     * @return mixed
     */
    public function getOptionsAttribute($value)
    {
        if (!empty($_COOKIE['Quick_user_lang_code'])
            && !empty($this->translations->{$_COOKIE['Quick_user_lang_code']}->options)) {
            return $this->translations->{$_COOKIE['Quick_user_lang_code']}->options;
        } else {
            return json_decode($value);
        }
    }
}
