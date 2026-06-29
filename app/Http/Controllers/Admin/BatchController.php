<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BatchController extends Controller
{
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
            "SELECT * from batch_master $query ORDER BY batch_id DESC LIMIT ? OFFSET ? ",
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:batch_master,code'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::insert(
            'INSERT INTO batch_master (code, name, status, created_by, created_at, updated_at) VALUES(?,?,?,?,?,?)',
            [$request->code, $request->name, $request->status, Auth::id(), now(), now()]
        );

        return back()->with('message', 'Batch created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $batch_id)
    {
        $request->merge(['batch_id' => $batch_id]);

        $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:batch_master,code,'.$batch_id.',batch_id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::update(
            'UPDATE batch_master SET code = ?, name = ?, status = ?, updated_at = ? WHERE batch_id =?',
            [$request->code, $request->name, $request->status, now(), $batch_id],
        );

        return back()->with('message', 'Batch updated successfully.');
    }

    /**
     * Update status of the specified resource in storage.
     */
    public function changeStatus(Request $request, $batch_id)
    {
        $request->merge(['batch_id' => $batch_id]);

        $request->validate([
            'batch_id' => ['required', 'exists:batch_master,batch_id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::update('UPDATE batch_master set status = ?, updated_at = ? WHERE batch_id = ?',
            [$request->status, now(), $batch_id]
        );

        return back()->with('message', 'Batch status changed successfully.');
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'batch_ids' => ['required', 'array'],
            'batch_ids.*' => ['required', 'exists:batch_master,batch_id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $placeholders = implode(',', array_fill(0, count($request->batch_ids), '?'));
        $bindings = [$request->status, now(), ...$request->batch_ids];

        DB::update("UPDATE batch_master SET status = ?, updated_at = ? WHERE batch_id IN ($placeholders)", $bindings);

        return back()->with('message', 'Batch status changed successfully.');
    }
}
