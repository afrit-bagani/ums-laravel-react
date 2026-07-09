import React, { useState } from 'react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import ErrorAlert from '@/components/ErrorAlert';
import { UploadCloud, FileImage } from 'lucide-react';

export default function CreateDocumentForm({ data, setData, errors, onNext, processing = false, buttonLabel = "Next Step" }) {
  const [photoPreview, setPhotoPreview] = useState(null);
  const [signaturePreview, setSignaturePreview] = useState(null);

  // We should initialize previews if data.photo or data.signature are strings (existing paths from backend)
  // Wait, if it's Edit mode, the parent component might pass existing URLs to preview?
  // We can just add useEffect to set initial previews if they exist.
  React.useEffect(() => {
    if (typeof data.photo === 'string') {
      setPhotoPreview(`/storage/${data.photo}`);
    }
    if (typeof data.signature === 'string') {
      setSignaturePreview(`/storage/${data.signature}`);
    }
  }, []);

  const handleFileChange = (e, field) => {
    const file = e.target.files[0];
    if (file) {
      setData(field, file);
      const reader = new FileReader();
      reader.onloadend = () => {
        if (field === 'photo') setPhotoPreview(reader.result);
        if (field === 'signature') setSignaturePreview(reader.result);
      };
      reader.readAsDataURL(file);
    }
  };

  return (
    <div className="space-y-8">
      <div>
        <h3 className="text-lg font-semibold text-indigo-950 mb-4 border-b pb-2">Mandatory Documents</h3>
        <p className="text-sm text-gray-500 mb-6">Upload the student's recent passport size photograph and digital signature.</p>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">

          <div className="border-2 border-dashed border-gray-200 rounded-xl p-6 hover:bg-gray-50/50 transition-colors">
            <Label className="text-base font-medium mb-4 block">Passport Size Photo <span className="text-red-500">*</span></Label>
            <div className="flex flex-col items-center justify-center space-y-4">
              {photoPreview ? (
                <div className="relative">
                  <img src={photoPreview} alt="Photo Preview" className="w-32 h-32 object-cover rounded-lg shadow-sm border border-gray-200" />
                  <Button type="button" variant="destructive" size="sm" className="absolute -top-2 -right-2 h-6 w-6 rounded-full p-0" onClick={() => { setData('photo', null); setPhotoPreview(null); }}>×</Button>
                </div>
              ) : (
                <div className="w-32 h-32 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-300">
                  <FileImage className="w-10 h-10" />
                </div>
              )}

              <div className="w-full relative">
                <Input type="file" accept="image/jpeg,image/png,image/jpg" onChange={(e) => handleFileChange(e, 'photo')} />
              </div>
              <p className="text-xs text-gray-400 text-center">JPG, JPEG, or PNG only. Max size 2MB.</p>
              {errors.photo && <div className="w-full"><ErrorAlert title={errors.photo} /></div>}
            </div>
          </div>

          <div className="border-2 border-dashed border-gray-200 rounded-xl p-6 hover:bg-gray-50/50 transition-colors">
            <Label className="text-base font-medium mb-4 block">Digital Signature <span className="text-red-500">*</span></Label>
            <div className="flex flex-col items-center justify-center space-y-4">
              {signaturePreview ? (
                <div className="relative">
                  <img src={signaturePreview} alt="Signature Preview" className="w-48 h-20 object-contain rounded-lg shadow-sm border border-gray-200 bg-white" />
                  <Button type="button" variant="destructive" size="sm" className="absolute -top-2 -right-2 h-6 w-6 rounded-full p-0" onClick={() => { setData('signature', null); setSignaturePreview(null); }}>×</Button>
                </div>
              ) : (
                <div className="w-48 h-20 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-300">
                  <UploadCloud className="w-8 h-8" />
                </div>
              )}

              <div className="w-full relative">
                <Input type="file" accept="image/jpeg,image/png,image/jpg" onChange={(e) => handleFileChange(e, 'signature')} />
              </div>
              <p className="text-xs text-gray-400 text-center">JPG, JPEG, or PNG only. Max size 2MB.</p>
              {errors.signature && <div className="w-full"><ErrorAlert title={errors.signature} /></div>}
            </div>
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
