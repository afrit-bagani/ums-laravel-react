<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Facades\DB;

class CourseRepository
{
    /**
     * Get paginated courses with optional search and status filters.
     */
    public function getPaginatedCourses(?string $search, ?string $status, int $perPage, int $offset)
    {
        $whereClause = [];
        $bindings = [];

        if (! empty($search)) {
            $whereClause[] = '(c.code LIKE ? OR c.name LIKE ?)';
            $bindings[] = "%{$search}%";
            $bindings[] = "%{$search}%";
        }

        if ($status !== null && $status !== 'all') {
            $whereClause[] = 'c.status = ?';
            $bindings[] = $status;
        }

        $query = '';
        if (count($whereClause) > 0) {
            $query = 'WHERE '.implode(' AND ', $whereClause);
        }

        $dataBindings = array_merge($bindings, [$perPage, $offset]);

        return DB::select("
            SELECT c.*, p.code as programme_code 
            FROM course_master c 
            JOIN programme_master p ON c.programme_id = p.programme_id 
            $query 
            ORDER BY c.created_at DESC 
            LIMIT ? OFFSET ?", $dataBindings);
    }

    /**
     * Get total count of courses for pagination.
     */
    public function getTotalCoursesCount(?string $search, ?string $status)
    {
        $whereClause = [];
        $bindings = [];

        if (! empty($search)) {
            $whereClause[] = '(c.code LIKE ? OR c.name LIKE ?)';
            $bindings[] = "%{$search}%";
            $bindings[] = "%{$search}%";
        }

        if ($status !== null && $status !== 'all') {
            $whereClause[] = 'c.status = ?';
            $bindings[] = $status;
        }

        $query = '';
        if (count($whereClause) > 0) {
            $query = 'WHERE '.implode(' AND ', $whereClause);
        }

        return DB::selectOne("
            SELECT COUNT(*) as count 
            FROM course_master c 
            JOIN programme_master p ON c.programme_id = p.programme_id 
            $query", $bindings)->count;
    }

    /**
     * Get all active programmes.
     */
    public function getActiveProgrammes()
    {
        $results = DB::select("SELECT programme_id, name FROM programme_master WHERE status = 'active'");
        return array_map(fn ($item) => (array) $item, $results);
    }

    /**
     * Create a new course.
     */
    public function createCourse(array $data, int $createdBy, string $createdAt, string $updatedAt)
    {
        DB::insert('INSERT INTO course_master (programme_id, code, name, status, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?)', [
            $data['programme_id'],
            $data['code'],
            $data['name'],
            $data['status'],
            $createdBy,
            $createdAt,
            $updatedAt,
        ]);
    }

    /**
     * Update an existing course.
     */
    public function updateCourse(int $courseId, array $data, string $updatedAt)
    {
        DB::update('UPDATE course_master SET programme_id = ?, code = ?, name = ?, status = ?, updated_at = ? where course_id = ?', [
            $data['programme_id'], 
            $data['code'], 
            $data['name'], 
            $data['status'], 
            $updatedAt, 
            $courseId,
        ]);
    }

    /**
     * Update the status of a specific course.
     */
    public function updateCourseStatus(int $courseId, string $status, string $updatedAt)
    {
        DB::update('UPDATE course_master SET status = ? , updated_at = ? WHERE course_id = ?', [
            $status,
            $updatedAt,
            $courseId,
        ]);
    }

    /**
     * Bulk update the status of multiple courses.
     */
    public function bulkUpdateCourseStatus(array $courseIds, string $status, string $updatedAt)
    {
        $placeHolders = implode(',', array_fill(0, count($courseIds), '?'));
        $bindings = array_merge([$status, $updatedAt], $courseIds);

        DB::update("UPDATE course_master SET status = ?, updated_at = ? WHERE course_id IN ($placeHolders)", $bindings);
    }
}
