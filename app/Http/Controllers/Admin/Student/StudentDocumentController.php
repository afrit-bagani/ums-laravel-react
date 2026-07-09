<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentDocumentController extends Controller
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentDocument $studentDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentDocument $studentDocument)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $updates = [];
        $bindings = [];

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('documents', 'public');
            $updates[] = 'photo = ?';
            $bindings[] = $photoPath;
        }

        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('documents', 'public');
            $updates[] = 'signature = ?';
            $bindings[] = $signaturePath;
        }

        if (! empty($updates)) {
            $updates[] = 'updated_at = ?';
            $bindings[] = now();

            $bindings[] = $id;

            $setClause = implode(', ', $updates);

            DB::update(
                "UPDATE student_documents SET {$setClause} WHERE student_profile_id = ?",
                $bindings
            );
        }

        return redirect()->back()->with('success', 'Documents updated successfully.');
    }

    public function destroy(StudentDocument $studentDocument)
    {
        //
    }
}
