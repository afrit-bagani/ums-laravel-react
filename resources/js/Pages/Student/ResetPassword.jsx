import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";
import { useRoute } from "ziggy-js";
import Logo from "@/components/AppLogoIcon";
import FlashMessageListner from "@/components/FlashMessageListner";
import { Field, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import ErrorAlert from "@/components/ErrorAlert";
import { Link } from "@inertiajs/react";
import { Eye, EyeOff } from "lucide-react";

export default function ResetPassword() {
    const { data, setData, errors, post, processing } = useForm({
        password: "",
        password_confirmation: "",
    });

    const [showPassword, setShowPassword] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);

    const route = useRoute();

    function submit(e) {
        e.preventDefault();
        post(route("student.password.reset.store"), {
            preserveState: true,
            preserveScroll: true,
        });
    }

    return (
        <>
            <Head title="Reset Password" />
            <FlashMessageListner />
            <div className="grid min-h-svh lg:grid-cols-2">
                <div className="flex flex-col gap-4 p-6 md:p-10">
                    <div className="flex justify-center gap-2 md:justify-start">
                        <Logo text="Student Portal" />
                    </div>
                    <div className="flex flex-1 items-center justify-center">
                        <div className="w-full max-w-xs">
                            <form className="flex flex-col gap-6" onSubmit={submit}>
                                <div className="flex flex-col items-center gap-2 text-center">
                                    <h1 className="text-2xl font-bold">Reset Password</h1>
                                    <p className="text-sm text-balance text-muted-foreground">
                                        Enter a new, strong password for your student account.
                                    </p>
                                </div>
                                <FieldGroup>
                                    <Field>
                                        <FieldLabel htmlFor="password">New Password</FieldLabel>
                                        <div className="relative">
                                            <Input
                                                id="password"
                                                type={showPassword ? "text" : "password"}
                                                value={data.password}
                                                onChange={(e) => setData('password', e.target.value)}
                                                className="bg-background w-full pr-10"
                                                required
                                            />
                                            <button
                                                type="button"
                                                className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground focus:outline-none"
                                                onClick={() => setShowPassword(!showPassword)}
                                            >
                                                {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
                                            </button>
                                        </div>
                                    </Field>

                                    {errors.password && <ErrorAlert title={errors.password} />}

                                    <Field>
                                        <FieldLabel htmlFor="password_confirmation">Confirm Password</FieldLabel>
                                        <div className="relative">
                                            <Input
                                                id="password_confirmation"
                                                type={showConfirm ? "text" : "password"}
                                                value={data.password_confirmation}
                                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                                className="bg-background w-full pr-10"
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
                                    </Field>

                                    {errors.error && <ErrorAlert title={errors.error} />}

                                    <Field>
                                        <Button type="submit" disabled={processing}>
                                            {processing ? 'Resetting...' : 'Save Password'}
                                        </Button>
                                    </Field>
                                </FieldGroup>

                                <div className="text-center text-sm">
                                    Changed your mind?{" "}
                                    <Link href={route("student.login")} className="underline underline-offset-4">
                                        Login
                                    </Link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div className="relative hidden bg-muted lg:block">
                    <img
                        src="/student-login.svg"
                        alt="Student Image"
                        className="absolute inset-0 h-full w-full object-cover dark:brightness-[0.2] dark:grayscale"
                    />
                </div>
            </div>
        </>
    );
}
