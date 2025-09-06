<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use Modules\Admin\Models\Admin;
use Modules\Admin\Repositories\Interfaces\AdminRepositoryInterface;
use App\Models\User;
use App\Models\MembershipPackage;
use App\Models\UserMembership;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{
    public function __construct(Admin $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all admins with optional filters
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        if (!empty($filters)) {
            $this->applyFilters($query, $filters);
        }

        return $query->get();
    }

    /**
     * Find admin by ID
     */
    public function find(int $id): ?Admin
    {
        return $this->findById($id);
    }

    /**
     * Find admin by field
     */
    public function findByField(string $field, $value): ?Admin
    {
        return parent::findByField($field, $value);
    }

    /**
     * Create new admin
     */
    public function create(array $data): Admin
    {
        return parent::create($data);
    }

    /**
     * Update admin
     */
    public function update(int $id, array $data): Admin
    {
        return parent::update($id, $data);
    }

    /**
     * Delete admin
     */
    public function delete(int $id): bool
    {
        return parent::delete($id);
    }

    /**
     * Find admin by email
     */
    public function findByEmail(string $email): ?Admin
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Find admin by username
     */
    public function findByUsername(string $username): ?Admin
    {
        return $this->model->where('username', $username)->first();
    }

    /**
     * Get paginated admins
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filters)) {
            $this->applyFilters($query, $filters);
        }

        return $query->with('roles')->paginate($perPage);
    }

    /**
     * Get dashboard data for admin
     */
    public function getDashboardData(): array
    {
        return [
            'total_users' => User::count(),
            'total_packages' => MembershipPackage::count(),
            'total_memberships' => UserMembership::count(),
            'active_admins' => $this->model->active()->count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_packages' => MembershipPackage::latest()->take(5)->get(),
        ];
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters): void
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['role'])) {
            $query->role($filters['role']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('username', 'LIKE', "%{$searchTerm}%");
            });
        }
    }


    /**
     * Find admin by ID including soft deleted
     */
    public function findWithTrashed(int $id): ?Admin
    {
        return $this->model->withTrashed()->find($id);
    }

    /**
     * Find only soft deleted admin by ID
     */
    public function findOnlyTrashed(int $id): ?Admin
    {
        return $this->model->onlyTrashed()->find($id);
    }

    /**
     * Restore soft deleted admin
     */
    public function restore(int $id): bool
    {
        $admin = $this->findOnlyTrashed($id);

        if (!$admin) {
            return false;
        }

        return $admin->restore();
    }

    /**
     * Force delete admin permanently
     */
    public function forceDelete(int $id): bool
    {
        $admin = $this->findWithTrashed($id);

        if (!$admin) {
            return false;
        }

        return $admin->forceDelete();
    }

    /**
     * Get paginated soft deleted admins
     */
    public function getTrashed(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->onlyTrashed();

        if (!empty($filters)) {
            $this->applyFilters($query, $filters);
        }

        return $query->with('roles')->paginate($perPage);
    }
}
