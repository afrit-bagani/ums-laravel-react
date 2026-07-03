<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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

        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullale', Rule::in(['active', 'inactive', 'all'])],
            'page' => ['nullable', 'integer'],
            'rows-per-page' => ['nullable', 'integer'],
        ]);

        $whereClause = [];
        $bindings = [];

        // search filter
        if ($request->has('search')) {
            $whereClause[] = '(code LIKE ? OR Name LIKE ?)';
            $bindings[] = "%{$request->input('search')}%";
            $bindings[] = "%{$request->input('search')}%";
        }

        if ($request->has('status') && $request->input('status') !== 'all') {
            $whereClause[] = 'status = ?';
            $bindings[] = $request->input('status');
        }

        $query = '';

        if (count($whereClause) > 0) {
            $query = 'WHERE '.implode(' AND ', $whereClause);
        }

        // pagination
        $currentPage = $request->input('page', 1);
        $perPage = $request->input('rows-per-page', 10);
        $offset = ($currentPage - 1) * $perPage;
        $dataBindings = array_merge($bindings, [$perPage, $offset]);

        $courses = DB::select("SELECT * FROM course_master $query ORDER BY created_at DESC OFF SET ? LIMIT ?", $dataBindings);
        $totalRecords = DB::select("SELECT COUNT(*) from course_master FROM $query", $dataBindings)->count();

        $paginator = new LengthAwarePaginator(
            $courses,
            $totalRecords,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'programme_id' => ['required', Rule::exists('programme_master', 'programme_id')],
            'code' => ['required', 'string', 'max:255', Rule::unique('course_master', 'code')],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::insert('INSERT INTO course_master (programme_id, code, name, status, created_by, created_at, updated_at)', [
            $request->programme_id, $request->code, $request->name, $request->status, Auth::id(), now(), now(),
        ]);

        return back()->with('message', 'Course created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $course_id)
    {
        //
    }

    /**
     * Update status of the specified resource from storage.
     */
    public function updateStatus(Request $request, $course_id)
    {
        //
    }

    public function bulkUpdateStatus(Request $request) {}
}
