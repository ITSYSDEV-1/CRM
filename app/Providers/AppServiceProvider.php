<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\Configuration;

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
        //
        $configuration=Configuration::find(1);
        view()->share('configuration',$configuration);
        Schema::defaultStringLength(191);
    }
}
