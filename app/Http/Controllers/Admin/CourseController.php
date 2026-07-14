<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\CourseRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CourseController extends Controller
{
    protected CourseRepository $courseRepo;

    public function __construct(CourseRepository $courseRepo)
    {
        $this->courseRepo = $courseRepo;
    }

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

        $offset = ($currentPage - 1) * $perPage;

        $courses = $this->courseRepo->getPaginatedCourses($search, $status, $perPage, $offset);
        $totalRecords = $this->courseRepo->getTotalCoursesCount($search, $status);

        $paginator = new LengthAwarePaginator(
            $courses,
            $totalRecords,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $programmes = Cache::rememberForever('active_programmes', function () {
            return $this->courseRepo->getActiveProgrammes();
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

        $this->courseRepo->createCourse(
            $validated,
            Auth::id(),
            now(),
            now()
        );

        return back()->with('success', 'Course created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $course_id)
    {
        $request->merge(['course_id' => $course_id]);

        $validated = $request->validate([
            'course_id' => ['required', Rule::exists('course_master', 'course_id')],
            'programme_id' => ['required', Rule::exists('programme_master', 'programme_id')],
            'code' => ['required', 'string', 'max:255', Rule::unique('course_master', 'code')->ignore($course_id, 'course_id')],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->courseRepo->updateCourse(
            $course_id,
            $validated,
            now()
        );

        return back()->with('success', 'Course updated successfully');
    }

    /**
     * Update status of the specified resource from storage.
     */
    public function updateStatus(Request $request, int $course_id)
    {
        $request->merge(['course_id' => $course_id]);

        $validated = $request->validate([
            'course_id' => ['required', Rule::exists('course_master', 'course_id')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->courseRepo->updateCourseStatus(
            $course_id,
            $validated['status'],
            now()
        );

        return back()->with('success', 'Course status changed successfully');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'course_ids' => ['required', 'array'],
            'course_ids.*' => ['required', Rule::exists('course_master', 'course_id')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->courseRepo->bulkUpdateCourseStatus(
            $validated['course_ids'],
            $validated['status'],
            now()
        );

        return back()->with('success', 'Course status changed successfully');
    }
}
