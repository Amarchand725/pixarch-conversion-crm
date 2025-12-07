<?php

namespace App\Services;

use App\Models\Status;
use App\Models\User;
use App\Modules\Lead\Models\Lead;

class LeadAssigner
{
    public static function getNextAgent()
    {
        $status_id = Status::where('model', 'User')->where('name', 'active')->value('id');
        
        // Get all active agents
        $agents = User::role('Agent')
        ->where('status_id', $status_id)
        ->orderBy('id')
        ->get();

        if ($agents->isEmpty()) {
            return null;
        }

        // Filter by daily capacity
        $eligibleAgents = $agents->filter(function ($agent) {
            $today = now()->toDateString();

            // Count today's assigned leads
            $todayCount = $agent->leads()
                ->whereDate('entity_relationships.created_at', $today)
                ->count();

            return $todayCount < $agent->daily_capacity;
        })->values();

        if ($eligibleAgents->isEmpty()) {
            // If all reached limit, fallback to all agents
            $eligibleAgents = $agents;
        }

        // Last assigned ID (global round robin)
        $lastAssigned = cache()->get('last_global_agent');

        $eligibleIds = $eligibleAgents->pluck('id')->toArray();

        if (!$eligibleIds) {
            return null;
        }

        // If first time
        if (!$lastAssigned || !in_array($lastAssigned, $eligibleIds)) {
            $nextAgent = $eligibleIds[0];
        } else {
            $index = array_search($lastAssigned, $eligibleIds);
            $nextAgent = $eligibleIds[($index + 1) % count($eligibleIds)];
        }

        // Store for next round
        cache()->put('last_global_agent', $nextAgent);

        return $nextAgent;
    }
}