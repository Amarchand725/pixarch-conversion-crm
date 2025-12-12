<?php

namespace App\Enum;


enum AgentTypeEnum: string {
    case AUTO_ASSIGNED = 'auto_assigned';
    case MANUAL_ASSIGNED = 'manual_assigned';
}
