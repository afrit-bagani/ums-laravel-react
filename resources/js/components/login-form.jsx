import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Field, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { Link } from "@inertiajs/react";
import { useRoute } from "ziggy-js";
import ErrorAlert from "./ErrorAlert";

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
                    <Input
                        id="password"
                        type="password"
                        value={data.password}
                        onChange={e => setData('password', e.target.value)}
                        placeholder="Enter Your Password"
                        required
                        className="bg-background"
                    />
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
