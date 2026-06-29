import FlashMessageListner from "@/components/FlashMessageListner";
import Sidebar from "./Sidebar";

export default function AdminDashboardLayout({ showSidebar = true, children }) {
    return (
        <div className="flex min-h-screen bg-gray-50">
            <FlashMessageListner />
            {showSidebar && <Sidebar />}
            <main className="flex-1">
                {children}
            </main>
        </div>
    );
}
