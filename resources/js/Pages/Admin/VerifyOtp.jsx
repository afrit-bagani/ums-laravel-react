import { Head, useForm } from "@inertiajs/react";
import { useRoute } from "ziggy-js";
import Logo from "@/components/AppLogoIcon";
import FlashMessageListner from "@/components/FlashMessageListner";
import { Field, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import ErrorAlert from "@/components/ErrorAlert";
import { Link } from "@inertiajs/react";
import { RefreshCwIcon } from "lucide-react";
import { REGEXP_ONLY_DIGITS } from "input-otp";
import {
  InputOTP,
  InputOTPGroup,
  InputOTPSlot,
} from "@/components/ui/input-otp";

export default function VerifyOtp({ login_identifier }) {
    const { data, setData, errors, post, processing } = useForm({
        otp: "",
    });

    const route = useRoute();

    function submit(e) {
        e.preventDefault();
        post(route("admin.password.verify.store"));
    }

    return (
        <>
            <Head title="Verify OTP" />
            <FlashMessageListner />
            <div className="grid min-h-svh lg:grid-cols-2">
                <div className="flex flex-col gap-4 p-6 md:p-10">
                    <div className="flex justify-center gap-2 md:justify-start">
                        <Logo text="Admin Portal" />
                    </div>
                    <div className="flex flex-1 items-center justify-center">
                        <div className="w-full max-w-xs">
                            <form className="flex flex-col gap-6" onSubmit={submit}>
                                <div className="flex flex-col items-center gap-2 text-center">
                                    <h1 className="text-2xl font-bold">Verify OTP</h1>
                                    <p className="text-sm text-balance text-muted-foreground">
                                        Enter the 6-digit OTP sent to {login_identifier}.
                                    </p>
                                </div>
                                <FieldGroup>
                                    <Field>
                                        <div className="flex items-center justify-between w-full mb-1">
                                            <FieldLabel htmlFor="digits-only">
                                                One Time Password
                                            </FieldLabel>
                                            <Button variant="outline" size="sm" className="h-7 px-2 text-xs gap-1" asChild>
                                                <Link href={route("admin.password.request")}>
                                                    <RefreshCwIcon className="w-3 h-3" />
                                                    Resend Code
                                                </Link>
                                            </Button>
                                        </div>
                                        <div className="flex justify-center w-full">
                                            <InputOTP 
                                                id="digits-only" 
                                                maxLength={6} 
                                                pattern={REGEXP_ONLY_DIGITS}
                                                value={data.otp}
                                                onChange={(value) => setData('otp', value)}
                                            >
                                                <InputOTPGroup>
                                                    <InputOTPSlot index={0} />
                                                    <InputOTPSlot index={1} />
                                                    <InputOTPSlot index={2} />
                                                    <InputOTPSlot index={3} />
                                                    <InputOTPSlot index={4} />
                                                    <InputOTPSlot index={5} />
                                                </InputOTPGroup>
                                            </InputOTP>
                                        </div>
                                    </Field>

                                    {errors.otp && <ErrorAlert title={errors.otp} />}
                                    {errors.error && <ErrorAlert title={errors.error} />}

                                    <Field>
                                        <Button type="submit" disabled={processing}>
                                            {processing ? 'Verifying...' : 'Verify Code'}
                                        </Button>
                                    </Field>
                                </FieldGroup>
                            </form>
                        </div>
                    </div>
                </div>
                <div className="relative hidden bg-muted lg:block">
                    <img
                        src="/admin-login.png"
                        alt="Admin Login Image"
                        className="absolute inset-0 h-full w-full object-cover dark:brightness-[0.2] dark:grayscale"
                    />
                </div>
            </div>
        </>
    );
}
