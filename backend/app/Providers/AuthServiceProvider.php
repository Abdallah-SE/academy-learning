<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\MembershipPackage;
use App\Models\UserMembership;
use Modules\Admin\Models\Admin;
use App\Policies\UserPolicy;
use App\Policies\MembershipPackagePolicy;
use App\Policies\AdminPolicy;
use App\Policies\UserMembershipPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        MembershipPackage::class => MembershipPackagePolicy::class,
        UserMembership::class => UserMembershipPolicy::class,
        Admin::class => AdminPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define additional gates if needed
        Gate::define('access-admin-panel', function (User $user) {
            return $user->hasRole(['admin', 'moderator']);
        });

        Gate::define('manage-system-settings', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('view-dashboard', function (User $user) {
            return $user->hasAnyRole(['admin', 'moderator', 'user']);
        });

        Gate::define('export-data', function (User $user) {
            return $user->hasPermissionTo('export.data') || $user->hasRole('admin');
        });

        Gate::define('import-data', function (User $user) {
            return $user->hasPermissionTo('import.data') || $user->hasRole('admin');
        });

        Gate::define('manage-backups', function (User $user) {
            return $user->hasPermissionTo('manage.backups') || $user->hasRole('admin');
        });

        Gate::define('view-logs', function (User $user) {
            return $user->hasPermissionTo('view.logs') || $user->hasRole('admin');
        });

        Gate::define('manage-notifications', function (User $user) {
            return $user->hasPermissionTo('manage.notifications') || $user->hasRole(['admin', 'moderator']);
        });

        Gate::define('manage-reports', function (User $user) {
            return $user->hasPermissionTo('manage.reports') || $user->hasRole(['admin', 'moderator']);
        });
    }
}
