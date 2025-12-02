<?php

namespace App\Http\Controllers\BackOffice;

use App\Models\Traits\ModuleInitializer;
use App\Http\Controllers\Controller;

abstract class BaseModuleController extends Controller
{
    use ModuleInitializer;

    /**
     * Merge module variables with extra variables (function-specific)
     */
    protected function viewWithVars(array $extra = []): array
    {
        return array_merge($this->moduleViewVars(), $extra);
    }
}
