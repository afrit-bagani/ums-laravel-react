<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Repositories\StudentRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StudentDashboardController extends Controller
{
    protected $studentRepo;

    public function __construct(StudentRepository $studentRepo)
    {
        $this->studentRepo = $studentRepo;
    }

    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $student = $this->studentRepo->getDashboardProfileByUserId($userId);

        return Inertia::render('Student/Dashboard', [
            'student' => (array) $student,
        ]);
    }

    public function downloadReceipt(Request $request)
    {
        $userId = Auth::id();

        $student = $this->studentRepo->getReceiptDataByUserId($userId);

        if (! $student || ! $student->transaction_id) {
            return back()->with('error', 'No payment records found.');
        }

        $pdf = Pdf::loadView('student.receipt', ['student' => $student]);

        return $pdf->download("Receipt_{$student->registration_number}.pdf");
    }
}
