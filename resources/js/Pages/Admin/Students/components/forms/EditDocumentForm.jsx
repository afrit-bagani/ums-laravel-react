import React from 'react';
import { useForm } from '@inertiajs/react';
import CreateDocumentForm from './CreateDocumentForm';
import { useRoute } from 'ziggy-js';

export default function EditDocumentForm({ studentProfile }) {
  const route = useRoute();

  const { data, setData, post, processing, errors } = useForm({
    photo: studentProfile?.photo || null,
    signature: studentProfile?.signature || null,
    _method: 'patch', // Required for file uploads in Laravel
  });

  const handleSubmit = (e) => {
    if (e) e.preventDefault();
    post(route('admin.students.documents.update', { id: studentProfile.id }), {
      preserveScroll: true,
    });
  };

  return (
    <form onSubmit={handleSubmit} encType="multipart/form-data" className="space-y-8">
      <CreateDocumentForm
        data={data}
        setData={setData}
        errors={errors}
        onNext={handleSubmit}
        buttonLabel="Update Documents"
        processing={processing}
      />
    </form>
  );
}
