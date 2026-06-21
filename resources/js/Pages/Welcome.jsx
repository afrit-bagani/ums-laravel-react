import { Button } from "@/components/ui/button";
import { Link } from "@inertiajs/react";
import { useRoute } from "ziggy-js";

export default function Welcome() {
    const route = useRoute();

    return (
        <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100">
            <h1 className="text-4xl font-bold mb-4">
                Welcome to the UMS
            </h1>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <Button asChild>
                    <Link href={route('student.login')} as="button">Student Login</Link>
                </Button>

                <Button asChild>
                    <Link href={route('admin.login')} as="button">Admin Login</Link>
                </Button>
            </div>
        </div>
    );
}
