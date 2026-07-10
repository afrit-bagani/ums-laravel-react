import React from 'react';
import { useForm } from '@inertiajs/react';
import CreatePaymentForm from './CreatePaymentForm';
import { useRoute } from 'ziggy-js';

export default function EditPaymentForm({ studentProfile }) {
  const route = useRoute();

  const { data, setData, patch, processing, errors } = useForm({
    fee_type: studentProfile?.fee_type || '',
    amount: studentProfile?.amount ? studentProfile.amount.toString() : '',
    payment_method: studentProfile?.payment_method || '',
    transaction_id: studentProfile?.transaction_id || '',
    payment_date: studentProfile?.payment_date || '',
  });

  const handleSubmit = (e) => {
    if (e) e.preventDefault();
    patch(route('admin.students.payments.update', { id: studentProfile.id }), {
      preserveScroll: true,
      onSuccess: () => {
        alert("Payment details updated successfully!");
      },
    });
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-8">
      <CreatePaymentForm 
        data={data} 
        setData={setData} 
        errors={errors} 
        processing={processing}
        buttonLabel="Update Payment Details"
      />
    </form>
  );
}
