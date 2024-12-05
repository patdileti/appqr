<?php
/* Auth */

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('auth/{provider}', 'Auth\SocialLoginController@redirect')->name('social.login');
Route::get('auth/{provider}/callback', 'Auth\SocialLoginController@callback')->name('social.callback');

Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');

Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

Route::middleware(['checkRegistrationDisabled'])->group(function () {

    Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('signup', 'Auth\RegisterController@register');
    Route::post('signup/check_availability', 'Auth\RegisterController@checkAvailability')->name('register.check_availability');

});

