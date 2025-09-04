<?php

namespace Modules\Admin\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdminRepositoryInterface
{
    public function all(array $filters = []);
    public function find(int $id);
    public function findByField(string $field, $value);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findByEmail(string $email);
    public function findByUsername(string $username);
    public function paginate(int $perPage = 15, array $filters = []);
    public function getDashboardData(): array;

    // Soft delete methods
    public function findWithTrashed(int $id);
    public function findOnlyTrashed(int $id);
    public function restore(int $id): bool;
    public function forceDelete(int $id): bool;
    public function getTrashed(int $perPage = 15, array $filters = []): LengthAwarePaginator;
}
