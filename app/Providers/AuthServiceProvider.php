<?php

  namespace App\Providers;

  use App\Models\Design;
  use App\Policies\DesignPolicy;
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
      // 'App\Models\Model' => 'App\Policies\ModelPolicy',
      Design::class => DesignPolicy::class,
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
