<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\SubjectRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SubjectController extends Controller
{
    protected SubjectRepository $subjectRepo;

    public function __construct(SubjectRepository $subjectRepo)
    {
        $this->subjectRepo = $subjectRepo;
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

        $search = $validated['search'] ?? '';
        $status = $validated['status'] ?? 'all';
        $currentPage = $validated['page'] ?? 1;
        $perPage = $validated['rows-per-page'] ?? 10;

        $offset = ($currentPage - 1) * $perPage;

        $subjects = $this->subjectRepo->getPaginatedSubjects($search, $status, $perPage, $offset);
        $totalSubjects = $this->subjectRepo->getTotalSubjectsCount($search, $status);

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
        $programmesWithCourses = $this->subjectRepo->getActiveProgrammesAndCourses();

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

        $this->subjectRepo->createSubject(
            $validated,
            Auth::id(),
            Auth::id(),
            now(),
            now()
        );

        return redirect()->route('admin.subjects.index')->with('success', 'Subject created successfully');
    }

    public function show(int $id)
    {
        $subject = $this->subjectRepo->getSubjectWithRelationsById($id);

        abort_if(! $subject, 404);

        return Inertia::render('Admin/Subjects/Show', [
            'subject' => $subject,
        ]);
    }

    public function edit(int $id)
    {
        $subject = $this->subjectRepo->getSubjectById($id);

        $programmesWithCourses = $this->subjectRepo->getActiveProgrammesAndCourses();

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

        $this->subjectRepo->updateSubject(
            $id,
            $validated,
            Auth::id(),
            now()
        );

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully');
    }

    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->subjectRepo->updateSubjectStatus(
            $id,
            $validated['status'],
            Auth::id(),
            now()
        );

        return redirect()->back()->with('success', 'Subject status updated successfully');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'subject_ids' => ['required', 'array', 'min:1'],
            'subject_ids.*' => ['integer', Rule::exists('subject_master', 'subject_id')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->subjectRepo->bulkUpdateSubjectStatus(
            $validated['subject_ids'],
            $validated['status'],
            Auth::id(),
            now()
        );

        return redirect()->back()->with('success', 'Selected subjects updated successfully');
    }
}
