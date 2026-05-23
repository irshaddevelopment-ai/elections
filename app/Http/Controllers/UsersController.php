<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profiles;
use App\Models\Admin;
use App\Models\users;
use App\Models\Election;
use App\Models\candidates;
use App\Models\CandidatesGroup;
use App\Models\Voter;
use App\Models\Leader;
use App\Models\ElectionRound;
use App\Models\EventTable;
use App\Models\VotersGroup;
use App\Models\ProfilesInfo;
use App\Models\LeaderVoterRelation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;

class UsersController extends Controller
{

    public function  resetusercode($prfcode)
    {
        try {
            Profiles::where('profile_code', $prfcode)
                ->update(['isconnected' => '0', 'session_handle' => '']);
            // Find the latest logged in datetime for a specific prf_code
            $latestLoggedinDatetime = DB::table('event_table')
                ->select(DB::raw('MAX(loggedin_datetime) as max_loggedin_datetime'))
                ->where('prf_code', $prfcode)
                ->value('max_loggedin_datetime');

            if ($latestLoggedinDatetime) {
                // Update the logged out datetime for the latest event
                DB::table('event_table')
                    ->where('prf_code', $prfcode)
                    ->where('loggedin_datetime', $latestLoggedinDatetime)
                    ->update(['loggedout_datetime' => now()]);
            }
            return 1;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            
        }
    }

    public function getevents($prfcode)
    {
        $events = EventTable::where('prf_code', $prfcode)->get();
        $fullname = Profiles::where('profile_code', $prfcode)->value('full_name');
        $data['full_name'] = $fullname;
        $data['events'] = $events;
        return $data;
    }
    public function getidcard($prfcode, $election_code)
    {
        $idcard = Profiles::select(
            'profiles.profile_code',
            'profiles.full_name',
            'profiles.picture',
            'profiles.age',
            'profiles.address',
            'profiles_infos.residence',
            'profiles_infos.joiningdate',
            'profiles_infos.category',
            'profiles_infos.nominate',
            'profiles_infos.candidate',
            'profiles_infos.nationality',
            'profiles_infos.social_situation',
            'profiles_infos.children',
            'profiles_infos.children_age',
            'profiles_infos.education',
            'profiles_infos.current_work',
            'profiles_infos.word_about_association',
            'profiles_infos.work_at_association',
            'profiles_infos.identifiers'
        )
            ->leftJoin('profiles_infos', 'profiles.profile_code', '=', 'profiles_infos.profile_code')
            ->where('profiles.profile_code', $prfcode)
            ->first();
        $elections_lists = CandidatesGroup::where('election_code', $election_code)
            ->select('group_code', 'group_name')->get();
        $data['idcard'] = $idcard;
        $data['elections_lists'] = $elections_lists;
        return $data;
    }

