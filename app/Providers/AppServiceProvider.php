<?php

namespace App\Providers;

use Qcloud\Cos\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $defer = true;
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 腾讯COS的操作类
        $this->app->singleton(Client::class, function ($app) {
            return new Client(array('region' => config('qcloud.region'),
                'credentials'=> array(
                    'appId' => config('qcloud.app_id'),
                    'secretId' => config('qcloud.secret_id'),
                    'secretKey' => config('qcloud.secret_key')
                )
            ));
        });
    }

    public function provides()
    {
        return [Client::class];
    }
}
