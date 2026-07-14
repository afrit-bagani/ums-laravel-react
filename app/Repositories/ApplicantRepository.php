<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Exception;

class ApplicantRepository
{
    /**
     * Get data required for the applicant form dropdowns.
     */
    public function getFormData()
    {
        $programmes = DB::select("SELECT programme_id, name as programme_name FROM programme_master WHERE status = 'active'");
        $courses = DB::select("SELECT course_id, programme_id, name as course_name FROM course_master WHERE status = 'active'");
        $batches = DB::select("SELECT batch_id, name as batch_name FROM batch_master WHERE status = 'active'");

        $groupedCourses = [];
        foreach ($courses as $course) {
            $groupedCourses[$course->programme_id][] = [
                'course_id' => $course->course_id,
                'name' => $course->course_name,
            ];
        }

        $formattedProgrammes = [];
        foreach ($programmes as $prog) {
            $formattedProgrammes[] = [
                'programme_id' => $prog->programme_id,
                'programme_name' => $prog->programme_name,
                'courses' => $groupedCourses[$prog->programme_id] ?? [],
            ];
        }

        return [
            'programmes_with_courses' => $formattedProgrammes,
            'batches' => $batches,
        ];
    }

    /**
     * Store the full applicant registration (Profile, Paper Selection, Documents, Payments).
     * We use a transaction so if any insert fails, the whole process rolls back.
     * 
     * @return int The ID of the newly created applicant profile.
     */
    public function createApplicant(array $profileData, array $paperSelectionData, array $documentData, array $paymentData): int
    {
        DB::beginTransaction();

        try {
            // 1. Insert Profile
            $profileInserted = DB::insert('INSERT INTO applicant_profiles (
                applicant_code, status, full_name, father_name, mother_name, gender, dob, abc_id, aadhaar_no, nationality, mobile_no, email,
                religion, caste, blood_group, marital_status, annual_family_income, parent_mobile_no,
                is_blind, is_bpl, is_minority, is_ph,
                present_address, present_city, present_country, present_state, present_district, present_pincode,
                permanent_address, permanent_city, permanent_country, permanent_state, permanent_district, permanent_pincode,
                admission_type, exam_name, board_name, institution_name, max_marks, marks_obtained, percentage,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $profileData['applicant_code'],
                $profileData['status'] ?? 'active',
                $profileData['full_name'],
                $profileData['father_name'],
                $profileData['mother_name'],
                $profileData['gender'],
                $profileData['dob'],
                $profileData['abc_id'],
                $profileData['aadhaar_no'],
                $profileData['nationality'],
                $profileData['mobile_no'],
                $profileData['email'],
                $profileData['religion'],
                $profileData['caste'],
                $profileData['blood_group'],
                $profileData['marital_status'],
                $profileData['annual_family_income'],
                $profileData['parent_mobile_no'],
                $profileData['is_blind'] ?? false,
                $profileData['is_bpl'] ?? false,
                $profileData['is_minority'] ?? false,
                $profileData['is_ph'] ?? false,
                $profileData['present_address'],
                $profileData['present_city'],
                $profileData['present_country'],
                $profileData['present_state'],
                $profileData['present_district'],
                $profileData['present_pincode'],
                $profileData['permanent_address'],
                $profileData['permanent_city'],
                $profileData['permanent_country'],
                $profileData['permanent_state'],
                $profileData['permanent_district'],
                $profileData['permanent_pincode'],
                $profileData['admission_type'],
                $profileData['exam_name'],
                $profileData['board_name'],
                $profileData['institution_name'],
                $profileData['max_marks'],
                $profileData['marks_obtained'],
                $profileData['percentage'],
                now(),
                now(),
            ]);

            if (!$profileInserted) {
                throw new Exception("Failed to insert applicant profile.");
            }

            // Get the ID of the newly created profile
            $applicantProfileId = DB::getPdo()->lastInsertId();

            // 2. Insert Paper Selection
            DB::insert('INSERT INTO applicant_paper_selections (
                applicant_profile_id, programme_id, course_id, batch_id, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?)', [
                $applicantProfileId,
                $paperSelectionData['programme_id'],
                $paperSelectionData['course_id'],
                $paperSelectionData['batch_id'],
                now(),
                now()
            ]);

            // 3. Insert Documents
            DB::insert('INSERT INTO applicant_documents (
                applicant_profile_id, photo_path, signature_path, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?)', [
                $applicantProfileId,
                $documentData['photo_path'],
                $documentData['signature_path'],
                now(),
                now()
            ]);

            // 4. Insert Payment
            DB::insert('INSERT INTO applicant_payments (
                applicant_profile_id, fee_type, amount, payment_method, transaction_id, payment_date, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
                $applicantProfileId,
                $paymentData['fee_type'],
                $paymentData['amount'],
                $paymentData['payment_method'],
                $paymentData['transaction_id'] ?? null,
                $paymentData['payment_date'] ?? null,
                now(),
                now()
            ]);

            DB::commit();

            return (int) $applicantProfileId;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
