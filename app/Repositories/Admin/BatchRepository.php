<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Facades\DB;

class BatchRepository
{
    /**
     * Get paginated batches with optional search and status filters.
     */
    public function getPaginatedBatches(?string $search, ?string $status, int $perPage, int $offset)
    {
        $whereClauses = [];
        $bindings = [];

        if (! empty($search)) {
            $whereClauses[] = '(code LIKE ? OR name LIKE ?)';
            $bindings[] = "%{$search}%";
            $bindings[] = "%{$search}%";
        }

        if ($status !== null && $status !== 'all') {
            $whereClauses[] = 'status = ?';
            $bindings[] = $status;
        }

        $query = '';
        if (count($whereClauses) > 0) {
            $query = 'WHERE '.implode(' AND ', $whereClauses);
        }

        $dataBindings = array_merge($bindings, [$perPage, $offset]);

        return DB::select(
            "SELECT * from batch_master $query ORDER BY batch_id DESC LIMIT ? OFFSET ? ",
            $dataBindings
        );
    }

    /**
     * Get total count of batches for pagination.
     */
    public function getTotalBatchesCount(?string $search, ?string $status)
    {
        $whereClauses = [];
        $bindings = [];

        if (! empty($search)) {
            $whereClauses[] = '(code LIKE ? OR name LIKE ?)';
            $bindings[] = "%{$search}%";
            $bindings[] = "%{$search}%";
        }

        if ($status !== null && $status !== 'all') {
            $whereClauses[] = 'status = ?';
            $bindings[] = $status;
        }

        $query = '';
        if (count($whereClauses) > 0) {
            $query = 'WHERE '.implode(' AND ', $whereClauses);
        }

        return DB::selectOne(
            "SELECT COUNT(*) as count FROM batch_master $query",
            $bindings
        )->count;
    }

    /**
     * Create a new batch.
     */
    public function createBatch(array $data, int $createdBy, string $createdAt, string $updatedAt)
    {
        DB::insert(
            'INSERT INTO batch_master (code, name, status, created_by, created_at, updated_at) VALUES(?,?,?,?,?,?)',
            [$data['code'], $data['name'], $data['status'], $createdBy, $createdAt, $updatedAt]
        );
    }

    /**
     * Update an existing batch.
     */
    public function updateBatch(int $batchId, array $data, string $updatedAt)
    {
        DB::update(
            'UPDATE batch_master SET code = ?, name = ?, status = ?, updated_at = ? WHERE batch_id =?',
            [$data['code'], $data['name'], $data['status'], $updatedAt, $batchId],
        );
    }

    /**
     * Update the status of a specific batch.
     */
    public function updateBatchStatus(int $batchId, string $status, string $updatedAt)
    {
        DB::update('UPDATE batch_master set status = ?, updated_at = ? WHERE batch_id = ?',
            [$status, $updatedAt, $batchId]
        );
    }

    /**
     * Bulk update the status of multiple batches.
     */
    public function bulkUpdateBatchStatus(array $batchIds, string $status, string $updatedAt)
    {
        $placeholders = implode(',', array_fill(0, count($batchIds), '?'));
        $bindings = array_merge([$status, $updatedAt], $batchIds);

        DB::update("UPDATE batch_master SET status = ?, updated_at = ? WHERE batch_id IN ($placeholders)", $bindings);
    }
}
