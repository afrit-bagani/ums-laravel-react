import { Head, useForm } from "@inertiajs/react";
import { useRoute } from "ziggy-js";
import Logo from "@/components/AppLogoIcon";
import FlashMessageListner from "@/components/FlashMessageListner";
import { Field, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import ErrorAlert from "@/components/ErrorAlert";
import { Link } from "@inertiajs/react";

export default function ForgotPassword() {
    const { data, setData, errors, post, processing } = useForm({
        login_identifier: "",
    });

    const route = useRoute();

    function submit(e) {
        e.preventDefault();
        post(route("admin.password.email"));
    }

    return (
        <>
            <Head title="Forgot Password" />
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
                                    <h1 className="text-2xl font-bold">Forgot Password</h1>
                                    <p className="text-sm text-balance text-muted-foreground">
                                        Enter your admin email address to receive a new temporary password.
                                    </p>
                                </div>
                                <FieldGroup>
                                    <Field>
                                        <FieldLabel htmlFor="login_identifier">Email Address</FieldLabel>
                                        <Input
                                            id="login_identifier"
                                            type="email"
                                            value={data.login_identifier}
                                            onChange={(e) => setData('login_identifier', e.target.value)}
                                            className="bg-background"
                                            placeholder="Enter your email address"
                                            required
                                        />
                                    </Field>

                                    {errors.error && <ErrorAlert title={errors.error} />}

                                    <Field>
                                        <Button type="submit" disabled={processing}>
                                            {processing ? 'Sending...' : 'Reset Password'}
                                        </Button>
                                    </Field>
                                </FieldGroup>

                                <div className="text-center text-sm">
                                    Remember your password?{" "}
                                    <Link href={route("admin.login")} className="underline underline-offset-4">
                                        Login
                                    </Link>
                                </div>
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
