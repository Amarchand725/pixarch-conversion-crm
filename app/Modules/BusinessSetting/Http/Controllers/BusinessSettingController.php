<?php

namespace App\Modules\BusinessSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\BusinessSetting\Repositories\Eloquent\BusinessSettingRepository;
use App\Modules\BusinessSetting\Http\Requests\BusinessSettingRequest;
use App\Modules\BusinessSetting\Models\BusinessSetting;
use Exception;

class BusinessSettingController extends Controller
{
    protected $businessSettingRepo;

    public function __construct(BusinessSettingRepository $businessSettingRepo)
    {
        $this->businessSettingRepo = $businessSettingRepo;
    }

    public function index()
    {
        $businessSettings = $this->businessSettingRepo->getAll();
        return view(strtolower('business_settings.index'), compact('businessSettings'));
    }

    public function create()
    {
        return view('business_settings.create');
    }

    public function edit($id)
    {
        $businessSetting = $this->businessSettingRepo->showModel($id);
        return view('business_settings.edit', compact('businessSetting'));
    }

    public function update(BusinessSettingRequest $request, BusinessSetting $businessSetting)
    {
        $payload = $request->validated();
        try {
            $this->businessSettingRepo->updateModel($businessSetting, $payload);
            return redirect()->route(strtolower('BusinessSetting.index'))->with('success', 'BusinessSetting updated successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $businessSetting = $this->businessSettingRepo->showModel($id);
        return view('business_settings.show', compact('businessSetting'));
    }
}