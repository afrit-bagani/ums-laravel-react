import React from 'react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import ErrorAlert from '@/components/ErrorAlert';
import { Loader2 } from 'lucide-react';

export default function CreatePaymentForm({ data, setData, errors, processing, buttonLabel = "Complete Registration" }) {
  return (
    <div className="space-y-8">
      <div>
        <h3 className="text-lg font-semibold text-indigo-950 mb-4 border-b pb-2">Payment Submission</h3>
        <p className="text-sm text-gray-500 mb-6">Enter the admission or registration fee payment details.</p>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="space-y-2">
            <Label>Fee Type <span className="text-red-500">*</span></Label>
            <Select value={data.fee_type} onValueChange={(val) => setData('fee_type', val)}>
              <SelectTrigger><SelectValue placeholder="Select Fee Type" /></SelectTrigger>
              <SelectContent>
                <SelectItem value="Admission Fee">Admission Fee</SelectItem>
                <SelectItem value="Registration Fee">Registration Fee</SelectItem>
                <SelectItem value="Other">Other</SelectItem>
              </SelectContent>
            </Select>
            {errors.fee_type && <ErrorAlert title={errors.fee_type} />}
          </div>

          <div className="space-y-2">
            <Label>Amount (₹) <span className="text-red-500">*</span></Label>
            <Input type="number" placeholder="Enter amount" value={data.amount} onChange={(e) => setData('amount', e.target.value)} />
            {errors.amount && <ErrorAlert title={errors.amount} />}
          </div>

          <div className="space-y-2">
            <Label>Payment Method <span className="text-red-500">*</span></Label>
            <Select value={data.payment_method} onValueChange={(val) => setData('payment_method', val)}>
              <SelectTrigger><SelectValue placeholder="Select Method" /></SelectTrigger>
              <SelectContent>
                <SelectItem value="UPI">UPI</SelectItem>
                <SelectItem value="Bank Transfer">Bank Transfer (NEFT/RTGS)</SelectItem>
                <SelectItem value="Cash">Cash</SelectItem>
                <SelectItem value="Demand Draft">Demand Draft</SelectItem>
              </SelectContent>
            </Select>
            {errors.payment_method && <ErrorAlert title={errors.payment_method} />}
          </div>

          <div className="space-y-2">
            <Label>Transaction / Reference ID <span className="text-red-500">*</span></Label>
            <Input type="text" placeholder="Enter transaction ID" value={data.transaction_id} onChange={(e) => setData('transaction_id', e.target.value)} />
            {errors.transaction_id && <ErrorAlert title={errors.transaction_id} />}
          </div>
        </div>
      </div>

      <div className="flex justify-end pt-4 border-t">
        <Button type="submit" size="lg" className="px-8 shadow-md" disabled={processing}>
          {processing ? <><Loader2 className="mr-2 h-4 w-4 animate-spin" /> Processing...</> : buttonLabel}
        </Button>
      </div>
    </div>
  );
}
