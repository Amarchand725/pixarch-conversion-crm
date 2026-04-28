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
        set_time_limit(0);

        $chunkSize = 500;
        $totalInserted = 0;
        $duplicates = 0;

        // cache existing emails (faster lookup)
        $existingEmails = DB::table('leads')->pluck('email')->map(function ($email) {
            return strtolower(trim($email));
        })->flip()->toArray();

        // cache related tables (VERY important)
        $sources = Source::pluck('id', 'name')->toArray();
        $users = User::pluck('id', 'name')->toArray();
        $defaultUserId = User::where('email', 'digital@100keys.ae')->value('id');
        $statuses = Status::where('model', 'Lead')
            ->get()
            ->mapWithKeys(function ($status) {
                return [strtolower(trim($status->name)) => $status->id];
            })
            ->toArray();
        
        DB::table('opportunities')
            ->orderBy('id')
            ->chunk($chunkSize, function ($rows) use (
                &$existingEmails,
                &$totalInserted,
                &$duplicates,
                $sources,
                $users,
                $defaultUserId,
                $statuses
            ) {

                foreach ($rows as $row) {
                    $email = null;
                    if($row->email != null || $row->email != ''){
                        // normalize email
                        $email = strtolower(trim($row->email));

                        // duplicate check (fast array key check instead of in_array)
                        if (isset($existingEmails[$email])) {
                            $duplicates++;
                            continue;
                        }
                    }

                    // FIX phone scientific notation
                    $phone = $row->phone;
                    if (is_numeric($phone) && str_contains((string)$phone, 'E')) {
                        $phone = number_format((float)$phone, 0, '', '');
                    }
                    $phone = (string) $phone;

                    // resolve source
                    $sourceId = $sources[$row->source] ?? null;

                    // create lead (still Eloquent as you wanted)
                    $model = Lead::create([
                        'source_id'  => $sourceId,
                        'name'       => $row->opportunity_name,
                        'phone'      => $phone,
                        'email'      => $email,
                        'budget'      => $row->lead_value,
                        'pipeline'   => $row->pipeline,
                        'status'     => $row->status,
                        'created_at' => $row->created_on,
                        'updated_at' => $row->updated_on,
                    ]);

                    // assign user (no query inside loop)
                    $assigneeId = $users[$row->assigned] ?? $defaultUserId;

                    // status lookup from cache
                    $stageKey = strtolower(trim(preg_replace('/\s+/', ' ', $row->stage)));

                    $statusId = $statuses[$stageKey] ?? null;

                    // status log
                    $model->statusLogs()->create([
                        'amount'   => $row->lead_value,
                        'status_id'   => $statusId,
                        'assignee_id' => $assigneeId,
                        'description'       => $row->notes,
                    ]);

                    // assign relation
                    if ($assigneeId) {
                        $model->assignees()->sync([$assigneeId]);
                    }

                    // mark email as processed
                    $existingEmails[$email] = true;

                    $totalInserted++;
                }
            });

        return 'Successfully inserted '.$totalInserted.' records and duplicated: '.$duplicates;
    }

    // public function importOpportunities()
    // {
    //     $chunkSize = 500;
    //     $totalInserted = 0;
    //     $duplicates = 0;
    //     $existingEmails = DB::table('leads')->pluck('email')->toArray(); // get existing emails

    //     DB::table('opportunities')
    //         ->whereNotNull('email')
    //         ->where('email', '!=', '')
    //         ->orderBy('id')
    //         ->chunk($chunkSize, function ($rows) use (&$existingEmails, &$totalInserted, &$duplicates) {
    //             foreach ($rows as $row) {
    //                 $email = strtolower(trim($row->email));
                    
    //                 // skip if email already exists
    //                 if (in_array($email, $existingEmails)) {
    //                     $duplicates++;
    //                     continue;
    //                 }

    //                 $source = Source::where('name', $row->source)->first();

    //                 $model = Lead::create([
    //                     'source_id' => !empty($source) ? $source->id : null,
    //                     'name' => $row->opportunity_name,
    //                     'phone' => $row->phone,
    //                     'email' => $email,
    //                     'value' => $row->lead_value,
    //                     'pipeline' => $row->pipeline,
    //                     'status' => $row->status,
    //                     'created_at' => $row->created_on,
    //                     'updated_at' => $row->updated_on,
    //                 ]);

    //                 $assignee = User::where('name', $row->assigned)->first();
    //                 if(!empty($assignee)){
    //                     $assigneeId = $assignee->id;
    //                 }else{
    //                     $assigneeId = User::where('email', 'digital@100keys.ae')->value('id'); // assign to default user if not found
    //                 }
    //                 $status = Status::where('model', 'Lead')->where('name', $row->stage)->first();
    //                 $logStatus['status_id'] = !empty($status) ? $status->id : null;
    //                 $logStatus['assignee_id'] = $assigneeId;
    //                 $logStatus['notes'] = $row->notes;
    //                 $logStatus['model_id'] = $model->id;
    //                 $logStatus['model_type'] = $model->getMorphClass();

    //                 $log = $model->statusLogs()->firstOrNew();
    //                 $log->toFill($logStatus);
    //                 $log->save();

    //                 if (!empty($assigneeId)) {
    //                     $model->assignees()->sync($assigneeId);
    //                 }

    //                 // add to existing emails to avoid duplicates in this run
    //                 $existingEmails[] = $email;
    //                 $totalInserted++;           // increment counter
    //             }
    //     });

    //     return 'Successfully inserted '.$totalInserted.' records and duplicated: '. $duplicates;
    // }

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