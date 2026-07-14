import { Link, useForm } from "@inertiajs/react";

import { useRoute } from "ziggy-js";
import { LoginForm } from "@/components/login-form";
import Logo from "@/components/AppLogoIcon";
import FlashMessageListner from "@/components/FlashMessageListner";

export default function Login() {
    const { data, setData, errors, post, processing } = useForm({
        login_identifier: "",
        password: "",
    });

    const route = useRoute();

    function submit(e) {
        e.preventDefault();
        post(route("student.login.store"));
    }


    return (
        <>
            <FlashMessageListner />
            <div className="grid min-h-svh lg:grid-cols-2">
                <div className="flex flex-col gap-4 p-6 md:p-10">
                    <Link href={route('welcome')} className="flex justify-center gap-2 md:justify-start">
                        <Logo text="Student Login" />
                    </Link>
                    <div className="flex flex-1 items-center justify-center">
                        <div className="w-full max-w-xs">
                            <LoginForm
                                isAdmin={false}
                                data={data}
                                setData={setData}
                                errors={errors}
                                processing={processing}
                                onSubmit={submit} />
                        </div>
                    </div>
                </div>
                <div className="relative hidden bg-muted lg:block">
                    <img
                        src="/student-login.svg"
                        alt="Student Login Image"
                        className="absolute inset-0 h-full w-full object-cover dark:brightness-[0.2] dark:grayscale"
                    />
                </div>
            </div>
        </>
    );
}
