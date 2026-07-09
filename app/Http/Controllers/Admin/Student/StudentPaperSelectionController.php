<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentPaperSelectionController extends Controller
{
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'programme_id' => ['required', 'integer', Rule::exists('programme_master', 'programme_id')],
            'course_id' => ['required', 'integer', Rule::exists('course_master', 'course_id')],
            'batch_id' => ['required', 'integer', Rule::exists('batch_master', 'batch_id')],
        ]);

        $updatedAt = now();

        DB::update(
            'UPDATE student_paper_selections SET programme_id = ?, course_id = ?, batch_id = ?, updated_at = ? WHERE student_profile_id = ?',
            [
                $validated['programme_id'],
                $validated['course_id'],
                $validated['batch_id'],
                $updatedAt,
                $id,
            ]
        );

        // change reg_no according to new batch
        $batchName = DB::selectOne('SELECT name FROM batch_master WHERE batch_id = ?', [$validated['batch_id']]);
        if ($batchName) {
            $batchStartYear = explode('-', $batchName->name)[0] ?? date('Y');

            $existingProfile = DB::selectOne('SELECT registration_number FROM student_profiles WHERE id = ?', [$id]);

            if ($existingProfile && str_starts_with($existingProfile->registration_number, $batchStartYear)) {
                // Batch year hasn't changed, keep the existing registration number
                $registrationNumber = $existingProfile->registration_number;
            } else {
                $lastProfile = DB::selectOne('SELECT registration_number FROM student_profiles WHERE registration_number LIKE ? ORDER BY registration_number DESC LIMIT 1', [$batchStartYear.'%']);
                if ($lastProfile) {
                    $lastNumber = (int) substr($lastProfile->registration_number, strlen($batchStartYear));
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }
                $registrationNumber = $batchStartYear.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            DB::update('UPDATE student_profiles SET registration_number = ? WHERE id = ?', [
                $registrationNumber,
                $id,
            ]);
        }

        return redirect()->back()->with('success', 'Academic placement updated successfully.');
    }
}
