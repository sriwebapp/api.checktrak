<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/test', 'TestController@index');

Route::post('/login', 'AuthController@login');
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');
Route::get('/tools/companies', 'ToolController@companies');

Route::middleware(['auth:api'])->group(function() {
    Route::post('/logout', 'AuthController@logout');
    Route::get('/auth', 'AuthController@user');

    Route::apiResource('/user', 'UserController');
    Route::post('/user/{user}/access', 'UserController@access');

    Route::apiResource('/access', 'AccessController');
    Route::apiResource('/company', 'CompanyController');
    Route::apiResource('/branch', 'BranchController');

    Route::prefix('/{company}')->group( function() {
        Route::apiResource('/payee', 'PayeeController');
        Route::apiResource('/account', 'AccountController');

        Route::get('/transmittal', 'TransmittalController@index');
        Route::get('/transmittal/{transmittal}', 'TransmittalController@show');

        Route::prefix('/check')->group( function() {
            Route::post('/', 'CheckController@index');
            Route::post('/create', 'CheckController@create');
            Route::post('/transmit', 'CheckController@transmit');
            Route::post('/receive', 'CheckController@receive');
            Route::post('/claim', 'CheckController@claim');
            Route::post('/clear', 'CheckController@clear');
            Route::post('/return', 'CheckController@return');
            Route::post('/cancel', 'CheckController@cancel');
            Route::get('/{check}', 'CheckController@show');
            Route::patch('/{check}', 'CheckController@edit');
            Route::delete('/{check}', 'CheckController@delete');
        });
    });

    Route::prefix('/tools')->group( function() {
        Route::get('/actions', "ToolController@actions");
        Route::get('/branches', "ToolController@branches");
        Route::get('/company/{company}', 'ToolController@company');
        Route::get('/access', "ToolController@access");
        Route::get('/modules', "ToolController@modules");
        Route::get('/users', 'ToolController@users');
        Route::get('/payee-group', 'ToolController@payeeGroup');
        Route::get('/payees/{company}', 'ToolController@payees');
        Route::get('/accounts/{company}', 'ToolController@accounts');
        Route::get('/series/{company}/{branch}', 'ToolController@series');
        // Route::get('/transmittals/sent/{company}', 'ToolController@sentTransmittals');
        // Route::get('/transmittals/returned/{company}', 'ToolController@returnedTransmittals');
        Route::get('/transmittals/received/{company}', 'ToolController@receivedTransmittals');
        Route::get('/checks/{transmittal}', 'ToolController@checks');
    });
});
