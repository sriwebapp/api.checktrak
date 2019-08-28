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

Route::middleware(['auth:api'])->group(function() {
    Route::post('/logout', 'AuthController@logout');
    Route::get('/auth', 'AuthController@user');

    Route::apiResource('/user', 'UserController');
    Route::post('/user/{user}/access', 'UserController@access');

    Route::apiResource('/group', 'GroupController');
    Route::apiResource('/company', 'CompanyController');
    Route::apiResource('/branch', 'BranchController');

    Route::prefix('/{company}')->group( function() {
        Route::apiResource('/payee', 'PayeeController');
        Route::apiResource('/account', 'AccountController');

        Route::prefix('/check')->group( function() {
            Route::get('/', 'CheckController@index');
            Route::post('/create', 'CheckController@create');
            Route::post('/transmit', 'CheckController@transmit');
            Route::post('/receive', 'CheckController@receive');
            Route::post('/claim', 'CheckController@claim');
            Route::post('/clear', 'CheckController@clear');
            Route::patch('/return/{transmittal}', 'CheckController@return');
            Route::post('/cancel', 'CheckController@cancel');
            Route::get('/{check}', 'CheckController@show');
            Route::patch('/{check}', 'CheckController@edit');
        });
    });

    Route::prefix('/tools')->group( function() {
        Route::get('/actions', "ToolController@actions");
        Route::get('/branches', "ToolController@branches");
        Route::get('/modules', "ToolController@modules");
    });
});
