<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\StudentRepository;
use Illuminate\Http\Request;

class StudentDocumentController extends Controller
{
    protected StudentRepository $studentRepo;

    public function __construct(StudentRepository $studentRepo)
    {
        $this->studentRepo = $studentRepo;
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
            $updates[] = 'photo_path = ?';
            $bindings[] = $photoPath;
        }

        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('documents', 'public');
            $updates[] = 'signature_path = ?';
            $bindings[] = $signaturePath;
        }

        if (! empty($updates)) {
            $updates[] = 'updated_at = ?';
            $bindings[] = now();

            $bindings[] = $id;

            $this->studentRepo->updateStudentDocuments($id, $updates, $bindings);
        }

        return redirect()->back()->with('success', 'Documents updated successfully.');
    }
}
