<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Facades\DB;

class SubjectRepository
{
    /**
     * Get paginated subjects with optional search and status filters.
     */
    public function getPaginatedSubjects(?string $search, ?string $status, int $perPage, int $offset)
    {
        $whereClause = [];
        $bindings = [];

        if (! empty($search)) {
            $whereClause[] = '(s.code LIKE ? OR s.name LIKE ?)';
            $bindings[] = "%$search%";
            $bindings[] = "%$search%";
        }

        if ($status !== null && $status !== 'all') {
            $whereClause[] = 's.status = ?';
            $bindings[] = $status;
        }

        $query = '';
        if (count($whereClause) > 0) {
            $query = 'WHERE '.implode(' AND ', $whereClause);
        }

        $dataBindings = array_merge($bindings, [$perPage, $offset]);

        return DB::select("
            SELECT s.subject_id, s.code, s.name, s.status, c.code as course_code, p.code as programme_code
            FROM subject_master s 
            JOIN course_master c on s.course_id = c.course_id
            JOIN programme_master p on c.programme_id = p.programme_id
            $query 
            ORDER BY s.created_at DESC 
            LIMIT ? OFFSET ?",
            $dataBindings
        );
    }

    /**
     * Get total count of subjects for pagination.
     */
    public function getTotalSubjectsCount(?string $search, ?string $status)
    {
        $whereClause = [];
        $bindings = [];

        if (! empty($search)) {
            $whereClause[] = '(s.code LIKE ? OR s.name LIKE ?)';
            $bindings[] = "%$search%";
            $bindings[] = "%$search%";
        }

        if ($status !== null && $status !== 'all') {
            $whereClause[] = 's.status = ?';
            $bindings[] = $status;
        }

        $query = '';
        if (count($whereClause) > 0) {
            $query = 'WHERE '.implode(' AND ', $whereClause);
        }

        return DB::selectOne("
            SELECT COUNT(*) as count
            FROM subject_master s 
            JOIN course_master c on s.course_id = c.course_id
            JOIN programme_master p on c.programme_id = p.programme_id
            $query", $bindings
        )->count;
    }

    /**
     * Get active programmes and courses for dropdowns.
     */
    public function getActiveProgrammesAndCourses()
    {
        $programmes = DB::select("SELECT programme_id, name as programme_name FROM programme_master WHERE status = 'active'");
        $courses = DB::select("SELECT course_id, programme_id, CONCAT(code, ' - ',  name) as course_name FROM course_master WHERE status = 'active' ORDER BY created_at DESC");

        $groupedCourses = [];
        foreach ($courses as $course) {
            $groupedCourses[$course->programme_id][] = [
                'course_id' => $course->course_id,
                'course_name' => $course->course_name,
            ];
        }

        $result = [];
        foreach ($programmes as $prog) {
            $result[] = [
                'programme_id' => $prog->programme_id,
                'programme_name' => $prog->programme_name,
                'courses' => $groupedCourses[$prog->programme_id] ?? [],
            ];
        }

        return $result;
    }

    /**
     * Create a new subject.
     */
    public function createSubject(array $data, int $createdBy, int $updatedBy, string $createdAt, string $updatedAt)
    {
        DB::insert('INSERT INTO subject_master (
            programme_id, course_id, code, name, status, 
            internal_full_marks, internal_pass_marks, 
            theory_full_marks, theory_pass_marks, 
            practical_full_marks, practical_pass_marks, 
            created_by, updated_by, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $data['programme_id'],
            $data['course_id'],
            $data['code'],
            $data['name'],
            $data['status'],
            $data['internal_full_marks'],
            $data['internal_pass_marks'],
            $data['theory_full_marks'],
            $data['theory_pass_marks'],
            $data['practical_full_marks'],
            $data['practical_pass_marks'],
            $createdBy,
            $updatedBy,
            $createdAt,
            $updatedAt,
        ]);
    }

    /**
     * Get a subject by ID with relations for the Show view.
     */
    public function getSubjectWithRelationsById(int $subjectId)
    {
        return DB::selectOne('
            SELECT s.*, 
                   c.name as course_name, c.code as course_code, 
                   p.name as programme_name, p.code as programme_code,
                   u1.name as created_by_name, u2.name as updated_by_name
            FROM subject_master s 
            LEFT JOIN course_master c on s.course_id = c.course_id
            LEFT JOIN programme_master p on s.programme_id = p.programme_id
            LEFT JOIN users u1 on s.created_by = u1.id
            LEFT JOIN users u2 on s.updated_by = u2.id
            WHERE s.subject_id = ?
        ', [$subjectId]);
    }

    /**
     * Get a subject by ID for editing.
     */
    public function getSubjectById(int $subjectId)
    {
        return DB::selectOne('SELECT * FROM subject_master WHERE subject_id = ?', [$subjectId]);
    }

    /**
     * Update an existing subject.
     */
    public function updateSubject(int $subjectId, array $data, int $updatedBy, string $updatedAt)
    {
        DB::update('
            UPDATE subject_master 
            SET programme_id = ?, 
                course_id = ?, 
                code = ?, 
                name = ?, 
                status = ?, 
                internal_full_marks = ?, 
                internal_pass_marks = ?, 
                theory_full_marks = ?, 
                theory_pass_marks = ?, 
                practical_full_marks = ?, 
                practical_pass_marks = ?, 
                updated_by = ?, 
                updated_at = ?
            WHERE subject_id = ?
        ', [
            $data['programme_id'],
            $data['course_id'],
            $data['code'],
            $data['name'],
            $data['status'],
            $data['internal_full_marks'],
            $data['internal_pass_marks'],
            $data['theory_full_marks'],
            $data['theory_pass_marks'],
            $data['practical_full_marks'],
            $data['practical_pass_marks'],
            $updatedBy,
            $updatedAt,
            $subjectId,
        ]);
    }

    /**
     * Update the status of a specific subject.
     */
    public function updateSubjectStatus(int $subjectId, string $status, int $updatedBy, string $updatedAt)
    {
        DB::update('UPDATE subject_master SET status = ?, updated_by = ?, updated_at = ? WHERE subject_id = ?', [
            $status,
            $updatedBy,
            $updatedAt,
            $subjectId,
        ]);
    }

    /**
     * Bulk update the status of multiple subjects.
     */
    public function bulkUpdateSubjectStatus(array $subjectIds, string $status, int $updatedBy, string $updatedAt)
    {
        $placeholders = implode(',', array_fill(0, count($subjectIds), '?'));
        $bindings = array_merge([$status, $updatedBy, $updatedAt], $subjectIds);

        DB::update("UPDATE subject_master SET status = ?, updated_by = ?, updated_at = ? WHERE subject_id IN ($placeholders)", $bindings);
    }
}
