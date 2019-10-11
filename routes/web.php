<?php
Route::get('/test/{transmittal}', 'TestController@index');

Route::get('/', function () {
    return redirect('/telescope');
})->middleware('auth');

Route::get('/logout', 'Auth\LoginController@showLogoutForm')->middleware('auth');

Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);

