<?php

namespace App\Modules\LeadCapture\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\LeadCapture\Repositories\Eloquent\LeadCaptureRepository;
use App\Modules\LeadCapture\Http\Requests\LeadCaptureRequest;
use App\Modules\LeadCapture\Models\LeadCapture;
use Exception;

class LeadCaptureController extends Controller
{
    protected $leadCaptureRepo;

    public function __construct(LeadCaptureRepository $leadCaptureRepo)
    {
        $this->leadCaptureRepo = $leadCaptureRepo;
    }

    public function index()
    {
        $leadCaptures = $this->leadCaptureRepo->getAll();
        return view(strtolower('lead_captures.index'), compact('leadCaptures'));
    }

    public function create()
    {
        return view('lead_captures.create');
    }

    public function store(LeadCaptureRequest $request)
    {
        $payload = $request->validated();
        try {
            $this->leadCaptureRepo->storeModel($payload);
            return redirect()->route(strtolower('LeadCapture.index'))->with('success', 'LeadCapture created successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $leadCapture = $this->leadCapture->showModel($id);
        return view('lead_captures.edit', compact('leadCapture'));
    }

    public function update(LeadCaptureRequest $request, LeadCapture $leadCapture)
    {
        $payload = $request->validated();
        try {
            $this->leadCaptureRepo->updateModel($leadCapture, $payload);
            return redirect()->route(strtolower('LeadCapture.index'))->with('success', 'LeadCapture updated successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $leadCapture = $this->leadCaptureRepo->showModel($id);
        return view('lead_captures.show', compact('leadCapture'));
    }

    public function destroy($id)
    {
        try {
            $this->leadCaptureRepo->softDeleteModel($id);
            return redirect()->route(strtolower('lead_captures.index'))->with('success', 'LeadCapture deleted successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function restore($id)
    {
        try {
            $this->leadCaptureRepo->restoreModel($id);
            return redirect()->route(strtolower('lead_captures.index'))->with('success', 'LeadCapture restored successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->leadCaptureRepo->permanentlyDeleteModel($id);
            return redirect()->route(strtolower('lead_captures.index'))->with('success', 'LeadCapture permanently deleted.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkDelete()
    {
        try {
            $this->leadCaptureRepo->bulkDelete();
            return redirect()->route(strtolower('lead_captures.index'))->with('success', 'Bulk delete successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->leadCaptureRepo->bulkRestore();
            return redirect()->route(strtolower('lead_captures.index'))->with('success', 'Bulk restore successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}