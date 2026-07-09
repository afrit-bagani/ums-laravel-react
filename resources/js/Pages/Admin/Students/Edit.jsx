import React from 'react';
import { Head } from '@inertiajs/react';
import AdminDashboardLayout from "@/Pages/Layouts/Admin/AdminDashboardLayout";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import EditBasicInfoForm from './components/forms/EditBasicInfoForm';
import EditPaperSelectionForm from './components/forms/EditPaperSelectionForm';
import EditDocumentForm from './components/forms/EditDocumentForm';
import EditPaymentForm from './components/forms/EditPaymentForm';

export default function EditStudent({ programmes_with_courses = [], batches = [], student_profile }) {
  const [activeTab, setActiveTab] = React.useState("basic-info");

  return (
    <div className="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
      <Head title={`Edit Student - ${student_profile.full_name}`} />

      <div className="mb-6">
        <h1 className="text-3xl font-bold tracking-tight text-gray-900">Edit Student Record</h1>
        <p className="text-gray-500 mt-1">
          Updating details for: <span className="font-semibold text-indigo-600">{student_profile.full_name}</span> (Reg No: {student_profile.registration_number})
        </p>
      </div>

      <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">

        <TabsList className="grid w-full grid-cols-4 mb-8 bg-gray-100/80 p-1 rounded-xl">
          <TabsTrigger value="basic-info" className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-indigo-600 data-[state=active]:shadow-sm transition-all">1. Basic Info</TabsTrigger>
          <TabsTrigger value="paper-selection" className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-indigo-600 data-[state=active]:shadow-sm transition-all">2. Academic Placement</TabsTrigger>
          <TabsTrigger value="documents" className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-indigo-600 data-[state=active]:shadow-sm transition-all">3. Documents</TabsTrigger>
          <TabsTrigger value="payment" className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-indigo-600 data-[state=active]:shadow-sm transition-all">4. Payments</TabsTrigger>
        </TabsList>

        <TabsContent value="basic-info" className="mt-0 outline-none">
          <Card className="border-0 shadow-xl ring-1 ring-gray-100 bg-white/60 backdrop-blur-md rounded-2xl overflow-hidden">
            <CardHeader className="bg-white/40 border-b border-gray-100 pb-6">
              <CardTitle className="text-2xl text-indigo-950">Basic Information</CardTitle>
              <CardDescription className="text-gray-500">Update personal and demographic information.</CardDescription>
            </CardHeader>
            <CardContent className="p-6">
              <EditBasicInfoForm studentProfile={student_profile} />
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="paper-selection" className="mt-0 outline-none">
          <Card className="border-0 shadow-xl ring-1 ring-gray-100 bg-white/60 backdrop-blur-md rounded-2xl overflow-hidden">
            <CardHeader className="bg-white/40 border-b border-gray-100 pb-6">
              <CardTitle className="text-2xl text-indigo-950">Academic Placement</CardTitle>
              <CardDescription className="text-gray-500">Update programme, course, or batch.</CardDescription>
            </CardHeader>
            <CardContent className="p-6">
              <EditPaperSelectionForm
                programmes_with_courses={programmes_with_courses}
                batches={batches}
                studentProfile={student_profile}
              />
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="documents" className="mt-0 outline-none">
          <Card className="border-0 shadow-xl ring-1 ring-gray-100 bg-white/60 backdrop-blur-md rounded-2xl overflow-hidden">
            <CardHeader className="bg-white/40 border-b border-gray-100 pb-6">
              <CardTitle className="text-2xl text-indigo-950">Documents Upload</CardTitle>
              <CardDescription className="text-gray-500">Update student documents.</CardDescription>
            </CardHeader>
            <CardContent className="p-6">
              <EditDocumentForm studentProfile={student_profile} />
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="payment" className="mt-0 outline-none">
          <Card className="border-0 shadow-xl ring-1 ring-gray-100 bg-white/60 backdrop-blur-md rounded-2xl overflow-hidden">
            <CardHeader className="bg-white/40 border-b border-gray-100 pb-6">
              <CardTitle className="text-2xl text-indigo-950">Payment Details</CardTitle>
              <CardDescription className="text-gray-500">Update payment information.</CardDescription>
            </CardHeader>
            <CardContent className="p-6">
              <EditPaymentForm studentProfile={student_profile} />
            </CardContent>
          </Card>
        </TabsContent>

      </Tabs>
    </div>
  );
}

EditStudent.layout = page => <AdminDashboardLayout children={page} />;
