export default function AdminDashboardLayout(showSidebar = true, children) {
    return (
        <div className="flex min-h-screen">
            {showSidebar && <Sidebar />}
            <main className="flex-1 p-6">
                {children}
            </main>
        </div>
    );
}
