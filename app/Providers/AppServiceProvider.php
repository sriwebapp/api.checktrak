<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Validator::extend('unique2', function ($attribute, $value, $parameters, $validator) {
            return DB::table($parameters[0])
                ->where($parameters[1], $value)
                ->where($parameters[2], $parameters[3])
                ->count() === 0;
        });

        Validator::extend('unique2NotDeleted', function ($attribute, $value, $parameters, $validator) {
            return DB::table($parameters[0])
                ->where($parameters[1], $value)
                ->where($parameters[2], $parameters[3])
                ->where('deleted_at', null)
                ->count() === 0;
        });
    }
}
