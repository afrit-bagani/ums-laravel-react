import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { KeyRound, ShieldCheck, Eye, EyeOff } from 'lucide-react';
import { useRoute } from 'ziggy-js';
import ErrorAlert from '@/components/ErrorAlert';
import FlashMessageListner from '@/components/FlashMessageListner';
import AdminDashboardLayout from "@/Pages/Layouts/Admin/AdminDashboardLayout";

export default function ChangePassword() {
  const route = useRoute();

  const [showCurrent, setShowCurrent] = useState(false);
  const [showNew, setShowNew] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);

  const { data, setData, post, processing, errors } = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    post(route('admin.password.update'), {
      preserveScroll: true,
      onSuccess: () => {
        setData({
          current_password: '',
          password: '',
          password_confirmation: '',
        });
      }
    });
  };

  return (
    <>
      <FlashMessageListner />
      <Head title="Change Password" />

      <div className="max-w-2xl mx-auto py-8">
        <Card className="w-full shadow-xl ring-1 ring-gray-100 bg-white">
          <CardHeader className="space-y-2 border-b border-gray-100 pb-6 bg-white rounded-t-xl">
            <div className="mx-auto w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
              <KeyRound className="w-6 h-6 text-indigo-600" />
            </div>
            <CardTitle className="text-2xl font-bold text-center text-indigo-950">Change Account Password</CardTitle>
            <div className="text-sm text-gray-500 flex items-center justify-center gap-2 mt-2 px-4">
              <ShieldCheck className="w-5 h-5 text-emerald-500 shrink-0" />
              <span className="text-center w-[90%] text-balance">
                Keep your administrative account secure by updating your password regularly.
              </span>
            </div>
          </CardHeader>
          <form onSubmit={handleSubmit}>
            <CardContent className="space-y-6 pt-6">
              <div className="space-y-2">
                <Label htmlFor="current_password">Current Password</Label>
                <div className="relative">
                  <Input
                    id="current_password"
                    type={showCurrent ? "text" : "password"}
                    value={data.current_password}
                    onChange={e => setData('current_password', e.target.value)}
                    placeholder="Enter current password"
                    className="w-full pr-10"
                    required
                  />
                  <button
                    type="button"
                    className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground focus:outline-none"
                    onClick={() => setShowCurrent(!showCurrent)}
                  >
                    {showCurrent ? <EyeOff size={18} /> : <Eye size={18} />}
                  </button>
                </div>
                {errors.current_password && <ErrorAlert title={errors.current_password} />}
              </div>

              <div className="space-y-2">
                <Label htmlFor="password">New Password</Label>
                <div className="relative">
                  <Input
                    id="password"
                    type={showNew ? "text" : "password"}
                    value={data.password}
                    onChange={e => setData('password', e.target.value)}
                    className="w-full pr-10"
                    placeholder="Must be at least 8 characters"
                    required
                  />
                  <button
                    type="button"
                    className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground focus:outline-none"
                    onClick={() => setShowNew(!showNew)}
                  >
                    {showNew ? <EyeOff size={18} /> : <Eye size={18} />}
                  </button>
                </div>
                {errors.password && <ErrorAlert title={errors.password} />}
              </div>

              <div className="space-y-2">
                <Label htmlFor="password_confirmation">Confirm New Password</Label>
                <div className="relative">
                  <Input
                    id="password_confirmation"
                    type={showConfirm ? "text" : "password"}
                    value={data.password_confirmation}
                    onChange={e => setData('password_confirmation', e.target.value)}
                    className="w-full pr-10"
                    placeholder="Repeat new password"
                    required
                  />
                  <button
                    type="button"
                    className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground focus:outline-none"
                    onClick={() => setShowConfirm(!showConfirm)}
                  >
                    {showConfirm ? <EyeOff size={18} /> : <Eye size={18} />}
                  </button>
                </div>
              </div>
            </CardContent>
            <CardFooter className="flex flex-col gap-3 pb-6 bg-gray-50/50 rounded-b-xl border-t border-gray-100 pt-6">
              <Button type="submit" className="w-full" disabled={processing}>
                {processing ? "Updating Password..." : "Update Password"}
              </Button>
            </CardFooter>
          </form>
        </Card>
      </div>
    </>
  );
}

ChangePassword.layout = (page) => <AdminDashboardLayout showSidebar={true} children={page} />;