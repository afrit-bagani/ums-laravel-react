import { Link } from "@inertiajs/react";
import { useRoute } from "ziggy-js";
import FlashMessageListner from "@/components/FlashMessageListner";
import { Button } from "@/components/ui/button";
import AdminDashboardLayout from "@/Pages/Layouts/Admin/AdminDashboardLayout";

export default function Dashboard() {

    const route = useRoute();

    FlashMessageListner();

    return (
        <>
            <h1>Admin Dashboard</h1>

        </>
    )
}

Dashboard.layout = (page) => {
    return (
        <AdminDashboardLayout showSidebar={true}>
            {page}
        </AdminDashboardLayout>
    )
}
