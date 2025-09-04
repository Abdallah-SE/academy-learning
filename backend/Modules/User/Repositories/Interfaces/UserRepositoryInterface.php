<?php

namespace Modules\User\Repositories\Interfaces;

use App\Repositories\BaseRepositoryInterface;
use App\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    // Basic CRUD operations are already in BaseRepositoryInterface:
    // - create()
    // - findById() 
    // - update()
    // - delete()
    // - all() (list)
    
    // Just add simple find by name method
    public function findByName(string $name): ?User;
}
