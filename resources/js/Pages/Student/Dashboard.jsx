import React from 'react';
import { Head, router } from '@inertiajs/react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { User, GraduationCap, FileText, CreditCard, LogOut } from 'lucide-react';
import { Button } from "@/components/ui/button";
import { useRoute } from 'ziggy-js';

function DetailItem({ label, value }) {
    return (
        <div className="flex flex-col space-y-1">
            <span className="text-sm font-medium text-gray-500">{label}</span>
            <span className="text-base font-semibold text-gray-900">{value || 'N/A'}</span>
        </div>
    );
}

export default function Dashboard({ student }) {
    const route = useRoute();

    const [activeTab, setActiveTab] = React.useState("basic-info");

    const handleLogout = () => {
        router.post(route('student.logout'));
    };

    return (
        <div className="min-h-screen bg-gray-50">
            <Head title={`Dashboard - ${student.full_name}`} />

            {/* Header */}
            <header className="bg-white shadow-sm sticky top-0 z-10">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <div className="flex items-center gap-2">
                        <GraduationCap className="w-8 h-8 text-indigo-600" />
                        <h1 className="text-xl font-bold text-gray-900">Student Portal</h1>
                    </div>
                    <Button variant="ghost" className="text-gray-500 hover:text-red-600 hover:bg-red-50" onClick={handleLogout}>
                        <LogOut className="w-4 h-4 mr-2" /> Logout
                    </Button>
                </div>
            </header>

            <main className="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
                <div className="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 className="text-3xl font-bold tracking-tight text-gray-900">Welcome, {student.full_name}!</h2>
                        <p className="text-gray-500 mt-1">
                            Registration No: <span className="font-semibold text-indigo-600">{student.registration_number}</span>
                        </p>
                    </div>
                    <div>
                        <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${student.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                            {student.status === 'active' ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                </div>

                <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
                    <TabsList className="grid w-full grid-cols-4 mb-8 bg-gray-100/80 p-1 rounded-xl">
                        <TabsTrigger value="basic-info" className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-indigo-600 data-[state=active]:shadow-sm transition-all flex items-center gap-2">
                            <User className="w-4 h-4" /> Basic Info
                        </TabsTrigger>
                        <TabsTrigger value="academic" className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-indigo-600 data-[state=active]:shadow-sm transition-all flex items-center gap-2">
                            <GraduationCap className="w-4 h-4" /> Academic
                        </TabsTrigger>
                        <TabsTrigger value="documents" className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-indigo-600 data-[state=active]:shadow-sm transition-all flex items-center gap-2">
                            <FileText className="w-4 h-4" /> Documents
                        </TabsTrigger>
                        <TabsTrigger value="payment" className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-indigo-600 data-[state=active]:shadow-sm transition-all flex items-center gap-2">
                            <CreditCard className="w-4 h-4" /> Payment
                        </TabsTrigger>
                    </TabsList>

                    <TabsContent value="basic-info" className="mt-0 outline-none">
                        <Card className="border-0 shadow-xl ring-1 ring-gray-100 bg-white/60 backdrop-blur-md rounded-2xl overflow-hidden">
                            <CardHeader className="bg-white/40 border-b border-gray-100 pb-6">
                                <CardTitle className="text-2xl text-indigo-950">Basic Information</CardTitle>
                                <CardDescription className="text-gray-500">Your personal and demographic details.</CardDescription>
                            </CardHeader>
                            <CardContent className="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <DetailItem label="Full Name" value={student.full_name} />
                                <DetailItem label="Father's Name" value={student.father_name} />
                                <DetailItem label="Mother's Name" value={student.mother_name} />
                                <DetailItem label="Gender" value={student.gender} />
                                <DetailItem label="Date of Birth" value={student.dob} />
                                <DetailItem label="Nationality" value={student.nationality} />
                                <DetailItem label="Religion" value={student.religion} />
                                <DetailItem label="Caste" value={student.caste} />
                                <DetailItem label="Blood Group" value={student.blood_group} />
                                <DetailItem label="Marital Status" value={student.marital_status} />
                                <DetailItem label="Annual Family Income" value={`₹${student.annual_family_income}`} />

                                <div className="md:col-span-3 border-t pt-4 mt-2">
                                    <h4 className="font-semibold text-indigo-950 mb-4">Contact Information</h4>
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <DetailItem label="Mobile Number" value={student.mobile_no} />
                                        <DetailItem label="Email Address" value={student.email} />
                                        <DetailItem label="Parent's Mobile" value={student.parent_mobile_no} />
                                    </div>
                                </div>

                                <div className="md:col-span-3 border-t pt-4 mt-2">
                                    <h4 className="font-semibold text-indigo-950 mb-4">Identification</h4>
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <DetailItem label="ABC ID" value={student.abc_id} />
                                        <DetailItem label="Aadhaar Number" value={student.aadhaar_no} />
                                    </div>
                                </div>

                                <div className="md:col-span-3 border-t pt-4 mt-2">
                                    <h4 className="font-semibold text-indigo-950 mb-4">Address Details</h4>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-xl">
                                        <div>
                                            <h5 className="font-medium text-gray-700 mb-2 border-b pb-1">Present Address</h5>
                                            <div className="space-y-2">
                                                <DetailItem label="Address" value={student.present_address} />
                                                <DetailItem label="City & District" value={`${student.present_city}, ${student.present_district}`} />
                                                <DetailItem label="State & Pincode" value={`${student.present_state} - ${student.present_pincode}`} />
                                                <DetailItem label="Country" value={student.present_country} />
                                            </div>
                                        </div>
                                        <div>
                                            <h5 className="font-medium text-gray-700 mb-2 border-b pb-1">Permanent Address</h5>
                                            <div className="space-y-2">
                                                <DetailItem label="Address" value={student.permanent_address} />
                                                <DetailItem label="City & District" value={`${student.permanent_city}, ${student.permanent_district}`} />
                                                <DetailItem label="State & Pincode" value={`${student.permanent_state} - ${student.permanent_pincode}`} />
                                                <DetailItem label="Country" value={student.permanent_country} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <TabsContent value="academic" className="mt-0 outline-none">
                        <Card className="border-0 shadow-xl ring-1 ring-gray-100 bg-white/60 backdrop-blur-md rounded-2xl overflow-hidden">
                            <CardHeader className="bg-white/40 border-b border-gray-100 pb-6">
                                <CardTitle className="text-2xl text-indigo-950">Academic Placement & History</CardTitle>
                                <CardDescription className="text-gray-500">Your current enrollment and past qualifications.</CardDescription>
                            </CardHeader>
                            <CardContent className="p-6">
                                <div className="mb-6 pb-6 border-b">
                                    <h4 className="font-semibold text-indigo-950 mb-4 text-lg">Current Placement</h4>
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                                        <DetailItem label="Programme" value={student.programme_name} />
                                        <DetailItem label="Course" value={student.course_name} />
                                        <DetailItem label="Batch" value={student.batch_name} />
                                    </div>
                                </div>

                                <div>
                                    <h4 className="font-semibold text-indigo-950 mb-4 text-lg">Previous Academic Record</h4>
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <DetailItem label="Admission Type" value={student.admission_type} />
                                        <DetailItem label="Exam Name" value={student.exam_name} />
                                        <DetailItem label="Board/University" value={student.board_name} />
                                        <DetailItem label="Institution Name" value={student.institution_name} />
                                        <DetailItem label="Marks Obtained" value={student.marks_obtained} />
                                        <DetailItem label="Max Marks" value={student.max_marks} />
                                        <DetailItem label="Percentage" value={`${student.percentage}%`} />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <TabsContent value="documents" className="mt-0 outline-none">
                        <Card className="border-0 shadow-xl ring-1 ring-gray-100 bg-white/60 backdrop-blur-md rounded-2xl overflow-hidden">
                            <CardHeader className="bg-white/40 border-b border-gray-100 pb-6">
                                <CardTitle className="text-2xl text-indigo-950">Uploaded Documents</CardTitle>
                                <CardDescription className="text-gray-500">Your submitted documents.</CardDescription>
                            </CardHeader>
                            <CardContent className="p-6">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div className="border border-gray-200 rounded-xl p-6 flex flex-col items-center">
                                        <h4 className="font-semibold text-gray-700 mb-4">Passport Size Photo</h4>
                                        {student.photo ? (
                                            <img src={`/storage/${student.photo}`} alt="Student Photo" className="w-40 h-40 object-cover rounded-lg shadow-md border border-gray-200" />
                                        ) : (
                                            <div className="w-40 h-40 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">No Photo</div>
                                        )}
                                    </div>
                                    <div className="border border-gray-200 rounded-xl p-6 flex flex-col items-center">
                                        <h4 className="font-semibold text-gray-700 mb-4">Digital Signature</h4>
                                        {student.signature ? (
                                            <img src={`/storage/${student.signature}`} alt="Student Signature" className="w-64 h-24 object-contain rounded-lg shadow-md border border-gray-200 bg-white" />
                                        ) : (
                                            <div className="w-64 h-24 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">No Signature</div>
                                        )}
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <TabsContent value="payment" className="mt-0 outline-none">
                        <Card className="border-0 shadow-xl ring-1 ring-gray-100 bg-white/60 backdrop-blur-md rounded-2xl overflow-hidden">
                            <CardHeader className="bg-white/40 border-b border-gray-100 pb-6">
                                <CardTitle className="text-2xl text-indigo-950">Payment Details</CardTitle>
                                <CardDescription className="text-gray-500">Information about your registration/admission payment.</CardDescription>
                            </CardHeader>
                            <CardContent className="p-6">
                                {student.fee_type ? (
                                    <>
                                        <div className="flex justify-between items-center bg-green-50 p-6 rounded-xl border border-green-100 mb-6">
                                            <div>
                                                <h3 className="text-lg font-semibold text-green-900">Payment Successful</h3>
                                                <p className="text-sm text-green-700">Your registration fee has been successfully processed.</p>
                                            </div>
                                            <a 
                                                href={route('student.payment.receipt')} 
                                                target="_blank" 
                                                rel="noopener noreferrer"
                                                className="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg shadow-sm font-medium transition-colors flex items-center gap-2"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                Download Receipt
                                            </a>
                                        </div>
                                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 bg-gray-50/50 p-6 rounded-xl border border-gray-100">
                                            <DetailItem label="Fee Type" value={student.fee_type} />
                                            <DetailItem label="Amount Paid" value={`₹${student.amount}`} />
                                            <DetailItem label="Payment Method" value={student.payment_method?.toUpperCase()} />
                                            <DetailItem label="Transaction ID" value={student.transaction_id} />
                                            <DetailItem label="Payment Date" value={student.payment_date ? new Date(student.payment_date).toLocaleDateString() : 'N/A'} />
                                        </div>
                                    </>
                                ) : (
                                    <div className="text-center p-8 text-gray-500">
                                        No payment records found.
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </TabsContent>

                </Tabs>
            </main>
        </div>
    );
}
