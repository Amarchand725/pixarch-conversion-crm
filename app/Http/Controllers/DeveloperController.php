<?php

namespace App\Http\Controllers;

use App\Models\Source;
use App\Models\Status;
use App\Models\User;
use App\Modules\Lead\Models\Lead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DeveloperController extends Controller
{
    public function exportedContacts()
    {
        return 'No need to import contacts table because contacts & leads actually same data.';
        $chunkSize = 500;
        $totalInserted = 0;
        $duplicates = 0;
        $defaultPassword = Hash::make('user@123'); // hash once
        $existingEmails = DB::table('users')->pluck('email')->toArray(); // get existing emails

        DB::table('export_contacts')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('id')
            ->chunk($chunkSize, function ($rows) use ($defaultPassword, &$existingEmails, &$totalInserted, &$duplicates) {
                
                foreach ($rows as $row) {
                    dd($row);
                    $email = strtolower(trim($row->email));
                    
                    // skip if email already exists
                    if (in_array($email, $existingEmails)) {
                        $duplicates++;
                        continue;
                    }

                    User::create([
                        'status_id' => 1,
                        'name' => $row->full_name,
                        'phone' => $row->phone,
                        'email' => $email,
                        'password' => $defaultPassword,
                        'created_at' => $row->created,
                        'updated_at' => $row->last_activity_at,
                    ]);

                    // add to existing emails to avoid duplicates in this run
                    $existingEmails[] = $email;
                    $totalInserted++;           // increment counter
                }
        });

        return 'Successfully inserted '.$totalInserted.' records and duplicated: '. $duplicates;
    }

    public function importOpportunities()
    {
        $chunkSize = 500;
        $totalInserted = 0;
        $duplicates = 0;
        $existingEmails = DB::table('leads')->pluck('email')->toArray(); // get existing emails

        DB::table('opportunities')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('id')
            ->chunk($chunkSize, function ($rows) use (&$existingEmails, &$totalInserted, &$duplicates) {
                foreach ($rows as $row) {
                    $email = strtolower(trim($row->email));
                    
                    // skip if email already exists
                    if (in_array($email, $existingEmails)) {
                        $duplicates++;
                        continue;
                    }

                    $source = Source::where('name', $row->source)->first();

                    $model = Lead::create([
                        'source_id' => !empty($source) ? $source->id : null,
                        'name' => $row->opportunity_name,
                        'phone' => $row->phone,
                        'email' => $email,
                        'value' => $row->lead_value,
                        'pipeline' => $row->pipeline,
                        'status' => $row->status,
                        'created_at' => $row->created_on,
                        'updated_at' => $row->updated_on,
                    ]);

                    $assignee = User::where('name', $row->assigned)->first();
                    if(!empty($assignee)){
                        $assigneeId = $assignee->id;
                    }else{
                        $assigneeId = User::where('email', 'digital@100keys.ae')->value('id'); // assign to default user if not found
                    }
                    $status = Status::where('model', 'Lead')->where('name', $row->stage)->first();
                    $logStatus['status_id'] = !empty($status) ? $status->id : null;
                    $logStatus['assignee_id'] = $assigneeId;
                    $logStatus['notes'] = $row->notes;
                    $logStatus['model_id'] = $model->id;
                    $logStatus['model_type'] = $model->getMorphClass();

                    $log = $model->statusLogs()->firstOrNew();
                    $log->toFill($logStatus);
                    $log->save();

                    if (!empty($assigneeId)) {
                        $model->assignees()->sync($assigneeId);
                    }

                    // add to existing emails to avoid duplicates in this run
                    $existingEmails[] = $email;
                    $totalInserted++;           // increment counter
                }
        });

        return 'Successfully inserted '.$totalInserted.' records and duplicated: '. $duplicates;
    }

    public function getOpportunitiesAssignee()
    {
        // $assignees = DB::table('opportunities')->get(['id', 'assigned'])->groupBy('assigned');
        $assignees = DB::table('opportunities')
        // ->where('assigned', '!=', 'Amarchand Khan')
        ->whereNotNull('assigned')
        ->where('assigned', '!=', '')
        ->select('assigned')
        ->groupBy('assigned')
        ->get();

        // return $assignees;
        $assignedNames = $assignees->pluck('assigned')->toArray();
        return $assignedNames;
    }
}