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
        $errors = [];

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoResult = $aiValidator->validatePassport($photo);

            if (!$photoResult['is_valid']) {
                $errors['photo'] = $photoResult['errors'];
            } else {
                $photoHash = $photoResult['hash'];
                if ($this->studentRepo->checkDocumentHashExists($photoHash)) {
                    $errors['photo'] = ['This passport photo has already been uploaded by another student.'];
                } else {
                    $photoPath = $photo->store('documents', 'public');
                    $updates[] = 'photo_path = ?';
                    $bindings[] = $photoPath;
                    $updates[] = 'photo_hash = ?';
                    $bindings[] = $photoHash;
                }
            }
        }

        if ($request->hasFile('signature')) {
            $signature = $request->file('signature');
            $signatureResult = $aiValidator->validateSignature($signature);

            if (!$signatureResult['is_valid']) {
                $errors['signature'] = $signatureResult['errors'];
            } else {
                $signatureHash = $signatureResult['hash'];
                if ($this->studentRepo->checkDocumentHashExists($signatureHash)) {
                    $errors['signature'] = ['This signature has already been uploaded by another student.'];
                } else {
                    $signaturePath = $signature->store('documents', 'public');
                    $updates[] = 'signature_path = ?';
                    $bindings[] = $signaturePath;
                    $updates[] = 'signature_hash = ?';
                    $bindings[] = $signatureHash;
                }
            }
        }

        if (!empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
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
