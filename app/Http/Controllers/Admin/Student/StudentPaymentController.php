<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentPaymentController extends Controller
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
    public function show(StudentPayment $studentPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentPayment $studentPayment)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'fee_type' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string'],
            'transaction_id' => ['required', 'string', 'max:255'],
        ]);

        DB::update(
            'UPDATE student_payments SET fee_type = ?, amount = ?, payment_method = ?, transaction_id = ?, updated_at = ? WHERE student_profile_id = ?',
            [
                $validated['fee_type'],
                $validated['amount'],
                $validated['payment_method'],
                $validated['transaction_id'],
                now(),
                $id,
            ]
        );

        return redirect()->back()->with('success', 'Payment details updated successfully.');
    }

    public function destroy(StudentPayment $studentPayment)
    {
        //
    }
}
