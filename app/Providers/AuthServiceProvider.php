<?php

  namespace App\Providers;

  use App\Models\Design;
  use App\Models\Invitation;
  use App\Models\Team;
  use App\Policies\DesignPolicy;
  use App\Policies\InvitationPolicy;
  use App\Policies\TeamPolicy;
  use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
  use Laravel\Passport\Passport;

  class AuthServiceProvider extends ServiceProvider
  {
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
      Design::class => DesignPolicy::class,
      Team::class => TeamPolicy::class,
      Invitation::class => InvitationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
      $this->registerPolicies();
      Passport::refreshTokensExpireIn(now()->addDays(30));
    }
  }
