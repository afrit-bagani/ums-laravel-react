<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentProfileRequest;
use App\Http\Requests\UpdateStudentProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class StudentProfileController extends Controller
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
        $status = $validated['status'] ?? 'all';
        $currentPage = $validated['page'] ?? 1;
        $perPage = $validated['rows-per-page'] ?? 10;

        $offset = ($currentPage - 1) * $perPage;

        $whereClause = [];
        $bindings = [];

        if (! empty($search)) {
            $whereClause[] = '(s.registration_number LIKE ? OR s.full_name LIKE ? OR s.email LIKE ? OR s.mobile_no LIKE ?)';
            $searchTerm = "%{$search}%";
            array_push($bindings, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
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

        $students = DB::select("
            SELECT s.id, s.registration_number, s.full_name, s.email, s.mobile_no, s.status,  
                   c.name as course_name, 
                   b.name as batch_name
            FROM student_profiles as s 
            LEFT JOIN student_paper_selections as sps on s.id = sps.student_profile_id
            LEFT JOIN course_master as c on c.course_id = sps.course_id
            LEFT JOIN batch_master as b on b.batch_id = sps.batch_id
            $query
            ORDER BY s.created_at DESC
            LIMIT ? OFFSET ?
        ", $dataBindings);

        $totalRecords = DB::selectOne("
            SELECT COUNT(*) as count 
            FROM student_profiles as s 
            LEFT JOIN student_paper_selections as sps on s.id = sps.student_profile_id
            LEFT JOIN course_master as c on c.course_id = sps.course_id
            LEFT JOIN batch_master as b on b.batch_id = sps.batch_id
            $query
        ", $bindings)->count;

        $paginator = new LengthAwarePaginator(
            $students,
            $totalRecords,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Admin/Students/Index', [
            'students' => $paginator,
            'filters' => $request->only(['search', 'status', 'rows-per-page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentProfileRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentProfile $studentProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentProfile $studentProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentProfileRequest $request, StudentProfile $studentProfile)
    {
        //
    }

    public function updateStatus(Request $request, $id) {}

    public function bulkUpdateStatus() {}
}
