import React, { useEffect, useState } from 'react';
import { Head, router, useForm, } from '@inertiajs/react';
import { useRoute } from 'ziggy-js';
import { Filter, Search } from 'lucide-react';

import AdminDashboardLayout from "@/Pages/Layouts/Admin/AdminDashboardLayout";
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

import CreateBatchDialog from './components/CreateBatchDialog';
import { BatchTable } from './components/BatchTable';

export default function Batches({ batches, filters }) {
    const route = useRoute();

    const activeSearch = filters?.search || '';
    const activeStatus = filters?.status || 'all';

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
        filterForm.setData({ search: '', status: 'all' });
        router.get(route('admin.batches.index'), {}, { preserveState: true, preserveScroll: true });
    }

    const handlePerPageChange = (value) => {
        router.get(route('admin.batches.index'), {
            search: activeSearch,
            status: activeStatus,
            'rows-per-page': value,
            page: 1
        }, { preserveState: true, preserveScroll: true });
    };

    // ------------------------------------------------------------------------
    // BULK ACTION FORM $ SELECTION
    // ------------------------------------------------------------------------
    const [selectedIds, setSelectedIds] = useState(new Set());

    const bulkForm = useForm({
        batch_ids: [],
        status: '',
    });

    useEffect(() => { bulkForm.setData('batch_ids', Array.from(selectedIds)) }, [selectedIds]);

    const handleSelectAll = (checked) => {
        if (checked) {
            setSelectedIds(new Set(batches.data.map(b => b.batch_id)));
        } else {
            setSelectedIds(new Set());
        }
    }

    const handleSelectRow = (id, checked) => {
        setSelectedIds(prev => {
            const newSelected = new Set(prev);
            if (checked) {
                newSelected.add(id);
            } else {
                newSelected.delete(id);
            }
            return newSelected;
        });
    }

    const handleBulkSubmit = (e) => {
        e.preventDefault();
        if (selectedIds.size === 0) return;

        bulkForm.patch(route('admin.batches.bulk-status'), {
            preserveScroll: true,
            onSuccess: () => {
                setSelectedIds(new Set());
                bulkForm.reset();
            }
        });
    }

    return (
        <>
            <Head title="Manage Batches" />
            <div className='p-4 md:p-5'>

                {/* Header */}
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-gray-900">Manage Batches</h1>
                        <p className="text-sm text-gray-500 mt-0.5">Configure student batches names and codes </p>
                    </div>
                    <CreateBatchDialog />
                </div>

                <div className='space-y-4'>

                    {/* FILTER PANEL */}
                    <div className="bg-white rounded-2xl border border-gray-200 p-1.5 shadow-sm flex flex-col lg:flex-row items-center justify-between">
                        <form onSubmit={handleFilterSubmit} className="flex flex-col sm:flex-row gap-2 w-full flex-1">
                            <div className="relative flex-1 lg:max-w-md group">
                                <Search className="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                                <input
                                    type="text"
                                    value={filterForm.data.search}
                                    onChange={(e) => filterForm.setData('search', e.target.value)}
                                    placeholder="Search code or name..."
                                    className="w-full pl-9 pr-4 py-2 bg-gray-50 border-transparent focus:bg-white border focus:border-indigo-500/30 rounded-xl focus:ring-4 focus:ring-indigo-500/10 text-sm outline-none"
                                />
                            </div>
                            <div className='flex flex-col sm:flex-row gap-2 sm:ml-auto w-full sm:w-auto'>
                                <div className="relative w-full sm:w-44">
                                    <Filter className="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4 z-10 pointer-events-none" />
                                    <Select
                                        value={filterForm.data.status}
                                        onValueChange={(value) => filterForm.setData('status', value)}
                                        defaultValue={filterForm.data.status}
                                    >
                                        <SelectTrigger className="w-full pl-9 pr-3 bg-gray-50 border border-transparent focus:bg-white focus:border-indigo-500/30 rounded-xl focus:ring-4 focus:ring-indigo-500/10 h-[38px] text-sm shadow-none">
                                            <SelectValue placeholder="All Statuses" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem value="all">All Statuses</SelectItem>
                                                <SelectItem value="active">Active Only</SelectItem>
                                                <SelectItem value="inactive">Inactive Only</SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div className="flex gap-2">
                                    <button type="submit" disabled={filterForm.processing} className="px-5 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-xl disabled:opacity-50">
                                        {filterForm.processing ? 'Searching ...' : 'Search'}
                                    </button>
                                    {(activeSearch || activeStatus !== 'all') && (
                                        <button type="button" onClick={handleClearFilters} className="px-4 py-2 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-xl">
                                            Clear
                                        </button>
                                    )}
                                </div>
                            </div>
                        </form>
                    </div>

                    {/* BULK ACTION BAR */}
                    <div className="bg-indigo-50 border border-indigo-100 rounded-xl p-2.5 flex flex-col sm:flex-row items-center justify-between gap-4 animate-in fade-in zoom-in-95">
                        <div className="flex items-center gap-3 px-2">
                            {selectedIds.size > 0 && (
                                <>
                                    <div className="bg-indigo-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">
                                        {selectedIds.size}
                                    </div>
                                    <span className="text-sm font-semibold text-indigo-900">Records Selected</span>
                                </>
                            )}
                        </div>

                        <form onSubmit={handleBulkSubmit} className="flex items-center gap-2">
                            <Select
                                value={bulkForm.data.status}
                                onValueChange={(value) => bulkForm.setData('status', value)}
                                required
                            >
                                <SelectTrigger className="w-[180px] h-[36px] border-indigo-200 text-indigo-900 bg-white font-medium rounded-lg shadow-sm">
                                    <SelectValue placeholder="Change status to..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem value="active">Active</SelectItem>
                                        <SelectItem value="inactive">Inactive</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <button type="submit" disabled={bulkForm.processing} className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium disabled:opacity-50">
                                {bulkForm.processing ? 'Submitting ...' : 'Submit'}
                            </button>
                        </form>
                    </div>


                    {/* DATA TABLE */}
                    <BatchTable batches={batches} selectedIds={selectedIds} handleSelectAll={handleSelectAll} handleSelectRow={handleSelectRow} handlePerPageChange={handlePerPageChange} />
                </div>
            </div>
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
