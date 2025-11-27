<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Modules\Lead\Repositories\Eloquent\LeadRepository;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $leadRepo;

    public function __construct(LeadRepository $leadRepo)
    {
        $this->leadRepo = $leadRepo;
    }
    public function dashboard(){
        // Get all leads grouped by status
        $statusLeads = $this->leadRepo->getAllCollection();
        
        // Count total agents
        $totalAgents = User::role('Agent')->count();
        return view('back-office.dashboard', get_defined_vars());
    }
}