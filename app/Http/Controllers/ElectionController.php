<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Election;
use App\Models\Profiles;
use App\Models\ListMasters;
use App\Models\ElectionRound;
use App\Models\CandidatesGroup;
use App\Models\users;
use App\Models\candidates;
use App\Models\Leader;
use App\Models\Voter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Carbon;

class ElectionController extends Controller
{
    public function getElectionInfo($electioncode,$roundnumber)
    {
        $data=array();
        DB::statement("SET sql_mode=''");
        $election_rounds = ElectionRound::where('election_code', $electioncode)->get();
        if(sizeof($election_rounds)>0){
        $election_type = Election::where('election_code', $electioncode)->value('election_type');
        if($roundnumber==0){
           $roundnumber=isset($election_rounds[0]->round_number)?$election_rounds[0]->round_number:0;
        }
        if ($election_type == 1) {
            $candidates = DB::select("
            SELECT profiles.profile_code, full_name, sex, IFNULL(mobile, '') AS mobile, ifnull(candidate_status,'') as candidate_status
            FROM profiles
            LEFT JOIN candidates ON profiles.profile_code = candidates.profile_code AND candidates.elections_code = '$electioncode' and
            round_number='$roundnumber'
        ");
        } else {
            $candidates = DB::select("SELECT candidates_groups.group_code,election_code,group_name,win_number,
            count(profile_code) as count_candidates
            FROM candidates_groups inner join candidates
            on candidates_groups.group_code=candidates.group_code AND candidates.elections_code = '$electioncode'
            AND candidates_groups.election_code = '$electioncode' and round_number='$roundnumber' group by group_code,election_code;
        ");
            $candidatestochoose = array();
            foreach ($candidates as $candidate) {
                $candidatestochoose[$candidate->group_code] = DB::select("
            SELECT profiles.profile_code, full_name, sex, IFNULL(mobile, '') AS mobile, ifnull(candidate_status,'') as candidate_status,
            '$candidate->group_name' as group_name
            FROM profiles
             JOIN candidates ON profiles.profile_code = candidates.profile_code AND candidates.elections_code = '$electioncode'
             and group_code='$candidate->group_code'
             and round_number='$roundnumber'
        ");
            }
            $data['candidatestochoose'] = $candidatestochoose;
        }
        
        $data['election_type'] = $election_type;
        $data['candidates'] = $candidates;
        $data['election_rounds'] = $election_rounds;
    }

        return json_encode($data);
    }

    public function getProfiles($electioncode)
    {
        DB::statement("SET sql_mode=''");
        $profiles = DB::select("
        SELECT profiles.profile_code, full_name, sex, IFNULL(mobile, '') AS mobile, ifnull(candidate_status,'') as candidate_status,candidates.group_code
        FROM profiles
        LEFT JOIN candidates ON profiles.profile_code = candidates.profile_code AND candidates.elections_code = '$electioncode'
    group by profiles.profile_code");
        $candidatesGroups = CandidatesGroup::where('election_code', $electioncode)->pluck('group_name', 'group_code');
        $data = array();
        foreach ($profiles as $index => $profile) {
            $data[$index]['profile_code'] = $profile->profile_code;
            $data[$index]['full_name'] = $profile->full_name;
            $data[$index]['sex'] = $profile->sex;
            $data[$index]['mobile'] = $profile->mobile;
            $data[$index]['candidate_status'] = $profile->candidate_status;
            if (isset($candidatesGroups[$profile->group_code])) {
                $data[$index]['group_name'] = $candidatesGroups[$profile->group_code];
            } else {
                $data[$index]['group_name'] = '';
            }
        }
        return json_encode($data);
    }
    public function ShowEditManagerForm($electioncode)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $current_election = Election::where('election_code', $electioncode)->first();
            $ElectionRounds = ElectionRound::where('election_code', $electioncode)->get();
            return view('electionmanager', [
                'full_name' => $full_name,
                'current_election' => $current_election, 'election_rounds' => $ElectionRounds
            ]);
        } else {
            return view('welcome');
        }
    }


    public function ShowElectionManagerForm(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $electioncode = $request->election_code;
            $current_election = Election::where('election_code', $electioncode)->first();
            return view('electionmanager', [
                'full_name' => $full_name,
                'current_election' => $current_election
            ]);
        } else {
            return view('welcome');
        }
    }
    public function ShowElectionwithlistManagerForm(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $Profiles = Profiles::all();
            $ListMasters = ListMasters::all();
            return view('electionwithlistsmanager', [
                'full_name' => $full_name,
                'ListMasters' => $ListMasters, 'Profiles' => $Profiles
            ]);
        } else {
            return view('welcome');
        }
    }
    public function ShowElectionLauncherForm(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $Elections = Election::all();
            // Perform the query and group the results by a specific column
            $ElectionRounds = ElectionRound::groupBy('election_code')->pluck('election_code');
            $ElectionRoundsHashMap = array();
            if (isset($ElectionRounds)) {
                foreach ($ElectionRounds as $election_code) {
                    $rounds_arr = ElectionRound::where('election_code', $election_code)->get();
                    foreach ($rounds_arr as $rounds_arr_var) {
                        $ElectionRoundsHashMap[$election_code][] = $rounds_arr_var;
                    }
                }
            }
            return view('electionlauncher', [
                'full_name' => $full_name,
                'Elections' => $Elections, 'ElectionRoundsHashMap' => $ElectionRoundsHashMap
            ]);
        } else {
            return view('welcome');
        }
    }
    public function ShowElectionListForm(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $Elections = Election::leftJoin('list_master', 'elections.election_code', '=', 'list_master.election_code')
                ->select('elections.election_code', 'election_name', 'election_status', 'election_date', 'election_type')
                ->get();
            $electionCodesCount  =  ElectionRound::select('election_code', DB::raw('COUNT(election_code) as electionrounds'))
                ->groupBy('election_code')
                ->get()
                ->pluck('electionrounds', 'election_code')
                ->toArray();
            $candidatesCounts = candidates::select('elections_code', DB::raw('count(profile_code) as candidates_count'))
                ->groupBy('elections_code')
                ->get()
                ->pluck('candidates_count', 'elections_code')
                ->toArray();
            $votersCounts = Voter::select('election_code', DB::raw('count(profile_code) as voters_count'))
                ->groupBy('election_code')
                ->get()
                ->pluck('voters_count', 'election_code')
                ->toArray();

            return view('electionslist', [
                'full_name' => $full_name, 'Elections' => $Elections,
                'electionCodesCount' => $electionCodesCount, 'candidatesCounts' => $candidatesCounts,
                'votersCounts' => $votersCounts
            ]);
        } else {
            return view('welcome');
        }
    }

    public function updateElectionStatus($election_code, $election_status, $codingvar)
    {
        if ($codingvar == 1) {
            users::where('election_code', $election_code)->delete();
        }
        // Perform the update using SQL update query
        DB::table('elections')
            ->where('election_code', $election_code)
            ->update(['election_status' => $election_status]); // Assuming the status is passed in the request
        $this->GenerateUsersForElections($election_code);
        return 1;
    }
    public function updateLaunchstatus($election_code, $round_number, $round_status)
    {
        try {
            DB::unprepared('LOCK TABLES users WRITE');
            DB::beginTransaction();
            // Perform the update using SQL update query

            DB::table('election_rounds')
                ->where('election_code', $election_code)
                ->where('round_number', $round_number)
                ->update(['round_status' => $round_status]); // Assuming the status is passed in the request

            DB::commit();
            DB::unprepared('UNLOCK TABLES');
        } catch (\Exception $e) {
            DB::unprepared('UNLOCK TABLES');
            DB::rollBack();
            throw $e;
        }
        return 1;
    }

    public function saveElectionInfo(Request $request)
    {
        try {
            DB::unprepared('LOCK TABLES elections WRITE');
            DB::beginTransaction();
            // Lock the table for writing
            //================save election
            $election_var = new Election();
            $electionrounds = new ElectionRound();
            // Assign values to the object's properties
            $election_var->election_name = $request->input('election_name');
            $input_election_type1 = ($request->input('input_election_type1') == "on") ? 1 : 0;
            $input_election_type2 = ($request->input('input_election_type2') == "on") ? 1 : 0;
            if ($input_election_type1 == 1) {
                $election_var->election_type = 1;
            }
            if ($input_election_type2 == 1) {
                $election_var->election_type = 2;
            }
            $election_var->election_status = 0; // Example value
            $election_var->election_date = $request->input('input_election_date');
            $electionrounds_array = $request->input('input_rounds_number');
            $electionroundspercent_array = $request->input('input_rounds_percent');
            $electionroundsminpercent_array = $request->input('input_rounds_min_percent');
            $electionroundssign_array = $request->input('input_rounds_sign');

            $image_file = $request->file('election_logo');
            $election_var->logo = "";
            $election_code = $request->input('input_election_code');
            if ($election_code == "") {
                $max_election_Id = Election::max('idelection') + 1;
                $election_var->election_code = 'ele_' . $max_election_Id;

                if (isset($image_file)) {
                    $imageName = $election_var->election_code . '.' . $image_file->getClientOriginalExtension();
                    $election_var->logo = $imageName;
                }
                $election_var->save();
            } else {
                Election::where('election_code', $election_code)
                    ->update([
                        'election_name' => $election_var->election_name,
                        'election_type' => $election_var->election_type,
                        'election_status' => $election_var->election_status,
                        'election_date' => $election_var->election_date
                    ]);
            }
            // Delete ElectionRound records where election_code matches
            ElectionRound::where('election_code', $election_code)->delete();
            $counter = 0;
            if (isset($electionrounds_array)) {
                foreach ($electionrounds_array as $electionround) {
                    $electionroundsarray[$counter]['election_code'] = ($election_code != '') ? $election_code : $election_var->election_code;
                    $electionroundsarray[$counter]['round_number'] = $electionround;
                    $electionroundsarray[$counter]['win_sign'] = $electionroundssign_array[$counter];
                    $cleanedPerc = str_replace('%', '', $electionroundspercent_array[$counter]);
                    $electionroundsarray[$counter]['win_percentage'] = $cleanedPerc;
                    $cleanedminPerc = str_replace('%', '', $electionroundsminpercent_array[$counter]);
                    $electionroundsarray[$counter]['min_win_percentage'] = $cleanedminPerc;
                    $electionroundsarray[$counter]['round_status'] = 0;
                    $counter++;
                }
            }
            if (isset($electionroundsarray)) {
                $electionrounds->insert($electionroundsarray);
            }
            DB::commit();
            DB::unprepared('UNLOCK TABLES');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return back()->with('Success', 'Save success');
    }

    public function GenerateUsersForElections($election_code)
    {
        $userscodes = users::where('isblocked', 0)
        ->where('admin','!=', 1)
        ->pluck('user_code')->toArray();
        $voters_array = Profiles::pluck('profile_code')->ToArray();
        $countervar = 0;
        foreach ($voters_array as $value) {
            $exists = users::where('profile_code', $value)
            ->where('admin','!=', 1)
            ->exists();
            if (!$exists) {
                $generated_user_code = $this->generateUniqueRandomNumber($userscodes);
                $users_array_to_insert[$countervar]['user_code'] = $generated_user_code;
                $users_array_to_insert[$countervar]['election_code'] = $election_code;
                $users_array_to_insert[$countervar]['profile_code'] = $value;
                $users_array_to_insert[$countervar]['isblocked'] = '0';
                $users_array_to_insert[$countervar]['admin'] = '0';
                $isleader = Leader::where('profile_code', $value)->count();
                $users_array_to_insert[$countervar]['isleader'] = $isleader;
                $isvoter = Voter::where('profile_code', $value)->count();
                $users_array_to_insert[$countervar]['isvoter'] = $isvoter;
                array_push($userscodes, $generated_user_code);
                $countervar++;
            }
        }
        if (isset($users_array_to_insert)) {
            $usersObj = new users();
            $usersObj->insert($users_array_to_insert);
        }
    }

    function generateUniqueRandomNumber($array)
    {
        do {
            $randomNumber = sprintf("%04d", rand(0, 9999)); // Generate a random 4-digit number
        } while (in_array($randomNumber, $array)); // Check if the number already exists in the array

        return $randomNumber;
    }

    public function deleteelection(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $election_code = $request->input('modal_election_code');
            // Delete records based on a condition
            Election::where('election_code', $election_code)->delete();
            ElectionRound::where('election_code', $election_code)->delete();
            Voter::where('election_code', $election_code)->delete();
            candidates::where('elections_code', $election_code)->delete();
            CandidatesGroup::where('election_code', $election_code)->delete();
            Leader::where('election_code', $election_code)->delete();
            $Elections = Election::leftJoin('list_master', 'elections.election_code', '=', 'list_master.election_code')
                ->select('elections.election_code', 'election_name', 'election_status', 'election_date', 'election_type')
                ->get();
            $electionCodesCount  =  ElectionRound::select('election_code', DB::raw('COUNT(election_code) as electionrounds'))
                ->groupBy('election_code')
                ->get()
                ->pluck('electionrounds', 'election_code')
                ->toArray();
            $candidatesCounts = candidates::select('elections_code', DB::raw('count(profile_code) as candidates_count'))
                ->groupBy('elections_code')
                ->get()
                ->pluck('candidates_count', 'elections_code')
                ->toArray();
            $votersCounts = Voter::select('election_code', DB::raw('count(profile_code) as voters_count'))
                ->groupBy('election_code')
                ->get()
                ->pluck('voters_count', 'election_code')
                ->toArray();

            return view('electionslist', [
                'full_name' => $full_name, 'Elections' => $Elections,
                'electionCodesCount' => $electionCodesCount, 'candidatesCounts' => $candidatesCounts,
                'votersCounts' => $votersCounts
            ]);
        } else {
            return view('welcome');
        }
    }
}
