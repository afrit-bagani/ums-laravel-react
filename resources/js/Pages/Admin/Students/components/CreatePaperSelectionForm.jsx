import React, { useState, useEffect } from 'react';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import ErrorAlert from '@/components/ErrorAlert';

export default function CreatePaperSelectionForm({ data, setData, errors, onNext, programmes_with_courses = [], batches = [], processing = false, buttonLabel = "Next Step" }) {
  const [availableCourses, setAvailableCourses] = useState([]);

  useEffect(() => {
    if (data.programme_id) {
      const selectedProgramme = programmes_with_courses.find(
        (p) => p.programme_id.toString() === data.programme_id.toString()
      );
      setAvailableCourses(selectedProgramme ? selectedProgramme.courses : []);
      if (selectedProgramme && data.course_id) {
          const isValid = selectedProgramme.courses.some(c => c.course_id.toString() === data.course_id.toString());
          if (!isValid) setData('course_id', '');
      }
    } else {
      setAvailableCourses([]);
    }
  }, [data.programme_id, programmes_with_courses]);

  return (
    <div className="space-y-8">
      <div>
        <h3 className="text-lg font-semibold text-indigo-950 mb-4 border-b pb-2">Academic Placement</h3>
        <p className="text-sm text-gray-500 mb-6">Select the programme, course, and batch for the student's enrollment.</p>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="space-y-2">
            <Label>Programme <span className="text-red-500">*</span></Label>
            <Select value={data.programme_id} onValueChange={(val) => setData('programme_id', val)}>
              <SelectTrigger><SelectValue placeholder="Select Programme" /></SelectTrigger>
              <SelectContent>
                {programmes_with_courses.map((prog) => (
                  <SelectItem key={prog.programme_id} value={prog.programme_id.toString()}>
                    {prog.programme_name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.programme_id && <ErrorAlert title={errors.programme_id} />}
          </div>

          <div className="space-y-2">
            <Label>Course <span className="text-red-500">*</span></Label>
            <Select value={data.course_id} onValueChange={(val) => setData('course_id', val)} disabled={!data.programme_id || availableCourses.length === 0}>
              <SelectTrigger><SelectValue placeholder="Select Course" /></SelectTrigger>
              <SelectContent>
                {availableCourses.map((course) => (
                  <SelectItem key={course.course_id} value={course.course_id.toString()}>
                    {course.course_name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.course_id && <ErrorAlert title={errors.course_id} />}
          </div>

          <div className="space-y-2">
            <Label>Batch <span className="text-red-500">*</span></Label>
            <Select value={data.batch_id} onValueChange={(val) => setData('batch_id', val)}>
              <SelectTrigger><SelectValue placeholder="Select Batch" /></SelectTrigger>
              <SelectContent>
                {batches.map((batch) => (
                  <SelectItem key={batch.id} value={batch.id.toString()}>
                    {batch.name} ({batch.code})
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.batch_id && <ErrorAlert title={errors.batch_id} />}
          </div>
        </div>
      </div>

      <div className="flex justify-end pt-4 border-t">
        <Button 
          type="button" 
          onClick={onNext} 
          disabled={processing}
          size="lg" 
          className="px-8 shadow-md"
        >
          {processing ? 'Processing...' : buttonLabel}
        </Button>
      </div>
    </div>
  );
}
