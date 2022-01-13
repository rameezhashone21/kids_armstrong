<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Models\WebSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
  public function boot(AppSetting $appSetting, WebSetting $webSetting)
  {
    // Get admin dashbaord basic settings
    $appSettings = $appSetting->getAppSetting(1);
    View::share('appSettings', $appSettings);

    // Get website basic settings
    $webSettings = $webSetting->getWebSetting(1);
    View::share('webSettings', $webSettings);

    // Custom blade directives //
    Blade::if('permission', function ($permission) {
      return Auth::check() && Auth::user()->checkPermission($permission);
    });
    // Custom blade directives //
  }
}
