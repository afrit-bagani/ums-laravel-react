<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Facades\DB;

class StudentRepository
{
    /**
     * Get paginated list of students with search and status filters.
     */
    public function getPaginatedStudents(?string $search, string $status, int $perPage, int $offset)
    {
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

        return DB::select("
            SELECT s.id, s.registration_number, s.full_name, s.email, s.mobile_no, s.status,  
                   c.name as course_name, 
                   CONCAT(b.code, ' - ', b.name) as batch_name
            FROM student_profiles as s 
            LEFT JOIN student_paper_selections as sps on s.id = sps.student_profile_id
            LEFT JOIN course_master as c on c.course_id = sps.course_id
            LEFT JOIN batch_master as b on b.batch_id = sps.batch_id
            $query
            ORDER BY s.created_at DESC
            LIMIT ? OFFSET ?
        ", $dataBindings);
    }

    /**
     * Get total count of students based on search and status filters.
     */
    public function getTotalStudentsCount(?string $search, string $status)
    {
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

        return DB::selectOne("
            SELECT COUNT(*) as count 
            FROM student_profiles as s 
            LEFT JOIN student_paper_selections as sps on s.id = sps.student_profile_id
            LEFT JOIN course_master as c on c.course_id = sps.course_id
            LEFT JOIN batch_master as b on b.batch_id = sps.batch_id
            $query
        ", $bindings)->count;
    }

    /**
     * Get all active programmes.
     */
    public function getActiveProgrammes()
    {
        return DB::select("SELECT programme_id, name as programme_name FROM programme_master WHERE status = 'active'");
    }

    /**
     * Get all active courses.
     */
    public function getActiveCourses()
    {
        return DB::select("SELECT course_id, programme_id, name as course_name FROM course_master WHERE status = 'active'");
    }

    /**
     * Get all active batches.
     */
    public function getActiveBatches()
    {
        return DB::select("SELECT batch_id, CONCAT(code, ' - ', name) as batch_name FROM batch_master WHERE status = 'active'");
    }

    /**
     * Get batch name by ID.
     */
    public function getBatchNameById(int $batchId)
    {
        return DB::selectOne('SELECT name FROM batch_master WHERE batch_id = ?', [$batchId]);
    }

    /**
     * Get the last registration number for a given batch year.
     */
    public function getLastRegistrationNumberByYear(string $batchStartYear)
    {
        return DB::selectOne('SELECT registration_number FROM student_profiles WHERE registration_number LIKE ? ORDER BY registration_number DESC LIMIT 1', [$batchStartYear.'%']);
    }

    /**
     * Create a user record and return the generated ID.
     */
    public function createUser(string $name, string $loginIdentifier, string $role, string $hashedPassword, bool $isPasswordChanged, string $createdAt, string $updatedAt)
    {
        DB::insert(
            'INSERT INTO users (name, login_identifier, role, password, is_password_changed, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$name, $loginIdentifier, $role, $hashedPassword, $isPasswordChanged, $createdAt, $updatedAt]
        );

        return DB::getPdo()->lastInsertId();
    }

    /**
     * Create a student profile record and return the generated ID.
     */
    public function createStudentProfile(int $userId, string $registrationNumber, array $data, string $createdAt, string $updatedAt)
    {
        DB::insert(
            'INSERT INTO student_profiles (user_id, registration_number, full_name, father_name, mother_name, gender, dob, abc_id, aadhaar_no, nationality, mobile_no, email, religion, caste, blood_group, marital_status, annual_family_income, parent_mobile_no, is_blind, is_bpl, is_minority, is_ph, present_address, present_city, present_country, present_state, present_district, present_pincode, permanent_address, permanent_city, permanent_country, permanent_state, permanent_district, permanent_pincode, admission_type, exam_name, board_name, institution_name, max_marks, marks_obtained, percentage, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $userId,
                $registrationNumber,
                $data['full_name'],
                $data['father_name'],
                $data['mother_name'],
                $data['gender'],
                $data['dob'],
                $data['abc_id'] ?? null,
                $data['aadhaar_no'] ?? null,
                $data['nationality'],
                $data['mobile_no'],
                $data['email'],
                $data['religion'],
                $data['caste'],
                $data['blood_group'],
                $data['marital_status'],
                $data['annual_family_income'] ?? null,
                $data['parent_mobile_no'] ?? null,
                $data['is_blind'] ?? false,
                $data['is_bpl'] ?? false,
                $data['is_minority'] ?? false,
                $data['is_ph'] ?? false,
                $data['present_address'],
                $data['present_city'],
                $data['present_country'],
                $data['present_state'],
                $data['present_district'],
                $data['present_pincode'],
                $data['permanent_address'],
                $data['permanent_city'],
                $data['permanent_country'],
                $data['permanent_state'],
                $data['permanent_district'],
                $data['permanent_pincode'],
                $data['admission_type'],
                $data['exam_name'],
                $data['board_name'],
                $data['institution_name'],
                $data['max_marks'],
                $data['marks_obtained'],
                $data['percentage'],
                $createdAt,
                $updatedAt,
            ]
        );

        return DB::getPdo()->lastInsertId();
    }

    /**
     * Create a student paper selection record.
     */
    public function createStudentPaperSelection(int $studentId, int $programmeId, int $courseId, int $batchId, string $createdAt, string $updatedAt)
    {
        DB::insert(
            'INSERT INTO student_paper_selections (student_profile_id, programme_id, course_id, batch_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
            [$studentId, $programmeId, $courseId, $batchId, $createdAt, $updatedAt]
        );
    }

    /**
     * Create a student document record.
     */
    public function createStudentDocument(int $studentId, ?string $photoPath, ?string $photoHash, ?string $signaturePath, ?string $signatureHash, string $createdAt, string $updatedAt)
    {
        DB::insert(
            'INSERT INTO student_documents (student_profile_id, photo_path, photo_hash, signature_path, signature_hash, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$studentId, $photoPath, $photoHash, $signaturePath, $signatureHash, $createdAt, $updatedAt]
        );
    }

    /**
     * Check if a document hash already exists in the system to prevent duplicates.
     */
    public function checkDocumentHashExists(string $hash): bool
    {
        $exists = DB::selectOne(
            'SELECT 1 FROM student_documents WHERE photo_hash = ? OR signature_hash = ? LIMIT 1',
            [$hash, $hash]
        );
        
        return $exists !== null;
    }

    /**
     * Create a student payment record.
     */
    public function createStudentPayment(int $studentId, array $data, string $createdAt, string $updatedAt)
    {
        DB::insert(
            'INSERT INTO student_payments (student_profile_id, fee_type, amount, payment_method, transaction_id, payment_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$studentId, $data['fee_type'], $data['amount'], $data['payment_method'], $data['transaction_id'], $data['payment_date'], $createdAt, $updatedAt]
        );
    }

    /**
     * Get student profile by ID.
     */
    public function getStudentProfileById(int $id)
    {
        return DB::selectOne('SELECT * FROM student_profiles WHERE id = ?', [$id]);
    }

    /**
     * Get a student's full profile including all relations by their profile ID.
     */
    public function getStudentWithRelationsById(int $id)
    {
        return DB::selectOne('
            SELECT sp.*, 
                   pm.name AS programme_name,
                   cm.name AS course_name,
                   bm.name AS batch_name,
                   sps.programme_id,
                   sps.course_id,
                   sps.batch_id,
                   sd.photo_path AS photo, 
                   sd.signature_path AS signature,
                   spay.fee_type, 
                   spay.amount, 
                   spay.payment_method, 
                   spay.transaction_id, 
                   spay.payment_date
            FROM student_profiles sp
            LEFT JOIN student_paper_selections sps ON sp.id = sps.student_profile_id
            LEFT JOIN programme_master pm ON sps.programme_id = pm.programme_id
            LEFT JOIN course_master cm ON sps.course_id = cm.course_id
            LEFT JOIN batch_master bm ON sps.batch_id = bm.batch_id
            LEFT JOIN student_documents sd ON sp.id = sd.student_profile_id
            LEFT JOIN student_payments spay ON sp.id = spay.student_profile_id
            WHERE sp.id = ?
        ', [$id]);
    }

    /**
     * Update a student profile's basic info.
     */
    public function updateStudentProfile(int $id, array $data, string $updatedAt)
    {
        DB::update(
            'UPDATE student_profiles SET full_name = ?, father_name = ?, mother_name = ?, gender = ?, dob = ?, abc_id = ?, aadhaar_no = ?, nationality = ?, mobile_no = ?, email = ?, religion = ?, caste = ?, blood_group = ?, marital_status = ?, annual_family_income = ?, parent_mobile_no = ?, is_blind = ?, is_bpl = ?, is_minority = ?, is_ph = ?, present_address = ?, present_city = ?, present_country = ?, present_state = ?, present_district = ?, present_pincode = ?, permanent_address = ?, permanent_city = ?, permanent_country = ?, permanent_state = ?, permanent_district = ?, permanent_pincode = ?, admission_type = ?, exam_name = ?, board_name = ?, institution_name = ?, max_marks = ?, marks_obtained = ?, percentage = ?, updated_at = ? WHERE id = ?',
            [
                $data['full_name'],
                $data['father_name'],
                $data['mother_name'],
                $data['gender'],
                $data['dob'],
                $data['abc_id'] ?? null,
                $data['aadhaar_no'] ?? null,
                $data['nationality'],
                $data['mobile_no'],
                $data['email'],
                $data['religion'],
                $data['caste'],
                $data['blood_group'],
                $data['marital_status'],
                $data['annual_family_income'] ?? null,
                $data['parent_mobile_no'] ?? null,
                $data['is_blind'] ?? false,
                $data['is_bpl'] ?? false,
                $data['is_minority'] ?? false,
                $data['is_ph'] ?? false,
                $data['present_address'],
                $data['present_city'],
                $data['present_country'],
                $data['present_state'],
                $data['present_district'],
                $data['present_pincode'],
                $data['permanent_address'],
                $data['permanent_city'],
                $data['permanent_country'],
                $data['permanent_state'],
                $data['permanent_district'],
                $data['permanent_pincode'],
                $data['admission_type'],
                $data['exam_name'],
                $data['board_name'],
                $data['institution_name'],
                $data['max_marks'],
                $data['marks_obtained'],
                $data['percentage'],
                $updatedAt,
                $id,
            ]
        );
    }

    /**
     * Update a student's status.
     */
    public function updateStudentStatus(int $id, string $status, string $updatedAt)
    {
        DB::update('UPDATE student_profiles SET status = ?, updated_at = ? WHERE id = ?', [$status, $updatedAt, $id]);
    }

    /**
     * Bulk update student statuses.
     */
    public function bulkUpdateStudentStatus(array $ids, string $status, string $updatedAt)
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $bindings = array_merge([$status, $updatedAt], $ids);

        DB::update(
            "UPDATE student_profiles SET status = ?, updated_at = ? WHERE id IN ($placeholders)",
            $bindings
        );
    }

    /**
     * Update a student's paper selection.
     */
    public function updateStudentPaperSelection(int $profileId, int $programmeId, int $courseId, int $batchId, string $updatedAt)
    {
        DB::update(
            'UPDATE student_paper_selections SET programme_id = ?, course_id = ?, batch_id = ?, updated_at = ? WHERE student_profile_id = ?',
            [$programmeId, $courseId, $batchId, $updatedAt, $profileId]
        );
    }

    /**
     * Update a student's registration number.
     */
    public function updateRegistrationNumber(int $profileId, string $registrationNumber)
    {
        DB::update('UPDATE student_profiles SET registration_number = ? WHERE id = ?', [$registrationNumber, $profileId]);
    }

    /**
     * Update a student's documents.
     */
    public function updateStudentDocuments(int $profileId, array $updates, array $bindings)
    {
        if (! empty($updates)) {
            $setClause = implode(', ', $updates);
            DB::update("UPDATE student_documents SET {$setClause} WHERE student_profile_id = ?", $bindings);
        }
    }

    /**
     * Update a student's payment.
     */
    public function updateStudentPayment(int $profileId, array $data, string $updatedAt)
    {
        DB::update(
            'UPDATE student_payments SET fee_type = ?, amount = ?, payment_method = ?, transaction_id = ?, payment_date = ?, updated_at = ? WHERE student_profile_id = ?',
            [
                $data['fee_type'],
                $data['amount'],
                $data['payment_method'],
                $data['transaction_id'],
                $data['payment_date'],
                $updatedAt,
                $profileId,
            ]
        );
    }
}
