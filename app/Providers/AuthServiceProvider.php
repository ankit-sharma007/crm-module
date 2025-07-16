<?php

namespace App\Providers;
use App\Models\Lead;
use App\Policies\LeadPolicy;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
                Lead::class => LeadPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('view-lead-activity', function (User $user, LeadActivity $activity) {
            return $user->is_admin || $activity->user_id === $user->id || $activity->lead->assigned_to === $user->id;
        });
    }
}
