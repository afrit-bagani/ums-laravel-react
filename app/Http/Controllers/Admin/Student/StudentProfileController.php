<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateStudentProfileRequest;
use App\Http\Requests\UpdateStudentProfileRequest;
use App\Mail\StudentWelcomeEmail;
use App\Repositories\Admin\StudentRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class StudentProfileController extends Controller
{
    protected StudentRepository $studentRepo;

    public function __construct(StudentRepository $studentRepo)
    {
        $this->studentRepo = $studentRepo;
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
        $status = $validated['status'] ?? 'all';
        $currentPage = $validated['page'] ?? 1;
        $perPage = $validated['rows-per-page'] ?? 10;

        $offset = ($currentPage - 1) * $perPage;

        $students = $this->studentRepo->getPaginatedStudents($search, $status, $perPage, $offset);
        $totalRecords = $this->studentRepo->getTotalStudentsCount($search, $status);

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
        $programmesWithCourses = Cache::remember('active_programmes_with_courses', 60 * 24, function () {
            $programmes = $this->studentRepo->getActiveProgrammes();
            $courses = $this->studentRepo->getActiveCourses();

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
        });

        $batches = Cache::remember('active_batches', 60 * 24, function () {
            return $this->studentRepo->getActiveBatches();
        });

        return Inertia::render('Admin/Students/Create', [
            'programmes_with_courses' => $programmesWithCourses,
            'batches' => $batches,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateStudentProfileRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($request, $validated) {
                $createdAt = now()->toDateTimeString();
                $updatedAt = now()->toDateTimeString();

                // 1. Generate Registration Number based on Batch Year (Sequential)
                $batchName = $this->studentRepo->getBatchNameById($validated['batch_id']);
                $batchStartYear = $batchName ? (explode('-', $batchName->name)[0] ?? date('Y')) : date('Y');

                $lastProfile = $this->studentRepo->getLastRegistrationNumberByYear($batchStartYear);

                if ($lastProfile) {
                    $lastNumber = (int) substr($lastProfile->registration_number, strlen($batchStartYear));
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }

                $registrationNumber = $batchStartYear.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                $temporaryPassword = Str::random(10);

                // 2. Create User
                $userId = $this->studentRepo->createUser(
                    $validated['full_name'],
                    $registrationNumber,
                    'student',
                    Hash::make($temporaryPassword),
                    false,
                    $createdAt,
                    $updatedAt
                );

                // 3. Insert into student_profiles
                $studentId = $this->studentRepo->createStudentProfile($userId, $registrationNumber, $validated, $createdAt, $updatedAt);

                // 4. Insert into student_paper_selections
                $this->studentRepo->createStudentPaperSelection($studentId, $validated['programme_id'], $validated['course_id'], $validated['batch_id'], $createdAt, $updatedAt);

                // 5. Store Documents and Insert into student_documents
                $photoPath = $request->file('photo')->store('documents', 'public');
                $signaturePath = $request->file('signature')->store('documents', 'public');

                $this->studentRepo->createStudentDocument($studentId, $photoPath, $signaturePath, $createdAt, $updatedAt);

                // 6. Insert into student_payments
                $this->studentRepo->createStudentPayment($studentId, $validated, $createdAt, $updatedAt);

                // 7. Dispatch Welcome Email
                Mail::to($validated['email'])->send(
                    new StudentWelcomeEmail($validated['full_name'], $registrationNumber, $temporaryPassword)
                );
            });

            return redirect()->route('admin.students.index')->with('success', 'Student successfully registered with all details.');
        } catch (Exception $e) {
            Log::error('Student Registration Failed: '.$e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while saving the data.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $student = $this->studentRepo->getStudentWithRelationsById($id);

        if (! $student) {
            abort(404, 'Student not found');
        }

        return Inertia::render('Admin/Students/Show', [
            'student' => (array) $student,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $studentProfile = $this->studentRepo->getStudentWithRelationsById($id);

        if (! $studentProfile) {
            abort(404, 'Student not found');
        }

        $programmesWithCourses = Cache::remember('active_programmes_with_courses', 60 * 24, function () {
            $programmes = $this->studentRepo->getActiveProgrammes();
            $courses = $this->studentRepo->getActiveCourses();

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
        });

        $batches = Cache::remember('active_batches', 60 * 24, function () {
            return $this->studentRepo->getActiveBatches();
        });

        return Inertia::render('Admin/Students/Edit', [
            'programmes_with_courses' => $programmesWithCourses,
            'batches' => $batches,
            'student_profile' => (array) $studentProfile,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentProfileRequest $request, int $id)
    {
        $validated = $request->validated();

        $this->studentRepo->updateStudentProfile($id, $validated, now());

        $studentProfile = $this->studentRepo->getStudentProfileById($id);

        return redirect()->back()->with([
            'success' => 'Basic information updated successfully.',
            'student_profile_id' => $id,
            'student_profile' => (array) $studentProfile,
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->merge(['id' => $id]);

        $request->validate([
            'id' => ['required', 'exists:student_profiles,id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->studentRepo->updateStudentStatus($id, $request->status, now());

        return back()->with('success', 'Student status changed successfully.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['required', 'exists:student_profiles,id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if (empty($request->student_ids)) {
            return back()->with('error', 'No students selected.');
        }

        $this->studentRepo->bulkUpdateStudentStatus($request->student_ids, $request->status, now());

        return back()->with('success', 'Selected students status changed successfully.');
    }
}
