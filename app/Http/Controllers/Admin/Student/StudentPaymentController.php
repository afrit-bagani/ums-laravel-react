<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentPaymentController extends Controller
{
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
}
