<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\StudentRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentPaperSelectionController extends Controller
{
    protected StudentRepository $studentRepo;

    public function __construct(StudentRepository $studentRepo)
    {
        $this->studentRepo = $studentRepo;
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'programme_id' => ['required', 'integer', Rule::exists('programme_master', 'programme_id')],
            'course_id' => ['required', 'integer', Rule::exists('course_master', 'course_id')],
            'batch_id' => ['required', 'integer', Rule::exists('batch_master', 'batch_id')],
        ]);

        $updatedAt = now();

        $this->studentRepo->updateStudentPaperSelection(
            $id,
            $validated['programme_id'],
            $validated['course_id'],
            $validated['batch_id'],
            $updatedAt
        );

        // change reg_no according to new batch
        $batchName = $this->studentRepo->getBatchNameById($validated['batch_id']);
        if ($batchName) {
            $batchStartYear = explode('-', $batchName->name)[0] ?? date('Y');

            $existingProfile = $this->studentRepo->getStudentProfileById($id);

            if ($existingProfile && str_starts_with($existingProfile->registration_number, $batchStartYear)) {
                // Batch year hasn't changed, keep the existing registration number
                $registrationNumber = $existingProfile->registration_number;
            } else {
                $lastProfile = $this->studentRepo->getLastRegistrationNumberByYear($batchStartYear);
                if ($lastProfile) {
                    $lastNumber = (int) substr($lastProfile->registration_number, strlen($batchStartYear));
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }
                $registrationNumber = $batchStartYear.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            $this->studentRepo->updateRegistrationNumber($id, $registrationNumber);
        }

        return redirect()->back()->with('success', 'Academic placement updated successfully.');
    }
}
