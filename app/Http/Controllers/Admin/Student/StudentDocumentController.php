<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\StudentRepository;
use App\Services\AiDocumentValidator;
use Illuminate\Http\Request;

class StudentDocumentController extends Controller
{
    protected StudentRepository $studentRepo;

    public function __construct(StudentRepository $studentRepo)
    {
        $this->studentRepo = $studentRepo;
    }

    public function update(Request $request, int $id, AiDocumentValidator $aiValidator)
    {
        $request->validate([
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $updates = [];
        $bindings = [];

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');

            $aiValidator->validatePassport($photo);

            $photoPath = $photo->store('documents', 'public');
            $updates[] = 'photo_path = ?';
            $bindings[] = $photoPath;
        }

        if ($request->hasFile('signature')) {
            $signature = $request->file('signature');

            $aiValidator->validateSignature($signature);

            $signaturePath = $signature->store('documents', 'public');
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
