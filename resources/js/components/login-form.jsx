import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Field, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { Link } from "@inertiajs/react";
import { useRoute } from "ziggy-js";
import ErrorAlert from "./ErrorAlert";
import { useState } from "react";
import { Eye, EyeOff } from "lucide-react";

export function LoginForm({
    className,
    isAdmin,
    data,
    setData,
    errors,
    processing,
    onSubmit,
    ...props }) {

    const route = useRoute();
    const [showPassword, setShowPassword] = useState(false);

    return (
        <form
            className={cn("flex flex-col gap-6", className)}
            {...props}
            onSubmit={onSubmit}
        >
            <FieldGroup>
                <div className="flex flex-col items-center gap-1 text-center">
                    <h1 className="text-2xl font-bold">
                        Login to your account
                    </h1>
                    <p className="text-sm text-balance text-muted-foreground">
                        Enter your credentials
                    </p>
                </div>
                {isAdmin ?
                    <Field>
                        <FieldLabel htmlFor="login_identifier">Email</FieldLabel>
                        <Input
                            id="login_identifier"
                            type="email"
                            value={data.login_identifier}
                            onChange={(e) => setData('login_identifier', e.target.value)}
                            className="bg-background"
                            placeholder="Enter your Email"
                            required
                        />
                    </Field>
                    :
                    <Field>
                        <FieldLabel htmlFor="login_identifier">Registration No</FieldLabel>
                        <Input
                            id="login_identifier"
                            type="text"
                            value={data.login_identifier}
                            onChange={(e) => setData('login_identifier', e.target.value)}
                            className="bg-background"
                            placeholder="Enter your registration number"
                            required
                        />
                    </Field>
                }

                <Field>
                    <div className="flex items-center">
                        <FieldLabel htmlFor="password">Password</FieldLabel>
                        <Link
                            href={isAdmin ? route('admin.password.request') : route('student.password.request')}
                            className="ml-auto text-sm underline-offset-4 hover:underline"
                        >
                            Forgot your password?
                        </Link>
                    </div>
                    <div className="relative">
                        <Input
                            id="password"
                            type={showPassword ? "text" : "password"}
                            value={data.password}
                            onChange={e => setData('password', e.target.value)}
                            placeholder="Enter Your Password"
                            required
                            className="bg-background pr-10"
                        />
                        <button
                            type="button"
                            className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground focus:outline-none"
                            onClick={() => setShowPassword(!showPassword)}
                            aria-label={showPassword ? "Hide password" : "Show password"}
                        >
                            {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
                        </button>
                    </div>
                </Field>
                {errors.error && <ErrorAlert title={errors.error} />}
                <Field>
                    <Button type="submit" disabled={processing}>
                        {processing ? 'Logging in..' : 'Login'}
                    </Button>
                </Field>
            </FieldGroup>
        </form>
    );
}
