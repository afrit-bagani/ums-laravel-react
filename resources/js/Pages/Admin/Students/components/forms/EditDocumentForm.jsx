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
      transform: (data) => {
        const payload = { _method: 'patch' };
        
        // Only send the property if the user actually selected a new file
        if (data.photo instanceof File) {
          payload.photo = data.photo;
        }
        if (data.signature instanceof File) {
          payload.signature = data.signature;
        }
        
        return payload;
      },
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
