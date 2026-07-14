<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\BatchRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BatchController extends Controller
{
    protected BatchRepository $batchRepo;

    public function __construct(BatchRepository $batchRepo)
    {
        $this->batchRepo = $batchRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'all'])],
            'page' => ['nullable', 'integer'],
            'rows-per-page' => ['nullable', 'integer'],
        ]);

        $search = $request->query('search');
        $status = $request->query('status');
        $currentPage = $request->query('page', 1);
        $perPage = $request->query('rows-per-page', 10);

        $offset = ($currentPage - 1) * $perPage;

        $batches = $this->batchRepo->getPaginatedBatches($search, $status, $perPage, $offset);
        $totalRecords = $this->batchRepo->getTotalBatchesCount($search, $status);

        $paginator = new LengthAwarePaginator(
            $batches,
            $totalRecords,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Admin/Batches/Index', [
            'batches' => $paginator,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:batch_master,code'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->batchRepo->createBatch(
            $request->only(['code', 'name', 'status']),
            Auth::id(),
            now(),
            now()
        );

        return back()->with('success', 'Batch created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $batch_id)
    {
        $request->merge(['batch_id' => $batch_id]);

        $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:batch_master,code,'.$batch_id.',batch_id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->batchRepo->updateBatch(
            $batch_id,
            $request->only(['code', 'name', 'status']),
            now()
        );

        return back()->with('success', 'Batch updated successfully.');
    }

    /**
     * Update status of the specified resource in storage.
     */
    public function updateStatus(Request $request, int $batch_id)
    {
        $request->merge(['batch_id' => $batch_id]);

        $request->validate([
            'batch_id' => ['required', 'exists:batch_master,batch_id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->batchRepo->updateBatchStatus(
            $batch_id,
            $request->status,
            now()
        );

        return back()->with('success', 'Batch status changed successfully.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'batch_ids' => ['required', 'array'],
            'batch_ids.*' => ['required', 'exists:batch_master,batch_id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->batchRepo->bulkUpdateBatchStatus(
            $request->batch_ids,
            $request->status,
            now()
        );

        return back()->with('success', 'Batch status changed successfully.');
    }
}
