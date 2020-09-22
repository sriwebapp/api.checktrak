<?php
Route::get('/test/{check}', 'TestController@index');

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

Route::prefix('/export')->group( function() {
    Route::post('/transmittal', 'ExportController@transmittal');

    Route::post('/check', 'ExportController@check')->middleware('checkUiRequest');
});
