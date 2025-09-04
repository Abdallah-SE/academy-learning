<?php

namespace Modules\Admin\Repositories\Interfaces;

use App\Repositories\BaseRepositoryInterface;
use App\Models\MembershipPackage;

interface MembershipRepositoryInterface extends BaseRepositoryInterface
{
    // Basic CRUD operations are already in BaseRepositoryInterface:
    // - create()
    // - findById() 
    // - update()
    // - delete()
    // - all() (list)
    
    // Just add simple find by name method
    public function findByName(string $name): ?MembershipPackage;
}
