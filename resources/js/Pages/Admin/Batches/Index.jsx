import React, { useEffect, useState } from 'react';
import AdminDashboardLayout from "@/Pages/Layouts/Admin/AdminDashboardLayout";
import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { useRoute } from 'ziggy-js';
import { Button } from '@/components/ui/button';
import { ChevronLeft, ChevronRight, Edit, Filter, Plus, RefreshCw, Search } from 'lucide-react';
import CreateBatchModal from './CreateBatchModal';

export default function Batches({ batches, filters }) {
    const route = useRoute();
    const { component } = usePage();

    const activeSearch = filters?.search || '';
    const activeStatus = filters?.status || 'All';

    // ------------------------------------------------------------------------
    // FILTER & SEARCH FORM
    // ------------------------------------------------------------------------
    const filterForm = useForm({
        search: activeSearch,
        status: activeStatus
    })

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        filterForm.get(route('admin.batches.index'), { preserveState: true, preserveScroll: true });
    }

    const handleClearFilters = () => {
        filterForm.setData({ search: '', status: 'All' });
        router.get(route('admin.batches.index'), {}, { preserveState: true, preserveScroll: true });
    }

    // ------------------------------------------------------------------------
    // PAGINATION HANDLER
    // ------------------------------------------------------------------------
    const handlePageChange = (pageNumber) => {
        router.get(route('admin.batches.index'), {
            ...filterForm.data,
            page: pageNumber
        }, { preserveState: true, preserveScroll: true });
    };

    // ------------------------------------------------------------------------
    // BULK ACTION FORM $ SELECTION
    // ------------------------------------------------------------------------
    const [selectedIds, setSelectedIds] = useState([]);

    const bulkForm = useForm({
        ids: [],
        status: '',
    });

    useEffect(() => { bulkForm.setData('ids', selectedIds) }, [selectedIds]);

    const handleSelectAll = (e) => {
        setSelectedIds(e.target.checked ? batches.data.map(b => b.batch_id) : []);
    }

    const handleSelectRow = (id) => {
        setSelectedIds(prev => prev.includes(id) ? prev.filter(rowId => rowId !== id) : [...prev, id])
    }

    const handleBulkSubmit = (e) => {
        e.preventDefault();
        if (selectedIds.length === 0) return;

        bulkForm.post(route('admin.batches.bulk-submit'), {
            preserveScroll: true,
            onSuccess: () => {
                setSelectedIds([]);
                bulkForm.reset();
            }
        })
    }

    const [isCreateBatchModalOpen, setIsCreateBatchModalOpen] = useState(false);

    const createBatchForm = useForm({
        code: '',
        name: '',
        status: ''
    })

    function handleNewBatch(e, post) {
        e.preventDefault();
        createBatchForm.post(route('admin.batches.store'))
    }

    return (
        <>
            <Head title="Manage Batches" />
            <div className='p-6'>

                {/* Header */}
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                    <div>
                        <h1 className="text-3xl font-extrabold tracking-tight text-gray-900">Manage Batches</h1>
                        <p className="text-sm text-gray-500 mt-1">Configure student batches names and codes </p>
                    </div>
                    <button
                        onClick={() => setIsCreateBatchModalOpen(true)}
                        className="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all">
                        <Plus className="w-4 h-4" /> New Batch
                    </button>
                </div>

                <div className='space-y-6'>

                    {/* FILTER PANEL */}
                    <div className="bg-white rounded-2xl border border-gray-200 p-2 shadow-sm flex flex-col lg:flex-row items-center justify-between">
                        <form onSubmit={handleFilterSubmit} className="flex flex-col sm:flex-row gap-2 w-full lg:w-auto flex-1">
                            <div className="relative flex-1 lg:max-w-md group">
                                <Search className="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                                <input
                                    type="text"
                                    value={filterForm.data.search}
                                    onChange={(e) => filterForm.setData('search', e.target.value)}
                                    placeholder="Search code or name..."
                                    className="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white border focus:border-indigo-500/30 rounded-xl focus:ring-4 focus:ring-indigo-500/10 text-sm outline-none"
                                />
                            </div>
                            <div className="relative w-full sm:w-48">
                                <Filter className="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                                <select
                                    value={filterForm.data.status}
                                    onChange={(e) => filterForm.setData('status', e.target.value)}
                                    className="w-full pl-10 pr-8 py-2.5 bg-gray-50 border-transparent focus:bg-white border focus:border-indigo-500/30 rounded-xl focus:ring-4 focus:ring-indigo-500/10 text-sm outline-none appearance-none"
                                >
                                    <option value="All">All Statuses</option>
                                    <option value="active">Active Only</option>
                                    <option value="inactive">Inactive Only</option>
                                </select>
                            </div>
                            <div className="flex gap-2">
                                <button type="submit" disabled={filterForm.processing} className="px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-xl disabled:opacity-50">
                                    {filterForm.processing ? 'Searching ...' : 'Search'}
                                </button>
                                {(activeSearch || activeStatus !== 'All') && (
                                    <button type="button" onClick={handleClearFilters} className="px-4 py-2.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-xl">
                                        Clear
                                    </button>
                                )}
                            </div>
                        </form>
                    </div>

                    {/* BULK ACTION BAR */}
                    <div className="bg-indigo-50 border border-indigo-100 rounded-xl p-3 flex flex-col sm:flex-row items-center justify-between gap-4 animate-in fade-in zoom-in-95">
                        <div className="flex items-center gap-3 px-2">
                            {selectedIds.length > 0 && (
                                <>
                                    <div className="bg-indigo-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">
                                        {selectedIds.length}
                                    </div>
                                    <span className="text-sm font-semibold text-indigo-900">Records Selected</span>
                                </>
                            )}
                        </div>

                        <form onSubmit={handleBulkSubmit} className="flex items-center gap-2">
                            <select
                                value={bulkForm.data.status}
                                onChange={(e) => bulkForm.setData('status', e.target.value)}
                                className="py-2.5 px-4 border border-indigo-200 rounded-lg text-sm bg-white text-indigo-900 font-medium"
                                required
                            >
                                <option value="" disabled>Change status to...</option>
                                <option value="active">Set Active</option>
                                <option value="inactive">Set Inactive</option>
                            </select>
                            <button type="submit" disabled={bulkForm.processing} className="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium disabled:opacity-50">
                                {bulkForm.processing ? 'Submitting ...' : 'Submit'}
                            </button>
                        </form>
                    </div>


                    {/* DATA TABLE */}
                    <div className="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left border-collapse whitespace-nowrap">
                                <thead>
                                    <tr className="bg-gray-50/80 border-b border-gray-100">
                                        <th className="py-4 px-6 w-12 text-center">
                                            <input
                                                type="checkbox"
                                                className="rounded-md border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4.5 h-4.5 cursor-pointer"
                                                onChange={handleSelectAll}
                                                checked={batches.data.length > 0 && batches.data.every(b => selectedIds.includes(b.batch_id))}
                                            />
                                        </th>
                                        <th className="py-4 px-6 text-xs font-semibold text-gray-500 uppercase text-center">Sl No</th>
                                        <th className="py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Status</th>
                                        <th className="py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Action</th>
                                        <th className="py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Batch Code</th>
                                        <th className="py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Batch Name</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 text-sm">
                                    {batches.data.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} className="py-16 text-center text-gray-500">
                                                No batches found. Try adjusting your filters.
                                            </td>
                                        </tr>
                                    ) : (
                                        batches.data.map((batch, index) => {
                                            const globalIndex = batches.from + index;

                                            const isSelected = selectedIds.includes(batch.batch_id);

                                            return (
                                                <tr key={batch.batch_id} className={`transition-colors ${isSelected ? 'bg-indigo-50/30' : 'hover:bg-gray-50/50'}`}>

                                                    <td className="py-4 px-6 text-center">
                                                        <input
                                                            type="checkbox"
                                                            className="rounded-md border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4.5 h-4.5 cursor-pointer"
                                                            checked={isSelected}
                                                            onChange={() => handleSelectRow(batch.batch_id)}
                                                        />
                                                    </td>

                                                    <td className="py-4 px-6 text-center text-gray-500 font-medium">
                                                        {globalIndex}
                                                    </td>

                                                    <td className="py-4 px-6">
                                                        <span className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold ${batch.status === 'active' ? 'bg-emerald-100/60 text-emerald-700' : 'bg-rose-100/60 text-rose-700'
                                                            }`}>
                                                            <span className={`w-1.5 h-1.5 rounded-full ${batch.status === 'active' ? 'bg-emerald-500' : 'bg-rose-500'}`}></span>
                                                            {batch.status.toUpperCase()}
                                                        </span>
                                                    </td>

                                                    <td className="py-4 px-6">
                                                        <div className="flex items-center gap-2">
                                                            <button
                                                                type="button"
                                                                onClick={() => openSingleStatusModal(batch)}
                                                                className="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg"
                                                                title="Quick toggle status"
                                                            >
                                                                <RefreshCw className="w-4 h-4" />
                                                            </button>
                                                            <button
                                                                type="button"
                                                                className="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg"
                                                                title="Edit batch"
                                                            >
                                                                <Edit className="w-4 h-4" />
                                                            </button>
                                                        </div>
                                                    </td>

                                                    <td className="py-4 px-6 font-semibold text-gray-900">{batch.code}</td>

                                                    <td className="py-4 px-6 text-gray-600">{batch.name}</td>
                                                </tr>
                                            );
                                        })
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* LARAVEL PAGINATION */}
                        <div className="border-t border-gray-100 p-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/30">
                            <p className="text-sm text-gray-500 font-medium">
                                Showing <span className="text-gray-900 font-semibold">{batches.from || 0}</span> to <span className="text-gray-900 font-semibold">{batches.to || 0}</span> of <span className="text-gray-900 font-semibold">{batches.total || 0}</span> results
                            </p>

                            <div className="flex items-center gap-1 p-1 bg-gray-100/50 border border-gray-200/60 rounded-xl">
                                <button
                                    onClick={() => handlePageChange(batches.current_page - 1)}
                                    disabled={batches.current_page === 1}
                                    className="p-2 rounded-lg text-gray-500 hover:bg-white hover:text-gray-900 disabled:opacity-40 disabled:bg-transparent"
                                >
                                    <ChevronLeft className="w-4 h-4" />
                                </button>

                                {Array.from({ length: batches.last_page }, (_, i) => i + 1).map(page => (
                                    <button
                                        key={page}
                                        onClick={() => handlePageChange(page)}
                                        className={`w-8 h-8 flex items-center justify-center text-sm font-medium rounded-lg ${batches.current_page === page ? 'bg-white text-gray-900 shadow-sm border border-gray-200' : 'text-gray-500 hover:bg-white hover:text-gray-900'
                                            }`}
                                    >
                                        {page}
                                    </button>
                                ))}

                                <button
                                    onClick={() => handlePageChange(batches.current_page + 1)}
                                    disabled={batches.current_page === batches.last_page}
                                    className="p-2 rounded-lg text-gray-500 hover:bg-white hover:text-gray-900 disabled:opacity-40 disabled:bg-transparent"
                                >
                                    <ChevronRight className="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {isCreateBatchModalOpen && (
                <CreateBatchModal
                    setIsCreateBatchModalOpen={setIsCreateBatchModalOpen}
                    onSubmit={handleNewBatch}
                    createForm={createBatchForm}
                />
            )}
        </>
    );
}

Batches.layout = (page) => {
    return (
        <AdminDashboardLayout showSidebar={true}>
            {page}
        </AdminDashboardLayout>
    )
}
