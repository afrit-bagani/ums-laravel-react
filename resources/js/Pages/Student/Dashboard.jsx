import FlashMessageListner from "@/components/FlashMessageListner";
import { Button } from "@/components/ui/button";;
import { Link, usePage } from "@inertiajs/react"
import { useRoute } from "ziggy-js";

export default function Dashboard() {
    const { auth } = usePage().props;
    const user = auth.user;

    const route = useRoute();

    return (
        <>
            <FlashMessageListner />
            <h1 className="text-red-600 text-6xl">Welcome to Student Dashboard</h1>
            {user && (
                <div>
                    <p>ID: {user.id}</p>
                    <p>Name: {user.name}</p>
                    <p>Identifier: {user.login_identifier}</p>
                </div>
            )}

            <div className="mt-8">
                <Button variant="destructive" asChild>
                    <Link href={route('student.logout')} method="post" as="button">Logout</Link>
                </Button>
            </div>
        </>
    )
}
