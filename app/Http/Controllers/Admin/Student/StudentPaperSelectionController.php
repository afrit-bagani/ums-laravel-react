<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentPaperSelection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentPaperSelectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'programme_id' => 'required|integer',
            'course_id' => 'required|integer',
            'batch_id' => 'required|integer',
        ]);

        $createdAt = now();
        $updatedAt = now();

        DB::insert('INSERT INTO student_paper_selections (student_profile_id, programme_id, course_id, batch_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)', [
            $id,
            $validated['programme_id'],
            $validated['course_id'],
            $validated['batch_id'],
            $createdAt,
            $updatedAt,
        ]);

        // Update the temporary Registration Number to use the true Batch Year
        $batchName = DB::selectOne('SELECT name FROM batche_master WHERE id = ?', [$validated['batch_id']]);
        if ($batchName) {
            $batchStartYear = explode('-', $batchName->name)[0] ?? date('Y');
            $registrationNumber = $batchStartYear.str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            DB::update('UPDATE student_profiles SET registration_number = ? WHERE id = ?', [
                $registrationNumber,
                $id,
            ]);
        }

        return redirect()->back()->with('success', 'Academic placement saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentPaperSelection $studentPaperSelection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentPaperSelection $studentPaperSelection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentPaperSelection $studentPaperSelection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentPaperSelection $studentPaperSelection)
    {
        //
    }
}
