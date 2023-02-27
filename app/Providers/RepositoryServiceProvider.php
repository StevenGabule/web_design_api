<?php

namespace App\Providers;

use App\Repositories\Contracts\{IComment, IUser, IDesign};
use App\Repositories\Eloquent\{CommentRepository, DesignRepository, UserRepository};
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
      $this->app->bind(IComment::class, CommentRepository::class);
    }
}
