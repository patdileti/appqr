<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('options')) {
            Schema::create('options', function (Blueprint $table) {
                $table->bigIncrements('option_id');
                $table->string('option_name', 191)->nullable()->unique('option_name');
                $table->longText('option_value')->nullable();
            });

            $options = [
                ['option_name' => 'site_title', 'option_value' => 'QuickQR Digital Menu'],
                ['option_name' => 'tpl_name', 'option_value' => 'classic-theme'],
                ['option_name' => 'site_logo', 'option_value' => 'classic-theme_logo.png'],
                ['option_name' => 'site_favicon', 'option_value' => 'favicon.png'],
                ['option_name' => 'site_logo_footer', 'option_value' => 'classic-theme_footer_logo.png'],
                ['option_name' => 'site_admin_logo', 'option_value' => 'adminlogo.png'],
                ['option_name' => 'social_share_image', 'option_value' => 'social_share.jpg'],
                ['option_name' => 'meta_keywords', 'option_value' => 'QuickQR, Digital Menu'],
                ['option_name' => 'meta_description', 'option_value' => 'QuickQR Digital Menu'],
                ['option_name' => 'default_user_plan', 'option_value' => 'free'],
                ['option_name' => 'termcondition_link', 'option_value' => ''],
                ['option_name' => 'privacy_link', 'option_value' => ''],
                ['option_name' => 'cookie_link', 'option_value' => ''],
                ['option_name' => 'non_active_msg', 'option_value' => '1'],
                ['option_name' => 'non_active_allow', 'option_value' => '1'],
                ['option_name' => 'userlangsel', 'option_value' => '1'],
                ['option_name' => 'cookie_consent', 'option_value' => '1'],
                ['option_name' => 'demo_mode', 'option_value' => '0'],
                ['option_name' => 'developer_credit', 'option_value' => '1'],
                ['option_name' => 'quickad_debug', 'option_value' => '0'],
                ['option_name' => 'timezone', 'option_value' => 'Asia/Kolkata'],
                ['option_name' => 'lang', 'option_value' => 'en'],
                ['option_name' => 'currency_sign', 'option_value' => '$'],
                ['option_name' => 'currency_code', 'option_value' => 'USD'],
                ['option_name' => 'currency_pos', 'option_value' => '1'],
                ['option_name' => 'admin_email', 'option_value' => 'test@gmail.com'],
                ['option_name' => 'smtp_host', 'option_value' => null],
                ['option_name' => 'smtp_port', 'option_value' => null],
                ['option_name' => 'smtp_username', 'option_value' => 'admin'],
                ['option_name' => 'smtp_password', 'option_value' => ''],
                ['option_name' => 'smtp_secure', 'option_value' => 'tls'],
                ['option_name' => 'smtp_auth', 'option_value' => 'true'],
                ['option_name' => 'smtp_from_email', 'option_value' => 'admin@gmail.com'],
                ['option_name' => 'smtp_from_name', 'option_value' => 'Admin'],
                ['option_name' => 'smtp_mailer', 'option_value' => 'log'],
                ['option_name' => 'show_membershipplan_home', 'option_value' => '1'],
                ['option_name' => 'show_partner_logo_home', 'option_value' => '1'],
                ['option_name' => 'theme_color', 'option_value' => '#2a41e8'],
                ['option_name' => 'contact_address', 'option_value' => 'test address'],
                ['option_name' => 'contact_phone', 'option_value' => '+9876543210'],
                ['option_name' => 'contact_email', 'option_value' => 'test@gmail.com'],
                ['option_name' => 'facebook_link', 'option_value' => 'https://facebook.com/bylancer.in'],
                ['option_name' => 'twitter_link', 'option_value' => 'https://x.com/bylancer'],
                ['option_name' => 'instagram_link', 'option_value' => 'https://instagram.com/bylancer'],
                ['option_name' => 'linkedin_link', 'option_value' => 'https://linkedin.com/bylancer'],
                ['option_name' => 'pinterest_link', 'option_value' => 'https://pinterest.com/bylancer'],
                ['option_name' => 'youtube_link', 'option_value' => 'https://www.youtube.com/c/Bylancer'],
                ['option_name' => 'external_code', 'option_value' => null],
                ['option_name' => 'invoice_nr_prefix', 'option_value' => 'INV-'],
                ['option_name' => 'invoice_admin_name', 'option_value' => ''],
                ['option_name' => 'invoice_admin_email', 'option_value' => ''],
                ['option_name' => 'invoice_admin_phone', 'option_value' => ''],
                ['option_name' => 'invoice_admin_address', 'option_value' => ''],
                ['option_name' => 'invoice_admin_city', 'option_value' => ''],
                ['option_name' => 'invoice_admin_state', 'option_value' => ''],
                ['option_name' => 'invoice_admin_zipcode', 'option_value' => ''],
                ['option_name' => 'invoice_admin_country', 'option_value' => ''],
                ['option_name' => 'invoice_admin_tax_type', 'option_value' => 'Tax ID'],
                ['option_name' => 'invoice_admin_tax_id', 'option_value' => ''],
                ['option_name' => 'facebook_login', 'option_value' => '0'],
                ['option_name' => 'facebook_app_id', 'option_value' => ''],
                ['option_name' => 'facebook_app_secret', 'option_value' => ''],
                ['option_name' => 'google_login', 'option_value' => '0'],
                ['option_name' => 'google_app_id', 'option_value' => ''],
                ['option_name' => 'google_app_secret', 'option_value' => ''],
                ['option_name' => 'recaptcha_mode', 'option_value' => '0'],
                ['option_name' => 'recaptcha_public_key', 'option_value' => ''],
                ['option_name' => 'recaptcha_private_key', 'option_value' => ''],
                ['option_name' => 'blog_enable', 'option_value' => '1'],
                ['option_name' => 'blog_banner', 'option_value' => '1'],
                ['option_name' => 'show_blog_home', 'option_value' => '1'],
                ['option_name' => 'blog_comment_enable', 'option_value' => '1'],
                ['option_name' => 'blog_comment_approval', 'option_value' => '1'],
                ['option_name' => 'blog_comment_user', 'option_value' => '1'],
                ['option_name' => 'testimonials_enable', 'option_value' => '1'],
                ['option_name' => 'show_testimonials_blog', 'option_value' => '1'],
                ['option_name' => 'show_testimonials_home', 'option_value' => '1'],
                ['option_name' => 'specific_country', 'option_value' => ''],
                ['option_name' => 'disable_landing_page', 'option_value' => '0'],
                ['option_name' => 'enable_user_registration', 'option_value' => '1'],
                ['option_name' => 'enable_force_ssl', 'option_value' => '0'],
                ['option_name' => 'include_language_code', 'option_value' => '1'],
                ['option_name' => 'enable_faqs', 'option_value' => '1'],
                ['option_name' => 'blog_page_limit', 'option_value' => '8'],
                ['option_name' => 'google_analytics', 'option_value' => '{"status":"0","measurement_id":null}'],
                ['option_name' => 'tawk_to', 'option_value' => '{"status":"0","chat_link":null}'],
                [
                    'option_name' => 'free_membership_plan',
                    'option_value' => '{"id":"free","status":"1","name":"Free Plan","description":"test","settings":{"category_limit":"10","menu_limit":"10","scan_limit":"50","allow_ordering":"1","hide_branding":"0","advertisements":"1","custom_features":null}}'
                ],
                [
                    'option_name' => 'trial_membership_plan',
                    'option_value' => '{"id":"trial","status":"1","name":"Trial Plan","description":null,"settings":{"category_limit":"999","menu_limit":"999","scan_limit":"500","allow_ordering":"1","hide_branding":"1","advertisements":"1","custom_features":null},"days":"10"}'
                ],
                ['option_name' => 'admin_allergies', 'option_value' => '1'],
                ['option_name' => 'admin_send_order_notification', 'option_value' => '1'],
                ['option_name' => 'admin_allow_online_payment', 'option_value' => '1'],
                ['option_name' => 'restaurant_text_editor', 'option_value' => '0'],
                [
                    'option_name' => 'email_sub_signup_details',
                    'option_value' => '{SITE_TITLE} - Thanks for signing up {USER_FULLNAME}!'
                ],
                ['option_name' => 'email_sub_signup_confirm', 'option_value' => '{SITE_TITLE} - Email Confirmation'],
                ['option_name' => 'email_sub_forgot_pass', 'option_value' => '{SITE_TITLE} - Forgot Password'],
                [
                    'option_name' => 'email_sub_contact',
                    'option_value' => 'Website Email: - {NAME} wants to contact you'
                ],
                ['option_name' => 'email_sub_feedback', 'option_value' => '{FEEDBACK_SUBJECT}'],
                [
                    'option_name' => 'email_message_signup_details', 'option_value' => '<p>Dear Valued Thanks for creating an account with {SITE_TITLE} ,</p>
<p>Your username: {USERNAME}</p>
<p>Have further questions?</p>
<p>You can start chat with live support team. Sincerely,</p>
<p>{SITE_TITLE} Team!</p>
<p>{SITE_URL}</p>'
                ],
                [
                    'option_name' => 'email_message_signup_confirm', 'option_value' => '<p>Greetings from {SITE_TITLE} Team!</p>
<p>Thanks for registering with {SITE_TITLE}. We are thrilled to have you as a registered member and hope that you find our service beneficial. Before we get you started please activate your account by clicking on the link below</p>
<p><a href="{CONFIRMATION_LINK}">{CONFIRMATION_LINK}</a></p>
<p>After your Account activation you can access the website. Once you have your Profile filled in you are ready to go.</p>
<p>{SITE_TITLE} Team!</p>
<p>{SITE_URL}</p>'
                ],
                [
                    'option_name' => 'email_message_forgot_pass', 'option_value' => '<p>To reset your password please click the link below<br><br>{FORGET_PASSWORD_LINK}</p>
<p>{SITE_TITLE} Team!</p>
<p>{SITE_URL}</p>'
                ],
                [
                    'option_name' => 'email_message_contact', 'option_value' => '<p>{NAME} wants to contact you</p>
<p>{SITE_TITLE}:</p>
<p>Name : {NAME}</p>
<p>Email : {EMAIL}</p>
<p>Message : {MESSAGE}</p>
<p>------------------------------------------</p>
<p>This message has been sent automatically by the {SITE_TITLE} system.</p>'
                ],
                [
                    'option_name' => 'email_message_feedback', 'option_value' => '<p>{NAME} Send a feedback</p>
<p>{SITE_TITLE}:</p>
<p>Name : {NAME}</p>
<p>Email : {EMAIL}</p>
<p>Phone : {PHONE}</p>
<p>Message : {MESSAGE}</p>
<p>------------------------------------------</p>
<p>This message has been sent automatically by the {SITE_TITLE} system.</p>'
                ],
                ['option_name' => 'email_sub_new_order', 'option_value' => '{RESTAURANT_NAME} - New Order'],
                [
                    'option_name' => 'email_message_new_order', 'option_value' => '<p>{RESTAURANT_NAME}</p>
<p>New Order</p>
<p>Customer: {CUSTOMER_NAME}<br>Table Number: {TABLE_NUMBER}<br>Message: {MESSAGE}</p>
<p>Order<br>{ORDER}</p>'
                ],
            ];

            DB::table('options')->insert($options);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
    }
};
