import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { useRoute } from 'ziggy-js';
import AdminDashboardLayout from "@/Pages/Layouts/Admin/AdminDashboardLayout";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { ChevronLeft, Edit, Calendar, User, BookOpen, GraduationCap, Calculator } from 'lucide-react';

export default function ShowSubject({ subject }) {
  const route = useRoute();
  
  const totalFullMarks = parseFloat(subject.internal_full_marks) + parseFloat(subject.theory_full_marks) + parseFloat(subject.practical_full_marks);
  
  return (
    <>
      <Head title={`View Subject - ${subject.code}`} />
      
      <div className="relative min-h-screen bg-gray-50/50 pb-12">
        {/* Background Decorative Gradient */}
        <div className="absolute inset-x-0 top-0 h-64 bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 rounded-b-[2.5rem] opacity-90 shadow-xl" />
        
        <div className="relative p-4 md:p-6 max-w-5xl mx-auto space-y-6 pt-10">
          
          {/* Header Section (Glassmorphism) */}
          <div className="bg-white/95 backdrop-blur-md border border-white/20 rounded-3xl shadow-lg p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 transition-all hover:shadow-xl">
            <div className="flex items-center gap-5">
              <Link href={route('admin.subjects.index')} className="p-2.5 rounded-2xl hover:bg-indigo-50 hover:text-indigo-600 transition-all bg-gray-50 text-gray-500 shadow-sm border border-gray-100">
                <ChevronLeft className="w-5 h-5" />
              </Link>
              <div>
                <div className="flex items-center gap-3 mb-1">
                  <h1 className="text-3xl font-extrabold tracking-tight text-gray-900 drop-shadow-sm">
                    {subject.name}
                  </h1>
                  <Badge className="text-xs font-bold px-2.5 py-1 uppercase tracking-widest bg-indigo-100 text-indigo-700 hover:bg-indigo-200 border-none shadow-sm">
                    {subject.code}
                  </Badge>
                </div>
                <p className="text-sm font-medium text-gray-500 flex items-center gap-2">
                  <BookOpen className="w-4 h-4 text-indigo-400" />
                  Detailed view of subject configuration and marks distribution.
                </p>
              </div>
            </div>
            
            <div className="flex items-center gap-4">
              <span className={`inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold uppercase tracking-wider shadow-sm border ${subject.status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100'}`}>
                <span className="relative flex h-2.5 w-2.5">
                  <span className={`animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 ${subject.status === 'active' ? 'bg-emerald-400' : 'bg-rose-400'}`}></span>
                  <span className={`relative inline-flex rounded-full h-2.5 w-2.5 ${subject.status === 'active' ? 'bg-emerald-500' : 'bg-rose-500'}`}></span>
                </span>
                {subject.status}
              </span>
              <Link href={route('admin.subjects.edit', subject.subject_id)}>
                <Button className="bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white shadow-md rounded-2xl px-6 py-5 transition-all transform hover:scale-105">
                  <Edit className="w-4 h-4 mr-2" /> Edit Subject
                </Button>
              </Link>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {/* Main Info Column */}
            <div className="lg:col-span-2 space-y-6">
              
              {/* Academic Hierarchy */}
              <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div className="p-6 border-b border-gray-50 bg-gray-50/50 flex items-center gap-3">
                  <div className="p-2 bg-blue-100 text-blue-600 rounded-lg">
                    <GraduationCap className="w-5 h-5" />
                  </div>
                  <h2 className="text-sm font-extrabold text-gray-700 uppercase tracking-widest">Academic Hierarchy</h2>
                </div>
                <div className="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div className="group p-5 bg-gradient-to-br from-gray-50 to-white rounded-2xl border border-gray-100 shadow-sm hover:border-indigo-200 hover:shadow-md transition-all">
                    <p className="text-xs text-gray-400 mb-1 font-bold uppercase tracking-wider">Programme</p>
                    <p className="text-gray-900 font-bold text-lg mb-1">{subject.programme_name || 'N/A'}</p>
                    {subject.programme_code && <Badge variant="outline" className="text-xs text-gray-500 font-semibold bg-white">{subject.programme_code}</Badge>}
                  </div>
                  <div className="group p-5 bg-gradient-to-br from-gray-50 to-white rounded-2xl border border-gray-100 shadow-sm hover:border-violet-200 hover:shadow-md transition-all">
                    <p className="text-xs text-gray-400 mb-1 font-bold uppercase tracking-wider">Course</p>
                    <p className="text-gray-900 font-bold text-lg mb-1">{subject.course_name || 'N/A'}</p>
                    {subject.course_code && <Badge variant="outline" className="text-xs text-gray-500 font-semibold bg-white">{subject.course_code}</Badge>}
                  </div>
                </div>
              </div>

              {/* Marks Distribution */}
              <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow relative">
                <div className="absolute top-0 left-0 w-1 bg-gradient-to-b from-indigo-500 via-fuchsia-500 to-rose-500 h-full"></div>
                <div className="p-6 border-b border-gray-50 bg-gray-50/50 flex items-center gap-3 pl-8">
                  <div className="p-2 bg-fuchsia-100 text-fuchsia-600 rounded-lg">
                    <Calculator className="w-5 h-5" />
                  </div>
                  <h2 className="text-sm font-extrabold text-gray-700 uppercase tracking-widest">Marks Distribution</h2>
                </div>
                
                <div className="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 pl-6">
                  {/* Internal */}
                  <div className="p-6 hover:bg-gray-50 transition-colors">
                    <div className="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs mb-4 mx-auto shadow-sm">IN</div>
                    <h3 className="font-extrabold text-gray-900 mb-5 text-center tracking-tight">Internal</h3>
                    <div className="space-y-4">
                      <div className="flex justify-between items-center py-2 border-b border-gray-100">
                        <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider">Full</span>
                        <span className="font-black text-gray-800 text-lg">{parseFloat(subject.internal_full_marks)}</span>
                      </div>
                      <div className="flex justify-between items-center py-2">
                        <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pass</span>
                        <span className="font-black text-emerald-600 text-lg">{parseFloat(subject.internal_pass_marks)}</span>
                      </div>
                    </div>
                  </div>

                  {/* Theory */}
                  <div className="p-6 hover:bg-gray-50 transition-colors">
                    <div className="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mb-4 mx-auto shadow-sm">TH</div>
                    <h3 className="font-extrabold text-gray-900 mb-5 text-center tracking-tight">Theory</h3>
                    <div className="space-y-4">
                      <div className="flex justify-between items-center py-2 border-b border-gray-100">
                        <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider">Full</span>
                        <span className="font-black text-gray-800 text-lg">{parseFloat(subject.theory_full_marks)}</span>
                      </div>
                      <div className="flex justify-between items-center py-2">
                        <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pass</span>
                        <span className="font-black text-emerald-600 text-lg">{parseFloat(subject.theory_pass_marks)}</span>
                      </div>
                    </div>
                  </div>

                  {/* Practical */}
                  <div className="p-6 hover:bg-gray-50 transition-colors">
                    <div className="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center font-bold text-xs mb-4 mx-auto shadow-sm">PR</div>
                    <h3 className="font-extrabold text-gray-900 mb-5 text-center tracking-tight">Practical</h3>
                    <div className="space-y-4">
                      <div className="flex justify-between items-center py-2 border-b border-gray-100">
                        <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider">Full</span>
                        <span className="font-black text-gray-800 text-lg">{parseFloat(subject.practical_full_marks)}</span>
                      </div>
                      <div className="flex justify-between items-center py-2">
                        <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pass</span>
                        <span className="font-black text-emerald-600 text-lg">{parseFloat(subject.practical_pass_marks)}</span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div className="p-5 bg-gradient-to-r from-gray-900 to-gray-800 flex justify-between items-center text-white rounded-br-3xl ml-6">
                   <span className="text-sm font-bold uppercase tracking-widest text-gray-300">Total Subject Full Marks</span>
                   <span className="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-violet-300 drop-shadow-md">
                      {totalFullMarks}
                   </span>
                </div>
              </div>

            </div>
            
            {/* Sidebar / Audit Column */}
            <div className="space-y-6">
              <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <h2 className="text-sm font-extrabold text-gray-500 uppercase tracking-widest mb-6 border-b border-gray-100 pb-4">Record Audit</h2>
                
                <div className="space-y-6">
                  <div className="flex gap-4 items-start">
                    <div className="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center flex-shrink-0 shadow-sm border border-blue-200">
                      <Calendar className="w-4 h-4 text-blue-600" />
                    </div>
                    <div>
                      <p className="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Created On</p>
                      <p className="text-sm font-extrabold text-gray-800">{new Date(subject.created_at).toLocaleString()}</p>
                    </div>
                  </div>
                  
                  <div className="flex gap-4 items-start">
                    <div className="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center flex-shrink-0 shadow-sm border border-emerald-200">
                      <User className="w-4 h-4 text-emerald-600" />
                    </div>
                    <div>
                      <p className="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Created By</p>
                      <p className="text-sm font-extrabold text-gray-800">{subject.created_by_name || 'System'}</p>
                    </div>
                  </div>

                  <div className="w-full h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent my-4"></div>

                  <div className="flex gap-4 items-start">
                    <div className="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center flex-shrink-0 shadow-sm border border-amber-200">
                      <Calendar className="w-4 h-4 text-amber-600" />
                    </div>
                    <div>
                      <p className="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Last Updated On</p>
                      <p className="text-sm font-extrabold text-gray-800">{new Date(subject.updated_at).toLocaleString()}</p>
                    </div>
                  </div>

                  <div className="flex gap-4 items-start">
                    <div className="w-10 h-10 rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 flex items-center justify-center flex-shrink-0 shadow-sm border border-purple-200">
                      <User className="w-4 h-4 text-purple-600" />
                    </div>
                    <div>
                      <p className="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Last Updated By</p>
                      <p className="text-sm font-extrabold text-gray-800">{subject.updated_by_name || 'System'}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </>
  );
}

ShowSubject.layout = (page) => {
  return (
    <AdminDashboardLayout showSidebar={true}>
      {page}
    </AdminDashboardLayout>
  )
}