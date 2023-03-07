<?php

namespace App\Providers;

use App\Repositories\Contracts\{IChat, IComment, IInvitation, IMessage, ITeam, IUser, IDesign};
use App\Repositories\Eloquent\{ChatRepository,
  CommentRepository,
  DesignRepository,
  InvitationRepository,
  MessageRepository,
  TeamRepository,
  UserRepository};
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
      $this->app->bind(ITeam::class, TeamRepository::class);
      $this->app->bind(IInvitation::class, InvitationRepository::class);
      $this->app->bind(IMessage::class, MessageRepository::class);
      $this->app->bind(IChat::class, ChatRepository::class);
    }
}
