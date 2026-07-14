<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Facades\DB;

class ProgrammeRepository
{
    /**
     * Get paginated programmes with optional search and status filters.
     */
    public function getPaginatedProgrammes(?string $search, ?string $status, int $perPage, int $offset)
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
            "SELECT * from programme_master $query ORDER BY programme_id ASC LIMIT ? OFFSET ? ",
            $dataBindings
        );
    }

    /**
     * Get total count of programmes for pagination.
     */
    public function getTotalProgrammesCount(?string $search, ?string $status)
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
            "SELECT COUNT(*) as count FROM programme_master $query",
            $bindings
        )->count;
    }

    /**
     * Create a new programme.
     */
    public function createProgramme(array $data, int $createdBy, string $createdAt, string $updatedAt)
    {
        DB::insert('INSERT INTO programme_master (code, name, status, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?)', [
            $data['code'], $data['name'], $data['status'], $createdBy, $createdAt, $updatedAt,
        ]);
    }

    /**
     * Update an existing programme.
     */
    public function updateProgramme(int $programmeId, array $data, string $updatedAt)
    {
        DB::update('UPDATE programme_master SET code = ? , name = ? , status = ?, updated_at = ? WHERE programme_id = ?', [
            $data['code'], $data['name'], $data['status'], $updatedAt, $programmeId,
        ]);
    }

    /**
     * Update the status of a specific programme.
     */
    public function updateProgrammeStatus(int $programmeId, string $status, string $updatedAt)
    {
        DB::update('UPDATE programme_master SET status = ?, updated_at = ? WHERE programme_id = ?', [
            $status, $updatedAt, $programmeId,
        ]);
    }

    /**
     * Bulk update the status of multiple programmes.
     */
    public function bulkUpdateProgrammeStatus(array $programmeIds, string $status, string $updatedAt)
    {
        $placeholders = implode(',', array_fill(0, count($programmeIds), '?'));
        $bindings = array_merge([$status, $updatedAt], $programmeIds);

        DB::update("UPDATE programme_master SET status = ? , updated_at = ? WHERE programme_id IN ($placeholders)", $bindings);
    }
}
