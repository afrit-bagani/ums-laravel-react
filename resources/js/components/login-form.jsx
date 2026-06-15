import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Field, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { Link } from "@inertiajs/react";

export function LoginForm({
    className,
    data,
    setData,
    errors,
    processing,
    onSubmit,
    ...props }) {
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
                        Enter your email below to login to your account
                    </p>
                </div>
                <Field>
                    <FieldLabel htmlFor="email">Email</FieldLabel>
                    <Input
                        id="email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        className="bg-background"
                        placeholder="Enter your login credential"
                        required
                    />
                    {errors.email && <p className="text-sm text-destructive mt-1">{errors.email}</p>}
                </Field>
                <Field>
                    <div className="flex items-center">
                        <FieldLabel htmlFor="password">Password</FieldLabel>
                        <Link
                            href="admin/forgot-password"
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
                    {errors.password && <p className="text-sm text-destructive mt-1">{errors.password}</p>}
                </Field>
                <Field>
                    <Button type="submit" disabled={processing}>
                        {processing ? 'Logging in..' : 'Login'}
                    </Button>
                </Field>
            </FieldGroup>
        </form>
    );
}
