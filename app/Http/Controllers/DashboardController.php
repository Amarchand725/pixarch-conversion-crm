<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Modules\Lead\Repositories\Eloquent\LeadRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $leadRepo;

    public function __construct(LeadRepository $leadRepo)
    {
        $this->leadRepo = $leadRepo;
    }
    public function dashboard(){
        $title = Auth::user()->name . "'s Dashboard";
        // Get all leads grouped by status
        $statusLeads = $this->leadRepo->getAllCollection();
        
        // Count total agents
        $totalAgents = User::role('Agent')->count();

        $agentsSummary = User::role('Agent')
            ->select('id', 'name')
            ->withCount(['leads as total_assigned'])
            ->withCount(['leadLogs as total_updated' => function ($q) {
                $q->select(DB::raw('count(distinct model_id)'));
            }])
            ->get();
        
        foreach ($agentsSummary as $agent) {
            // Total assigned leads
            $agent->total_assigned = $agent->leads->count();

            // Total worked leads (unique lead IDs in logs)
            $agent->total_updated = $agent->leadLogs->pluck('model_id')->unique()->count();

            // Status summary per agent
            $statusCounts = DB::table('leads')
                        ->join('log_entity_statuses', 'leads.id', '=', 'log_entity_statuses.model_id')
                        ->where('log_entity_statuses.author_id', $agent->id)
                        ->select('log_entity_statuses.status_id', DB::raw('count(*) as total'))
                        ->groupBy('log_entity_statuses.status_id')
                        ->get();
            
            $agent->statusCounts = collect($statusCounts);
        }
        
        return view('back-office.dashboard', get_defined_vars());
    }
    public function profile(){
        $title = Auth::user()->name . "'s Profile";
        return view('back-office.profile', get_defined_vars());
    }
}