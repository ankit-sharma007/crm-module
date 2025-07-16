<?php

namespace App\Providers;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Lead::class => \App\Policies\LeadPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('view-lead-activity', function (User $user, LeadActivity $activity) {
            return $user->is_admin || $user->id === $activity->user_id;
        });
    }
}
