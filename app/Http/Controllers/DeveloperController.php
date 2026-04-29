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
        $defaultUserId = User::where('email', 'digital@100keys.ae')->value('id');
        
        // $users = User::pluck('name')->toArray();
        $users = User::get()
            ->mapWithKeys(function ($user) {
                return [
                    strtolower(trim(preg_replace('/\s+/u', ' ', $user->name))) => $user->id
                ];
            })
            ->toArray();

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
                    $userKey = strtolower(trim(preg_replace('/\s+/u', ' ', $row->assigned)));

                    $assigneeId = $users[$userKey] ?? $defaultUserId;

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

    function findBestUserMatch($inputName, $users)
    {
        $inputName = strtolower(trim($inputName));
        $inputName = preg_replace('/\s+/', ' ', $inputName);

        $inputWords = array_unique(
            array_filter(explode(' ', $inputName), fn($w) => strlen($w) >= 3)
        );

        $bestMatchId = null;
        $bestScore = 0;

        foreach ($users as $user) {
            $commonWords = array_intersect($inputWords, $user['words']);
            $score = count($commonWords);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatchId = $user['id'];
            }
        }

        // optional threshold (avoid wrong matches)
        return $bestScore >= 1 ? $bestMatchId : null;
    }

    public function getOpportunitiesAssignee()
    {
        $assignees = DB::table('opportunities')
        ->whereNotNull('assigned')
        ->where('assigned', '!=', '')
        ->select('assigned')
        ->groupBy('assigned')
        ->get();

        // return $assignees;
        $assignedNames = $assignees->pluck('assigned')->toArray();
        return $assignedNames;
    }

    public function userCredentials(){
        $users = User::where('email', '!=', 'admin@gmail.com')->get();
        $userCredentials = [];
        foreach($users as $user){
            $plainPassword = Str::random(8); // generate random password
            $user->password = Hash::make($plainPassword); // generate random password
            $userCredentials[] = [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $plainPassword
            ];
        }

        return $userCredentials;
    }

    public function matchUserName(){
        $shortNames = [
            "Adam Asif",
            "Nida Bhadelia",
            "Hassan K",
            "Zaki Asif",
            "Hammad Kiyani",
            "Hammad Khatri",
            "Areel Rehman",
            "Mushyyen Hammad",
            "",
            "Sadaf Hyder",
            "Amarchand Khan",
            "Angelita Hipolito",
            "Farmeen  Khan",
            "Natalliya Varabyova",
            "Shahrukh  Salman",
            "Mujahid Ghani",
            "Khawaja Umair",
            "Khaled  Hossam"
        ];

        $fullNames = [
            "Dr. Brady Kunde",
            "Rizwan Naeem Sheikh",
            "SHAHRUKH SALMAN QAYYUM SALMAN ZUBERI",
            "ANGELITA ROMERO HIPOLITO",
            "HAMMAD UR REHMAN TAHMURAS IFZAL KIYANI",
            "ZAKI ASIF MUHAMMAD ASIF SHEHZAD",
            "HAFIZA MUSHYYEN HAMMAD",
            "HAMMAD SHAUKAT HUSSAIN",
            "ADAM ASIF ASIF SHERAFUDEEN",
            "SADAF KHAN HAIDER HUSSAIN KHAN",
            "FARMEEN ASHAR KHAN",
            "HASSAN ALI ABASSI",
            "AREEL UR REHMAN HAFEEZ",
            "NIDA BHADELIA",
            "Natallia Varabyova",
            "Khaled hossan ( SIM I USING BY DIVYA )",
            "Khawaja Umair"
        ];

        $result = [];

        foreach ($fullNames as $full) {
            $matched = false;

            foreach ($shortNames as $short) {
                if (empty(trim($short))) continue;

                $shortWords = preg_split('/\s+/', strtolower(trim($short)));
                $fullLower = strtolower($full);

                foreach ($shortWords as $word) {
                    if ($word && str_contains($fullLower, $word)) {
                        $result[] = $short; // replace with short name
                        $matched = true;
                        break 2; // break both loops
                    }
                }
            }

            if (!$matched) {
                $result[] = $full; // keep original if no match
            }
        }

        // print_r($result);
        dd($shortNames, $fullNames, $result);
    }
}