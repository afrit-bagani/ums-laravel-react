import React from 'react';
import AdminDashboardLayout from "@/Pages/Layouts/Admin/AdminDashboardLayout";

export default function BatchesIndex({ batches }) {
    return (
        <div className="p-6">
            <h1 className="text-2xl font-bold mb-4">Manage Batches</h1>

            {/* Map through your data here */}
            <div className="bg-white rounded shadow p-4">
                <ul>
                    {batches.map(batch => (
                        <li key={batch.id}>{batch.name}</li>
                    ))}
                </ul>
            </div>
        </div>
    );
}

BatchesIndex.layout = (page) => {
    return (
        <AdminDashboardLayout showSidebar={true}>
            {page}
        </AdminDashboardLayout>
    )
}
