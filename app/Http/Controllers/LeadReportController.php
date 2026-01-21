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
    
        $columns = [
            'name' => ['label' => 'Agent Name', 'html' => true, 'searchable' => false]
        ];

        foreach($leadStages as $stage){
            $columns[$stage->name] = ['label' => $stage->name, 'html' => true, 'searchable' => false];    
        }

        $columns['action'] = ['label' => 'Conversion %', 'html' => true, 'searchable' => false];

        // 2. Get all agents
        $agents = $this->userRepo->role('agent')->with('avatar')->get();
        $total_count = $agents->count();
        // 3. Build agent-wise lead counts
        $agentLeads = $agents->map(function($agent) use ($leadStages) {
            // attach counts as properties instead of array
            foreach ($leadStages as $stage) {
                $agent->{$stage->name} = Lead::whereHas('statusLogs', function ($q) use ($agent, $stage) {
                    $q->where('status_id', $stage->id)
                    ->where('assignee_id', $agent->id);
                })->count();
            }

            $totalLeads = collect($leadStages)->sum(fn($stage) => $agent->{$stage->name});
            $converted = $agent->{'sales closed'} ?? 0;
            $agent->conversion = $totalLeads > 0 ? round(($converted / $totalLeads) * 100, 2) . '%' : '0%';

            return $agent; // keep as object
        });


        // 4. Calculate totals for all stages
        $totals = [];
        foreach ($leadStages as $stage) {
            $totals[$stage->name] = $agentLeads->sum(fn($agent) => $agent[$stage->name]);
        }
        
        $totals['conversion'] = '-'; // optional, or calculate overall conversion
        $agents = $agentLeads;

        $dataTable = new \App\Services\DataTableService(
            model: $agents,
            columns: $columns,
            rowFormatter: function($row) use ($leadStages) {
                return $this->formatRow($row, $leadStages);
            }
        );

        if ($request->ajax() && $request->loaddata == "yes") {
            return $dataTable->ajax();
        }

        return view($this->pathInitialize.'.index', $this->viewWithVars(get_defined_vars()));
    }

    public function formatRow($row, $leadStages)
    {   
        $row->name = view('back-office.partials.avatar', [
                'user' => $row
            ]
        )->render();

        // Lead counts per stage with badge
        foreach($leadStages as $stage) {
            $count = $row->{$stage->name} ?? 0;
            $row->{$stage->name} = '<span class="badge ' . badgeClass($stage->name) . '">' . $count . '</span>';
        }
        
        // Conversion %
        $row->action = $row->conversion ?? '0%';
        
        return $row;
    }
}