<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profiles;
use App\Models\Election;
use App\Models\Leader;
use App\Models\VotersGroup;
use App\Models\Voter;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function ShowGroupManagerForm(Request $request){
        $full_name = session('full_name', '');
        if($full_name!=''){
        $Profiles=Profiles::all();
        $Elections=Election::all();
        $Leaders = Leader::join('profiles', 'leaders.profile_code', '=', 'profiles.profile_code')
    ->groupBy('profiles.profile_code','profiles.full_name')
    ->select('profiles.profile_code', 'profiles.full_name')
    ->get();
        return view('groupmanager',['full_name'=>$full_name,
        'Profiles'=>$Profiles,
        'Elections'=>$Elections,
        'Leaders'=>$Leaders]);
        }else{
            return view('welcome');
        }
    }

    public function ShowGroupsListForm(Request $request){
        $full_name = session('full_name', '');
        if($full_name!=''){
            $Elections = Election::all();
            return view('groupslist', ['full_name' => $full_name, 'Elections' => $Elections]);
        }else{
            return view('welcome');
        }
    }

    public function getVoterGroups($electioncode)
    {
        $groups = DB::table('voters_group')
            ->select(
                'voters_group.voter_group_code',
                'voters_group.voter_group_name',
                DB::raw('COUNT(DISTINCT voters.profile_code) as member_count')
            )
            ->leftJoin('voters', function($join) use ($electioncode) {
                $join->on('voters.voter_group_code', '=', 'voters_group.voter_group_code')
                     ->where('voters.election_code', '=', $electioncode);
            })
            ->where('voters_group.election_code', $electioncode)
            ->groupBy('voters_group.voter_group_code', 'voters_group.voter_group_name')
            ->get();
        return response()->json($groups);
    }

    public function updateVoterGroup(Request $request, $voter_group_code)
    {
        try {
            VotersGroup::where('voter_group_code', $voter_group_code)
                ->update(['voter_group_name' => $request->input('voter_group_name')]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteVoterGroup($voter_group_code)
    {
        try {
            DB::beginTransaction();
            Voter::where('voter_group_code', $voter_group_code)
                ->update(['voter_group_code' => null]);
            VotersGroup::where('voter_group_code', $voter_group_code)->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