    public function showidentificationcard()
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $profilecode = session('profile_code', '');
            $electioncode = Election::where('election_status', '1')->value('election_code');
            // Retrieve the profile where profile_code equals a specific value
            $current_profile = Profiles::where('profile_code', $profilecode)->first();
            $electionobj = Election::where('election_code', $electioncode)->get()->first();
            $election_rounds = ElectionRound::where('election_code', $electioncode)->select('round_number')->get();
            return view('identificationcard', [
                'full_name' => $full_name, 'current_profile' => $current_profile,
                'electionobj' => $electionobj, 'election_rounds' => $election_rounds
            ]);
        } else {
            return view('welcome');
        }
    }

    public function showadminresults()
    {
        DB::statement("SET sql_mode=''");
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $profilecode = session('profile_code', '');
            $electioncode = Election::where('election_status', '1')->value('election_code');
            $candidategroup = CandidatesGroup::where('election_code', $electioncode)
                ->select('group_name', 'group_code')->get();
            // Retrieve the profile where profile_code equals a specific value
            $current_profile = Profiles::where('profile_code', $profilecode)->first();
            $electionobj = Election::where('election_code', $electioncode)->get()->first();
            $election_rounds = ElectionRound::where('election_code', $electioncode)->select('round_number')->get();
           
            $curr_election_rounds = ElectionRound::where('election_code', $electioncode)
            ->where('round_status', 2)
    ->orderBy('round_number', 'desc')
    ->first();
            return view('adminresults', [
                'full_name' => $full_name, 'current_profile' => $current_profile,
                'electionobj' => $electionobj, 'election_rounds' => $election_rounds, 
                'candidategroup' => $candidategroup,'curr_election_rounds'=>$curr_election_rounds
            ]);
        } else {
            return view('welcome');
        }
    }
    public function ShowUserManagerForm(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $profilecode = $request->profile_code;
            // Retrieve the profile where profile_code equals a specific value
            $current_profile = Profiles::where('profile_code', $profilecode)->first();
            return view('usermanager', ['full_name' => $full_name, 'current_profile' => $current_profile]);
        } else {
            return view('welcome');
        }
    }
    public function ShowEditUserManagerForm($profilecode)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            // Retrieve the profile where profile_code equals a specific value
            $current_profile = Profiles::where('profile_code', $profilecode)->first();
            $isadmin = Admin::where('profile_code', $profilecode)->exists();
            return view('usermanager', ['full_name' => $full_name, 'current_profile' => $current_profile, 'isadmin' => $isadmin]);
        } else {
            return view('welcome');
        }
    }
    public function ShowUsersListForm(Request $request)
    {
        DB::statement("SET sql_mode=''");
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $current_election = Election::where('election_status', '1')->get()->first();
            $election_code = isset($current_election->election_code) ? $current_election->election_code : '';
            $Profiles = DB::table('profiles')
                ->select('profiles.*', 'users.user_code')
                ->leftJoin('users', function ($join) use ($election_code) {
                    $join->on('profiles.profile_code', '=', 'users.profile_code')
                        ->where('users.election_code', '=', $election_code);
                })
                ->groupBy('profiles.profile_code')
                ->get();
            return view('userslist', ['full_name' => $full_name, 'Profiles' => $Profiles]);
        } else {
            return view('welcome');
        }
    }
    public function deleteUser(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $profilecode = $request->input('modal_profile_code');
            // Delete records based on a condition
            Profiles::where('profile_code', $profilecode)->delete();
            Admin::where('profile_code', $profilecode)->delete();
            users::where('profile_code', $profilecode)->delete();
            $Profiles = Profiles::all();
            return view('userslist', ['full_name' => $full_name, 'Profiles' => $Profiles]);
        } else {
            return view('welcome');
        }
    }

    public function importexcel(Request $request)
    {
        if ($request->hasFile('excelFile')) {
            try {
                DB::unprepared('LOCK TABLES profiles WRITE');
                DB::beginTransaction();
                $file = $request->file('excelFile');

                $profile_var = new profiles();
                $election_var = new Election();
                $candidate_var = new candidates();
                $candidategroup_var = new CandidatesGroup();
                $votergroup_var = new VotersGroup();
                $voter_var = new Voter();
                $leader_var = new Leader();
                $electionround = new ElectionRound();
                $leadervoterrelation = new LeaderVoterRelation();

                // Process and save the file as needed
                // Example: Save to storage or process the Excel file
                $path = $file->storeAs('excel', $file->getClientOriginalName());
                $real_path = $request->file('excelFile')->getRealPath();
                $data = Excel::toArray([], $path)[0];
                $firstRow = $data[0]; // Get the first row from the array
                $full_name_index = array_search('الاسم', $firstRow);
                $mobile_index = array_search('رقم الهاتف', $firstRow);
                $sex_index = array_search('الجنس', $firstRow);
                $age_index = array_search('العمر', $firstRow);
                $address_index = array_search('العنوان', $firstRow);
                $picture_index = array_search('الصورة', $firstRow);
                $attach_index = array_search('التعريف', $firstRow);
                $election_name_index = array_search('العملية الإنتخابية', $firstRow);
                $election_type_index = array_search('نوع العملية', $firstRow);
                $election_date_index = array_search('تاريخ العملية', $firstRow);
                $iscandidate_index = array_search('مرشح', $firstRow);
                $isvoter_index = array_search('ناخب', $firstRow);
                $candidate_gourp_name_index = array_search('اللائحة', $firstRow);
                $win_number_index = array_search('العدد المطلوب للنجاح', $firstRow);
                $rounds_index = array_search('عدد الجولات', $firstRow);
                $voters_gourp_name_index = array_search('المجموعة', $firstRow);
                $leader_index = array_search('المرشد', $firstRow);
                // Remove the first row from the array
                array_shift($data);
                // Return a response, you might want to send more information
                $counter = 0;
                $counter_election = 0;
                $counter_candidate = 0;
                $counter_candidatetgroup = 0;
                $counter_votertgroup = 0;
                $counter_voter = 0;
                $counter_leader = 0;
                $max_profile_Id = profiles::max('idprofile');
                $max_profile_array_Id = profiles::max('idprofile');
                $max_election_Id = Election::max('idelection');
                $max_candidategroup_Id = CandidatesGroup::max('idgroups');
                $max_votergroup_Id = VotersGroup::max('idvoters_group');
                //====================old values
                $old_election_name = '';
                $old_candidategroup_name = '';
                $old_voter_gourp_name = '';
                $old_leader_name = '';
                $profiles_ids_array = array();
                //===========arrays
                $array_to_insert = array();
                $electionarray_to_insert = array();
                $electionroundsarray_to_insert = array();
                $votergrouparray_to_insert = array();
                $candidategrouparray_to_insert = array();
                $candidatearray_to_insert = array();
                $leaderarray_to_insert = array();
                $leadervoter_rel_array_to_insert = array();
                $votersarray_to_insert = array();
                //=====================
                foreach ($data as $value) {
                    $max_profile_array_Id++;
                    $profiles_ids_array[$value[$full_name_index]] = $max_profile_array_Id;
                }
                foreach ($data as $value) {
                    $max_profile_Id++;
                    //=================add profiles========================
                    $exists = profiles::where(['full_name' => $data[$counter][$full_name_index]])->exists();
                    $exists_in_array = in_array($data[$counter][$full_name_index], array_column($array_to_insert, 'full_name'));
                    if (($exists == false) && ($exists_in_array == false)) {
                        $profile_code_var = $max_profile_Id;
                        $array_to_insert[$counter]["profile_code"] = $profile_code_var;
                        $array_to_insert[$counter]["full_name"] = $data[$counter][$full_name_index];
                        $array_to_insert[$counter]["mobile"] = $data[$counter][$mobile_index];
                        $sex = 1;
                        if ($data[$counter][$sex_index] != 'ذكر') {
                            $sex = 2;
                        }
                        $array_to_insert[$counter]["sex"] = $sex;
                        $age = $data[$counter][$age_index];
                        $currentYear = date('Y');
                        $birthYear = $currentYear - $age;
                        $date = $birthYear . date('-m-d');
                        $array_to_insert[$counter]["age"] = $date;
                        $array_to_insert[$counter]["picture"] = $data[$counter][$picture_index];
                        $array_to_insert[$counter]["attachment"] = $data[$counter][$attach_index];
                    } else {
                        $profile_code_var = profiles::where(['full_name' => $data[$counter][$full_name_index]])->value('profile_code');
                    }
                    //=================add profiles========================
                    //==============add elections
                    $election_name = $data[$counter][$election_name_index];
                    $election_type = ($data[$counter][$election_type_index] == 'فردية') ? '1' : '2';
                    $election_date = $data[$counter][$election_date_index];
                    $iscandidate = $data[$counter][$iscandidate_index];
                    $isvoter = $data[$counter][$isvoter_index];
                    $candidate_gourp_name = $data[$counter][$candidate_gourp_name_index];
                    $voter_gourp_name = $data[$counter][$voters_gourp_name_index];
                    // Convert Unix timestamp to MySQL date format
                    $UNIX_DATE = ($election_date - 25569) * 86400;
                    $mysqlDate = gmdate("Y-m-d H:i:s", $UNIX_DATE);

                    if ($election_name != '') {
                        //if ($old_election_name != $election_name) {
                        if (1 == 1) {
                            $exists = Election::where(['election_name' => $data[$counter][$election_name_index]])->exists();
                            $exists_in_array = in_array($data[$counter][$election_name_index], array_column($electionarray_to_insert, 'election_name'));
                            if (($exists == false) && ($exists_in_array == false)) {
                                $max_election_Id++;
                                $election_code_var = 'ele_' . $max_election_Id;
                                $electionarray_to_insert[$counter_election]["election_code"] = $election_code_var;
                                $electionarray_to_insert[$counter_election]["election_name"] = $election_name;
                                $electionarray_to_insert[$counter_election]["election_type"] = $election_type;
                                $electionarray_to_insert[$counter_election]["election_status"] = 0;
                                $electionarray_to_insert[$counter_election]["election_date"] = $mysqlDate;
                                if ($election_type == 'فردية') {
                                    $electionarray_to_insert[$counter_election]["win_number"] =
                                        $data[$counter][$win_number_index];
                                }
                                $rounds_number = $data[$counter][$rounds_index];
                                $rounds_number_data = json_decode($rounds_number, true);

                                $round_counter_var = 0;
                                foreach ($rounds_number_data as $key => $values) {
                                    $electionroundsarray_to_insert[$round_counter_var]["election_code"] = $election_code_var;
                                    $electionroundsarray_to_insert[$round_counter_var]["round_number"] = $key;
                                    $electionroundsarray_to_insert[$round_counter_var]["win_percentage"] = $values[0];
                                    $electionroundsarray_to_insert[$round_counter_var]["min_win_percentage"] = $values[1];
                                    $electionroundsarray_to_insert[$round_counter_var]["win_sign"] = 1;
                                    $electionroundsarray_to_insert[$round_counter_var]["round_status"] = 0;
                                    $round_counter_var++;
                                }
                            } else {

                                $election_code_var = array_column(
                                    $electionarray_to_insert,
                                    'election_code'
                                )[array_search($data[$counter][$election_name_index], array_column($electionarray_to_insert, 'election_name'))];
                            }
                            $old_election_name = $election_name;
                            $counter_election++;
                        }
                    }
                    $exists_user = profiles::where(['full_name' => $data[$counter][$full_name_index]])->exists();
                    $exists_election = Election::where(['election_name' => $data[$counter][$election_name_index]])->exists();
                    if (1 == 1) {
                        //======================candidates groups
                        if ($voter_gourp_name != '') {
                            //if ($old_voter_gourp_name != $voter_gourp_name) {
                            if (1 == 1) {
                                $exists = VotersGroup::where(['voter_group_name' => $voter_gourp_name, 'election_code' => $election_code_var])->exists();
                                $exists_in_array = array_reduce($votergrouparray_to_insert, function ($carry, $item) use ($voter_gourp_name, $election_code_var) {
                                    return $carry || ($item["voter_group_name"] == $voter_gourp_name && $item["election_code"] == $election_code_var);
                                }, false);
                                if (($exists == false) && ($exists_in_array == false)) {
                                    $max_votergroup_Id++;
                                    $votergroup_code_var = $max_votergroup_Id;
                                    $votergrouparray_to_insert[$counter_votertgroup]["voter_group_code"] = $votergroup_code_var;
                                    $votergrouparray_to_insert[$counter_votertgroup]["voter_group_name"] = $voter_gourp_name;
                                    $votergrouparray_to_insert[$counter_votertgroup]["election_code"] = $election_code_var;
                                    $votergrouparray_to_insert[$counter_votertgroup]["description"] = "";
                                } else {

                                    $votergroup_code_var = array_column(
                                        $votergrouparray_to_insert,
                                        'voter_group_code'
                                    )[array_search($voter_gourp_name, array_column($votergrouparray_to_insert, 'voter_group_name'))];
                                }
                                $old_voter_gourp_name = $voter_gourp_name;
                                $counter_votertgroup++;
                            }
                        }
                        //======================candidates groups
                        if ($candidate_gourp_name != '') {
                            //if ($old_candidategroup_name != $candidate_gourp_name) {
                            if (1 == 1) {
                                $exists = CandidatesGroup::where(['group_name' => $candidate_gourp_name, 'election_code' => $election_code_var])->exists();
                                $exists_in_array = array_reduce($candidategrouparray_to_insert, function ($carry, $item) use ($candidate_gourp_name, $election_code_var) {
                                    return $carry || ($item["group_name"] == $candidate_gourp_name && $item["election_code"] == $election_code_var);
                                }, false);
                                if (($exists == false) && ($exists_in_array == false)) {
                                    $max_candidategroup_Id++;
                                    $candidategroup_code_var = $max_candidategroup_Id;
                                    $candidategrouparray_to_insert[$counter_candidatetgroup]["group_name"] = $candidate_gourp_name;
                                    $candidategrouparray_to_insert[$counter_candidatetgroup]["election_code"] = $election_code_var;
                                    $candidategrouparray_to_insert[$counter_candidatetgroup]["group_code"] = $candidategroup_code_var;
                                    $candidategrouparray_to_insert[$counter_candidatetgroup]["win_number"] =
                                        $data[$counter][$win_number_index];
                                } else {
                                    $candidategroup_code_var = array_column(
                                        $candidategrouparray_to_insert,
                                        'group_code'
                                    )[array_search($candidate_gourp_name, array_column($candidategrouparray_to_insert, 'group_name'))];
                                }
                                $old_candidategroup_name = $candidate_gourp_name;
                                $counter_candidatetgroup++;
                            }
                            if ($iscandidate == 1) {
                                $exists = candidates::where(['profile_code' => $profile_code_var, 'elections_code' => $election_code_var])->exists();
                                $exists_in_array = array_reduce($candidatearray_to_insert, function ($carry, $item) use ($profile_code_var, $election_code_var) {
                                    return $carry || ($item["profile_code"] == $profile_code_var && $item["elections_code"] == $election_code_var);
                                }, false);
                                if (($exists == false) && ($exists_in_array == false)) {
                                    $candidatearray_to_insert[$counter_candidate]["profile_code"] = $profile_code_var;
                                    $candidatearray_to_insert[$counter_candidate]["elections_code"] = $election_code_var;
                                    $candidatearray_to_insert[$counter_candidate]["candidate_status"] = 1;
                                    $candidatearray_to_insert[$counter_candidate]["round_number"] = 1;
                                    if ($candidate_gourp_name != '') {
                                        $candidatearray_to_insert[$counter_candidate]["group_code"] = $candidategroup_code_var;
                                    }
                                    $counter_candidate++;
                                }
                            }
                        }
                        //============add leaders
                        $leader_name = $data[$counter][$leader_index];
                        if (($leader_name != '') && ($voter_gourp_name != '')) {
                            //if ($old_leader_name != $leader_name) {
                            if (1 == 1) {
                                $leader_prf_code = $profiles_ids_array[$data[$counter][$leader_index]];
                                $exists = Leader::where([
                                    'profile_code' => $leader_prf_code,
                                    'election_code' => $election_code_var, 'voter_group_code' => $votergroup_code_var
                                ])->exists();
                                $exists_in_array = array_reduce($leaderarray_to_insert, function ($carry, $item) use ($leader_prf_code, $election_code_var, $votergroup_code_var) {
                                    return $carry || ($item["profile_code"] == $leader_prf_code && $item["election_code"] == $election_code_var && $item["voter_group_code"] == $votergroup_code_var);
                                }, false);
                                if (($exists == false) && ($exists_in_array == false)) {
                                    $leaderarray_to_insert[$counter_leader]["profile_code"] = $profiles_ids_array[$data[$counter][$leader_index]];
                                    $leaderarray_to_insert[$counter_leader]["voter_group_code"] = $votergroup_code_var;
                                    $leaderarray_to_insert[$counter_leader]["election_code"] = $election_code_var;
                                    $counter_leader++;
                                }
                                $old_leader_name = $leader_name;
                            }
                            $exists = leadervoterrelation::where([
                                'leader_profile_code' => $leader_prf_code,
                                'election_code' => $election_code_var, 'voter_group_code' => $votergroup_code_var,
                                'voter_profile_code' => $profile_code_var
                            ])->exists();
                            $exists_in_array = array_reduce($leadervoter_rel_array_to_insert, function ($carry, $item) use ($leader_prf_code, $election_code_var, $votergroup_code_var, $profile_code_var) {
                                return $carry || ($item["leader_profile_code"] == $leader_prf_code && $item["election_code"] == $election_code_var && $item["voter_group_code"] == $votergroup_code_var && $item["voter_profile_code"] == $profile_code_var);
                            }, false);
                            if (($exists == false) && ($exists_in_array == false)) {
                                $leadervoter_rel_array_to_insert[$counter_leader]["leader_profile_code"] = $profiles_ids_array[$data[$counter][$leader_index]];
                                $leadervoter_rel_array_to_insert[$counter_leader]["voter_group_code"] = $votergroup_code_var;
                                $leadervoter_rel_array_to_insert[$counter_leader]["election_code"] = $election_code_var;
                                $leadervoter_rel_array_to_insert[$counter_leader]["voter_profile_code"] = $profile_code_var;
                                $counter_leader++;
                            }
                        }
                        //==============add voters
                        if ($isvoter == 1) {
                            $exists = Voter::where(['profile_code' => $profile_code_var, 'election_code' => $election_code_var])->exists();
                            $exists_in_array = array_reduce($votersarray_to_insert, function ($carry, $item) use ($profile_code_var, $election_code_var) {
                                return $carry || ($item["profile_code"] == $profile_code_var && $item["election_code"] == $election_code_var);
                            }, false);
                            if (($exists == false) && ($exists_in_array == false)) {
                                $votersarray_to_insert[$counter_voter]["profile_code"] = $profile_code_var;
                                $votersarray_to_insert[$counter_voter]["election_code"] = $election_code_var;
                                $votersarray_to_insert[$counter_voter]["voter_status"] = 1;
                                $votersarray_to_insert[$counter_voter]["voter_group_code"] = $votergroup_code_var;
                            }
                            $counter_voter++;
                        }
                    }
                    $counter++;
                }
                if (isset($array_to_insert)) {
                    $profile_var->insert($array_to_insert);
                }
                if (isset($electionarray_to_insert)) {
                    $election_var->insert($electionarray_to_insert);
                }
                if (isset($electionroundsarray_to_insert)) {
                    $electionround->insert($electionroundsarray_to_insert);
                }
                if (isset($candidategrouparray_to_insert)) {
                    $candidategroup_var->insert($candidategrouparray_to_insert);
                }
                if (isset($votergrouparray_to_insert)) {
                    $votergroup_var->insert($votergrouparray_to_insert);
                }
                if (isset($candidatearray_to_insert)) {
                    $candidate_var->insert($candidatearray_to_insert);
                }
                if (isset($votersarray_to_insert)) {
                    $voter_var->insert($votersarray_to_insert);
                }
                if (isset($leaderarray_to_insert)) {
                    $leader_var->insert($leaderarray_to_insert);
                }
                if (isset($leadervoter_rel_array_to_insert)) {
                    $leadervoterrelation->insert($leadervoter_rel_array_to_insert);
                }
                DB::commit();
                DB::unprepared('UNLOCK TABLES');
                //return 1;
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
                //return $e;
            }
        }
        //====================================
        if ($request->hasFile('cardFile')) {
            try {
                DB::unprepared('LOCK TABLES profiles WRITE');
                DB::unprepared('LOCK TABLES profiles_infos WRITE');
                DB::beginTransaction();
                $file = $request->file('cardFile');
                $profile_var = new profiles();
                $profile_info_var = new ProfilesInfo();
                $path = $file->storeAs('excel', $file->getClientOriginalName());
                $real_path = $request->file('excelFile')->getRealPath();
                $data = Excel::toArray([], $path)[0];
                $firstRow = $data[0]; // Get the first row from the array
                $full_name_index = array_search('الاسم', $firstRow);
                $joiningdate_index = array_search('تاريخ الانتساب', $firstRow);
                $category_index = array_search('الفئة', $firstRow);
                $age_index = array_search('المواليد', $firstRow);
                $nationality_index = array_search('الجنسية', $firstRow);
                $residence_index = array_search('محل السكن الحالي', $firstRow);
                $socialsituation_index = array_search('الوضع الاجتماعي', $firstRow);
                $childs_index = array_search('عدد الأولاد', $firstRow);
                $education_index = array_search('التعليم', $firstRow);
                $currentwork_index = array_search('العمل الحالي', $firstRow);
                $work_index = array_search('WORK', $firstRow);
                $indentifier_index = array_search('أذكر اسم شخص أو أكثر من الجمعية يعرفون عنك', $firstRow);
                $wordaboutassoc_index = array_search('كلمة عن الجمعية', $firstRow);
                $childsage_index = array_search('أعمار الأولاد بالترتيب ', $firstRow);
                // Remove the first row from the array
                array_shift($data);
                $counter = 0;
                $array_to_insert = array();
                foreach ($data as $value) {
                    //=================add profiles infos========================
                    $profile_code = profiles::where(['full_name' => $data[$counter][$full_name_index]])->pluck('profile_code')->first();
                    if (isset($profile_code)) {
                        $exists = ProfilesInfo::where(['profile_code' => $profile_code])->exists();
                        if ($exists == false) {
                            $array_to_insert[$counter]["profile_code"] = $profile_code;
                            $array_to_insert[$counter]["residence"] = $data[$counter][$residence_index];
                            $joiningdate = $data[$counter][$joiningdate_index];
                            // Convert Excel serial number to Unix timestamp
                            $join_dateunixTimestamp = ($joiningdate - 25569) * 86400;
                            $join_date = date("Y-m-d",  $join_dateunixTimestamp);
                            $array_to_insert[$counter]["joiningdate"] = $join_date;
                            $array_to_insert[$counter]["category"] = $data[$counter][$category_index];
                            $array_to_insert[$counter]["nationality"] = $data[$counter][$nationality_index];
                            $array_to_insert[$counter]["social_situation"] = $data[$counter][$socialsituation_index];
                            $array_to_insert[$counter]["children"] = $data[$counter][$childs_index];
                            $array_to_insert[$counter]["children_age"] = $data[$counter][$childsage_index];
                            $array_to_insert[$counter]["education"] = $data[$counter][$education_index];
                            $array_to_insert[$counter]["current_work"] = $data[$counter][$currentwork_index];
                            $array_to_insert[$counter]["word_about_association"] = $data[$counter][$wordaboutassoc_index];
                            $array_to_insert[$counter]["work_at_association"] = $data[$counter][$work_index];
                            $array_to_insert[$counter]["identifiers"] = $data[$counter][$indentifier_index];

                            $age = $data[$counter][$age_index];
                            $ageunixTimestamp = ($age - 25569) * 86400;
                            $date = date("Y-m-d",  $ageunixTimestamp);
                            profiles::where('profile_code', $profile_code)
                                ->update(['address' => $data[$counter][$residence_index],'age' => $date]);
                        }
                    }
                    $counter++;
                }
                if (isset($array_to_insert)) {
                    $profile_info_var->insert($array_to_insert);
                }
                DB::commit();
                DB::unprepared('UNLOCK TABLES');
                return 1;
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }

        return response()->json(['success' => true]);
    }
    public function importCardInfo(Request $request)
    {
        if (!$request->hasFile('cardInfoFile')) {
            return response()->json(['message' => 'No file provided.'], 400);
        }
        try {
            $file = $request->file('cardInfoFile');
            $path = $file->storeAs('excel', $file->getClientOriginalName());
            $data = Excel::toArray([], $path)[0];

            $firstRow = array_map(fn($h) => is_string($h) ? trim($h) : $h, $data[0]);
            array_shift($data);

            $columns = [
                'full_name'    => 'الاسم',
                'joiningdate'  => 'تاريخ الانتساب',
                'category'     => 'الفئة',
                'birthdate'    => 'المواليد',
                'nationality'  => 'الجنسية',
                'residence'    => 'محل السكن الحالي',
                'social'       => 'الوضع الاجتماعي',
                'children'     => 'عدد الأولاد',
                'education'    => 'التعليم',
                'current_work' => 'العمل الحالي',
                'work'         => 'WORK',
                'identifiers'  => 'أذكر اسم شخص أو أكثر من الجمعية يعرفون عنك',
                'word'         => 'كلمة عن الجمعية',
                'children_age' => 'أعمار الأولاد بالترتيب',
            ];
            $idx = [];
            foreach ($columns as $key => $header) {
                $idx[$key] = array_search($header, $firstRow);
            }

            $imported  = 0;
            $not_found = [];
            $errors    = [];

            foreach ($data as $row) {
                $name = $idx['full_name'] !== false ? trim($row[$idx['full_name']] ?? '') : '';
                if ($name === '') continue;

                $profile_code = Profiles::where('full_name', $name)->value('profile_code');
                if (!$profile_code) {
                    $not_found[] = $name;
                    continue;
                }

                try {
                    $join_date  = $this->parseExcelDate($idx['joiningdate']  !== false ? ($row[$idx['joiningdate']]  ?? null) : null);
                    $birth_date = $this->parseExcelDate($idx['birthdate']     !== false ? ($row[$idx['birthdate']]    ?? null) : null);

                    ProfilesInfo::updateOrCreate(
                        ['profile_code' => $profile_code],
                        [
                            'residence'              => $idx['residence']    !== false ? ($row[$idx['residence']]    ?? null) : null,
                            'joiningdate'            => $join_date,
                            'category'               => $idx['category']     !== false ? ($row[$idx['category']]     ?? null) : null,
                            'nationality'            => $idx['nationality']   !== false ? ($row[$idx['nationality']]  ?? null) : null,
                            'social_situation'       => $idx['social']        !== false ? ($row[$idx['social']]       ?? null) : null,
                            'children'               => $idx['children']      !== false ? ($row[$idx['children']]     ?? null) : null,
                            'children_age'           => $idx['children_age']  !== false ? ($row[$idx['children_age']] ?? null) : null,
                            'education'              => $idx['education']     !== false ? ($row[$idx['education']]    ?? null) : null,
                            'current_work'           => $idx['current_work']  !== false ? ($row[$idx['current_work']] ?? null) : null,
                            'word_about_association' => $idx['word']          !== false ? ($row[$idx['word']]         ?? null) : null,
                            'work_at_association'    => $idx['work']          !== false ? ($row[$idx['work']]         ?? null) : null,
                            'identifiers'            => $idx['identifiers']   !== false ? ($row[$idx['identifiers']]  ?? null) : null,
                        ]
                    );

                    if ($birth_date) {
                        Profiles::where('profile_code', $profile_code)
                            ->update(['age' => $birth_date, 'address' => $idx['residence'] !== false ? ($row[$idx['residence']] ?? null) : null]);
                    }

                    $imported++;
                } catch (\Exception $rowEx) {
                    $errors[] = $name . ': ' . $rowEx->getMessage();
                }
            }

            return response()->json([
                'imported'  => $imported,
                'not_found' => $not_found,
                'errors'    => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function parseExcelDate($value): ?string
    {
        if ($value === null || $value === '') return null;
        // Excel serial number (e.g. 38353)
        if (is_numeric($value) && $value > 1000) {
            return date('Y-m-d', ($value - 25569) * 86400);
        }
        // String date: try common formats
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function saveuserinfo(Request $request)
    {
        try {
            DB::unprepared('LOCK TABLES profiles WRITE');
            DB::beginTransaction();
            // Lock the table for writing
            //================save profile
            $profile_var = new profiles();
            $profile_var->full_name = $request->input('input_fullname');
            $profile_var->age = $request->input('input_age');
            $profile_var->mobile = $request->input('input_tel');
            $profile_var->address = $request->input('input_address');
            $profile_var->sex = $request->input('sex');

            $profile_code = $request->input('input_profile_code');
            $isadmin = ($request->input('input_users_status') == "on") ? 1 : 0;

            $image_file = $request->file('profile_picture');
            $attach_file = $request->file('input_profile_attach');
            $profile_var->picture = "";
            $profile_var->attachment = "";

            if ($profile_code == "") {
                $max_profile_Id = profiles::max('idprofile') + 1;
                $profile_var->profile_code = $max_profile_Id;

                if (isset($image_file)) {
                    $imageName = $profile_var->profile_code . '.' . $image_file->getClientOriginalExtension();
                    $profile_var->picture = $imageName;
                }
                if (isset($attach_file)) {
                    $attachname = $profile_var->profile_code . '.' . $attach_file->getClientOriginalExtension();
                    $profile_var->attachment = $attachname;
                }
                $profile_var->save();
            } else {
                if (isset($image_file)) {
                    $imageName = $profile_code . '.' . $image_file->getClientOriginalExtension();
                    $profile_var->picture = $imageName;
                }
                if (isset($attach_file)) {
                    $attachname = $profile_code . '.' . $attach_file->getClientOriginalExtension();
                    $profile_var->attachment = $attachname;
                }
                $updateData = [
                    'full_name' => $profile_var->full_name,
                    'age' => $profile_var->age,
                    'mobile' => $profile_var->mobile,
                    'address' => $profile_var->address,
                    'sex' => $profile_var->sex
                ];

                // Check if picture is not empty and add it to the update data
                if (!empty($profile_var->picture)) {
                    $updateData['picture'] = $profile_var->picture;
                }
                // Check if attachment is not empty and add it to the update data
                if (!empty($profile_var->attachment)) {
                    $updateData['attachment'] = $profile_var->attachment;
                }

                profiles::where('profile_code', $profile_code)->update($updateData);
            }
            if (isset($image_file)) {
                $fileName = $image_file->getClientOriginalName();
                //$profile_var->picture = $fileName;

                // Validate the file if needed
                $request->validate([
                    'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
                ]);
                // Specify the destination folder
                $destinationPath = public_path('profile_picture');
                // Check if the file with the same name already exists
                $existingFile = $destinationPath . '/' . $imageName;

                if (File::exists($existingFile)) {
                    // If it exists, delete the existing file
                    File::delete($existingFile);
                }

                // Move the uploaded file to the destination folder
                $image_file->move($destinationPath, $imageName);
            }
            if (isset($attach_file)) {
                $fileName = $attach_file->getClientOriginalName();
                // $profile_var->attachment = $fileName;

                // Specify the destination folder
                $destinationPath = public_path('profile_attachment');
                // Check if the file with the same name already exists
                $existingFile = $destinationPath . '/' . $attachname;

                if (File::exists($existingFile)) {
                    // If it exists, delete the existing file
                    File::delete($existingFile);
                }

                // Move the uploaded file to the destination folder
                $attach_file->move($destinationPath, $attachname);
            }
            //=============================================================
            if ($isadmin == 1) {
                $Admin_var = new Admin();
                $generated_code = $this->GenerateUsersAdmin();
                $Admin_var->user_code = $generated_code;
                $Admin_var->profile_code = $profile_code = ($profile_code == "") ? $profile_var->profile_code : $profile_code;
                $Admin_var->status = $isadmin;
                $Admin_var->save();

                $users_var = new users();
                $users_var->user_code = $generated_code;
                $users_var->profile_code = $profile_code = ($profile_code == "") ? $profile_var->profile_code : $profile_code;
                $users_var->admin = $isadmin;
                $users_var->isblocked = 0;
                $users_var->election_code = '';
                $users_var->save();
            } else {
                if (isset($profile_code)) {
                    $Admin_var = Admin::where('profile_code', $profile_code)->first();
                    if (isset($Admin_var)) {
                        $Admin_var->delete();
                    }

                    $users_var = users::where('profile_code', $profile_code)
                        ->where('admin', 1)
                        ->first();
                    if (isset($users_var)) {
                        $users_var->delete();
                    }
                }
            }

            DB::commit();
            DB::unprepared('UNLOCK TABLES');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return back()->with('Success', 'Save success');
    }

    public function GenerateUsersAdmin()
    {
        $adminscode = Admin::where('status', 1)->pluck('user_code')->toArray();
        do {
            $randomNumber = sprintf("%04d", rand(0, 9999)); // Generate a random 4-digit number
        } while (in_array($randomNumber, $adminscode)); // Check if the number already exists in the array

        return $randomNumber;
    }

    public function uploadusersfolder(Request $request)
    {
        $defaultpicture_folder = env('APP_DEFAULTPICTURE_PATH', 'profile_picture');
        $defaultattachement_folder = env('APP_DEFAULTATTACHEMENT_PATH', 'profile_attachment');

        if (!File::exists(public_path($defaultpicture_folder))) {
            File::makeDirectory(public_path($defaultpicture_folder), 0755, true, true);
        }
        if (!File::exists(public_path($defaultattachement_folder))) {
            File::makeDirectory(public_path($defaultattachement_folder), 0755, true, true);
        }

        if (!$request->hasFile('files')) {
            return response()->json(['message' => 'No files to upload'], 400);
        }

        $files     = $request->file('files');
        $updated   = 0;
        $unmatched = [];

        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            $file->move(public_path($defaultpicture_folder), $fileName);

            $nameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);
            $profile = Profiles::where('full_name', $nameWithoutExt)->first();
            if ($profile) {
                $profile->picture = $fileName;
                $profile->save();
                $updated++;
            } else {
                $unmatched[] = $nameWithoutExt;
            }
        }

        return response()->json([
            'message'   => 'Files uploaded successfully',
            'updated'   => $updated,
            'unmatched' => $unmatched,
        ]);
    }

    public function saveprofileextrainfo(Request $request)
    {
        try {
            DB::unprepared('LOCK TABLES profiles WRITE');
            DB::beginTransaction();
            $profilecode = $request->input('idcard_input_profile_code');
            $fullname = $request->input('idcard_input_fullname');
            $age = $request->input('idcard_input_age');
            $nationality = $request->input('idcard_input_nationality');
            $category = $request->input('idcard_input_category');
            $social_situation = $request->input('idcard_input_social_situation');
            $children = $request->input('idcard_input_children');
            $children_age = $request->input('idcard_input_children_age');
            $residence = $request->input('idcard_input_residence');
            $education = $request->input('idcard_input_education');
            $current_work = $request->input('idcard_input_current_work');
            $joiningdate = $request->input('idcard_input_joiningdate');
            $identifiers = $request->input('idcard_input_identifiers');
            $word_about_association = $request->input('idcard_input_word_about_association');

           
            $from_year = $request->input('input_from_year');
            $to_year = $request->input('input_to_year');
            $work_description = $request->input('work_description');
            $workarrayOfArrays = [];

            // Assuming all arrays have the same length
            if ((isset($from_year)) && (isset($to_year))) {
                for ($i = 0; $i < count($from_year); $i++) {
                    $work_desc = isset($work_description[$i]) ? $work_description[$i] : '';
                    $innerArray = [
                        "from_year" => $this->faTOen($from_year[$i]) != "" ?$this->faTOen($from_year[$i]) : "",
                        "to_year" => $this->faTOen($to_year[$i]) != "" ?$this->faTOen($to_year[$i]) : "",
                        "work_description" => $work_desc
                    ];
                    $workarrayOfArrays[] = $innerArray;
                }
            }

            $image_file = $request->file('profile_picture');


            $imageName = '';
            if (isset($image_file)) {
                $imageName = $profilecode . '.' . $image_file->getClientOriginalExtension();
            }

            $propfileexists = profiles::where('profile_code', $profilecode)->exists();
            if ($propfileexists) {
                $updateData = [
                    'full_name' => $fullname,
                    'age' => $age
                ];

                // Check if picture is not empty and add it to the update data
                if (!empty($image_file)) {
                    $updateData['picture'] = $imageName;
                }
                profiles::where('profile_code', $profilecode)->update($updateData);
            } else {
                $profile_var = new profiles();
                $profile_var->full_name = $fullname;
                $profile_var->age = $age;
                $profile_var->save();
            }
            $propfileinfoexists = ProfilesInfo::where('profile_code', $profilecode)->exists();
            if ($propfileinfoexists) {
                $updateData = [
                    'residence' => $residence,
                    'joiningdate' => $joiningdate,
                    'category' => $category,
                    'nationality' => $nationality,
                    'social_situation' => $social_situation,
                    'children' => $children,
                    'children_age' => $children_age,
                    'education' => $education,
                    'current_work' => $current_work,
                    'word_about_association' => $word_about_association,
                    'work_at_association' => json_encode($workarrayOfArrays),
                    'identifiers' => $identifiers
                ];
                ProfilesInfo::where('profile_code', $profilecode)->update($updateData);
            } else {
                $profiles_infos_var = new ProfilesInfo();
                // Assign values to the model properties
                $profiles_infos_var->profile_code = $profilecode;
                $profiles_infos_var->residence = $residence;
                $profiles_infos_var->joiningdate = $joiningdate;
                $profiles_infos_var->category = $category;
                $profiles_infos_var->nationality = $nationality;
                $profiles_infos_var->social_situation = $social_situation;
                $profiles_infos_var->children = $children;
                $profiles_infos_var->children_age = $children_age;
                $profiles_infos_var->education = $education;
                $profiles_infos_var->current_work = $current_work;
                $profiles_infos_var->word_about_association = $word_about_association;
                $profiles_infos_var->work_at_association = json_encode($workarrayOfArrays);
                $profiles_infos_var->identifiers = $identifiers;

                // Save the model
                $profiles_infos_var->save();
            }
            //=============================================================
            if (isset($image_file)) {
                $fileName = $image_file->getClientOriginalName();
                //$profile_var->picture = $fileName;

                // Validate the file if needed
                $request->validate([
                    'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                // Specify the destination folder
                $destinationPath = public_path('profile_picture');
                // Check if the file with the same name already exists
                $existingFile = $destinationPath . '/' . $imageName;

                if (File::exists($existingFile)) {
                    // If it exists, delete the existing file
                    File::delete($existingFile);
                }

                // Move the uploaded file to the destination folder
                $image_file->move($destinationPath, $imageName);
            }

            DB::commit();
            DB::unprepared('UNLOCK TABLES');
            return back()->with('Success', 'Save success');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    function faTOen($string)
    {
        return strtr($string, array('۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9'));
    }

  
}
