export default function EditBatchModal() {
    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm">
            <div className="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-md w-full overflow-hidden animate-in zoom-in-95">
                <div className="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 className="font-bold text-lg">Edit Batch</h3>
                    <button type="button" onClick={() => setIsEditModalOpen(false)} className="text-gray-400 hover:text-gray-600"><X className="w-4 h-4" /></button>
                </div>
                <form onSubmit={handleUpdateBatch} className="p-6 space-y-4">
                    <div>
                        <label className="block text-sm font-semibold mb-1.5">Batch Code</label>
                        <input type="text" required value={editForm.data.code} onChange={(e) => editForm.setData('code', e.target.value)} className="w-full px-4 py-2.5 border rounded-xl text-sm" />
                        {editForm.errors.code && <p className="text-red-500 text-xs mt-1">{editForm.errors.code}</p>}
                    </div>
                    <div>
                        <label className="block text-sm font-semibold mb-1.5">Batch Name / Cohort</label>
                        <input type="text" required value={editForm.data.name} onChange={(e) => editForm.setData('name', e.target.value)} className="w-full px-4 py-2.5 border rounded-xl text-sm" />
                        {editForm.errors.name && <p className="text-red-500 text-xs mt-1">{editForm.errors.name}</p>}
                    </div>
                    <button type="submit" disabled={editForm.processing} className="w-full py-3 bg-gray-900 text-white font-semibold rounded-xl text-sm mt-2 disabled:opacity-50">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    )
}
