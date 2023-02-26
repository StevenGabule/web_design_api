<?php

namespace App\Providers;

use App\Repositories\Contracts\{IUser,IDesign};
use App\Repositories\Eloquent\{DesignRepository,UserRepository};
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
      $this->app->bind(IDesign::class, DesignRepository::class);
      $this->app->bind(IUser::class, UserRepository::class);
    }
}
