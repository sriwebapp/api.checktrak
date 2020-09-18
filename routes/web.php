<?php
// Route::get('/test', 'TestController@index');

Route::middleware('csrf')->group( function() {
    Route::get('/', function () {
        return redirect('/telescope');
    })->middleware('auth');

    Route::get('/logout', 'Auth\LoginController@showLogoutForm')->middleware('auth');

    Auth::routes([
        'register' => false, // Registration Routes...
        'reset' => false, // Password Reset Routes...
        'verify' => false, // Email Verification Routes...
    ]);
});

Route::get('/transmittal/{transmittal}/export', 'TransmittalController@export');

Route::post('/export/check', 'ExportController@check')->middleware('checkUiRequest');
