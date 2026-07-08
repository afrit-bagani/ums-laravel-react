import React, { useState, useEffect } from 'react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import ErrorAlert from '@/components/ErrorAlert';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

export default function CreateBasicInfoForm({ data, setData, errors, onNext, buttonLabel = "Next Step", processing = false }) {
  const [sameAsPresent, setSameAsPresent] = useState(false);
  // Auto-fill percentage when marks change
  useEffect(() => {
    if (data.max_marks && data.marks_obtained) {
      const max = parseFloat(data.max_marks);
      const obtained = parseFloat(data.marks_obtained);
      if (max > 0) {
        const pct = ((obtained / max) * 100).toFixed(2);
        setData('percentage', pct);
      }
    }
  }, [data.max_marks, data.marks_obtained]);

  const handleSameAddressChange = (checked) => {
    setSameAsPresent(checked);
    if (checked) {
      setData({
        ...data,
        permanent_address: data.present_address,
        permanent_city: data.present_city,
        permanent_district: data.present_district,
        permanent_state: data.present_state,
        permanent_country: data.present_country,
        permanent_pincode: data.present_pincode,
      });
    }
  };

  return (
    <div className="space-y-8">
      {/* 1. Personal Information */}
      <div>
        <h3 className="text-lg font-semibold text-indigo-950 mb-4 border-b pb-2">Personal Information</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div className="space-y-2">
            <Label htmlFor="full_name">Full Name <span className="text-red-500">*</span></Label>
            <Input id="full_name" value={data.full_name} onChange={e => setData('full_name', e.target.value)} />
            {errors.full_name && <ErrorAlert title={errors.full_name} />}
          </div>

          <div className="space-y-2">
            <Label htmlFor="email">Email Address <span className="text-red-500">*</span></Label>
            <Input type="email" id="email" value={data.email} onChange={e => setData('email', e.target.value)} />
            {errors.email && <ErrorAlert title={errors.email} />}
          </div>

          <div className="space-y-2">
            <Label htmlFor="mobile_no">Mobile Number <span className="text-red-500">*</span></Label>
            <Input id="mobile_no" maxLength={10} value={data.mobile_no} onChange={e => setData('mobile_no', e.target.value)} />
            {errors.mobile_no && <ErrorAlert title={errors.mobile_no} />}
          </div>

          <div className="space-y-2">
            <Label htmlFor="dob">Date of Birth <span className="text-red-500">*</span></Label>
            <Input type="date" id="dob" value={data.dob} onChange={e => setData('dob', e.target.value)} />
            {errors.dob && <ErrorAlert title={errors.dob} />}
          </div>

          <div className="space-y-2">
            <Label>Gender <span className="text-red-500">*</span></Label>
            <Select value={data.gender} onValueChange={(val) => setData('gender', val)}>
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
            <Label>Blood Group <span className="text-red-500">*</span></Label>
            <Select value={data.blood_group} onValueChange={(val) => setData('blood_group', val)}>
              <SelectTrigger><SelectValue placeholder="Select Blood Group" /></SelectTrigger>
              <SelectContent>
                {['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'].map(bg => (
                  <SelectItem key={bg} value={bg}>{bg}</SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.blood_group && <ErrorAlert title={errors.blood_group} />}
          </div>

          <div className="space-y-2">
            <Label>Religion <span className="text-red-500">*</span></Label>
            <Select value={data.religion} onValueChange={(val) => setData('religion', val)}>
              <SelectTrigger><SelectValue placeholder="Select Religion" /></SelectTrigger>
              <SelectContent>
                {['Hindu', 'Muslim', 'Christian', 'Sikh', 'Buddhist', 'Jain'].map(r => (
                  <SelectItem key={r} value={r}>{r}</SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.religion && <ErrorAlert title={errors.religion} />}
          </div>

          <div className="space-y-2">
            <Label>Caste <span className="text-red-500">*</span></Label>
            <Select value={data.caste} onValueChange={(val) => setData('caste', val)}>
              <SelectTrigger><SelectValue placeholder="Select Caste" /></SelectTrigger>
              <SelectContent>
                {['General', 'SC', 'ST', 'OBC', 'EWS'].map(c => (
                  <SelectItem key={c} value={c}>{c}</SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.caste && <ErrorAlert title={errors.caste} />}
          </div>

          <div className="space-y-2">
            <Label>Marital Status <span className="text-red-500">*</span></Label>
            <Select value={data.marital_status} onValueChange={(val) => setData('marital_status', val)}>
              <SelectTrigger><SelectValue placeholder="Select Status" /></SelectTrigger>
              <SelectContent>
                {['Single', 'Married', 'Divorced', 'Widowed'].map(s => (
                  <SelectItem key={s} value={s}>{s}</SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.marital_status && <ErrorAlert title={errors.marital_status} />}
          </div>

          <div className="space-y-2">
            <Label htmlFor="nationality">Nationality <span className="text-red-500">*</span></Label>
            <Input id="nationality" value={data.nationality} onChange={e => setData('nationality', e.target.value)} />
            {errors.nationality && <ErrorAlert title={errors.nationality} />}
          </div>

          <div className="space-y-2">
            <Label htmlFor="aadhaar_no">Aadhaar Number</Label>
            <Input id="aadhaar_no" maxLength={12} value={data.aadhaar_no} onChange={e => setData('aadhaar_no', e.target.value)} />
            {errors.aadhaar_no && <ErrorAlert title={errors.aadhaar_no} />}
          </div>

          <div className="space-y-2">
            <Label htmlFor="abc_id">ABC ID</Label>
            <Input id="abc_id" maxLength={12} value={data.abc_id} onChange={e => setData('abc_id', e.target.value)} />
            {errors.abc_id && <ErrorAlert title={errors.abc_id} />}
          </div>
        </div>
      </div>

      {/* 2. Family Information */}
      <div>
        <h3 className="text-lg font-semibold text-indigo-950 mb-4 border-b pb-2">Family Information</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div className="space-y-2">
            <Label htmlFor="father_name">Father's Name <span className="text-red-500">*</span></Label>
            <Input id="father_name" value={data.father_name} onChange={e => setData('father_name', e.target.value)} />
            {errors.father_name && <ErrorAlert title={errors.father_name} />}
          </div>
          <div className="space-y-2">
            <Label htmlFor="mother_name">Mother's Name <span className="text-red-500">*</span></Label>
            <Input id="mother_name" value={data.mother_name} onChange={e => setData('mother_name', e.target.value)} />
            {errors.mother_name && <ErrorAlert title={errors.mother_name} />}
          </div>
          <div className="space-y-2">
            <Label htmlFor="parent_mobile_no">Parent Mobile Number</Label>
            <Input id="parent_mobile_no" maxLength={10} value={data.parent_mobile_no} onChange={e => setData('parent_mobile_no', e.target.value)} />
            {errors.parent_mobile_no && <ErrorAlert title={errors.parent_mobile_no} />}
          </div>
          <div className="space-y-2">
            <Label htmlFor="annual_family_income">Annual Family Income</Label>
            <Input id="annual_family_income" type="number" value={data.annual_family_income} onChange={e => setData('annual_family_income', e.target.value)} />
            {errors.annual_family_income && <ErrorAlert title={errors.annual_family_income} />}
          </div>
        </div>
      </div>

      {/* 3. Special Categories */}
      <div>
        <h3 className="text-lg font-semibold text-indigo-950 mb-4 border-b pb-2">Special Categories</h3>
        <div className="flex flex-wrap gap-8">
          <div className="flex items-center space-x-2">
            <Checkbox id="is_blind" checked={data.is_blind} onCheckedChange={(c) => setData('is_blind', c)} />
            <Label htmlFor="is_blind" className="font-normal cursor-pointer">Visually Impaired (Blind)</Label>
          </div>
          <div className="flex items-center space-x-2">
            <Checkbox id="is_bpl" checked={data.is_bpl} onCheckedChange={(c) => setData('is_bpl', c)} />
            <Label htmlFor="is_bpl" className="font-normal cursor-pointer">Below Poverty Line (BPL)</Label>
          </div>
          <div className="flex items-center space-x-2">
            <Checkbox id="is_minority" checked={data.is_minority} onCheckedChange={(c) => setData('is_minority', c)} />
            <Label htmlFor="is_minority" className="font-normal cursor-pointer">Minority Community</Label>
          </div>
          <div className="flex items-center space-x-2">
            <Checkbox id="is_ph" checked={data.is_ph} onCheckedChange={(c) => setData('is_ph', c)} />
            <Label htmlFor="is_ph" className="font-normal cursor-pointer">Physically Handicapped (PH)</Label>
          </div>
        </div>
      </div>

      {/* 4. Address Information */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Present Address */}
        <div className="bg-gray-50/50 p-6 rounded-xl border border-gray-100">
          <h3 className="text-lg font-semibold text-indigo-950 mb-4">Present Address</h3>
          <div className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="present_address">Full Address Line <span className="text-red-500">*</span></Label>
              <Input id="present_address" value={data.present_address} onChange={e => setData('present_address', e.target.value)} />
              {errors.present_address && <ErrorAlert title={errors.present_address} />}
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="present_city">City <span className="text-red-500">*</span></Label>
                <Input id="present_city" value={data.present_city} onChange={e => setData('present_city', e.target.value)} />
                {errors.present_city && <ErrorAlert title={errors.present_city} />}
              </div>
              <div className="space-y-2">
                <Label htmlFor="present_district">District <span className="text-red-500">*</span></Label>
                <Input id="present_district" value={data.present_district} onChange={e => setData('present_district', e.target.value)} />
                {errors.present_district && <ErrorAlert title={errors.present_district} />}
              </div>
            </div>
            <div className="grid grid-cols-3 gap-4">
              <div className="space-y-2">
                <Label htmlFor="present_state">State <span className="text-red-500">*</span></Label>
                <Input id="present_state" value={data.present_state} onChange={e => setData('present_state', e.target.value)} />
                {errors.present_state && <ErrorAlert title={errors.present_state} />}
              </div>
              <div className="space-y-2">
                <Label htmlFor="present_country">Country <span className="text-red-500">*</span></Label>
                <Input id="present_country" value={data.present_country} onChange={e => setData('present_country', e.target.value)} />
                {errors.present_country && <ErrorAlert title={errors.present_country} />}
              </div>
              <div className="space-y-2">
                <Label htmlFor="present_pincode">Pincode <span className="text-red-500">*</span></Label>
                <Input id="present_pincode" maxLength={6} value={data.present_pincode} onChange={e => setData('present_pincode', e.target.value)} />
                {errors.present_pincode && <ErrorAlert title={errors.present_pincode} />}
              </div>
            </div>
          </div>
        </div>

        {/* Permanent Address */}
        <div className="bg-indigo-50/30 p-6 rounded-xl border border-indigo-100/50 relative">
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-lg font-semibold text-indigo-950">Permanent Address</h3>
            <div className="flex items-center space-x-2">
              <Checkbox id="same_address" checked={sameAsPresent} onCheckedChange={handleSameAddressChange} />
              <Label htmlFor="same_address" className="font-normal cursor-pointer text-indigo-700">Same as Present</Label>
            </div>
          </div>
          <div className={`space-y-4 ${sameAsPresent ? 'opacity-60 pointer-events-none' : ''}`}>
            <div className="space-y-2">
              <Label htmlFor="permanent_address">Full Address Line <span className="text-red-500">*</span></Label>
              <Input id="permanent_address" value={data.permanent_address} onChange={e => setData('permanent_address', e.target.value)} />
              {errors.permanent_address && <ErrorAlert title={errors.permanent_address} />}
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="permanent_city">City <span className="text-red-500">*</span></Label>
                <Input id="permanent_city" value={data.permanent_city} onChange={e => setData('permanent_city', e.target.value)} />
                {errors.permanent_city && <ErrorAlert title={errors.permanent_city} />}
              </div>
              <div className="space-y-2">
                <Label htmlFor="permanent_district">District <span className="text-red-500">*</span></Label>
                <Input id="permanent_district" value={data.permanent_district} onChange={e => setData('permanent_district', e.target.value)} />
                {errors.permanent_district && <ErrorAlert title={errors.permanent_district} />}
              </div>
            </div>
            <div className="grid grid-cols-3 gap-4">
              <div className="space-y-2">
                <Label htmlFor="permanent_state">State <span className="text-red-500">*</span></Label>
                <Input id="permanent_state" value={data.permanent_state} onChange={e => setData('permanent_state', e.target.value)} />
                {errors.permanent_state && <ErrorAlert title={errors.permanent_state} />}
              </div>
              <div className="space-y-2">
                <Label htmlFor="permanent_country">Country <span className="text-red-500">*</span></Label>
                <Input id="permanent_country" value={data.permanent_country} onChange={e => setData('permanent_country', e.target.value)} />
                {errors.permanent_country && <ErrorAlert title={errors.permanent_country} />}
              </div>
              <div className="space-y-2">
                <Label htmlFor="permanent_pincode">Pincode <span className="text-red-500">*</span></Label>
                <Input id="permanent_pincode" maxLength={6} value={data.permanent_pincode} onChange={e => setData('permanent_pincode', e.target.value)} />
                {errors.permanent_pincode && <ErrorAlert title={errors.permanent_pincode} />}
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* 5. Academic Information (Last Exam Passed) */}
      <div>
        <h3 className="text-lg font-semibold text-indigo-950 mb-4 border-b pb-2">Previous Academic Details</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div className="space-y-2">
            <Label>Admission Type <span className="text-red-500">*</span></Label>
            <Select value={data.admission_type} onValueChange={(val) => setData('admission_type', val)}>
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
            <Label htmlFor="exam_name">Last Exam Name <span className="text-red-500">*</span></Label>
            <Input id="exam_name" placeholder="e.g. 12th Grade, B.Sc" value={data.exam_name} onChange={e => setData('exam_name', e.target.value)} />
            {errors.exam_name && <ErrorAlert title={errors.exam_name} />}
          </div>
          <div className="space-y-2">
            <Label htmlFor="board_name">Board / University <span className="text-red-500">*</span></Label>
            <Input id="board_name" placeholder="e.g. CBSE, State Board" value={data.board_name} onChange={e => setData('board_name', e.target.value)} />
            {errors.board_name && <ErrorAlert title={errors.board_name} />}
          </div>
          <div className="space-y-2">
            <Label htmlFor="institution_name">Institution Name <span className="text-red-500">*</span></Label>
            <Input id="institution_name" value={data.institution_name} onChange={e => setData('institution_name', e.target.value)} />
            {errors.institution_name && <ErrorAlert title={errors.institution_name} />}
          </div>
          <div className="space-y-2">
            <Label htmlFor="max_marks">Max Marks <span className="text-red-500">*</span></Label>
            <Input id="max_marks" type="number" step="0.01" value={data.max_marks} onChange={e => setData('max_marks', e.target.value)} />
            {errors.max_marks && <ErrorAlert title={errors.max_marks} />}
          </div>
          <div className="space-y-2">
            <Label htmlFor="marks_obtained">Marks Obtained <span className="text-red-500">*</span></Label>
            <Input id="marks_obtained" type="number" step="0.01" value={data.marks_obtained} onChange={e => setData('marks_obtained', e.target.value)} />
            {errors.marks_obtained && <ErrorAlert title={errors.marks_obtained} />}
          </div>
          <div className="space-y-2">
            <Label htmlFor="percentage">Percentage <span className="text-red-500">*</span></Label>
            <Input id="percentage" readOnly className="bg-gray-50 font-semibold" value={data.percentage} />
          </div>
        </div>
      </div>

      <div className="flex justify-end pt-4 border-t border-gray-100">
        <Button 
          type="button"
          onClick={onNext}
          disabled={processing}
          className="px-8 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg h-10"
        >
          {processing ? 'Processing...' : buttonLabel}
        </Button>
      </div>
    </div>
  );
}
