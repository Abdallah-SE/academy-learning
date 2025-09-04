<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * The model instance
     */
    protected Model $model;

    /**
     * Constructor
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all models
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Find model by ID
     */
    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find model by field
     */
    public function findByField(string $field, $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    /**
     * Create new model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update model
     */
    public function update(int $id, array $data): Model
    {
        $model = $this->findById($id);

        if (!$model) {
            throw new \Exception('Model not found');
        }

        $model->update($data);
        return $model->fresh();
    }

    /**
     * Delete model
     */
    public function delete(int $id): bool
    {
        $model = $this->findById($id);

        if (!$model) {
            return false;
        }

        return $model->delete();
    }

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
    ): LengthAwarePaginator {
        $query = $this->model->newQuery();

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply search
        if ($search && !empty($searchableFields)) {
            $this->applySearch($query, $search, $searchableFields);
        }

        // Apply sorting
        $this->applySorting($query, $sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get filtered results
     */
    public function getFiltered(array $filters = []): Collection
    {
        $query = $this->model->newQuery();
        $this->applyFilters($query, $filters);
        return $query->get();
    }

    /**
     * Search models
     */
    public function search(string $query, array $fields = []): Collection
    {
        $queryBuilder = $this->model->newQuery();
        $this->applySearch($queryBuilder, $query, $fields);
        return $queryBuilder->get();
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'created_today' => $this->model->whereDate('created_at', today())->count(),
            'created_this_week' => $this->model->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'created_this_month' => $this->model->whereMonth('created_at', now()->month)->count(),
        ];
    }

    /**
     * Bulk operations
     */
    public function bulkAction(string $action, array $ids, array $data = []): array
    {
        $results = [];

        DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                switch ($action) {
                    case 'delete':
                        $results[$id] = $this->delete($id);
                        break;
                    case 'update':
                        $results[$id] = $this->update($id, $data);
                        break;
                    case 'activate':
                        $results[$id] = $this->update($id, ['status' => 'active']);
                        break;
                    case 'deactivate':
                        $results[$id] = $this->update($id, ['status' => 'inactive']);
                        break;
                    default:
                        $results[$id] = false;
                }
            }

            DB::commit();
            return $results;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Export data
     */
    public function export(array $filters = [], string $format = 'json'): array
    {
        $data = $this->getFiltered($filters);

        switch ($format) {
            case 'json':
                return $data->toArray();
            case 'csv':
                return $this->toCsv($data);
            case 'xml':
                return $this->toXml($data);
            default:
                return $data->toArray();
        }
    }

    /**
     * Count models
     */
    public function count(array $filters = []): int
    {
        $query = $this->model->newQuery();
        $this->applyFilters($query, $filters);
        return $query->count();
    }

    /**
     * Check if model exists
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get model with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Get models with relationships
     */
    public function getWithRelations(array $relations = []): Collection
    {
        return $this->model->with($relations)->get();
    }

    /**
     * Get models by condition
     */
    public function getWhere(array $conditions): Collection
    {
        return $this->model->where($conditions)->get();
    }

    /**
     * Get first model by condition
     */
    public function getFirstWhere(array $conditions): ?Model
    {
        return $this->model->where($conditions)->first();
    }

    /**
     * Update or create model
     */
    public function updateOrCreate(array $search, array $data): Model
    {
        return $this->model->updateOrCreate($search, $data);
    }

    /**
     * Get models in chunks
     */
    public function chunk(int $count, callable $callback): void
    {
        $this->model->chunk($count, $callback);
    }

    /**
     * Get models cursor
     */
    public function cursor(array $filters = []): \Generator
    {
        $query = $this->model->newQuery();
        $this->applyFilters($query, $filters);
        return $query->cursor();
    }

    /**
     * Get models with pagination cursor
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } elseif (is_string($value) && str_contains($value, '%')) {
                $query->where($field, 'like', $value);
            } else {
                $query->where($field, $value);
            }
        }
    }

    /**
     * Apply search to query
     */
    protected function applySearch(Builder $query, string $search, array $fields): void
    {
        if (!empty($fields)) {
            $query->where(function ($q) use ($search, $fields) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting(Builder $query, string $sortBy, string $sortDirection): void
    {
        $query->orderBy($sortBy, $sortDirection);
    }

    /**
     * Convert to CSV format
     */
    protected function toCsv(Collection $data): array
    {
        if ($data->isEmpty()) {
            return [];
        }

        $headers = array_keys($data->first()->toArray());
        $csv = [implode(',', $headers)];

        foreach ($data as $item) {
            $csv[] = implode(',', array_values($item->toArray()));
        }

        return $csv;
    }

    /**
     * Convert to XML format
     */
    protected function toXml(Collection $data): string
    {
        if ($data->isEmpty()) {
            return '<?xml version="1.0" encoding="UTF-8"?><data></data>';
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?><data>';

        foreach ($data as $item) {
            $xml .= '<item>';
            foreach ($item->toArray() as $key => $value) {
                $xml .= "<{$key}>" . htmlspecialchars($value) . "</{$key}>";
            }
            $xml .= '</item>';
        }

        $xml .= '</data>';
        return $xml;
    }

    /**
     * Get the model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Set the model instance
     */
    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * Get new query builder
     */
    public function newQuery(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Get table name
     */
    public function getTable(): string
    {
        return $this->model->getTable();
    }

    /**
     * Get connection
     */
    public function getConnection()
    {
        return $this->model->getConnection();
    }


    /**
     * Find model by ID including soft deleted
     */
    public function findWithTrashed(int $id): ?Model
    {
        if (method_exists($this->model, 'withTrashed')) {
            return $this->model->withTrashed()->find($id);
        }

        return $this->findById($id);
    }

    /**
     * Find only soft deleted model by ID
     */
    public function findOnlyTrashed(int $id): ?Model
    {
        if (method_exists($this->model, 'onlyTrashed')) {
            return $this->model->onlyTrashed()->find($id);
        }

        return null;
    }

    /**
     * Restore soft deleted model
     */
    public function restore(int $id): bool
    {
        if (method_exists($this->model, 'onlyTrashed')) {
            $model = $this->findOnlyTrashed($id);

            if (!$model) {
                return false;
            }

            return $model->restore();
        }

        return false;
    }

    /**
     * Force delete model permanently
     */
    public function forceDelete(int $id): bool
    {
        if (method_exists($this->model, 'forceDelete')) {
            $model = $this->findWithTrashed($id);

            if (!$model) {
                return false;
            }

            return $model->forceDelete();
        }

        return $this->delete($id);
    }

    /**
     * Get paginated soft deleted models
     */
    public function getTrashed(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        if (method_exists($this->model, 'onlyTrashed')) {
            $query = $this->model->onlyTrashed();

            if (!empty($filters)) {
                $this->applyFilters($query, $filters);
            }

            return $query->paginate($perPage);
        }

        return $this->getPaginated($filters, null, [], 'created_at', 'desc', $perPage);
    }
}
