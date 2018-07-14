<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;


class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        News::class => News::class,

    ];
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
        $this->app->bind(Text::class, function ($app) {
            return new Text('');
        });

        $this->app->bind(NewsItem::class, function ($app) {
            return new NewsItem();
        });
    }
}
