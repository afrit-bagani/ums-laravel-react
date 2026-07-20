import React from 'react';
import { useForm } from '@inertiajs/react';
import CreateBasicInfoForm from './CreateBasicInfoForm';
import { useRoute } from 'ziggy-js';

export default function EditBasicInfoForm({ studentProfile }) {
  const route = useRoute();

  const { data, setData, patch, processing, errors } = useForm({
    full_name: studentProfile?.full_name || '',
    father_name: studentProfile?.father_name || '',
    mother_name: studentProfile?.mother_name || '',
    gender: studentProfile?.gender || '',
    dob: studentProfile?.dob || '',
    abc_id: studentProfile?.abc_id || '',
    aadhaar_no: studentProfile?.aadhaar_no || '',
    nationality: studentProfile?.nationality || 'Indian',
    mobile_no: studentProfile?.mobile_no || '',
    email: studentProfile?.email || '',
    parent_mobile_no: studentProfile?.parent_mobile_no || '',
    religion: studentProfile?.religion || '',
    caste: studentProfile?.caste || '',
    blood_group: studentProfile?.blood_group || '',
    marital_status: studentProfile?.marital_status || '',
    annual_family_income: studentProfile?.annual_family_income || '',

    is_blind: !!studentProfile?.is_blind,
    is_bpl: !!studentProfile?.is_bpl,
    is_minority: !!studentProfile?.is_minority,
    is_ph: !!studentProfile?.is_ph,

    present_address: studentProfile?.present_address || '',
    present_city: studentProfile?.present_city || '',
    present_district: studentProfile?.present_district || '',
    present_state: studentProfile?.present_state || '',
    present_country: studentProfile?.present_country || 'India',
    present_pincode: studentProfile?.present_pincode || '',

    permanent_address: studentProfile?.permanent_address || '',
    permanent_city: studentProfile?.permanent_city || '',
    permanent_district: studentProfile?.permanent_district || '',
    permanent_state: studentProfile?.permanent_state || '',
    permanent_country: studentProfile?.permanent_country || 'India',
    permanent_pincode: studentProfile?.permanent_pincode || '',

    admission_type: studentProfile?.admission_type || '',
    exam_name: studentProfile?.exam_name || '',
    board_name: studentProfile?.board_name || '',
    institution_name: studentProfile?.institution_name || '',
    max_marks: studentProfile?.max_marks || '',
    marks_obtained: studentProfile?.marks_obtained || '',
    percentage: studentProfile?.percentage || '',
  });

  const handleSubmit = (e) => {
    if (e) e.preventDefault();
    patch(route('admin.students.update', { id: studentProfile.id }), {
      preserveScroll: true,
    });
  };

  return (
    <form onSubmit={handleSubmit}>
      <CreateBasicInfoForm
        data={data}
        setData={setData}
        errors={errors}
        onNext={handleSubmit}
        buttonLabel="Update Basic Info"
        processing={processing}
      />
    </form>
  );
}
