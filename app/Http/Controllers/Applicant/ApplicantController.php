<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Repositories\ApplicantRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ApplicantController extends Controller
{
    protected ApplicantRepository $applicantRepo;

    public function __construct(ApplicantRepository $applicantRepo)
    {
        $this->applicantRepo = $applicantRepo;
    }

    /**
     * Show the applicant registration form.
     */
    public function create()
    {
        $formData = $this->applicantRepo->getFormData();

        return Inertia::render('Applicant/Apply', [
            'programmes_with_courses' => $formData['programmes_with_courses'],
            'batches' => $formData['batches'],
        ]);
    }

    /**
     * Store the applicant registration data.
     */
    public function store(Request $request)
    {
        // 1. Validate Profile Data
        $profileRules = [
            'full_name' => ['required', 'string', 'max:50'],
            'father_name' => ['required', 'string', 'max:50'],
            'mother_name' => ['required', 'string', 'max:50'],
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'dob' => ['required', 'date', 'before:today'],
            'abc_id' => ['nullable', 'string', 'max:12', 'unique:applicant_profiles,abc_id'],
            'aadhaar_no' => ['nullable', 'string', 'max:12', 'unique:applicant_profiles,aadhaar_no'],
            'nationality' => ['required', 'string', 'max:50'],
            'mobile_no' => ['required', 'string', 'size:10', 'unique:applicant_profiles,mobile_no'],
            'email' => ['required', 'email', 'unique:applicant_profiles,email'],
            'religion' => ['required', Rule::in(['Hindu', 'Muslim', 'Christian', 'Sikh', 'Buddhist', 'Jain'])],
            'caste' => ['required', Rule::in(['General', 'SC', 'ST', 'OBC', 'EWS'])],
            'blood_group' => ['required', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'marital_status' => ['required', Rule::in(['Single', 'Married', 'Divorced', 'Widowed'])],
            'annual_family_income' => ['nullable', 'numeric'],
            'parent_mobile_no' => ['nullable', 'string', 'size:10'],

            'is_blind' => ['boolean'],
            'is_bpl' => ['boolean'],
            'is_minority' => ['boolean'],
            'is_ph' => ['boolean'],

            'present_address' => ['required', 'string', 'max:255'],
            'present_city' => ['required', 'string', 'max:50'],
            'present_country' => ['required', 'string', 'max:50'],
            'present_state' => ['required', 'string', 'max:50'],
            'present_district' => ['required', 'string', 'max:50'],
            'present_pincode' => ['required', 'string', 'size:6'],

            'permanent_address' => ['required', 'string', 'max:255'],
            'permanent_city' => ['required', 'string', 'max:50'],
            'permanent_country' => ['required', 'string', 'max:50'],
            'permanent_state' => ['required', 'string', 'max:50'],
            'permanent_district' => ['required', 'string', 'max:50'],
            'permanent_pincode' => ['required', 'string', 'size:6'],

            'admission_type' => ['required', Rule::in(['Regular', 'Lateral Entry', 'Transfer'])],
            'exam_name' => ['required', 'string', 'max:100'],
            'board_name' => ['required', 'string', 'max:100'],
            'institution_name' => ['required', 'string', 'max:100'],
            'max_marks' => ['required', 'numeric', 'min:0'],
            'marks_obtained' => ['required', 'numeric', 'min:0', 'lte:max_marks'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ];

        // 2. Validate Paper Selection Data
        $paperRules = [
            'programme_id' => ['required', 'exists:programme_master,programme_id'],
            'course_id' => ['required', 'exists:course_master,course_id'],
            'batch_id' => ['required', 'exists:batch_master,batch_id'],
        ];

        // 3. Validate Documents Data
        $documentRules = [
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'signature' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];

        // 4. Validate Payment Data
        $paymentRules = [
            'fee_type' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', Rule::in(['cash', 'upi', 'cheque', 'NEFT', 'RTGS'])],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['nullable', 'date'],
        ];

        // Merge all rules and validate
        $validated = $request->validate(array_merge($profileRules, $paperRules, $documentRules, $paymentRules));

        // Generate Unique Applicant Code: APP-YYYY-RANDOM
        $applicantCode = 'APP-'.date('Y').'-'.strtoupper(Str::random(6));

        // Upload Documents
        $photoPath = $request->file('photo')->store('applicants/photos', 'public');
        $signaturePath = $request->file('signature')->store('applicants/signatures', 'public');

        // Extract validated data sets
        $profileData = $request->only(array_keys($profileRules));
        $profileData['applicant_code'] = $applicantCode;

        $paperSelectionData = $request->only(array_keys($paperRules));

        $documentData = [
            'photo_path' => $photoPath,
            'signature_path' => $signaturePath,
        ];

        $paymentData = $request->only(array_keys($paymentRules));

        // Store via Repository (Wrapped in DB Transaction inside repo)
        $this->applicantRepo->createApplicant($profileData, $paperSelectionData, $documentData, $paymentData);

        return back()->with([
            'success' => 'Application submitted successfully!',
            'applicant_code' => $applicantCode,
        ]);
    }
}
