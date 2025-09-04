<?php

namespace Modules\User\Repositories\Eloquent;

use Modules\User\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    // Basic CRUD operations are inherited from BaseRepository:
    // - create()
    // - findById() 
    // - update()
    // - delete()
    // - all() (list)

    /**
     * Find user by name
     */
    public function findByName(string $name): ?User
    {
        return $this->model->where('name', 'like', "%{$name}%")->first();
    }
}
