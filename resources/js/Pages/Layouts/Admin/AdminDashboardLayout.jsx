import Sidebar from "./Sidebar";

export default function AdminDashboardLayout({ showSidebar = true, children }) {
    return (
        <div className="flex min-h-screen bg-gray-50">
            {showSidebar && <Sidebar />}
            <main className="flex-1">
                {children}
            </main>
        </div>
    );
}
