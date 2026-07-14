<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class StudentRepository
{
    /**
     * Get the student's full profile and dashboard data by their user ID.
     */
    public function getDashboardProfileByUserId(int $userId)
    {
        return DB::selectOne('
            SELECT sp.*, 
                   pm.name AS programme_name,
                   cm.name AS course_name,
                   bm.name AS batch_name,
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
            WHERE sp.user_id = ?
        ', [$userId]);
    }

    /**
     * Get the student's payment receipt data by their user ID.
     */
    public function getReceiptDataByUserId(int $userId)
    {
        return DB::selectOne('
            SELECT sp.*, 
                   cm.name AS course_name,
                   spay.fee_type, 
                   spay.amount, 
                   spay.payment_method, 
                   spay.transaction_id, 
                   spay.payment_date
            FROM student_profiles sp
            LEFT JOIN student_paper_selections sps ON sp.id = sps.student_profile_id
            LEFT JOIN course_master cm ON sps.course_id = cm.course_id
            LEFT JOIN student_payments spay ON sp.id = spay.student_profile_id
            WHERE sp.user_id = ?
        ', [$userId]);
    }
}
