import { X } from "lucide-react";

export default function CreateBatchModal({ setIsCreateBatchModalOpen, createForm, onSubmit }) {

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm">
            <div className="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-md w-full overflow-hidden animate-in zoom-in-95">
                <div className="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 className="font-bold text-lg">Create New Batch</h3>
                    <button type="button" onClick={() => setIsCreateBatchModalOpen(false)} className="text-gray-400 hover:text-gray-600"><X className="w-4 h-4" /></button>
                </div>
                <form onSubmit={onSubmit} className="p-6 space-y-4">
                    <div>
                        <label className="block text-sm font-semibold mb-1.5">Batch Code</label>
                        <input type="text" required value={createForm.data.code} onChange={(e) => createForm.setData('code', e.target.value)} className="w-full px-4 py-2.5 border rounded-xl text-sm" />
                        {createForm.errors.code && <p className="text-red-500 text-xs mt-1">{createForm.errors.code}</p>}
                    </div>
                    <div>
                        <label className="block text-sm font-semibold mb-1.5">Batch Name</label>
                        <input type="text" required value={createForm.data.name} onChange={(e) => createForm.setData('name', e.target.value)} className="w-full px-4 py-2.5 border rounded-xl text-sm" />
                        {createForm.errors.name && <p className="text-red-500 text-xs mt-1">{createForm.errors.name}</p>}
                    </div>
                    <div>
                        <label className="block text-sm font-semibold mb-1.5">Initial Status</label>
                        <select value={createForm.data.status} onChange={(e) => createForm.setData('status', e.target.value)} className="w-full px-4 py-2.5 border rounded-xl text-sm">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" disabled={createForm.processing} className="w-full py-3 bg-gray-900 text-white font-semibold rounded-xl text-sm mt-2 disabled:opacity-50">
                        Create Batch
                    </button>
                </form>
            </div>
        </div>
    )
}
