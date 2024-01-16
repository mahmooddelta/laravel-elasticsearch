<?php

namespace Afzali\LaravelElasticsearch\Providers;

use Afzali\LaravelElasticsearch\ElasticBuilder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('Elastic', function () {
            return new ElasticBuilder();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
