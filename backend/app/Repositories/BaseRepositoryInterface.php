<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Get all models
     */
    public function all(): Collection;

    /**
     * Find model by ID
     */
    public function findById(int $id): ?Model;

    /**
     * Find model by field
     */
    public function findByField(string $field, $value): ?Model;

    /**
     * Create new model
     */
    public function create(array $data): Model;

    /**
     * Update model
     */
    public function update(int $id, array $data): Model;

    /**
     * Delete model
     */
    public function delete(int $id): bool;

    /**
     * Get paginated results
     */
    public function getPaginated(
        array $filters = [],
        ?string $search = null,
        array $searchableFields = [],
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 15
    ): LengthAwarePaginator;

    /**
     * Get filtered results
     */
    public function getFiltered(array $filters = []): Collection;

    /**
     * Search models
     */
    public function search(string $query, array $fields = []): Collection;

    /**
     * Get statistics
     */
    public function getStatistics(): array;

    /**
     * Bulk operations
     */
    public function bulkAction(string $action, array $ids, array $data = []): array;

    /**
     * Export data
     */
    public function export(array $filters = [], string $format = 'json'): array;

    /**
     * Count models
     */
    public function count(array $filters = []): int;

    /**
     * Check if model exists
     */
    public function exists(int $id): bool;

    /**
     * Get model with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Model;

    /**
     * Get models with relationships
     */
    public function getWithRelations(array $relations = []): Collection;

    /**
     * Get models by condition
     */
    public function getWhere(array $conditions): Collection;

    /**
     * Get first model by condition
     */
    public function getFirstWhere(array $conditions): ?Model;

    /**
     * Update or create model
     */
    public function updateOrCreate(array $search, array $data): Model;

    /**
     * Get models in chunks
     */
    public function chunk(int $count, callable $callback): void;

    /**
     * Get models cursor
     */
    public function cursor(array $filters = []): \Generator;

    /**
     * Get models with pagination cursor
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): \Illuminate\Pagination\CursorPaginator;



    // Soft delete methods (optional - only for models that use SoftDeletes)
    public function findWithTrashed(int $id): ?Model;
    public function findOnlyTrashed(int $id): ?Model;
    public function restore(int $id): bool;
    public function forceDelete(int $id): bool;
    public function getTrashed(int $perPage = 15, array $filters = []): LengthAwarePaginator;
}
