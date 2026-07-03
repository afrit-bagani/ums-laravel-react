<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
// use App\Models\Admin\Programme;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProgrammeController extends Controller
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

        $programmes = DB::select(
            "SELECT * from programme_master $query ORDER BY programme_id DESC LIMIT ? OFFSET ? ",
            $dataBindings
        );

        $totalRecords = DB::selectOne(
            "SELECT COUNT(*) as count FROM programme_master $query",
            $bindings
        )->count;

        $paginator = new LengthAwarePaginator(
            $programmes,
            $totalRecords,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Admin/Programmes/Index', [
            'programmes' => $paginator,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:programme_master,code'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::insert('INSERT INTO programme_master (code, name, status, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?)', [
            $request->code, $request->name, $request->status, Auth::user()->id, now(), now(),
        ]);

        return back()->with('message', 'Programme created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $programme_id)
    {
        $request->merge(['programme_id' => $programme_id]);

        $request->validate([
            'programme_id' => ['required', Rule::exists('programme_master', 'programme_id')],
            'code' => ['required', 'string', 'max:255', Rule::unique('programme_master', 'code')->ignore($programme_id, 'programme_id')],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::update('UPDATE programme_master SET code = ? , name = ? , status = ?, updated_at = ? WHERE programme_id = ?', [
            $request->code, $request->name, $request->status, now(), $programme_id,
        ]);

        return back()->with('message', 'Programme updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function changeStatus(Request $request, $programme_id)
    {
        $request->merge(['programme_id' => $programme_id]);

        $request->validate([
            'programme_id' => ['required', Rule::exists('programme_master', 'programme_id')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::update('UPDATE programme_master SET status = ?, updated_at = ? WHERE programme_id = ?', [
            $request->status, now(), $programme_id,
        ]);

        return back()->with('message', 'Programme status changed successfully');
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'programme_ids' => ['required', 'array'],
            'programme_ids.*' => ['required', 'exists:programme_master,programme_id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $placeholders = implode(',', array_fill(0, count($request->programme_ids), '?'));

        DB::update("UPDATE programme_master SET status = ? , updated_at = ? WHERE programme_id IN ($placeholders)", [
            $request->status, now(), ...$request->programme_ids,
        ]);

        return back()->with('message', 'Programme status changed successfully');
    }
}
