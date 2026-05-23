<?php

namespace App\Http\Controllers;

use App\Models\candidates;
use App\Models\Votemaster;
use App\Models\VoteDetail;
use Illuminate\Http\Request;
use App\Models\Profiles;
use App\Models\Election;
use App\Models\CandidatesGroup;
use App\Models\ElectionRound;
use Illuminate\Support\Facades\DB;

class CandidateController extends Controller
{
    public function ShowCandidateManagerForm(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $profilestemp = Profiles::select('profiles.profile_code', 'full_name', 'mobile', 'candidates.group_code')
                ->leftJoin('candidates', 'profiles.profile_code', '=', 'candidates.profile_code')
                ->get();
            $candidatesGroups = CandidatesGroup::pluck('group_name', 'group_code')->toArray();
            $profilesArray = array();
            foreach ($profilestemp as $index => $profile) {
                // Append profile data into the array along with the index
                $profilesArray[$index]['profile_code'] = $profile->profile_code;
                $profilesArray[$index]['full_name'] = $profile->full_name;
                $profilesArray[$index]['mobile'] = $profile->mobile;
                if (isset($profile->group_code)) {
                    $profilesArray[$index]['group_code'] = $profile->group_code;
                    if (isset($candidatesGroups[$profile->group_code])) {
                        $profilesArray[$index]['group_name'] = $candidatesGroups[$profile->group_code];
                    } else {
                        $profilesArray[$index]['group_name'] = '';
                    }
                } else {
                    $profilesArray[$index]['group_code'] = '';
                    $profilesArray[$index]['group_name'] = '';
                }
            }
            $Elections = Election::all();

            return view('candidatemanager', ['full_name' => $full_name, 'Profiles' => $profilesArray, 'Elections' => $Elections]);
        } else {
            return view('welcome');
        }
    }

    public function getcandidatesbygroup($election_code, $group_code, $round_number)
    {
        $candidatesgroup_winnumber = CandidatesGroup::where('election_code', $election_code)
            ->where('group_code', $group_code)
            ->pluck('win_number');
        $cand_winnumber = array();
        foreach ($candidatesgroup_winnumber as $index => $winnumber) {
            $cand_winnumber[$group_code] = $winnumber;
        }
        $candidatesgroup_winnumber = CandidatesGroup::where('election_code', $election_code)
            ->pluck('win_number');
        $sum_win_number = $candidatesgroup_winnumber->sum();
        $candidates = profiles::Join('candidates', 'profiles.profile_code', '=', 'candidates.profile_code')
            ->selectRaw('full_name, ifnull(address,"") as address, sex, picture, attachment,profiles.profile_code as profile_code')
            ->where('elections_code', $election_code)
            ->where('round_number', $round_number)
            ->where('candidate_status', 1)
            ->where('group_code', $group_code)
            ->get();
        $data['group_win_number'] = $cand_winnumber;
        $data['sum_win_number'] = $sum_win_number;
        $data['candidates'] = $candidates;
        return $data;
    }

     public function resetcandidate(Request $request){
        try {
            $jsonData = $request->json()->all();
            $profile_code = $jsonData["profile_code"];
            $election_code = $jsonData["election_code"];
            $round_number = $jsonData["round_number"];
            candidates::where('elections_code', $election_code)
            ->where('round_number', $round_number)
            ->where('profile_code', $profile_code)
            ->update(['candidate_status' => 1]);
            candidates::where('elections_code', $election_code)
            ->where('round_number','>', $round_number)
            ->where('profile_code', $profile_code)
            ->delete();
            $voteCode = Votemaster::where('election_code', $election_code)
            ->where('round_number',$round_number)
            ->pluck('vote_code')->toarray();
            VoteDetail::whereIn('vote_code', $voteCode)
            ->where('candidate',$profile_code)
            ->delete();
            return response()->json(['message' => 'Data saved successfully'], 200);
        } catch (\Exception $e) {
            // Exception handling
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
        
     }


    public function savecandidateinfo(Request $request)
    {
        try {
            /*DB::unprepared('LOCK TABLES profiles WRITE');
            DB::beginTransaction();
            // Retrieve selected option from the form
            $election_code = $request->input('select_election');
            $round_number = $request->input('selectelectionround');
            $election_type = $request->input('input_election_type');
            $candidates_str = $request->input('input_candidates_codes');
            $candidates = json_decode($candidates_str);

            // Delete candidates where elections_code is empty
            candidates::where('elections_code', $election_code)
                ->delete();
            // Sort the array
            sort($candidates);

            foreach ($candidates as $index => $candidate) {
                $candidates_array[$index]['profile_code'] = $candidate;
                $candidates_array[$index]['elections_code'] = $election_code;
                $candidates_array[$index]['round_number'] = $round_number;
                $candidates_array[$index]['candidate_status'] = 1;
                if ($election_type == 2) {
                    $candidates_array[$index]['group_code'] = "";
                } else {
                    $candidates_array[$index]['group_code'] = "";
                }
            }

            $candidatesObj = new candidates();
            if (isset($candidates_array)) {
                $candidatesObj->insert($candidates_array);
            }
            DB::commit();
            DB::unprepared('UNLOCK TABLES');*/
            return back()->with('Success', 'Save success');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function savecandidatelist(Request $request)
    {
        try {
            DB::unprepared('LOCK TABLES profiles WRITE');
            DB::beginTransaction();
            $jsonData = $request->json()->all();
            $listcode = $jsonData["listcode"];
            $listname = $jsonData["listname"];
            $winnumber = $jsonData["winnumber"];
            $round_number = $jsonData["round_number"];
            $electioncode = $jsonData["electioncode"];
            $listmembers = $jsonData["listmembers"];
            //================save vote master
            $candidates = new candidates();
            candidates::where('group_code', $listcode)
            ->where('elections_code', $electioncode)
            ->where('round_number', $round_number)
            ->delete();
            DB::commit();
            DB::beginTransaction();
            $exists = CandidatesGroup::where(['group_code' => $listcode, 'election_code' => $electioncode])->exists();
                if($exists==false){
                    $max_groupcode = CandidatesGroup::max('group_code') + 1;
                    $candidategroup = new CandidatesGroup();
                    $candidategroup->group_code = $max_groupcode;
                    $candidategroup->election_code = $electioncode;
                    $candidategroup->group_name = $listname;
                    $candidategroup->win_number = $winnumber;
                    $candidategroup->save();
                }else{
                    CandidatesGroup::where('election_code', $electioncode)
                    ->where('group_code', $listcode)
                    ->update(['group_name' => $listname,'win_number'=>$winnumber]);
                }


            $counter = 0;
            foreach ($listmembers as $key => $listmember) {
                if (isset($listcode) && ($listcode != '')) {
                    $array_to_insert[$counter]["group_code"] = $listcode;
                } else {
                    $array_to_insert[$counter]["group_code"] = $max_groupcode;
                }
                $array_to_insert[$counter]["elections_code"] = $electioncode;
                $array_to_insert[$counter]["candidate_status"] = 1;
                $array_to_insert[$counter]["round_number"] = $round_number;
                $array_to_insert[$counter]["profile_code"] = $listmember[2];
                $counter++;
            }
            if (isset($array_to_insert)) {
                $candidates->insert($array_to_insert);
            }
            DB::commit();
            DB::unprepared('UNLOCK TABLES');
            return 1;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteCandidateList($group_code)
    {
        try {
            DB::beginTransaction();
            candidates::where('group_code', $group_code)->delete();
            CandidatesGroup::where('group_code', $group_code)->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
