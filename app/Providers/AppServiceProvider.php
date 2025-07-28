<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {



        if (request('lang')) {
            App::setLocale(request('lang') == 'Hindi' ? 'hi' : 'en');
        }

        $state = DB::table("state_city")->distinct('state')->select("state")->get();
        $setting = DB::table("company_settings")->where("id", 1)->first();
        $Leadstatus = DB::table("status")->get();



        View::share('state', $state);
        View::share('setting', $setting);
        View::share('Leadstatus', $Leadstatus);
        // Bootstrap 5
        Paginator::useBootstrapFive();

        // Bootstrap 4
        Paginator::useBootstrapFour();

        // Bootstrap 4
        Paginator::useBootstrap();
    }
}
