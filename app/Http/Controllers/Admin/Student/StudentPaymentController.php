<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\StudentRepository;
use Illuminate\Http\Request;

class StudentPaymentController extends Controller
{
    protected StudentRepository $studentRepo;

    public function __construct(StudentRepository $studentRepo)
    {
        $this->studentRepo = $studentRepo;
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'fee_type' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string'],
            'transaction_id' => ['required', 'string', 'max:255'],
            'payment_date' => ['required', 'date'],
        ]);

        $this->studentRepo->updateStudentPayment($id, $validated, now());

        return redirect()->back()->with('success', 'Payment details updated successfully.');
    }
}
