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
use App\Models\LogEntityStatus;
use App\Models\Meeting;
use App\Services\LicenseService;

class AuthController extends Controller
{
    protected $leadRepo;

    public function __construct(LeadRepository $leadRepo)
    {
        $this->leadRepo = $leadRepo;
    }
    public function dashboard(){
        // Check if trial expired
        // if (!LicenseService::canWrite()) {
        //     $trialExpired = true;
        //     $trialDaysLeft = 0;
        // } elseif (LicenseService::isTrialActive()) {
        //     $trialExpired = false;
        //     $trialDaysLeft = LicenseService::trialDaysLeft();
        // } else {
        //     $trialExpired = false;
        //     $trialDaysLeft = 0;
        // }
        
        $title = Auth::user()->name . "'s Dashboard";
        // Get all leads grouped by status
        $statusLeads = $this->leadRepo->getAllCollection();
        // $statusLeads = $this->leadRepo->getKanbanLeads();
        
        // Count total agents
        $totalAgents = User::role('Agent')->count();

        $agentsSummary = User::role('Agent')
            ->with(['leads.lastStatusLog', 'leadLogs'])
            ->get();

        foreach ($agentsSummary as $agent) {
            $agent->total_assigned = $agent->leads->count();

            $agent->total_updated = $agent->leadLogs()
                ->distinct('model_id')
                ->count('model_id');

            $statusCounts = [];

            foreach ($agent->leads as $lead) {
                $latestLog = $lead->lastStatusLog;

                $statusName = 'Not Updated';

                if ($latestLog && $latestLog->author_id === $agent->id) {
                    $statusName = (string) $latestLog->status;
                }

                $statusCounts[$statusName] = ($statusCounts[$statusName] ?? 0) + 1;
            }

            $agent->statusCounts = collect($statusCounts);
        }
        // dd($statusLeads);
        // dd(get_defined_vars());
        return view('back-office.dashboard.dashboard', [
            'title' => $title,
            'statusLeads' => $statusLeads,
            'totalAgents' => $totalAgents,
            'agentsSummary' => $agentsSummary,
        ]);
        // return view('back-office.dashboard.dashboard', get_defined_vars());
    }
    public function profile(){
        $title = Auth::user()->name . "'s Profile";

        $authUser = auth()->user();
        $isAdmin = $authUser->hasRole('Admin'); // adjust according to your role system
        $agentId = $authUser->id;

        // 1️⃣ Lead Activities
        $leadQuery = LogEntityStatus::with(['assignee', 'lead']);

        $leadActivities = null;
        if (!$isAdmin) {
            $leadQuery->where('assignee_id', $agentId);
        }

        // 2️⃣ Meeting Activities
        $meetingQuery = Meeting::with(['attendees', 'lead']);

        if (!$isAdmin) {
            $meetingQuery->whereHas('attendees', fn($q) => $q->where('user_id', $agentId));
        }

        $meetingActivities = $meetingQuery->get()
            ->map(function($meeting) {
                return [
                    'type' => 'meeting_scheduled',
                    'description' => "Meeting with '{$meeting->lead?->name}' scheduled @ ".getDateTimeFormat($meeting->start_date_time),
                    'related_user' => $meeting->attendees->first(), // main agent
                    'created_at' => $meeting->created_at,
                ];
            });

        // 3️⃣ Merge and sort all activities
        $activities = collect($leadActivities)
            ->merge($meetingActivities)
            ->sortByDesc('created_at')
            ->values(); // reset keys
            
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
            'phone' => [
                'nullable',
                'intl_phone', // validates full international number
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