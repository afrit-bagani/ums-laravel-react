import { useState, useEffect } from 'react';
import { Head, useForm, usePage, Link } from '@inertiajs/react';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/Components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Checkbox } from '@/Components/ui/checkbox';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/Components/ui/dialog';
import { FileUp } from 'lucide-react';
import { toast } from 'sonner';
import ErrorAlert from '@/Components/ErrorAlert';
import { useRoute } from 'ziggy-js';

export default function Apply({ programmes_with_courses, batches }) {
    const route = useRoute();

    const { flash } = usePage().props;
    const [activeTab, setActiveTab] = useState('profile');
    const [showSuccessDialog, setShowSuccessDialog] = useState(false);
    const [applicantCode, setApplicantCode] = useState('');

    const { data, setData, post, processing, errors, hasErrors, reset } = useForm({
        // Profile
        full_name: '', father_name: '', mother_name: '', gender: '', dob: '',
        abc_id: '', aadhaar_no: '', nationality: 'Indian', mobile_no: '', email: '',
        religion: '', caste: '', blood_group: '', marital_status: '', annual_family_income: '', parent_mobile_no: '',
        is_blind: false, is_bpl: false, is_minority: false, is_ph: false,
        // Address
        present_address: '', present_city: '', present_country: 'India', present_state: '', present_district: '', present_pincode: '',
        permanent_address: '', permanent_city: '', permanent_country: 'India', permanent_state: '', permanent_district: '', permanent_pincode: '',
        // Academic / Paper Selection
        admission_type: '', exam_name: '', board_name: '', institution_name: '', max_marks: '', marks_obtained: '', percentage: '',
        programme_id: '', course_id: '', batch_id: '',
        // Documents
        photo: null, signature: null,
        // Payment
        fee_type: 'Application Fee', amount: '', payment_method: '', transaction_id: '', payment_date: ''
    });

    const [availableCourses, setAvailableCourses] = useState([]);

    useEffect(() => {
        if (data.programme_id) {
            const prog = programmes_with_courses.find(p => p.programme_id == data.programme_id);
            setAvailableCourses(prog ? prog.courses : []);
            // reset course if it doesn't belong to the new programme
            if (!prog?.courses.find(c => c.course_id == data.course_id)) {
                setData('course_id', '');
            }
        } else {
            setAvailableCourses([]);
        }
    }, [data.programme_id, programmes_with_courses]);

    // Calculate percentage automatically
    useEffect(() => {
        if (data.max_marks && data.marks_obtained) {
            const max = parseFloat(data.max_marks);
            const obtained = parseFloat(data.marks_obtained);
            if (max > 0) {
                const percent = ((obtained / max) * 100).toFixed(2);
                setData('percentage', percent);
            }
        }
    }, [data.max_marks, data.marks_obtained]);

    useEffect(() => {
        if (flash.success && flash.applicant_code) {
            setApplicantCode(flash.applicant_code);
            setShowSuccessDialog(true);
            reset();
            setActiveTab('profile');
        }
    }, [flash]);

    useEffect(() => {
        if (hasErrors) {
            toast.error("Please fix the errors in the form before submitting.");
            // Determine which tab has errors and switch to it
            const profileErrors = ['full_name', 'father_name', 'mother_name', 'gender', 'dob', 'mobile_no', 'email', 'religion', 'caste', 'blood_group', 'marital_status', 'present_address', 'present_city', 'present_state', 'present_pincode', 'permanent_address', 'permanent_city', 'permanent_state', 'permanent_pincode'];
            const academicErrors = ['admission_type', 'exam_name', 'board_name', 'institution_name', 'max_marks', 'marks_obtained', 'percentage', 'programme_id', 'course_id', 'batch_id'];
            const documentErrors = ['photo', 'signature'];
            const paymentErrors = ['fee_type', 'amount', 'payment_method'];

            const errorKeys = Object.keys(errors);
            if (errorKeys.some(k => profileErrors.includes(k))) setActiveTab('profile');
            else if (errorKeys.some(k => academicErrors.includes(k))) setActiveTab('academic');
            else if (errorKeys.some(k => documentErrors.includes(k))) setActiveTab('documents');
            else if (errorKeys.some(k => paymentErrors.includes(k))) setActiveTab('payment');
        }
    }, [errors, hasErrors]);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('applicant.apply.store'));
    };

    const copyAddress = () => {
        setData(data => ({
            ...data,
            permanent_address: data.present_address,
            permanent_city: data.present_city,
            permanent_country: data.present_country,
            permanent_state: data.present_state,
            permanent_district: data.present_district,
            permanent_pincode: data.present_pincode,
        }));
    };

    return (
        <div className="min-h-screen bg-neutral-100 flex flex-col justify-center items-center py-10 px-4 sm:px-6 lg:px-8">
            <Head title="Applicant Registration" />

            <div className="max-w-5xl w-full">
                <div className="text-center mb-8">
                    <h1 className="text-3xl font-extrabold text-neutral-900 tracking-tight">University Application Portal</h1>
                    <p className="mt-2 text-neutral-600">Complete your application process in four simple steps.</p>
                </div>

                <form onSubmit={handleSubmit}>
                    <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
                        <TabsList className="grid w-full grid-cols-4 mb-8">
                            <TabsTrigger value="profile">1. Profile</TabsTrigger>
                            <TabsTrigger value="academic">2. Academic</TabsTrigger>
                            <TabsTrigger value="documents">3. Documents</TabsTrigger>
                            <TabsTrigger value="payment">4. Payment</TabsTrigger>
                        </TabsList>

                        {/* TAB 1: PROFILE */}
                        <TabsContent value="profile">
                            <Card className="shadow-lg border-0 ring-1 ring-neutral-200">
                                <CardHeader className="bg-white rounded-t-xl border-b border-neutral-100">
                                    <CardTitle className="text-xl">Personal Information</CardTitle>
                                    <CardDescription>Enter your basic details and address.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-8 p-6 bg-white">
                                    {/* Personal Details */}
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div className="space-y-2">
                                            <Label htmlFor="full_name">Full Name *</Label>
                                            <Input id="full_name" value={data.full_name} onChange={e => setData('full_name', e.target.value)} />
                                            {errors.full_name && <ErrorAlert title={errors.full_name} />}
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="father_name">Father's Name *</Label>
                                            <Input id="father_name" value={data.father_name} onChange={e => setData('father_name', e.target.value)} />
                                            {errors.father_name && <ErrorAlert title={errors.father_name} />}
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="mother_name">Mother's Name *</Label>
                                            <Input id="mother_name" value={data.mother_name} onChange={e => setData('mother_name', e.target.value)} />
                                            {errors.mother_name && <ErrorAlert title={errors.mother_name} />}
                                        </div>

                                        <div className="space-y-2">
                                            <Label>Gender *</Label>
                                            <Select value={data.gender} onValueChange={v => setData('gender', v)}>
                                                <SelectTrigger><SelectValue placeholder="Select Gender" /></SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="Male">Male</SelectItem>
                                                    <SelectItem value="Female">Female</SelectItem>
                                                    <SelectItem value="Other">Other</SelectItem>
                                                </SelectContent>
                                            </Select>
                                            {errors.gender && <ErrorAlert title={errors.gender} />}
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="dob">Date of Birth *</Label>
                                            <Input id="dob" type="date" value={data.dob} onChange={e => setData('dob', e.target.value)} />
                                            {errors.dob && <ErrorAlert title={errors.dob} />}
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="mobile_no">Mobile No *</Label>
                                            <Input id="mobile_no" maxLength="10" value={data.mobile_no} onChange={e => setData('mobile_no', e.target.value)} />
                                            {errors.mobile_no && <ErrorAlert title={errors.mobile_no} />}
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="email">Email Address *</Label>
                                            <Input id="email" type="email" value={data.email} onChange={e => setData('email', e.target.value)} />
                                            {errors.email && <ErrorAlert title={errors.email} />}
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="aadhaar_no">Aadhaar Number</Label>
                                            <Input id="aadhaar_no" maxLength="12" value={data.aadhaar_no} onChange={e => setData('aadhaar_no', e.target.value)} />
                                            {errors.aadhaar_no && <ErrorAlert title={errors.aadhaar_no} />}
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="abc_id">ABC ID</Label>
                                            <Input id="abc_id" maxLength="12" value={data.abc_id} onChange={e => setData('abc_id', e.target.value)} />
                                            {errors.abc_id && <ErrorAlert title={errors.abc_id} />}
                                        </div>

                                        <div className="space-y-2">
                                            <Label>Religion *</Label>
                                            <Select value={data.religion} onValueChange={v => setData('religion', v)}>
                                                <SelectTrigger><SelectValue placeholder="Select Religion" /></SelectTrigger>
                                                <SelectContent>
                                                    {['Hindu', 'Muslim', 'Christian', 'Sikh', 'Buddhist', 'Jain'].map(r => <SelectItem key={r} value={r}>{r}</SelectItem>)}
                                                </SelectContent>
                                            </Select>
                                            {errors.religion && <ErrorAlert title={errors.religion} />}
                                        </div>
                                        <div className="space-y-2">
                                            <Label>Caste Category *</Label>
                                            <Select value={data.caste} onValueChange={v => setData('caste', v)}>
                                                <SelectTrigger><SelectValue placeholder="Select Caste" /></SelectTrigger>
                                                <SelectContent>
                                                    {['General', 'SC', 'ST', 'OBC', 'EWS'].map(c => <SelectItem key={c} value={c}>{c}</SelectItem>)}
                                                </SelectContent>
                                            </Select>
                                            {errors.caste && <ErrorAlert title={errors.caste} />}
                                        </div>
                                        <div className="space-y-2">
                                            <Label>Blood Group *</Label>
                                            <Select value={data.blood_group} onValueChange={v => setData('blood_group', v)}>
                                                <SelectTrigger><SelectValue placeholder="Select Blood Group" /></SelectTrigger>
                                                <SelectContent>
                                                    {['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'].map(bg => <SelectItem key={bg} value={bg}>{bg}</SelectItem>)}
                                                </SelectContent>
                                            </Select>
                                            {errors.blood_group && <ErrorAlert title={errors.blood_group} />}
                                        </div>

                                        <div className="space-y-2">
                                            <Label>Marital Status *</Label>
                                            <Select value={data.marital_status} onValueChange={v => setData('marital_status', v)}>
                                                <SelectTrigger><SelectValue placeholder="Select Status" /></SelectTrigger>
                                                <SelectContent>
                                                    {['Single', 'Married', 'Divorced', 'Widowed'].map(s => <SelectItem key={s} value={s}>{s}</SelectItem>)}
                                                </SelectContent>
                                            </Select>
                                            {errors.marital_status && <ErrorAlert title={errors.marital_status} />}
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="annual_family_income">Annual Family Income</Label>
                                            <Input id="annual_family_income" type="number" value={data.annual_family_income} onChange={e => setData('annual_family_income', e.target.value)} />
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="parent_mobile_no">Parent Mobile No</Label>
                                            <Input id="parent_mobile_no" maxLength="10" value={data.parent_mobile_no} onChange={e => setData('parent_mobile_no', e.target.value)} />
                                        </div>
                                    </div>

                                    {/* Checkboxes */}
                                    <div className="flex flex-wrap gap-6 py-2 border-y border-neutral-100">
                                        <label className="flex items-center space-x-2 cursor-pointer"><Checkbox checked={data.is_blind} onCheckedChange={c => setData('is_blind', c)} /><span>Is Blind</span></label>
                                        <label className="flex items-center space-x-2 cursor-pointer"><Checkbox checked={data.is_bpl} onCheckedChange={c => setData('is_bpl', c)} /><span>Is BPL</span></label>
                                        <label className="flex items-center space-x-2 cursor-pointer"><Checkbox checked={data.is_minority} onCheckedChange={c => setData('is_minority', c)} /><span>Is Minority</span></label>
                                        <label className="flex items-center space-x-2 cursor-pointer"><Checkbox checked={data.is_ph} onCheckedChange={c => setData('is_ph', c)} /><span>Physically Handicapped</span></label>
                                    </div>

                                    {/* Addresses */}
                                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div className="space-y-4">
                                            <h3 className="text-lg font-semibold text-neutral-800">Present Address</h3>
                                            <div className="space-y-2">
                                                <Label>Address Line *</Label>
                                                <Input value={data.present_address} onChange={e => setData('present_address', e.target.value)} />
                                                {errors.present_address && <ErrorAlert title={errors.present_address} />}
                                            </div>
                                            <div className="grid grid-cols-2 gap-4">
                                                <div className="space-y-2">
                                                    <Label>City *</Label>
                                                    <Input value={data.present_city} onChange={e => setData('present_city', e.target.value)} />
                                                    {errors.present_city && <ErrorAlert title={errors.present_city} />}
                                                </div>
                                                <div className="space-y-2">
                                                    <Label>District *</Label>
                                                    <Input value={data.present_district} onChange={e => setData('present_district', e.target.value)} />
                                                    {errors.present_district && <ErrorAlert title={errors.present_district} />}
                                                </div>
                                                <div className="space-y-2">
                                                    <Label>State *</Label>
                                                    <Input value={data.present_state} onChange={e => setData('present_state', e.target.value)} />
                                                    {errors.present_state && <ErrorAlert title={errors.present_state} />}
                                                </div>
                                                <div className="space-y-2">
                                                    <Label>Pincode *</Label>
                                                    <Input maxLength="6" value={data.present_pincode} onChange={e => setData('present_pincode', e.target.value)} />
                                                    {errors.present_pincode && <ErrorAlert title={errors.present_pincode} />}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="space-y-4">
                                            <div className="flex justify-between items-center">
                                                <h3 className="text-lg font-semibold text-neutral-800">Permanent Address</h3>
                                                <Button type="button" variant="outline" size="sm" onClick={copyAddress}>Same as Present</Button>
                                            </div>
                                            <div className="space-y-2">
                                                <Label>Address Line *</Label>
                                                <Input value={data.permanent_address} onChange={e => setData('permanent_address', e.target.value)} />
                                                {errors.permanent_address && <ErrorAlert title={errors.permanent_address} />}
                                            </div>
                                            <div className="grid grid-cols-2 gap-4">
                                                <div className="space-y-2">
                                                    <Label>City *</Label>
                                                    <Input value={data.permanent_city} onChange={e => setData('permanent_city', e.target.value)} />
                                                    {errors.permanent_city && <ErrorAlert title={errors.permanent_city} />}
                                                </div>
                                                <div className="space-y-2">
                                                    <Label>District *</Label>
                                                    <Input value={data.permanent_district} onChange={e => setData('permanent_district', e.target.value)} />
                                                    {errors.permanent_district && <ErrorAlert title={errors.permanent_district} />}
                                                </div>
                                                <div className="space-y-2">
                                                    <Label>State *</Label>
                                                    <Input value={data.permanent_state} onChange={e => setData('permanent_state', e.target.value)} />
                                                    {errors.permanent_state && <ErrorAlert title={errors.permanent_state} />}
                                                </div>
                                                <div className="space-y-2">
                                                    <Label>Pincode *</Label>
                                                    <Input maxLength="6" value={data.permanent_pincode} onChange={e => setData('permanent_pincode', e.target.value)} />
                                                    {errors.permanent_pincode && <ErrorAlert title={errors.permanent_pincode} />}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                                <CardFooter className="bg-neutral-50 p-6 rounded-b-xl flex justify-end">
                                    <Button type="button" onClick={() => setActiveTab('academic')}>Next: Academic Details</Button>
                                </CardFooter>
                            </Card>
                        </TabsContent>

                        {/* TAB 2: ACADEMIC */}
                        <TabsContent value="academic">
                            <Card className="shadow-lg border-0 ring-1 ring-neutral-200">
                                <CardHeader className="bg-white rounded-t-xl border-b border-neutral-100">
                                    <CardTitle className="text-xl">Academic & Program Selection</CardTitle>
                                    <CardDescription>Enter your past academic record and select the program you are applying for.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-8 p-6 bg-white">
                                    <div className="space-y-4">
                                        <h3 className="text-lg font-semibold text-neutral-800 border-b pb-2">Past Qualification</h3>
                                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <div className="space-y-2">
                                                <Label>Admission Type *</Label>
                                                <Select value={data.admission_type} onValueChange={v => setData('admission_type', v)}>
                                                    <SelectTrigger><SelectValue placeholder="Select Type" /></SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="Regular">Regular</SelectItem>
                                                        <SelectItem value="Lateral Entry">Lateral Entry</SelectItem>
                                                        <SelectItem value="Transfer">Transfer</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                {errors.admission_type && <ErrorAlert title={errors.admission_type} />}
                                            </div>
                                            <div className="space-y-2">
                                                <Label>Exam Name *</Label>
                                                <Input value={data.exam_name} onChange={e => setData('exam_name', e.target.value)} placeholder="e.g. 12th Board, B.Sc" />
                                                {errors.exam_name && <ErrorAlert title={errors.exam_name} />}
                                            </div>
                                            <div className="space-y-2">
                                                <Label>Board / University *</Label>
                                                <Input value={data.board_name} onChange={e => setData('board_name', e.target.value)} />
                                                {errors.board_name && <ErrorAlert title={errors.board_name} />}
                                            </div>
                                            <div className="space-y-2">
                                                <Label>Institution Name *</Label>
                                                <Input value={data.institution_name} onChange={e => setData('institution_name', e.target.value)} />
                                                {errors.institution_name && <ErrorAlert title={errors.institution_name} />}
                                            </div>
                                            <div className="space-y-2">
                                                <Label>Max Marks *</Label>
                                                <Input type="number" value={data.max_marks} onChange={e => setData('max_marks', e.target.value)} />
                                                {errors.max_marks && <ErrorAlert title={errors.max_marks} />}
                                            </div>
                                            <div className="space-y-2">
                                                <Label>Marks Obtained *</Label>
                                                <Input type="number" value={data.marks_obtained} onChange={e => setData('marks_obtained', e.target.value)} />
                                                {errors.marks_obtained && <ErrorAlert title={errors.marks_obtained} />}
                                            </div>
                                            <div className="space-y-2">
                                                <Label>Percentage *</Label>
                                                <Input type="number" step="0.01" value={data.percentage} readOnly className="bg-neutral-50" />
                                                {errors.percentage && <ErrorAlert title={errors.percentage} />}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="space-y-4">
                                        <h3 className="text-lg font-semibold text-neutral-800 border-b pb-2">Program Selection</h3>
                                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <div className="space-y-2">
                                                <Label>Programme *</Label>
                                                <Select value={data.programme_id.toString()} onValueChange={v => setData('programme_id', v)}>
                                                    <SelectTrigger><SelectValue placeholder="Select Programme" /></SelectTrigger>
                                                    <SelectContent>
                                                        {programmes_with_courses.map(p => (
                                                            <SelectItem key={p.programme_id} value={p.programme_id.toString()}>{p.programme_name}</SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                                {errors.programme_id && <ErrorAlert title={errors.programme_id} />}
                                            </div>
                                            <div className="space-y-2">
                                                <Label>Course *</Label>
                                                <Select value={data.course_id.toString()} onValueChange={v => setData('course_id', v)} disabled={!data.programme_id}>
                                                    <SelectTrigger><SelectValue placeholder="Select Course" /></SelectTrigger>
                                                    <SelectContent>
                                                        {availableCourses.map(c => (
                                                            <SelectItem key={c.course_id} value={c.course_id.toString()}>{c.name}</SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                                {errors.course_id && <ErrorAlert title={errors.course_id} />}
                                            </div>
                                            <div className="space-y-2">
                                                <Label>Batch / Year *</Label>
                                                <Select value={data.batch_id.toString()} onValueChange={v => setData('batch_id', v)}>
                                                    <SelectTrigger><SelectValue placeholder="Select Batch" /></SelectTrigger>
                                                    <SelectContent>
                                                        {batches.map(b => (
                                                            <SelectItem key={b.batch_id} value={b.batch_id.toString()}>{b.batch_name}</SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                                {errors.batch_id && <ErrorAlert title={errors.batch_id} />}
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                                <CardFooter className="bg-neutral-50 p-6 rounded-b-xl flex justify-between">
                                    <Button type="button" variant="outline" onClick={() => setActiveTab('profile')}>Previous</Button>
                                    <Button type="button" onClick={() => setActiveTab('documents')}>Next: Documents</Button>
                                </CardFooter>
                            </Card>
                        </TabsContent>

                        {/* TAB 3: DOCUMENTS */}
                        <TabsContent value="documents">
                            <Card className="shadow-lg border-0 ring-1 ring-neutral-200">
                                <CardHeader className="bg-white rounded-t-xl border-b border-neutral-100">
                                    <CardTitle className="text-xl">Upload Documents</CardTitle>
                                    <CardDescription>Upload your passport size photograph and signature. Supported formats: JPG, PNG (Max 2MB).</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-8 p-6 bg-white flex flex-col md:flex-row gap-8">
                                    <div className="flex-1 space-y-4">
                                        <Label className="text-base font-semibold">Passport Photograph *</Label>
                                        <div className="border-2 border-dashed border-neutral-300 rounded-lg p-8 text-center bg-neutral-50 hover:bg-neutral-100 transition relative">
                                            <Input type="file" accept="image/jpeg, image/png" className="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onChange={e => setData('photo', e.target.files[0])} />
                                            <div className="flex flex-col items-center pointer-events-none">
                                                <FileUp className="w-10 h-10 text-neutral-400 mb-2" />
                                                <span className="text-sm text-neutral-600 font-medium">
                                                    {data.photo ? data.photo.name : "Click or drag file to upload"}
                                                </span>
                                            </div>
                                        </div>
                                        {errors.photo && <ErrorAlert title={errors.photo} />}
                                    </div>
                                    <div className="flex-1 space-y-4">
                                        <Label className="text-base font-semibold">Digital Signature *</Label>
                                        <div className="border-2 border-dashed border-neutral-300 rounded-lg p-8 text-center bg-neutral-50 hover:bg-neutral-100 transition relative">
                                            <Input type="file" accept="image/jpeg, image/png" className="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onChange={e => setData('signature', e.target.files[0])} />
                                            <div className="flex flex-col items-center pointer-events-none">
                                                <FileUp className="w-10 h-10 text-neutral-400 mb-2" />
                                                <span className="text-sm text-neutral-600 font-medium">
                                                    {data.signature ? data.signature.name : "Click or drag file to upload"}
                                                </span>
                                            </div>
                                        </div>
                                        {errors.signature && <ErrorAlert title={errors.signature} />}
                                    </div>
                                </CardContent>
                                <CardFooter className="bg-neutral-50 p-6 rounded-b-xl flex justify-between">
                                    <Button type="button" variant="outline" onClick={() => setActiveTab('academic')}>Previous</Button>
                                    <Button type="button" onClick={() => setActiveTab('payment')}>Next: Payment</Button>
                                </CardFooter>
                            </Card>
                        </TabsContent>

                        {/* TAB 4: PAYMENT */}
                        <TabsContent value="payment">
                            <Card className="shadow-lg border-0 ring-1 ring-neutral-200">
                                <CardHeader className="bg-white rounded-t-xl border-b border-neutral-100">
                                    <CardTitle className="text-xl">Payment Details</CardTitle>
                                    <CardDescription>Enter the application fee payment details to complete registration.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-6 p-6 bg-white">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div className="space-y-2">
                                            <Label>Fee Type *</Label>
                                            <Input value={data.fee_type} readOnly className="bg-neutral-50" />
                                        </div>
                                        <div className="space-y-2">
                                            <Label>Amount *</Label>
                                            <Input type="number" step="0.01" value={data.amount} onChange={e => setData('amount', e.target.value)} />
                                            {errors.amount && <ErrorAlert title={errors.amount} />}
                                        </div>
                                        <div className="space-y-2">
                                            <Label>Payment Method *</Label>
                                            <Select value={data.payment_method} onValueChange={v => setData('payment_method', v)}>
                                                <SelectTrigger><SelectValue placeholder="Select Method" /></SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="cash">Cash</SelectItem>
                                                    <SelectItem value="upi">UPI</SelectItem>
                                                    <SelectItem value="cheque">Cheque</SelectItem>
                                                    <SelectItem value="NEFT">NEFT</SelectItem>
                                                    <SelectItem value="RTGS">RTGS</SelectItem>
                                                </SelectContent>
                                            </Select>
                                            {errors.payment_method && <ErrorAlert title={errors.payment_method} />}
                                        </div>
                                        <div className="space-y-2">
                                            <Label>Transaction ID (If Online)</Label>
                                            <Input value={data.transaction_id} onChange={e => setData('transaction_id', e.target.value)} />
                                        </div>
                                        <div className="space-y-2">
                                            <Label>Payment Date</Label>
                                            <Input type="date" value={data.payment_date} onChange={e => setData('payment_date', e.target.value)} />
                                        </div>
                                    </div>
                                </CardContent>
                                <CardFooter className="bg-neutral-50 p-6 rounded-b-xl flex justify-between items-center">
                                    <Button type="button" variant="outline" onClick={() => setActiveTab('documents')}>Previous</Button>
                                    <Button type="submit" disabled={processing} size="lg" className="w-1/3">
                                        {processing ? 'Submitting...' : 'Submit Application'}
                                    </Button>
                                </CardFooter>
                            </Card>
                        </TabsContent>
                    </Tabs>
                </form>
            </div>

            {/* Success Dialog */}
            <Dialog open={showSuccessDialog} onOpenChange={setShowSuccessDialog}>
                <DialogContent className="sm:max-w-md text-center">
                    <DialogHeader>
                        <DialogTitle className="text-2xl font-bold text-green-600">Application Submitted!</DialogTitle>
                        <DialogDescription className="text-base pt-2">
                            Your application has been successfully recorded in our system.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="bg-neutral-100 p-6 rounded-lg my-4">
                        <p className="text-sm text-neutral-500 mb-2">Your Unique Applicant Code</p>
                        <p className="text-3xl font-mono font-bold tracking-wider text-neutral-900">{applicantCode}</p>
                    </div>
                    <p className="text-sm text-red-500 font-semibold mb-4">
                        Please save this code! You will need it for future references.
                    </p>
                    <DialogFooter className="sm:justify-center">
                        <Button type="button" onClick={() => setShowSuccessDialog(false)}>
                            I have saved my code
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}
