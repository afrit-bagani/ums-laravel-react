<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateStudentProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Personal Information
            'full_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', Rule::unique('student_profiles', 'email')],
            'mobile_no' => ['required', 'string', 'size:10', Rule::unique('student_profiles', 'mobile_no')],
            'dob' => ['required', 'date'],
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'blood_group' => ['required', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'religion' => ['required', Rule::in(['Hindu', 'Muslim', 'Christian', 'Sikh', 'Buddhist', 'Jain'])],
            'caste' => ['required', Rule::in(['General', 'SC', 'ST', 'OBC', 'EWS'])],
            'marital_status' => ['required', Rule::in(['Single', 'Married', 'Divorced', 'Widowed'])],
            'nationality' => ['required', 'string', 'max:50'],
            'aadhaar_no' => ['nullable', 'string', 'max:12', Rule::unique('student_profiles', 'aadhaar_no')],
            'abc_id' => ['nullable', 'string', 'max:12', Rule::unique('student_profiles', 'abc_id')],

            // Family Information
            'father_name' => ['required', 'string', 'max:50'],
            'mother_name' => ['required', 'string', 'max:50'],
            'parent_mobile_no' => ['nullable', 'string', 'size:10'],
            'annual_family_income' => ['nullable', 'numeric', 'min:0'],

            // Special Categories
            'is_blind' => ['boolean'],
            'is_bpl' => ['boolean'],
            'is_minority' => ['boolean'],
            'is_ph' => ['boolean'],

            // Present Address
            'present_address' => ['required', 'string', 'max:255'],
            'present_city' => ['required', 'string', 'max:50'],
            'present_district' => ['required', 'string', 'max:50'],
            'present_state' => ['required', 'string', 'max:50'],
            'present_country' => ['required', 'string', 'max:50'],
            'present_pincode' => ['required', 'string', 'size:6'],

            // Permanent Address
            'permanent_address' => ['required', 'string', 'max:255'],
            'permanent_city' => ['required', 'string', 'max:50'],
            'permanent_district' => ['required', 'string', 'max:50'],
            'permanent_state' => ['required', 'string', 'max:50'],
            'permanent_country' => ['required', 'string', 'max:50'],
            'permanent_pincode' => ['required', 'string', 'size:6'],

            // Academic Information
            'admission_type' => ['required', Rule::in(['Regular', 'Lateral Entry', 'Transfer'])],
            'exam_name' => ['required', 'string', 'max:100'],
            'board_name' => ['required', 'string', 'max:100'],
            'institution_name' => ['required', 'string', 'max:100'],
            'max_marks' => ['required', 'numeric', 'min:0'],
            'marks_obtained' => ['required', 'numeric', 'min:0', 'lte:max_marks'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],

            // Paper Selection
            'programme_id' => ['required', 'integer', Rule::exists('programme_master', 'programme_id')],
            'course_id' => ['required', 'integer', Rule::exists('course_master', 'course_id')],
            'batch_id' => ['required', 'integer', Rule::exists('batch_master', 'batch_id')],

            // Documents
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'signature' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],

            // Payments
            'fee_type' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string'],
            'transaction_id' => ['required', 'string', 'max:255'],
        ];
    }
}
