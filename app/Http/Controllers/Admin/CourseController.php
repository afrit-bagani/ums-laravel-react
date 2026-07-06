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

class CourseController extends Controller
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

        $search = $validated['search'] ?? null;
        $status = $validated['status'] ?? null;
        $currentPage = $validated['page'] ?? 1;
        $perPage = $validated['rows-per-page'] ?? 10;

        $whereClause = [];
        $bindings = [];

        // search filter
        if (! empty($search)) {
            $whereClause[] = '(c.code LIKE ? OR c.name LIKE ?)';
            $bindings[] = "%{$search}%";
            $bindings[] = "%{$search}%";
        }

        // status filter
        if ($status !== null && $status !== 'all') {
            $whereClause[] = 'c.status = ?';
            $bindings[] = $status;
        }

        $query = '';

        if (count($whereClause) > 0) {
            $query = 'WHERE '.implode(' AND ', $whereClause);
        }

        // pagination
        $offset = ($currentPage - 1) * $perPage;
        $dataBindings = array_merge($bindings, [$perPage, $offset]);

        $courses = DB::select("
            SELECT c.*, p.code as programme_code 
            FROM course_master c 
            JOIN programme_master p ON c.programme_id = p.programme_id 
            $query 
            ORDER BY c.created_at DESC 
            LIMIT ? OFFSET ?", $dataBindings);

        $totalRecords = DB::selectOne("
            SELECT COUNT(*) as count 
            FROM course_master c 
            JOIN programme_master p ON c.programme_id = p.programme_id 
            $query", $bindings)->count;

        $paginator = new LengthAwarePaginator(
            $courses,
            $totalRecords,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $programmes = Cache::rememberForever('active_programmes', function () {
            $results = DB::select("SELECT programme_id, name FROM programme_master WHERE status = 'active'");

            return array_map(fn ($item) => (array) $item, $results);
        });

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $paginator,
            'programmes' => $programmes,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'programme_id' => ['required', Rule::exists('programme_master', 'programme_id')],
            'code' => ['required', 'string', 'max:255', Rule::unique('course_master', 'code')],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::insert('INSERT INTO course_master (programme_id, code, name, status, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?)', [
            $validated['programme_id'],
            $validated['code'],
            $validated['name'],
            $validated['status'],
            Auth::id(),
            now(),
            now(),
        ]);

        return back()->with('message', 'Course created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $course_id)
    {
        $request->merge(['course_id' => $course_id]);

        $validated = $request->validate([
            'course_id' => ['required', Rule::exists('course_master', 'course_id')],
            'programme_id' => ['required', Rule::exists('programme_master', 'programme_id')],
            'code' => ['required', 'string', 'max:255', Rule::unique('course_master', 'code')->ignore($course_id, 'course_id')],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::update('UPDATE course_master SET programme_id = ?, code = ?, name = ?, status = ?, updated_at = ? where course_id = ?', [
            $validated['programme_id'], $validated['code'], $validated['name'], $validated['status'], now(), $course_id,
        ]);

        return back()->with('message', 'Course updated successfully');
    }

    /**
     * Update status of the specified resource from storage.
     */
    public function updateStatus(Request $request, $course_id)
    {
        $request->merge(['course_id' => $course_id]);

        $validated = $request->validate([
            'course_id' => ['required', Rule::exists('course_master', 'course_id')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::update('UPDATE course_master SET status = ? , updated_at = ? WHERE course_id = ?', [
            $validated['status'],
            now(),
            $course_id,
        ]);

        return back()->with('message', 'Course status changed successfully');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'course_ids' => ['required', 'array'],
            'course_ids.*' => ['required', Rule::exists('course_master', 'course_id')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $placeHolders = implode(',', array_fill(0, count($request->course_ids), '?'));

        DB::update("UPDATE course_master SET status = ?, updated_at = ? WHERE course_id IN ($placeHolders)", [
            $validated['status'],
            now(),
            ...$validated['course_ids'],
        ]);

        return back()->with('message', 'Course status changed successfully');
    }
}
