<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Models\Status;
use App\Models\User;
use App\Modules\Lead\Models\Lead;
use App\Modules\Lead\Repositories\Contracts\LeadContract;
use Illuminate\Http\Request;

class LeadReportController extends BaseModuleController
{
    protected $leadStatus;
    protected $userRepo;

    public function __construct(
        protected LeadContract $leadRepo
    ){
        $this->leadStatus = new Status();
        $this->userRepo = new User();

        // Initialize common module variables automatically
        $this->autoInit();
    }
    
    public function index(Request $request)
    {
        // 1. Get all stages dynamically (from leads table or a stages table)
        $leadStages = $this->leadStatus->where("model", "Lead")->get();
        
        // 2. Get all agents
        $agents = $this->userRepo->role('agent')->get();

        // 3. Build agent-wise lead counts
        $agentLeads = $agents->map(function($agent) use ($leadStages) {
            $data = ['name' => $agent->name, 'email' => $agent->email, 'uuid' => $agent->uuid];

            // Count leads per stage
            foreach ($leadStages as $stage) {
                $data[$stage->name] = Lead::whereHas('statusLogs', function ($q) use ($agent, $stage) {
                        $q->where('status_id', $stage->id);
                        $q->where('assignee_id', $agent->id);
                    })
                    ->count();
            }

            // Calculate conversion (last stage / total)
            $totalLeads = array_sum(array_values($data)); // sum only numeric values
            $converted = $data['sales closed'] ?? 0; // use stage name as key
            
            $data['conversion'] = $totalLeads > 0 
                ? round(($converted / $totalLeads) * 100, 2) . '%' 
                : '0%';

            return $data;
        });

        // 4. Calculate totals for all stages
        $totals = [];
        foreach ($leadStages as $stage) {
            $totals[$stage->name] = $agentLeads->sum(fn($agent) => $agent[$stage->name]);
        }
        
        $totals['conversion'] = '-'; // optional, or calculate overall conversion
        $agents = $agentLeads;
        
        return view($this->pathInitialize.'.index', $this->viewWithVars(get_defined_vars()));
    }
}