<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
	/**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Laravel’s IoC Container
        
        $this->app->bind(
        'App\Repositories\Interfaces\PartialViewsRepositoryInterface',
        'App\Repositories\PartialViewsRepositories'
        );

        $this->app->bind(
        'App\Repositories\Interfaces\ActivitiesRepositoryInterface',
        'App\Repositories\ActivityRepositories'
       );
    }
}