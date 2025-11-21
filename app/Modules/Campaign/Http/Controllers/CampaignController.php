<?php

namespace App\Modules\Campaign\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Campaign\Repositories\Eloquent\CampaignRepository;
use App\Modules\Campaign\Http\Requests\CampaignRequest;
use App\Modules\Campaign\Models\Campaign;
use Exception;

class CampaignController extends Controller
{
    protected $campaignRepo;

    public function __construct(CampaignRepository $campaignRepo)
    {
        $this->campaignRepo = $campaignRepo;
    }

    public function index()
    {
        $campaigns = $this->campaignRepo->getAll();
        return view(strtolower('campaigns.index'), compact('campaigns'));
    }

    public function create()
    {
        return view('campaigns.create');
    }

    public function store(CampaignRequest $request)
    {
        $payload = $request->validated();
        try {
            $this->campaignRepo->storeModel($payload);
            return redirect()->route(strtolower('Campaign.index'))->with('success', 'Campaign created successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $campaign = $this->campaignRepo->showModel($id);
        return view('campaigns.edit', compact('campaign'));
    }

    public function update(CampaignRequest $request, Campaign $campaign)
    {
        $payload = $request->validated();
        try {
            $this->campaignRepo->updateModel($campaign, $payload);
            return redirect()->route(strtolower('Campaign.index'))->with('success', 'Campaign updated successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $campaign = $this->campaignRepo->showModel($id);
        return view('campaigns.show', compact('campaign'));
    }

    public function destroy($id)
    {
        try {
            $this->campaignRepo->softDeleteModel($id);
            return redirect()->route(strtolower('campaigns.index'))->with('success', 'Campaign deleted successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function restore($id)
    {
        try {
            $this->campaignRepo->restoreModel($id);
            return redirect()->route(strtolower('campaigns.index'))->with('success', 'Campaign restored successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->campaignRepo->permanentlyDeleteModel($id);
            return redirect()->route(strtolower('campaigns.index'))->with('success', 'Campaign permanently deleted.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkDelete()
    {
        try {
            $this->campaignRepo->bulkDelete();
            return redirect()->route(strtolower('campaigns.index'))->with('success', 'Bulk delete successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->campaignRepo->bulkRestore();
            return redirect()->route(strtolower('campaigns.index'))->with('success', 'Bulk restore successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}