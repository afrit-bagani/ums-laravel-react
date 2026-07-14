import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { useRoute } from 'ziggy-js';
import AdminDashboardLayout from "@/Pages/Layouts/Admin/AdminDashboardLayout";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { ErrorAlert } from "@/components/ErrorAlert";
import { ChevronLeft, Save } from 'lucide-react';

export default function EditSubject({ subject, programmes_with_courses }) {
  const route = useRoute();

  const { data, setData, patch, processing, errors } = useForm({
    programme_id: subject.programme_id?.toString() || '',
    course_id: subject.course_id?.toString() || '',
    code: subject.code || '',
    name: subject.name || '',
    status: subject.status || 'active',
    internal_full_marks: subject.internal_full_marks?.toString() || '0',
    internal_pass_marks: subject.internal_pass_marks?.toString() || '0',
    theory_full_marks: subject.theory_full_marks?.toString() || '0',
    theory_pass_marks: subject.theory_pass_marks?.toString() || '0',
    practical_full_marks: subject.practical_full_marks?.toString() || '0',
    practical_pass_marks: subject.practical_pass_marks?.toString() || '0',
  });

  const selectedProgramme = programmes_with_courses.find(p => p.programme_id == data.programme_id);
  const availableCourses = selectedProgramme ? selectedProgramme.courses : [];

  const handleSubmit = (e) => {
    e.preventDefault();
    patch(route('admin.subjects.update', subject.subject_id));
  };

  return (
    <>
      <Head title={`Edit Subject - ${subject.code}`} />
      <div className="p-4 md:p-5 max-w-4xl mx-auto">
        <div className="flex items-center gap-4 mb-6">
          <Link href={route('admin.subjects.index')} className="p-2 rounded-full hover:bg-gray-200 transition-colors bg-gray-100">
            <ChevronLeft className="w-5 h-5 text-gray-600" />
          </Link>
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-gray-900">Edit Subject</h1>
            <p className="text-sm text-gray-500 mt-0.5">Update configuration and marks distribution for <span className="font-semibold text-gray-700">{subject.name}</span>.</p>
          </div>
        </div>

        <form onSubmit={handleSubmit}>
          {/* Basic Information Panel */}
          <div className="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-100 pb-3">Basic Information</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">

              <div className="space-y-2">
                <Label htmlFor="programme_id">Select Programme</Label>
                <Select value={data.programme_id} onValueChange={(value) => { setData('programme_id', value); setData('course_id', ''); }}>
                  <SelectTrigger className="w-full">
                    <SelectValue placeholder="Select a programme..." />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      {programmes_with_courses.map(prog => (
                        <SelectItem key={prog.programme_id} value={prog.programme_id.toString()}>
                          {prog.programme_name}
                        </SelectItem>
                      ))}
                    </SelectGroup>
                  </SelectContent>
                </Select>
                {errors.programme_id && <ErrorAlert title={errors.programme_id} />}
              </div>

              <div className="space-y-2">
                <Label htmlFor="course_id">Select Course</Label>
                <Select value={data.course_id} onValueChange={(val) => setData('course_id', val)} disabled={!data.programme_id}>
                  <SelectTrigger className="w-full">
                    <SelectValue placeholder={data.programme_id ? "Select a course..." : "Select programme first"} />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      {availableCourses.map(course => (
                        <SelectItem key={course.course_id} value={course.course_id.toString()}>
                          {course.course_name}
                        </SelectItem>
                      ))}
                    </SelectGroup>
                  </SelectContent>
                </Select>
                {errors.course_id && <ErrorAlert title={errors.course_id} />}
              </div>

              <div className="space-y-2">
                <Label htmlFor="code">Subject Code</Label>
                <Input id="code" value={data.code} onChange={e => setData('code', e.target.value)} placeholder="e.g. CS101" />
                {errors.code && <ErrorAlert title={errors.code} />}
              </div>

              <div className="space-y-2">
                <Label htmlFor="name">Subject Name</Label>
                <Input id="name" value={data.name} onChange={e => setData('name', e.target.value)} placeholder="e.g. Data Structures" />
                {errors.name && <ErrorAlert title={errors.name} />}
              </div>

              <div className="space-y-2 md:col-span-2">
                <Label htmlFor="status">Status</Label>
                <div className="w-full md:w-1/2 md:pr-3">
                  <Select value={data.status} onValueChange={(val) => setData('status', val)}>
                    <SelectTrigger className="w-full">
                      <SelectValue placeholder="Select status..." />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem value="active">Active</SelectItem>
                        <SelectItem value="inactive">Inactive</SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                  {errors.status && <ErrorAlert title={errors.status} />}
                </div>
              </div>

            </div>
          </div>

          {/* Marks Configuration Panel */}
          <div className="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-5 border-b border-gray-100 pb-3">Marks Configuration</h2>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div className="col-span-full mb-0 md:mb-[-10px]">
                <h3 className="text-sm font-bold text-gray-500 uppercase tracking-wider">Internal Marks</h3>
              </div>
              <div className="space-y-2">
                <Label htmlFor="internal_full_marks">Full Marks</Label>
                <Input id="internal_full_marks" type="number" min="0" step="0.01" value={data.internal_full_marks} onChange={e => setData('internal_full_marks', e.target.value)} />
                {errors.internal_full_marks && <ErrorAlert title={errors.internal_full_marks} />}
              </div>
              <div className="space-y-2">
                <Label htmlFor="internal_pass_marks">Pass Marks</Label>
                <Input id="internal_pass_marks" type="number" min="0" step="0.01" value={data.internal_pass_marks} onChange={e => setData('internal_pass_marks', e.target.value)} />
                {errors.internal_pass_marks && <ErrorAlert title={errors.internal_pass_marks} />}
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div className="col-span-full mb-0 md:mb-[-10px]">
                <h3 className="text-sm font-bold text-gray-500 uppercase tracking-wider">Theory Marks</h3>
              </div>
              <div className="space-y-2">
                <Label htmlFor="theory_full_marks">Full Marks</Label>
                <Input id="theory_full_marks" type="number" min="0" step="0.01" value={data.theory_full_marks} onChange={e => setData('theory_full_marks', e.target.value)} />
                {errors.theory_full_marks && <ErrorAlert title={errors.theory_full_marks} />}
              </div>
              <div className="space-y-2">
                <Label htmlFor="theory_pass_marks">Pass Marks</Label>
                <Input id="theory_pass_marks" type="number" min="0" step="0.01" value={data.theory_pass_marks} onChange={e => setData('theory_pass_marks', e.target.value)} />
                {errors.theory_pass_marks && <ErrorAlert title={errors.theory_pass_marks} />}
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="col-span-full mb-0 md:mb-[-10px]">
                <h3 className="text-sm font-bold text-gray-500 uppercase tracking-wider">Practical Marks</h3>
              </div>
              <div className="space-y-2">
                <Label htmlFor="practical_full_marks">Full Marks</Label>
                <Input id="practical_full_marks" type="number" min="0" step="0.01" value={data.practical_full_marks} onChange={e => setData('practical_full_marks', e.target.value)} />
                {errors.practical_full_marks && <ErrorAlert title={errors.practical_full_marks} />}
              </div>
              <div className="space-y-2">
                <Label htmlFor="practical_pass_marks">Pass Marks</Label>
                <Input id="practical_pass_marks" type="number" min="0" step="0.01" value={data.practical_pass_marks} onChange={e => setData('practical_pass_marks', e.target.value)} />
                {errors.practical_pass_marks && <ErrorAlert title={errors.practical_pass_marks} />}
              </div>
            </div>

          </div>

          <div className="flex justify-end gap-3">
            <Link href={route('admin.subjects.index')}>
              <Button type="button" variant="outline">Cancel</Button>
            </Link>
            <Button type="submit" disabled={processing} className="bg-indigo-600 hover:bg-indigo-700 text-white">
              <Save className="w-4 h-4 mr-2" />
              {processing ? 'Saving...' : 'Update Subject'}
            </Button>
          </div>
        </form>
      </div>
    </>
  );
}

EditSubject.layout = (page) => {
  return (
    <AdminDashboardLayout showSidebar={true}>
      {page}
    </AdminDashboardLayout>
  )
}