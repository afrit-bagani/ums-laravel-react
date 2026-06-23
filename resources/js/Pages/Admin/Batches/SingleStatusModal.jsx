export default function SingleStatusModal() {
    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm">
            <div className="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-sm w-full p-6 animate-in zoom-in-95">
                <h3 className="text-lg font-bold text-gray-900 mb-1">Update Status</h3>
                <p className="text-sm text-gray-500 mb-5">Change status for <strong className="text-gray-900">{statusChangeTargetName}</strong>.</p>
                <form onSubmit={handleSingleStatusSubmit}>
                    <select
                        value={statusForm.data.status} onChange={(e) => statusForm.setData('status', e.target.value)}
                        className="w-full mb-6 py-2.5 px-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 text-sm"
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <div className="flex gap-3">
                        <button type="button" onClick={() => setIsSingleStatusModalOpen(false)} className="flex-1 py-2.5 border rounded-xl text-sm hover:bg-gray-50">Cancel</button>
                        <button type="submit" disabled={statusForm.processing} className="flex-1 py-2.5 bg-indigo-600 text-white rounded-xl text-sm flex items-center justify-center gap-2 disabled:opacity-50">
                            <Check className="w-4 h-4" /> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    )
}
