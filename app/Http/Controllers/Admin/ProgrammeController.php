<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProgrammeRequest;
use App\Http\Requests\UpdateProgrammeRequest;
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

        $batches = DB::select(
            "SELECT * from program_master $query ORDER BY program_id DESC LIMIT ? OFFSET ? ",
            $dataBindings
        );

        $totalRecords = DB::selectOne(
            "SELECT COUNT(*) as count FROM program_master $query",
            $bindings
        )->count;

        $paginator = new LengthAwarePaginator(
            $batches,
            $totalRecords,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Admin/Programs/Index', [
            'programs' => $paginator,
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
    public function store(StoreProgrammeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Programme $programme)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Programme $programme)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProgrammeRequest $request, Programme $programme)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Programme $programme)
    {
        //
    }
}
