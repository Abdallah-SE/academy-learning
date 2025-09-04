<?php

namespace Modules\Admin\Repositories\Eloquent;

use Modules\Admin\Repositories\Interfaces\MembershipRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\MembershipPackage;

class MembershipRepository extends BaseRepository implements MembershipRepositoryInterface
{
    public function __construct(MembershipPackage $model)
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
     * Find package by name
     */
    public function findByName(string $name): ?MembershipPackage
    {
        return $this->model->where('name', 'like', "%{$name}%")->first();
    }
}

