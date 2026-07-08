<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
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

        $batches = Cache::rememberForever('active_batches', function () {
            return DB::select("SELECT batch_id, name as batch_name FROM batch_master WHERE status = 'active'");
        });

        return Inertia::render('Admin/Students/Create', [
            'programmes_with_courses' => $programmesWithCourses,
            'batches' => $batches,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentProfileRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated) {
            $createdAt = now();
            $updatedAt = now();

            // 1. Generate Registration Number based on Batch Year
            $batchName = DB::selectOne('SELECT name FROM batch_master WHERE id = ?', [$validated['batch_id']]);
            $batchStartYear = $batchName ? (explode('-', $batchName->name)[0] ?? date('Y')) : date('Y');
            $registrationNumber = $batchStartYear . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // 2. Insert into student_profiles
            DB::insert(
                'INSERT INTO student_profiles (registration_number, full_name, father_name, mother_name, gender, dob, abc_id, aadhaar_no, nationality, mobile_no, email, religion, caste, blood_group, marital_status, annual_family_income, parent_mobile_no, is_blind, is_bpl, is_minority, is_ph, present_address, present_city, present_country, present_state, present_district, present_pincode, permanent_address, permanent_city, permanent_country, permanent_state, permanent_district, permanent_pincode, admission_type, exam_name, board_name, institution_name, max_marks, marks_obtained, percentage, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $registrationNumber,
                    $validated['full_name'],
                    $validated['father_name'],
                    $validated['mother_name'],
                    $validated['gender'],
                    $validated['dob'],
                    $validated['abc_id'] ?? null,
                    $validated['aadhaar_no'] ?? null,
                    $validated['nationality'],
                    $validated['mobile_no'],
                    $validated['email'],
                    $validated['religion'],
                    $validated['caste'],
                    $validated['blood_group'],
                    $validated['marital_status'],
                    $validated['annual_family_income'] ?? null,
                    $validated['parent_mobile_no'] ?? null,
                    $validated['is_blind'] ?? false,
                    $validated['is_bpl'] ?? false,
                    $validated['is_minority'] ?? false,
                    $validated['is_ph'] ?? false,
                    $validated['present_address'],
                    $validated['present_city'],
                    $validated['present_country'],
                    $validated['present_state'],
                    $validated['present_district'],
                    $validated['present_pincode'],
                    $validated['permanent_address'],
                    $validated['permanent_city'],
                    $validated['permanent_country'],
                    $validated['permanent_state'],
                    $validated['permanent_district'],
                    $validated['permanent_pincode'],
                    $validated['admission_type'],
                    $validated['exam_name'],
                    $validated['board_name'],
                    $validated['institution_name'],
                    $validated['max_marks'],
                    $validated['marks_obtained'],
                    $validated['percentage'],
                    $createdAt,
                    $updatedAt,
                ]
            );

            $studentId = DB::getPdo()->lastInsertId();

            // 3. Insert into student_paper_selections
            DB::insert(
                'INSERT INTO student_paper_selections (student_profile_id, programme_id, course_id, batch_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
                [$studentId, $validated['programme_id'], $validated['course_id'], $validated['batch_id'], $createdAt, $updatedAt]
            );

            // 4. Store Documents and Insert into student_documents
            $photoPath = $request->file('photo')->store('documents', 'public');
            $signaturePath = $request->file('signature')->store('documents', 'public');
            
            DB::insert(
                'INSERT INTO student_documents (student_profile_id, photo, signature, created_at, updated_at) VALUES (?, ?, ?, ?, ?)',
                [$studentId, $photoPath, $signaturePath, $createdAt, $updatedAt]
            );

            // 5. Insert into student_payments
            DB::insert(
                'INSERT INTO student_payments (student_profile_id, fee_type, amount, payment_method, transaction_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$studentId, $validated['fee_type'], $validated['amount'], $validated['payment_method'], $validated['transaction_id'], $createdAt, $updatedAt]
            );
        });

        return redirect()->route('admin.students.index')->with('success', 'Student successfully registered with all details.');
    }

    /**
     * Display the specified resource.
     */
    public function show($studentProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $studentProfile = DB::selectOne("
            SELECT sp.*, 
                   sps.programme_id, sps.course_id, sps.batch_id,
                   sd.photo, sd.signature,
                   spay.fee_type, spay.amount, spay.payment_method, spay.transaction_id
            FROM student_profiles sp
            LEFT JOIN student_paper_selections sps ON sp.id = sps.student_profile_id
            LEFT JOIN student_documents sd ON sp.id = sd.student_profile_id
            LEFT JOIN student_payments spay ON sp.id = spay.student_profile_id
            WHERE sp.id = ?
        ", [$id]);

        if (!$studentProfile) {
            abort(404, 'Student not found');
        }

        $programmesWithCourses = DB::select("
            SELECT pm.programme_id, pm.name as programme_name,
                   cm.course_id, cm.name as course_name
            FROM programme_master pm
            JOIN course_master cm ON pm.programme_id = cm.programme_id
            WHERE pm.status = 'active' AND cm.status = 'active'
        ");

        $programmes = [];
        foreach ($programmesWithCourses as $row) {
            $progId = $row->programme_id;
            if (!isset($programmes[$progId])) {
                $programmes[$progId] = [
                    'programme_id' => $progId,
                    'programme_name' => $row->programme_name,
                    'courses' => [],
                ];
            }
            $programmes[$progId]['courses'][] = [
                'course_id' => $row->course_id,
                'course_name' => $row->course_name,
            ];
        }

        $batches = DB::select("SELECT id, name, code FROM batche_master WHERE status = 'active'");

        return Inertia::render('Admin/Students/Create', [
            'programmes_with_courses' => array_values($programmes),
            'batches' => $batches,
            'student_profile' => (array) $studentProfile,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StudentProfileRequest $request, $id)
    {
        $validated = $request->validated();
        $validated['updated_at'] = now();

        DB::update(
            'UPDATE student_profiles SET full_name = ?, father_name = ?, mother_name = ?, gender = ?, dob = ?, abc_id = ?, aadhaar_no = ?, nationality = ?, mobile_no = ?, email = ?, religion = ?, caste = ?, blood_group = ?, marital_status = ?, annual_family_income = ?, parent_mobile_no = ?, is_blind = ?, is_bpl = ?, is_minority = ?, is_ph = ?, present_address = ?, present_city = ?, present_country = ?, present_state = ?, present_district = ?, present_pincode = ?, permanent_address = ?, permanent_city = ?, permanent_country = ?, permanent_state = ?, permanent_district = ?, permanent_pincode = ?, admission_type = ?, exam_name = ?, board_name = ?, institution_name = ?, max_marks = ?, marks_obtained = ?, percentage = ?, updated_at = ? WHERE id = ?',
            [
                $validated['full_name'],
                $validated['father_name'],
                $validated['mother_name'],
                $validated['gender'],
                $validated['dob'],
                $validated['abc_id'] ?? null,
                $validated['aadhaar_no'] ?? null,
                $validated['nationality'],
                $validated['mobile_no'],
                $validated['email'],
                $validated['religion'],
                $validated['caste'],
                $validated['blood_group'],
                $validated['marital_status'],
                $validated['annual_family_income'] ?? null,
                $validated['parent_mobile_no'] ?? null,
                $validated['is_blind'] ?? false,
                $validated['is_bpl'] ?? false,
                $validated['is_minority'] ?? false,
                $validated['is_ph'] ?? false,
                $validated['present_address'],
                $validated['present_city'],
                $validated['present_country'],
                $validated['present_state'],
                $validated['present_district'],
                $validated['present_pincode'],
                $validated['permanent_address'],
                $validated['permanent_city'],
                $validated['permanent_country'],
                $validated['permanent_state'],
                $validated['permanent_district'],
                $validated['permanent_pincode'],
                $validated['admission_type'],
                $validated['exam_name'],
                $validated['board_name'],
                $validated['institution_name'],
                $validated['max_marks'],
                $validated['marks_obtained'],
                $validated['percentage'],
                $validated['updated_at'],
                $id,
            ]
        );

        $studentProfile = DB::selectOne('SELECT * FROM student_profiles WHERE id = ?', [$id]);

        return redirect()->back()->with([
            'success' => 'Basic information updated successfully.',
            'student_profile_id' => $id,
            'student_profile' => (array) $studentProfile,
        ]);
    }

    public function updateStatus(Request $request, $id) {}

    public function bulkUpdateStatus() {}
}
