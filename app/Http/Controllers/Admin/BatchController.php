<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('rows-per-page', 10);
        $currentPage = $request->query('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $search = $request->query('search');
        $status = $request->query('status');

        $whereClauses = [];
        $bindings = [];

        // search filter
        if (! empty($search)) {
            $whereClauses[] = '(code LIKE ? OR name LIKE ?)';
            $bindings[] = "%{$search}%";
            $bindings[] = "%{$search}%";
        }

        // status filter
        if ($status !== null && $status !== 'all') {
            $whereClauses[] = 'status = ?';
            $bindings[] = $status;
        }

        $query = '';
        if (count($whereClauses) > 0) {
            $query = 'WHERE '.implode(' AND ', $whereClauses); // final query string
        }

        $dataBindings = array_merge($bindings, [$perPage, $offset]);

        $batches = DB::select(
            "SELECT * from batch_master $query LIMIT ? OFFSET ?",
            $dataBindings
        );

        $totalRecords = DB::selectOne(
            "SELECT COUNT(*) as count FROM batch_master $query",
            $bindings
        )->count;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBatchRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Batch $batch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Batch $batch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBatchRequest $request, Batch $batch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Batch $batch)
    {
        //
    }

    public function bulkStatus() {}
}
