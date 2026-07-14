import React, { useState, useEffect } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { toast } from 'sonner';
import { useRoute } from 'ziggy-js';
import AdminDashboardLayout from "@/Pages/Layouts/Admin/AdminDashboardLayout";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import CreateBasicInfoForm from './components/forms/CreateBasicInfoForm';
import CreatePaperSelectionForm from './components/forms/CreatePaperSelectionForm';
import CreateDocumentForm from './components/forms/CreateDocumentForm';
import CreatePaymentForm from './components/forms/CreatePaymentForm';

export default function CreateStudent({ programmes_with_courses = [], batches = [] }) {
  const route = useRoute();
  const [activeTab, setActiveTab] = useState("basic-info");

  const { data, setData, post, processing, errors, hasErrors } = useForm({
    // Basic Info
    full_name: '', father_name: '', mother_name: '', gender: '', dob: '', abc_id: '', aadhaar_no: '', nationality: 'Indian', mobile_no: '', email: '', parent_mobile_no: '', religion: '', caste: '', blood_group: '', marital_status: '', annual_family_income: '', is_blind: false, is_bpl: false, is_minority: false, is_ph: false, present_address: '', present_city: '', present_district: '', present_state: '', present_country: 'India', present_pincode: '', permanent_address: '', permanent_city: '', permanent_district: '', permanent_state: '', permanent_country: 'India', permanent_pincode: '', admission_type: '', exam_name: '', board_name: '', institution_name: '', max_marks: '', marks_obtained: '', percentage: '',

    // Paper Selection
    programme_id: '', course_id: '', batch_id: '',

    // Documents
    photo: null, signature: null,

    // Payments
    fee_type: '', amount: '', payment_method: '', transaction_id: '', payment_date: ''
  });

  useEffect(() => {
    if (hasErrors) {
      toast.error("Please fix the errors in the form before submitting.");

      const basicFields = ['full_name', 'father_name', 'mother_name', 'gender', 'dob', 'abc_id', 'aadhaar_no', 'nationality', 'mobile_no', 'email', 'parent_mobile_no', 'religion', 'caste', 'blood_group', 'marital_status', 'annual_family_income', 'present_address', 'present_city', 'present_district', 'present_state', 'present_country', 'present_pincode', 'permanent_address', 'permanent_city', 'permanent_district', 'permanent_state', 'permanent_country', 'permanent_pincode', 'admission_type', 'exam_name', 'board_name', 'institution_name', 'max_marks', 'marks_obtained', 'percentage'];
      const paperFields = ['programme_id', 'course_id', 'batch_id'];
      const docFields = ['photo', 'signature'];
      const paymentFields = ['fee_type', 'amount', 'payment_method', 'transaction_id', 'payment_date'];

      const errorKeys = Object.keys(errors);

      if (errorKeys.some(k => basicFields.includes(k))) setActiveTab('basic-info');
      else if (errorKeys.some(k => paperFields.includes(k))) setActiveTab('paper-selection');
      else if (errorKeys.some(k => docFields.includes(k))) setActiveTab('documents');
      else if (errorKeys.some(k => paymentFields.includes(k))) setActiveTab('payment');
    }
  }, [errors, hasErrors]);

  const handleSubmit = (e) => {
    e.preventDefault();
    post(route('admin.students.store'), {
      preserveScroll: true
    });
  };

  return (
    <div className="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
      <Head title="Register New Student" />

      <div className="mb-6">
        <h1 className="text-3xl font-bold tracking-tight text-gray-900">Student Registration Wizard</h1>
        <p className="text-gray-500 mt-1">Complete the 4-step process to enroll a new student.</p>
      </div>

      {hasErrors && (
        <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <h4 className="text-red-700 font-semibold mb-2">Please fix the following errors:</h4>
          <ul className="list-disc list-inside text-sm text-red-600 space-y-1">
            {Object.entries(errors).map(([field, message]) => (
              <li key={field}>{message}</li>
            ))}
          </ul>
        </div>
      )}

      <form onSubmit={handleSubmit} encType="multipart/form-data">
        <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">

          <TabsList className="grid w-full grid-cols-4 mb-8 bg-gray-100/80 p-1 rounded-xl">
            <TabsTrigger value="basic-info" className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-indigo-600 data-[state=active]:shadow-sm transition-all">1. Basic Info</TabsTrigger>
            <TabsTrigger value="paper-selection" className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-indigo-600 data-[state=active]:shadow-sm transition-all">2. Academic Placement</TabsTrigger>
            <TabsTrigger value="documents" className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-indigo-600 data-[state=active]:shadow-sm transition-all">3. Documents Upload</TabsTrigger>
            <TabsTrigger value="payment" className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-indigo-600 data-[state=active]:shadow-sm transition-all">4. Payment Details</TabsTrigger>
          </TabsList>

          <TabsContent value="basic-info" className="mt-0 outline-none">
            <Card className="border-0 shadow-xl ring-1 ring-gray-100 bg-white/60 backdrop-blur-md rounded-2xl overflow-hidden">
              <CardHeader className="bg-white/40 border-b border-gray-100 pb-6">
                <CardTitle className="text-2xl text-indigo-950">Basic Information</CardTitle>
                <CardDescription className="text-gray-500">
                  Enter the student's personal and academic history.
                </CardDescription>
              </CardHeader>
              <CardContent className="p-6">
                <CreateBasicInfoForm data={data} setData={setData} errors={errors} onNext={() => setActiveTab('paper-selection')} />
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="paper-selection" className="mt-0 outline-none">
            <Card className="border-0 shadow-xl ring-1 ring-gray-100 bg-white/60 backdrop-blur-md rounded-2xl overflow-hidden">
              <CardHeader className="bg-white/40 border-b border-gray-100 pb-6">
                <CardTitle className="text-2xl text-indigo-950">Academic Placement</CardTitle>
                <CardDescription className="text-gray-500">
                  Select the programme, course, and batch.
                </CardDescription>
              </CardHeader>
              <CardContent className="p-6">
                <CreatePaperSelectionForm
                  data={data} setData={setData} errors={errors}
                  programmes_with_courses={programmes_with_courses}
                  batches={batches}
                  onNext={() => setActiveTab('documents')}
                />
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="documents" className="mt-0 outline-none">
            <Card className="border-0 shadow-xl ring-1 ring-gray-100 bg-white/60 backdrop-blur-md rounded-2xl overflow-hidden">
              <CardHeader className="bg-white/40 border-b border-gray-100 pb-6">
                <CardTitle className="text-2xl text-indigo-950">Documents Upload</CardTitle>
                <CardDescription className="text-gray-500">
                  Upload required certificates and identification documents.
                </CardDescription>
              </CardHeader>
              <CardContent className="p-6">
                <CreateDocumentForm data={data} setData={setData} errors={errors} onNext={() => setActiveTab('payment')} />
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="payment" className="mt-0 outline-none">
            <Card className="border-0 shadow-xl ring-1 ring-gray-100 bg-white/60 backdrop-blur-md rounded-2xl overflow-hidden">
              <CardHeader className="bg-white/40 border-b border-gray-100 pb-6">
                <CardTitle className="text-2xl text-indigo-950">Payment Details</CardTitle>
                <CardDescription className="text-gray-500">
                  Record the initial admission transaction and fee details.
                </CardDescription>
              </CardHeader>
              <CardContent className="p-6">
                <CreatePaymentForm data={data} setData={setData} errors={errors} processing={processing} />
              </CardContent>
            </Card>
          </TabsContent>

        </Tabs>
      </form>
    </div>
  );
}

CreateStudent.layout = (page) => {
  return <AdminDashboardLayout showSidebar={true} children={page} />;
};