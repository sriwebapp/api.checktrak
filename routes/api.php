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
Route::post('/test/{company}', 'TestController@index');

Route::post('/login', 'AuthController@login');
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');
Route::get('/tools/companies', 'ToolController@companies');

Route::middleware(['auth:api'])->group(function() {
    Route::post('/logout', 'AuthController@logout');
    Route::get('/auth', 'AuthController@user');
    Route::post('/auth/avatar', 'AuthController@avatar');

    Route::apiResource('/user', 'UserController');
    Route::post('/user/{user}/access', 'UserController@access');

    Route::apiResource('/access', 'AccessController');
    Route::apiResource('/company', 'CompanyController');
    Route::apiResource('/branch', 'BranchController');
    Route::apiResource('/group', 'GroupController');

    Route::prefix('/{company}')->group( function() {
        Route::apiResource('/account', 'AccountController');

        Route::prefix('check-book')->group( function() {
            Route::post('/', 'CheckBookController@index');
            Route::post('/create', 'CheckBookController@store');
            Route::get('/{check_book}', 'CheckBookController@show');
            Route::delete('/{check_book}', 'CheckBookController@destroy');
        });

        Route::prefix('/payee')->group( function() {
            Route::post('/', 'PayeeController@index');
            Route::post('/create', 'PayeeController@store');
            Route::get('/{payee}', 'PayeeController@show');
            Route::patch('/{payee}', 'PayeeController@update');
            Route::delete('/{payee}', 'PayeeController@destroy');
        });

        Route::post('/transmittal', 'TransmittalController@index');
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
            Route::post('/stale', 'CheckController@stale');
            Route::post('/undo', 'CheckController@undo');
            Route::get('/{check}', 'CheckController@show');
            Route::patch('/{check}', 'CheckController@edit');
            Route::delete('/{check}', 'CheckController@delete');
        });

        Route::patch('/history/{history}', 'HistoryController@update');

        Route::prefix('/import')->group( function() {
            Route::post('/check', 'ImportController@check');
            Route::post('/clear-check', 'ImportController@clearCheck');
            Route::post('/payee', 'ImportController@payee');
        });
        Route::apiResource('/import', 'ImportController');

        Route::prefix('/report')->group( function() {
            Route::post('/masterlist', 'ReportController@countMasterlist');
        });
    });

    Route::prefix('/tools')->group( function() {
        Route::get('/actions', "ToolController@actions");
        Route::get('/status', "ToolController@status");
        Route::get('/branches', "ToolController@branches");
        Route::get('/groups', "ToolController@groups");
        Route::get('/company/{company}', 'ToolController@company');
        Route::get('/access', "ToolController@access");
        Route::get('/modules', "ToolController@modules");
        Route::get('/reports', "ToolController@reports");
        Route::get('/payee-group', 'ToolController@payeeGroup');
        Route::get('/users', 'ToolController@users');
        Route::get('/users/{branch}', 'ToolController@branchUsers');
        Route::get('/incharge/{group}', 'ToolController@groupIncharge');
        Route::post('/payees/{company}', 'ToolController@payees');
        Route::get('/accounts/{company}', 'ToolController@accounts');
        Route::get('/transmittalRef/{company}/{branch}', 'ToolController@transmittalRef');
        Route::get('/transmittals/received/{company}', 'ToolController@receivedTransmittals');
        Route::get('/transmittals/sent/{company}', 'ToolController@sentTransmittals');
        Route::get('/transmittals/returned/{company}', 'ToolController@returnedTransmittals');
        Route::post('/transmittals/{company}', 'ToolController@transmittals');
        Route::get('/staled-checks/{company}', 'ToolController@staledChecks');
        Route::get('/checks/{transmittal}', 'ToolController@checks');
        Route::get('/{company}/report/masterlist', 'ToolController@masterlistReport');
    });
});
