import { Link } from "@inertiajs/react";
import { useRoute } from "ziggy-js";
import { Ziggy } from "@/ziggy";
import FlashMessageListner from "@/components/FlashMessageListner";
import { Button } from "@/components/ui/button";
import AdminDashboardLayout from "@/Pages/Layouts/Admin/AdminDashboardLayout";

export default function AdminDashboard() {

    const route = useRoute(Ziggy);

    FlashMessageListner();

    return (
        <>
            <h1>Admin Dashboard</h1>
            <Button variant="destructive" asChild>
                <Link href={route('admin.logout')} method="post" as="button">Logout</Link>
            </Button>
        </>
    )
}

AdminDashboard.layout = (page) => {
    return (
        <AdminDashboardLayout showSidebar={true}>
            {page}
        </AdminDashboardLayout>
    )
}
