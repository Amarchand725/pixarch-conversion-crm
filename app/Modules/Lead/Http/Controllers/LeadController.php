<?php

namespace App\Modules\Lead\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lead\Repositories\Eloquent\LeadRepository;
use App\Modules\Lead\Http\Requests\LeadRequest;
use App\Modules\Lead\Http\Requests\LeadStatusRequest;
use App\Modules\Lead\Models\Lead;
use Exception;

class LeadController extends Controller
{
    protected $leadRepo;

    public function __construct(LeadRepository $leadRepo)
    {
        $this->leadRepo = $leadRepo;
    }

    public function index()
    {
        $title = 'Leads';
        $statusLeads = $this->leadRepo->getAllCollection();
        return view(strtolower('back-office.leads.index'), get_defined_vars());
    }

    public function create()
    {
        return view('back-office.leads.create');
    }

    public function store(LeadRequest $request)
    {
        $payload = $request->validated();
        try {
            $this->leadRepo->storeModel($payload);
            return redirect()->route(strtolower('Lead.index'))->with('success', 'Lead created successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $lead = $this->leadRepo->showModel($id);
        return view('backOffice.leads.edit', compact('lead'));
    }

    public function update(LeadRequest $request, Lead $lead)
    {
        $payload = $request->validated();
        try {
            $this->leadRepo->updateModel($lead, $payload);
            return redirect()->route(strtolower('Lead.index'))->with('success', 'Lead updated successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $lead = $this->leadRepo->showModel($id);
        return view('backOffice.leads.show', compact('lead'));
    }

    public function destroy($id)
    {
        try {
            $this->leadRepo->softDeleteModel($id);
            return redirect()->route(strtolower('leads.index'))->with('success', 'Lead deleted successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function restore($id)
    {
        try {
            $this->leadRepo->restoreModel($id);
            return redirect()->route(strtolower('leads.index'))->with('success', 'Lead restored successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->leadRepo->permanentlyDeleteModel($id);
            return redirect()->route(strtolower('leads.index'))->with('success', 'Lead permanently deleted.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkDelete()
    {
        try {
            $this->leadRepo->bulkDelete();
            return redirect()->route(strtolower('leads.index'))->with('success', 'Bulk delete successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->leadRepo->bulkRestore();
            return redirect()->route(strtolower('leads.index'))->with('success', 'Bulk restore successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function updateStatus(LeadStatusRequest $request)
    {
        $payload = $request->validated();
        
        try {
            $this->leadRepo->statusModel($payload);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500); // optional HTTP 500
        }
    }
}