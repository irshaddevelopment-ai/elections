<?php

namespace App\Http\Controllers;

use App\Models\VotersGroup;
use Illuminate\Http\Request;
use App\Models\Election;
use App\Models\Profiles;
use App\Models\Leader;
use App\Models\CandidatesGroup;
use App\Models\ElectionRound;
use App\Models\Candidate;
use App\Models\Setting;
use App\Models\LeaderVoterRelation;
use App\Models\users;
use App\Models\Voter;
use Illuminate\Support\Facades\DB;

class LeaderController extends Controller
{

    public function getvotersbyleader($election_code, $leader_code)
    {
        //===get all groups code with names
        $all_groups = VotersGroup::where('election_code', $election_code)->pluck('voter_group_name', 'voter_group_code')->toarray();
        //=============get voters groups for this election_code
        $votersGroups = Voter::where('election_code', $election_code)->
        where('voter_group_code','!=', '')
        ->pluck('voter_group_code', 'profile_code')->toarray();
        //=======all voters for this election, excluding those assigned to other leaders
        $allfreeprofiles = Profiles::whereIn('profile_code', function ($query) use ($election_code) {
            $query->select('profile_code')->from('voters')->where('election_code', $election_code);
        })->whereNotIn('profile_code', function ($query) use ($leader_code, $election_code) {
            $query->select('voter_profile_code')->from('leader_voter_rel')
                ->where('leader_profile_code', '!=', $leader_code)
                ->where('election_code', $election_code);
        })->get();

        $allfreeprofilesarray = array();
        foreach ($allfreeprofiles as $index => $allfreeprofilesObj) {
            $allfreeprofilesarray[$index]['profile_code'] = $allfreeprofilesObj['profile_code'];
            $allfreeprofilesarray[$index]['full_name'] = $allfreeprofilesObj['full_name'];
            $allfreeprofilesarray[$index]['mobile'] = $allfreeprofilesObj['mobile'];
            $group_name = $all_groups[$votersGroups[$allfreeprofilesObj['profile_code']]?? '']?? '';
            if (isset($group_name)) {
                $allfreeprofilesarray[$index]['voter_group_name'] = $group_name;
            } else {
                $allfreeprofilesarray[$index]['voter_group_name'] = '';
            }
            $group_code=$votersGroups[$allfreeprofilesObj['profile_code']]?? '';
            if (isset($group_code)) {
                $allfreeprofilesarray[$index]['voter_group_code'] = $group_code;
            } else {
                $allfreeprofilesarray[$index]['voter_group_code'] = '';
            }
        }
        //===============================================
        //===========get voters related to leader_code
        $voters_rel_leader = LeaderVoterRelation::where('election_code', $election_code)
            ->where('leader_profile_code', $leader_code)
            ->pluck('voter_profile_code')->toarray();
        //===========================================
        $data['allfreeprofiles'] = $allfreeprofilesarray;
        $data['voters_rel_leader'] = $voters_rel_leader;
        return $data;
    }
    public function ShowLeaderManagerForm(Request $request)
    {
        DB::statement("SET sql_mode=''");
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $Profiles = Profiles::all();
            $Elections = Election::all();
            $Groups = VotersGroup::groupby('voter_group_code')->get();
            /*$leaders = Leader::select('profile_code', 'leaders.voter_group_code', 'voter_group_name')
                ->join('voters_group', 'leaders.voter_group_code', '=', 'voters_group.voter_group_code')
                ->groupBy('profile_code', 'leaders.voter_group_code')
                ->get();*/
            return view('leadermanager', [
                'full_name' => $full_name,
                'Profiles' => $Profiles, 'Elections' => $Elections, 'Groups' => $Groups
            ]);
        } else {
            return view('welcome');
        }
    }
    
    public function getvotersbyelectioncode($electioncode){
        DB::statement("SET sql_mode=''");
        $alluserscode=users::where('election_code',$electioncode)
        ->pluck('user_code','profile_code')->toarray();

 
            //====================================
            $leaderstemp = Leader::select('profiles.full_name', 'profiles.profile_code', 'mobile') // Selecting all columns from the leaders table
                ->join('profiles', 'leaders.profile_code', '=', 'profiles.profile_code')
                ->where('election_code', $electioncode)
                ->groupBy('leaders.idleaders')
                ->get();
                $voter_names_Array=array();
                foreach ($leaderstemp as $index => $leaderstempObj) {
                    $voters_profiles= Profiles::select('profiles.profile_code', 'full_name', 'mobile', 'address')
                    ->join('leader_voter_rel', 'profiles.profile_code', '=', 'leader_voter_rel.voter_profile_code')
                    ->where('leader_profile_code', $leaderstempObj->profile_code)
                    ->where('election_code', $electioncode)
                    ->get();
                    $voter_prf_Array=array();
                    foreach ($voters_profiles as $index => $voters_profilesObj) {
                        $voter_prf_Array[$index]['profile_code']=$voters_profilesObj->profile_code;
                        $voter_prf_Array[$index]['full_name']=$voters_profilesObj->full_name;
                        $voter_prf_Array[$index]['mobile']=$voters_profilesObj->mobile;
                        $voter_prf_Array[$index]['address']=$voters_profilesObj->address;
                        $voter_prf_Array[$index]['user_code']=
                          isset($alluserscode[$voters_profilesObj->profile_code]) 
                          ? $alluserscode[$voters_profilesObj->profile_code] : '';
                    }
                    $voter_names_Array[$leaderstempObj->profile_code]=$voter_prf_Array;
                }
            //===========get leaders members count
            $leaders_with_countmembers = LeaderVoterRelation::select('leader_profile_code', \DB::raw('count(voter_profile_code) as count_voters_codes'))
    ->where('election_code', $electioncode)
    ->groupBy('leader_profile_code')
    ->pluck('count_voters_codes','leader_profile_code')->toarray();

            $leaders = array();
            foreach ($leaderstemp as $index => $leader) {
                $leaders[$index]['full_name'] = $leader->full_name;
                $leaders[$index]['mobile'] = $leader->mobile;
                $leaders[$index]['profile_code'] = $leader->profile_code;
                $leaders[$index]['leader_counts_members'] = isset($leaders_with_countmembers[$leader->profile_code]) ? $leaders_with_countmembers[$leader->profile_code]: '';
                $leaders[$index]['full_names_json'] = isset($voter_names_Array[$leader->profile_code]) ? json_encode($voter_names_Array[$leader->profile_code]): '';
                $leaders[$index]['user_code'] = isset($alluserscode[$leader->profile_code]) ? $alluserscode[$leader->profile_code]: '';
                
            }
            
            return $leaders;
    }

    public function ShowLeadersListForm(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            DB::statement("SET sql_mode=''");
            $election = Election::get()->first();
         //$electioncode=$election->election_code;
        $alluserscode=users::where('election_code',$election->election_code)
        ->pluck('user_code','profile_code')->toarray();

 
            //====================================
            $leaderstemp = Leader::select('profiles.full_name', 'profiles.profile_code', 'mobile') // Selecting all columns from the leaders table
                ->join('profiles', 'leaders.profile_code', '=', 'profiles.profile_code')
                ->groupBy('leaders.idleaders')
                ->get();
                $voter_names_Array=array();
                foreach ($leaderstemp as $index => $leaderstempObj) {
                    $voters_profiles= Profiles::select('profiles.profile_code', 'full_name', 'mobile', 'address')
                    ->join('leader_voter_rel', 'profiles.profile_code', '=', 'leader_voter_rel.voter_profile_code')
                    ->where('leader_profile_code', $leaderstempObj->profile_code)
                    ->where('election_code', $election->election_code)
                    ->get();
                    $voter_prf_Array=array();
                    foreach ($voters_profiles as $index => $voters_profilesObj) {
                        $voter_prf_Array[$index]['profile_code']=$voters_profilesObj->profile_code;
                        $voter_prf_Array[$index]['full_name']=$voters_profilesObj->full_name;
                        $voter_prf_Array[$index]['mobile']=$voters_profilesObj->mobile;
                        $voter_prf_Array[$index]['address']=$voters_profilesObj->address;
                        $voter_prf_Array[$index]['user_code']=
                          isset($alluserscode[$voters_profilesObj->profile_code]) 
                          ? $alluserscode[$voters_profilesObj->profile_code] : '';
                    }
                    $voter_names_Array[$leaderstempObj->profile_code]=$voter_prf_Array;
                }
            //===========get leaders members count
            $leaders_with_countmembers = LeaderVoterRelation::select('leader_profile_code', \DB::raw('count(voter_profile_code) as count_voters_codes'))
    ->where('election_code', $election->election_code)
    ->groupBy('leader_profile_code')
    ->pluck('count_voters_codes','leader_profile_code')->toarray();

            $leaders = array();
            foreach ($leaderstemp as $index => $leader) {
                $leaders[$index]['full_name'] = $leader->full_name;
                $leaders[$index]['mobile'] = $leader->mobile;
                $leaders[$index]['profile_code'] = $leader->profile_code;
                $leaders[$index]['leader_counts_members'] = isset($leaders_with_countmembers[$leader->profile_code]) ? $leaders_with_countmembers[$leader->profile_code]: '';
                $leaders[$index]['full_names_json'] = isset($voter_names_Array[$leader->profile_code]) ? json_encode($voter_names_Array[$leader->profile_code]): '';
                $leaders[$index]['user_code'] = isset($alluserscode[$leader->profile_code]) ? $alluserscode[$leader->profile_code]: '';
                
            }
            $Elections = Election::all();
            return view('leaderslist', [
                'full_name' => $full_name, 'Profiles' => $leaders,
                'election' => $election,'Elections'=>$Elections
            ]);
        } else {
            return view('welcome');
        }
    }

    public function saveleaderinfo(Request $request)
    {
        try {
            DB::unprepared('LOCK TABLES leaders WRITE');
            DB::beginTransaction();
            $jsonData = $request->input('input_voters_codes');
            $leadersData = json_decode($jsonData, true); // Decode JSON string into an associative array
            $electioncode = $leadersData['electioncode'];
            $leadercode = $leadersData['leader_code'];
            $votersarray = json_decode($leadersData['voters']);
            // Check if a record with the specified user_code does not exist
            if (!Leader::where('election_code', $electioncode)
                ->where('profile_code', $leadercode)
                ->exists()) {
                // If it doesn't exist, proceed with saving the model
                $leader = new Leader();
                $leader->profile_code = $leadercode;
                $leader->election_code = $electioncode;
                $leader->save();
            }
            if (isset($votersarray)) {
                $leader_voter_rel_obj=new LeaderVoterRelation();
                // Delete records where leader_code in leader_voter_rel exists
                LeaderVoterRelation::where('election_code', $electioncode)
                ->where('leader_profile_code',$leadercode)
                ->delete();
                //=========insert leader viters relations
                foreach ($votersarray as $index => $voter) {
                    $array_to_insert[$index]["leader_profile_code"] = $leadercode;
                    $array_to_insert[$index]["voter_profile_code"] = $voter->voter_code;
                    $array_to_insert[$index]["voter_group_code"] = $voter->voter_group_code;
                    $array_to_insert[$index]["election_code"] = $electioncode;
                }
                if (isset($array_to_insert)) {
                    $leader_voter_rel_obj->insert($array_to_insert);
                }
            }
            DB::commit();
            DB::unprepared('UNLOCK TABLES');
            return back()->with('Success', 'Save success');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }



    public function showleaderdash(Request $request)
    {
        $full_name = session('full_name', '');
        $user_code = session('guest_usercode', '');
        if ($full_name != '') {
            $user = users::Join('profiles', 'users.profile_code', '=', 'profiles.profile_code')
                ->select('user_code', 'full_name', 'users.profile_code', 'election_code', 'isconnected')
                ->where('user_code', $user_code)
                ->first();
            $isleader = Leader::where('profile_code', $user->profile_code)->count();
            $electionobj = Election::where('election_code', $user->election_code)->get()->first();
            $count_round = ElectionRound::where('round_status', 1)->value('round_number');
            $setting = Setting::where('settings_name', 'sett_resetusercode')->get()->first();
            return view('leaderdash', [
                'users' => $user,
                'isleader' => $isleader, 'electionobj' => $electionobj,
                'count_round' => $count_round,'setting'=>$setting
            ]);
        } else {
            return view('welcome');
        }
    }
}
