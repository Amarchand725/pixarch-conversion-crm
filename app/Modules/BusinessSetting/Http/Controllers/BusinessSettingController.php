<?php

namespace App\Modules\BusinessSetting\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\BusinessSetting\Http\Requests\BusinessSettingRequest;
use App\Modules\BusinessSetting\Models\BusinessSetting;
use App\Modules\BusinessSetting\Repositories\Contracts\BusinessSettingContract;
use Exception;
use Illuminate\Support\Facades\DB;

class BusinessSettingController extends BaseModuleController
{
    public function __construct(
        protected BusinessSettingContract $businessSettingRepo
    ){
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index()
    {
        $businessSettings = $this->businessSettingRepo->getAll();
        return view(strtolower($this->pathInitialize.'.index'), $this->viewWithVars(get_defined_vars()));
    }

    public function edit(BusinessSetting $business)
    {
        $model = $this->businessSettingRepo->showModel($business);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(BusinessSettingRequest $request, BusinessSetting $businessSetting)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $businessSetting) {
                $this->businessSettingRepo->updateModel($businessSetting, $payload);
            });
            return successResponse($response, $this->singularLabel. ' updated successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(BusinessSetting $business)
    {
        $model = $this->businessSettingRepo->showModel($business);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }
}