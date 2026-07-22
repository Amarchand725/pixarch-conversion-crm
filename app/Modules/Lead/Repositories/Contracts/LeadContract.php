<?php

namespace App\Modules\Lead\Repositories\Contracts;

use App\Repositories\Contracts\BaseContract;

interface LeadContract extends BaseContract
{
    public function getKanbanLeads();

    public function bulkAssign(array $payload);
}