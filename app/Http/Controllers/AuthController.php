<?php

namespace App\Http\Controllers;

use App\Helpers\FileUploader;
use App\Models\User;
use App\Modules\Lead\Repositories\Eloquent\LeadRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enum\GenderEnum;

class AuthController extends Controller
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
            ->with(['leads.lastStatusLog', 'leadLogs'])
            ->get();
        
        foreach ($agentsSummary as $agent) {
            // Total assigned leads
            $agent->total_assigned = $agent->leads->count();

            // Total worked leads (unique lead IDs in logs)
            $agent->total_updated = $agent->leadLogs->pluck('model_id')->unique()->count();

            // Status summary per agent
            $statusCounts = [];

            foreach ($agent->leads as $lead) {
                // Get latest status log (lastStatusLog already eager loaded)
                $latestLog = $lead->lastStatusLog;

                // Only consider updates by this agent
                $statusName = 'Not Updated';
                if ($latestLog && $latestLog->author_id == $agent->id) {
                    $statusName = (string) $latestLog->status; // cast to string to avoid Illegal offset
                }

                if (!isset($statusCounts[$statusName])) {
                    $statusCounts[$statusName] = 0;
                }

                $statusCounts[$statusName]++;
            }

            $agent->statusCounts = collect($statusCounts);
        }

        return view('back-office.dashboard.dashboard', get_defined_vars());
    }
    public function profile(){
        $title = Auth::user()->name . "'s Profile";
        return view('back-office.dashboard.profile', get_defined_vars());
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return response()->json(['error' => false, 'message' => 'The provided old password is incorrect.'], 422);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['success' => true, 'message' => 'You have changed password successfully!.'], 200);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function updateProfile(Request $request)
    {
        $model = auth()->user();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone'    => [
                'nullable',
                'regex:/^\(\d{3}\)\s-\s\d{8}$/',
                Rule::unique('users', 'phone')->ignore($model),
            ],
            'gender' => ['required', Rule::in(GenderEnum::cases())],
            'avatar' => [ 'nullable'],
        ]);

        if (!empty($request->avatar) && $request->avatar instanceof \Illuminate\Http\UploadedFile) {
            // Delete existing avatar if exists
            if ($model?->avatar?->path) {
                FileUploader::deleteFile($model?->avatar?->path);
            }

            // Upload new avatar
            $model->avatar_id = FileUploader::uploadFile(
                $request->avatar, 
                $model, 
                'avatars', 
                size: 64
            )?->id;
        }

        $model->name = $request->name;
        $model->phone = $request->phone;
        $model->gender = $request->gender;
        $model->save();

        return response()->json(['success' => true, 'message' => 'You have updated profile successfully!.'], 200);
    }
}