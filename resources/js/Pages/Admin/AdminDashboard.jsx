import AdminDashboardLayout from "@/Pages/Layouts/Admin/AdminDashboardLayout";

export default function AdminDashboard() {
    return (
        <h1>Admin Dashboard</h1>
    )
}

AdminDashboard.layout = [AdminDashboardLayout, { showSidebar: true }];
