<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'all'])],
            'page' => ['nullable', 'integer'],
            'rows-per-page' => ['nullable', 'integer'],
        ]);

        $search = $validated['search'] ?? '';
        $status = $validated['status'] ?? 'all';
        $currentPage = $validated['page'] ?? 1;
        $perPage = $validated['rows-per-page'] ?? 10;

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

        $offset = ($currentPage - 1) * $perPage;
        $dataBindings = array_merge($bindings, [$perPage, $offset]);

        $subjects = DB::select("
            SELECT s.subject_id, s.code, s.name, s.status, c.code as course_code, p.code as programme_code
            FROM subject_master s 
            JOIN course_master c on s.course_id = c.course_id
            JOIN programme_master p on c.programme_id = p.programme_id
            $query 
            ORDER BY s.created_at DESC 
            LIMIT ? OFFSET ?",
            $dataBindings
        );

        $totalSubjects = DB::selectOne("
            SELECT COUNT(*) as count
            FROM subject_master s 
            JOIN course_master c on s.course_id = c.course_id
            JOIN programme_master p on c.programme_id = p.programme_id
            $query", $bindings
        )->count;

        $paginator = new LengthAwarePaginator(
            $subjects,
            $totalSubjects,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Admin/Subjects/Index', [
            'subjects' => $paginator,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        $programmesWithCourses = Cache::rememberForever('active_programmes_with_courses', function () {
            $programmes = DB::select("SELECT programme_id, name as programme_name FROM programme_master WHERE status = 'active'");
            $courses = DB::select("SELECT course_id, programme_id, name as course_name FROM course_master WHERE status = 'active'");

            $groupedCourses = [];
            foreach ($courses as $course) {
                $groupedCourses[$course->programme_id][] = [
                    'course_id' => $course->course_id,
                    'name' => $course->course_name,
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
        });

        return Inertia::render('Admin/Subjects/Create', [
            'programmes_with_courses' => $programmesWithCourses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'programme_id' => ['required', Rule::exists('programme_master', 'programme_id')],
            'course_id' => ['required', Rule::exists('course_master', 'course_id')],
            'code' => ['required', 'string', 'max:255', Rule::unique('subject_master', 'code')],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'internal_full_marks' => ['required', 'numeric', 'min:0'],
            'internal_pass_marks' => ['required', 'numeric', 'min:0'],
            'theory_full_marks' => ['required', 'numeric', 'min:0'],
            'theory_pass_marks' => ['required', 'numeric', 'min:0'],
            'practical_full_marks' => ['required', 'numeric', 'min:0'],
            'practical_pass_marks' => ['required', 'numeric', 'min:0'],
        ]);

        DB::insert('INSERT INTO subject_master (
            programme_id, course_id, code, name, status, 
            internal_full_marks, internal_pass_marks, 
            theory_full_marks, theory_pass_marks, 
            practical_full_marks, practical_pass_marks, 
            created_by, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $validated['programme_id'],
            $validated['course_id'],
            $validated['code'],
            $validated['name'],
            $validated['status'],
            $validated['internal_full_marks'],
            $validated['internal_pass_marks'],
            $validated['theory_full_marks'],
            $validated['theory_pass_marks'],
            $validated['practical_full_marks'],
            $validated['practical_pass_marks'],
            Auth::id(),
            now(),
            now(),
        ]);

        return redirect()->route('admin.subjects.index')->with('message', 'Subject created successfully');
    }

    public function show(int $id)
    {
        $subject = DB::selectOne('
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
        ', [$id]);

        abort_if(! $subject, 404);

        return Inertia::render('Admin/Subjects/Show', [
            'subject' => $subject,
        ]);
    }

    public function edit(int $id)
    {
        $subject = DB::selectOne('SELECT * FROM subject_master WHERE subject_id = ?', [$id]);
        $programmesWithCourses = Cache::rememberForever('active_programmes_with_courses', function () {
            $programmes = DB::select("SELECT programme_id, name as programme_name FROM programme_master WHERE status = 'active'");
            $courses = DB::select("SELECT course_id, programme_id, name as course_name FROM course_master WHERE status = 'active'");

            $groupedCourses = [];
            foreach ($courses as $course) {
                $groupedCourses[$course->programme_id][] = [
                    'course_id' => $course->course_id,
                    'name' => $course->course_name,
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
        });

        return Inertia::render('Admin/Subjects/Edit', [
            'subject' => $subject,
            'programmes_with_courses' => $programmesWithCourses,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'programme_id' => ['required', 'integer', Rule::exists('programme_master', 'programme_id')],
            'course_id' => ['required', 'integer', Rule::exists('course_master', 'course_id')],
            'code' => ['required', 'string', 'max:50', Rule::unique('subject_master', 'code')->ignore($id, 'subject_id')],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'internal_full_marks' => ['required', 'numeric', 'min:0'],
            'internal_pass_marks' => ['required', 'numeric', 'min:0'],
            'theory_full_marks' => ['required', 'numeric', 'min:0'],
            'theory_pass_marks' => ['required', 'numeric', 'min:0'],
            'practical_full_marks' => ['required', 'numeric', 'min:0'],
            'practical_pass_marks' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['updated_at'] = now();

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
            $validated['programme_id'],
            $validated['course_id'],
            $validated['code'],
            $validated['name'],
            $validated['status'],
            $validated['internal_full_marks'],
            $validated['internal_pass_marks'],
            $validated['theory_full_marks'],
            $validated['theory_pass_marks'],
            $validated['practical_full_marks'],
            $validated['practical_pass_marks'],
            $validated['updated_by'],
            $validated['updated_at'],
            $id,
        ]);

        return redirect()->route('admin.subjects.index')->with('message', 'Subject updated successfully');
    }

    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::update('UPDATE subject_master SET status = ?, updated_by = ?, updated_at = ? WHERE subject_id = ?', [
            $validated['status'],
            Auth::id(),
            now(),
            $id,
        ]);

        return redirect()->back()->with('message', 'Subject status updated successfully');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'subject_ids' => ['required', 'array', 'min:1'],
            'subject_ids.*' => ['integer', Rule::exists('subject_master', 'subject_id')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $subjectIds = $validated['subject_ids'];
        $placeholders = implode(',', array_fill(0, count($subjectIds), '?'));

        $bindings = array_merge([$validated['status'], Auth::id(), now()], $subjectIds);

        DB::update("UPDATE subject_master SET status = ?, updated_by = ?, updated_at = ? WHERE subject_id IN ($placeholders)", $bindings);

        return redirect()->back()->with('message', 'Selected subjects updated successfully');
    }
}
