<?php

namespace Modules\Admin\Providers;

use App\Services\ImageService;
use App\Services\Interfaces\ImageServiceInterface;
use Illuminate\Support\ServiceProvider;
use Modules\Admin\Repositories\Interfaces\AdminRepositoryInterface;
use Modules\Admin\Repositories\Eloquent\AdminRepository;
use Modules\Admin\Repositories\Interfaces\MembershipRepositoryInterface;
use Modules\Admin\Repositories\Eloquent\MembershipRepository;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Admin Repository
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        
        $this->app->bind(\Modules\Admin\Services\AdminService::class);
        $this->app->bind(ImageServiceInterface::class, ImageService::class);

        // Bind Membership Repository
        $this->app->bind(MembershipRepositoryInterface::class, MembershipRepository::class);
    
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
