import React from 'react';
import { useForm } from '@inertiajs/react';
import CreatePaperSelectionForm from './CreatePaperSelectionForm';
import { useRoute } from 'ziggy-js';

export default function EditPaperSelectionForm({ studentProfile, programmes_with_courses = [], batches = [] }) {
  const route = useRoute();

  const { data, setData, patch, processing, errors } = useForm({
    programme_id: studentProfile?.programme_id ? studentProfile.programme_id.toString() : '',
    course_id: studentProfile?.course_id ? studentProfile.course_id.toString() : '',
    batch_id: studentProfile?.batch_id ? studentProfile.batch_id.toString() : '',
  });

  const handleSubmit = (e) => {
    if (e) e.preventDefault();
    patch(route('admin.students.papers.update', { id: studentProfile.id }), {
      preserveScroll: true,
    });
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-8">
      <CreatePaperSelectionForm
        data={data}
        setData={setData}
        errors={errors}
        onNext={handleSubmit}
        programmes_with_courses={programmes_with_courses}
        batches={batches}
        buttonLabel="Update Academic Placement"
        processing={processing}
      />
    </form>
  );
}
