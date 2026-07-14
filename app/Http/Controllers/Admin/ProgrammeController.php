<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\ProgrammeRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProgrammeController extends Controller
{
    protected ProgrammeRepository $programmeRepo;

    public function __construct(ProgrammeRepository $programmeRepo)
    {
        $this->programmeRepo = $programmeRepo;
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

        $programmes = $this->programmeRepo->getPaginatedProgrammes($search, $status, $perPage, $offset);
        $totalRecords = $this->programmeRepo->getTotalProgrammesCount($search, $status);

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

        $this->programmeRepo->createProgramme(
            $request->only(['code', 'name', 'status']),
            Auth::id(),
            now(),
            now()
        );

        return back()->with('success', 'Programme created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $programme_id)
    {
        $request->merge(['programme_id' => $programme_id]);

        $request->validate([
            'programme_id' => ['required', Rule::exists('programme_master', 'programme_id')],
            'code' => ['required', 'string', 'max:255', Rule::unique('programme_master', 'code')->ignore($programme_id, 'programme_id')],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->programmeRepo->updateProgramme(
            $programme_id,
            $request->only(['code', 'name', 'status']),
            now()
        );

        return back()->with('success', 'Programme updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function updateStatus(Request $request, int $programme_id)
    {
        $request->merge(['programme_id' => $programme_id]);

        $request->validate([
            'programme_id' => ['required', Rule::exists('programme_master', 'programme_id')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->programmeRepo->updateProgrammeStatus(
            $programme_id,
            $request->status,
            now()
        );

        return back()->with('success', 'Programme status changed successfully');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'programme_ids' => ['required', 'array'],
            'programme_ids.*' => ['required', 'exists:programme_master,programme_id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $this->programmeRepo->bulkUpdateProgrammeStatus(
            $request->programme_ids,
            $request->status,
            now()
        );

        return back()->with('success', 'Programme status changed successfully');
    }
}
