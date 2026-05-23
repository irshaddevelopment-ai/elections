<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profiles;
use App\Models\Election;
use App\Models\Votemaster;
use App\Models\VoteDetail;
use App\Models\ElectionRound;
use App\Models\candidates;
use App\Models\CandidatesGroup;
use App\Models\Leader;
use App\Models\Voter;
use App\Models\users;
use App\Models\EventTable;
use App\Models\VotersGroup;
use App\Models\LeaderVoterRelation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class VoterController extends Controller
{
    public function ShowVoterManagerForm(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $Profiles = Profiles::all();
            $Elections = Election::all();
            return view('votermanager', ['full_name' => $full_name, 'Profiles' => $Profiles, 'Elections' => $Elections]);
        } else {
            return view('welcome');
        }
    }


    public function getvotersbyelection($electioncode, $voterstatus, $clickvar, $datetosend)
    {
        try {
            DB::statement("SET sql_mode=''");
            $session_id_var = Session::getId();
            $voters_view = "voters_" . $session_id_var . "";
            $event_view = "event_view_" . $session_id_var . "";
            $event_votes_view = "event_votes_view_" . $session_id_var . "";
            $leaders = Profiles::pluck('full_name', 'profile_code')->toarray();
            if($datetosend!='0'){
            $newloggeduser=DB::table('event_table as e1')
            ->select(DB::raw('count(*)'))
            ->whereDate('e1.loggedin_datetime', $datetosend)
            ->whereNotIn('e1.user_code', function($query) use ($datetosend) {
                $query->select('user_code')
                      ->from('event_table as e2')
                      ->whereDate('e2.loggedin_datetime', '<', $datetosend);
            })
            ->whereIn('e1.user_code', function($query) use ($electioncode) {
                $query->select('user_code')
                      ->from('users')
                      ->where('isleader', 1)
                      ->orWhere('isvoter', 1)
                      ->where('election_code', $electioncode);
            })
            ->whereIn('e1.created_at', function($query) use ($datetosend) {
                $query->select(DB::raw('MAX(created_at)'))
                      ->from('event_table')
                      ->whereDate('created_at', $datetosend)
                      ->groupBy('prf_code');
            })
            ->count();
        }else{
            $newloggeduser=0; 
        }
            $data = array();
           
            $voter_user = users::where('election_code', $electioncode)
                ->pluck('user_code', 'profile_code')->toarray();
            DB::statement("
            CREATE OR REPLACE VIEW $voters_view AS
            SELECT full_name, profiles.profile_code, mobile, leader_profile_code
            FROM profiles
            JOIN leader_voter_rel ON profiles.profile_code = leader_voter_rel.voter_profile_code
            WHERE leader_voter_rel.election_code = '$electioncode'
            and profiles.profile_code in
            (SELECT profile_code FROM users where (isleader=1 or isvoter=1) 
            and election_code='$electioncode')
        ");
            if ($clickvar == 2) {
                if($datetosend!='0'){
                DB::statement("
                CREATE OR REPLACE VIEW $event_view AS
                SELECT prf_code,user_code,loggedin_datetime,loggedout_datetime FROM event_table
                WHERE created_at in (
                    SELECT MAX(created_at) FROM event_table WHERE DATE(created_at) = '$datetosend' group by prf_code
                ) AND DATE(created_at) = '$datetosend' and prf_code!='admin' and event_description='login';
                      ");
                }else{
                    DB::statement("
                    CREATE OR REPLACE VIEW $event_view AS
                    SELECT prf_code,user_code,loggedin_datetime,loggedout_datetime FROM event_table
                    where   prf_code!='admin' and event_description='login' group by prf_code,user_code;
                          ");  
                }
                if ($voterstatus == 1) {
                    //===============================================================
                    $profiles = DB::select("
    SELECT full_name,$voters_view.profile_code,mobile,$voters_view.leader_profile_code,user_code,
    loggedin_datetime,loggedout_datetime
    FROM $voters_view, $event_view 
    WHERE $voters_view.profile_code = $event_view.prf_code 
");
                } else {
                    $profiles = DB::select("
                    SELECT full_name,$voters_view.profile_code,mobile,$voters_view.leader_profile_code,
                    '' as loggedin_datetime,'' as loggedout_datetime
                    FROM $voters_view 
                    where $voters_view.profile_code not in
                    (select prf_code from  $event_view);
                ");
                }
                foreach ($profiles as $index => $profile) {
                    $data[$index]['voter_name'] = $profile->full_name;
                    $data[$index]['mobile'] = $profile->mobile;
                    $data[$index]['prf_code'] = $profile->profile_code;
                    $data[$index]['usercode'] = $voter_user[$profile->profile_code];
                    $data[$index]['leader_name'] = $leaders[$profile->leader_profile_code];
                    $data[$index]['loggedin'] = "كلا";
                    $data[$index]['loggedout'] = "كلا";
                    $data[$index]['loggedin_datetime'] = '';
                    $data[$index]['votestatus'] = "كلا";
                    if (($profile->loggedin_datetime != null) || ($profile->loggedin_datetime != '')) {
                        $data[$index]['loggedin'] = "نعم";
                        $data[$index]['loggedin_datetime'] = $profile->loggedin_datetime;
                    }
                    if (($profile->loggedout_datetime != null) || ($profile->loggedout_datetime != '')) {
                        $data[$index]['loggedout'] = "نعم";
                    }
                }
            } else {
                if($datetosend!='0'){
                DB::statement("
                CREATE OR REPLACE VIEW $event_view AS
                SELECT prf_code,user_code,loggedin_datetime,loggedout_datetime FROM event_table
                WHERE created_at in (
                    SELECT MAX(created_at) FROM event_table WHERE DATE(created_at) = '$datetosend' and vote_description='vote' group by prf_code
                ) AND DATE(created_at) = '$datetosend' and prf_code!='admin' and vote_description='vote';
                      ");
                }else{
                    DB::statement("
                CREATE OR REPLACE VIEW $event_view AS
                SELECT prf_code,user_code,loggedin_datetime,loggedout_datetime FROM event_table
                WHERE prf_code!='admin' and vote_description='vote' group by prf_code,user_code;
                      ");
                }
                if ($voterstatus == 1) {
                    //===============================================================
                    $profiles = DB::select("
        SELECT full_name,$voters_view.profile_code,mobile,$voters_view.leader_profile_code,user_code
        FROM $voters_view, $event_view 
        WHERE $voters_view.profile_code = $event_view.prf_code 
    ");
                    foreach ($profiles as $index => $profile) {
                        $data[$index]['voter_name'] = $profile->full_name;
                        $data[$index]['mobile'] = $profile->mobile;
                        $data[$index]['prf_code'] = $profile->profile_code;
                        $data[$index]['usercode'] = $voter_user[$profile->profile_code];
                        $data[$index]['leader_name'] = $leaders[$profile->leader_profile_code];
                        $data[$index]['loggedin'] = "كلا";
                        $data[$index]['loggedout'] = "كلا";
                        $data[$index]['loggedin_datetime'] = '';
                        $data[$index]['votestatus'] = "تعم";
                        $data[$index]['loggedin'] = "كلا";
                        $data[$index]['loggedin_datetime']='';
                        $data[$index]['loggedout'] = "كلا";
                    }
                } else {
                    $profiles = DB::select("
                        SELECT full_name,$voters_view.profile_code,mobile,$voters_view.leader_profile_code,
                        '' as loggedin_datetime,'' as loggedout_datetime
                        FROM $voters_view 
                        where $voters_view.profile_code not in
                        (select prf_code from  $event_view);
                    ");
                    foreach ($profiles as $index => $profile) {
                        $data[$index]['voter_name'] = $profile->full_name;
                        $data[$index]['mobile'] = $profile->mobile;
                        $data[$index]['prf_code'] = $profile->profile_code;
                        $data[$index]['usercode'] = $voter_user[$profile->profile_code];
                        $data[$index]['leader_name'] = $leaders[$profile->leader_profile_code];
                        $data[$index]['loggedin'] = "كلا";
                        $data[$index]['loggedout'] = "كلا";
                        $data[$index]['loggedin_datetime'] = '';
                        $data[$index]['votestatus'] = "كلا";
                        $data[$index]['loggedin'] = "كلا";
                        $data[$index]['loggedin_datetime']='';
                        $data[$index]['loggedout'] = "كلا";
                       
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        } finally {
            DB::statement('DROP VIEW IF EXISTS ' . $voters_view . '');
            DB::statement('DROP VIEW IF EXISTS ' . $event_view . '');
        }
        $g_array[0]=$data;
        $g_array[1]=$newloggeduser;
        return json_encode($g_array);
    }

    public function getloggedinperc($electioncode)
    {
        $active_round_datetime = DB::table('election_rounds')
    ->where('election_code', $electioncode)
    ->where('round_status', 1)
    ->value('updated_at');

    $active_round_number = DB::table('election_rounds')
    ->where('election_code', $electioncode)
    ->where('round_status', 1)
    ->value('round_number');

    $candidates_count = DB::table('candidates')
    ->where('elections_code', $electioncode)
    ->where('round_number', $active_round_number?? 1)
    ->count();
    $voters_count = DB::table('voters')
    ->where('election_code', $electioncode)
    ->count();
    

    
       /* $all_userscode = users::where('election_code', $electioncode)
            ->pluck('user_code')->toArray();*/
        $all_userscode = users::
        where('election_code', $electioncode)
        ->where('isleader', 1)
        ->orWhere('isvoter', 1)
        ->pluck('user_code')->toArray();
        if(isset($active_round_datetime)){
            $countUsersLoggedIn = EventTable::distinct()
            ->where('created_at','>',$active_round_datetime)
            ->whereIn('user_code', $all_userscode)
            ->count('user_code');
        }else{
            $countUsersLoggedIn = EventTable::distinct()
            //->whereDate('created_at', now()->toDateString())
            ->whereIn('user_code', $all_userscode)
            ->where('event_description','login')
            ->count('user_code');
            
        }

        $allprofiles=users::where('election_code', $electioncode)->count();
        

        $countUsersAll = users:: where('election_code', $electioncode)
        ->where('isleader', 1)
        ->orWhere('isvoter', 1)
            ->count();

        $notloggedinusers = $countUsersAll - $countUsersLoggedIn;
        $data=array();
        $data['login'][0]=$notloggedinusers;
        $data['login'][1]=$countUsersLoggedIn;
        $data['candidates'][0]=$candidates_count;
        $data['voters'][1]=$voters_count;
        $data['allprofiles'][0]=$allprofiles;
        return [$data];
    }
    public function getvotersperc($electioncode)
    {
        $voters = Voter::where('election_code', $electioncode)->count();
        $active_round = ElectionRound::where("election_code", $electioncode)
            ->where("round_status", 1)
            ->select("round_number", "win_percentage", "min_win_percentage", "win_sign")
            ->first();
        $votersmaster = Votemaster::distinct()
            ->where('election_code', $electioncode)
            ->where('round_number', $active_round->round_number)
            ->count('user_code');
        $remainingvoters = $voters - $votersmaster;
     
        return [$remainingvoters, $votersmaster];
    }

    public function getvoterprofiles($electioncode)
    {
        $votersprofiles = DB::select("
            SELECT profiles.profile_code, full_name, sex, IFNULL(mobile, '') AS mobile, ifnull(voter_status,'') as voter_status
            FROM profiles
            LEFT JOIN voters ON profiles.profile_code = voters.profile_code AND voters.election_code = '$electioncode'
        ");
        return json_encode($votersprofiles);
    }
    public function getvoterprofilesforgroups($electioncode)
    {
        DB::statement("SET sql_mode=''");
        DB::statement("
        create or replace view v1 as
        SELECT profiles.profile_code, full_name,sex, IFNULL(mobile, '') AS mobile,
        ifnull(voter_group_code,'') as voter_group_code
                    FROM profiles
                    INNER JOIN voters ON profiles.profile_code = voters.profile_code AND voters.election_code = '$electioncode'
                     group by profiles.profile_code;
        ");
        DB::statement("
        create or replace view v2 as
        select profile_code,full_name,sex,mobile,voter_group_name,v1.voter_group_code
        from v1 left join voters_group
        on v1.voter_group_code=voters_group.voter_group_code
        and voters_group.election_code='$electioncode'  group by profile_code;
        ");
        $votersprofiles = DB::select("select profile_code,full_name,sex,mobile,voter_group_name
        from v2 where voter_group_code='' group by profile_code;  
        ");
        return json_encode($votersprofiles);
    }

    public function getvotersforleaderinfo($datevar, $eleccode)
    {
        $logged_user_code = session("guest_usercode", "");
        $user = users::where('user_code', $logged_user_code)->first();
        $profiles = DB::select("
    SELECT full_name, profiles.profile_code, isconnected, IFNULL(mobile, '') AS mobile
    FROM profiles, leader_voter_rel 
    WHERE profiles.profile_code = leader_voter_rel.voter_profile_code 
    AND leader_voter_rel.leader_profile_code = '$user->profile_code'
    and profiles.profile_code in(select profile_code from voters where election_code='$eleccode')
");
        $data = array();
        foreach ($profiles as $index => $profile) {
            $voter_user = users::where('election_code', $eleccode)
                ->where('profile_code', $profile->profile_code)
                ->get()->first();
            $voter_user_code = isset($voter_user->user_code) ? $voter_user->user_code : '';
            $events_login = null;
            $events_vote = null;
            if ($voter_user_code != '') {
                $events_login = EventTable::where('user_code', $voter_user_code)
                    ->whereDate('created_at', $datevar)
                    ->where('event_description', 'login')
                    ->get()->last();
                $events_vote = EventTable::where('user_code', $voter_user_code)
                    ->whereDate('created_at', $datevar)
                    ->where('event_description', 'vote')
                    ->get()->last();
            }
            $data[$index]['profile_code'] = $profile->profile_code;
            $data[$index]['voter_name'] = $profile->full_name;
            $data[$index]['mobile'] = $profile->mobile;
            $data[$index]['usercode'] = $voter_user_code;
            $data[$index]['isconnected'] = $profile->isconnected;
            if (isset($events_login)) {
                if ($events_login->connected == "1") {
                    $data[$index]['loggedin'] = "نعم";
                    
                } else {
                    $data[$index]['loggedin'] = "كلا";
                }
            } else {
                $data[$index]['loggedin'] = "كلا";
            }
            if (isset($events_vote)) {
                if ($events_vote->event_description == "vote") {
                    $data[$index]['votestatus'] = "نعم";
                } else {
                    $data[$index]['votestatus'] = "كلا";
                }
            } else {
                $data[$index]['votestatus'] = "كلا";
            }
        }
        return $data;
    }

    public function getcandidatesstatus($eleccode)
    {
        $active_round_number = ElectionRound::where('election_code', $eleccode)
            ->where('round_status', 1)
            ->value('round_number');

        if (!isset($active_round_number)) {
            $active_round_number = ElectionRound::where('election_code', $eleccode)
                ->orderBy('round_number')
                ->value('round_number') ?? 1;
        }

        $logged_users = DB::table('event_table')
            ->select('user_code')
            ->where('event_description', 'login')
            ->groupBy('user_code');

        $voted_users = DB::table('vote_master')
            ->select('user_code')
            ->where('election_code', $eleccode)
            ->where('round_number', $active_round_number)
            ->groupBy('user_code');

        $candidates_status = DB::table('candidates')
            ->join('profiles', 'profiles.profile_code', '=', 'candidates.profile_code')
            ->leftJoin('users', function ($join) use ($eleccode) {
                $join->on('users.profile_code', '=', 'candidates.profile_code')
                    ->where('users.election_code', '=', $eleccode);
            })
            ->leftJoinSub($logged_users, 'logged_users', function ($join) {
                $join->on('logged_users.user_code', '=', 'users.user_code');
            })
            ->leftJoinSub($voted_users, 'voted_users', function ($join) {
                $join->on('voted_users.user_code', '=', 'users.user_code');
            })
            ->leftJoin('leader_voter_rel', function ($join) use ($eleccode) {
                $join->on('leader_voter_rel.voter_profile_code', '=', 'candidates.profile_code')
                    ->where('leader_voter_rel.election_code', '=', $eleccode);
            })
            ->leftJoin('profiles as leader_profiles', 'leader_profiles.profile_code', '=', 'leader_voter_rel.leader_profile_code')
            ->select(
                'profiles.full_name as candidate_name',
                'profiles.profile_code',
                DB::raw("IFNULL(users.user_code, '') as usercode"),
                DB::raw("IFNULL(leader_profiles.full_name, '') as leader_name"),
                DB::raw("CASE WHEN logged_users.user_code IS NULL THEN 'كلا' ELSE 'نعم' END as loggedin"),
                DB::raw("CASE WHEN voted_users.user_code IS NULL THEN 'كلا' ELSE 'نعم' END as votestatus")
            )
            ->where('candidates.elections_code', $eleccode)
            ->where('candidates.round_number', $active_round_number)
            ->groupBy(
                'profiles.full_name',
                'profiles.profile_code',
                'users.user_code',
                'leader_profiles.full_name',
                'logged_users.user_code',
                'voted_users.user_code'
            )
            ->orderBy('profiles.full_name')
            ->get();

        return json_encode($candidates_status);
    }

    public function getvoterstatus($usercode, $electioncode, $round_count)
    {
        $voter_status = Votemaster::where('election_code', $electioncode)
            ->where('user_code', $usercode)
            ->where('round_number', $round_count)
            ->count();
        return $voter_status;
    }

    public function getvoterschoosen($hashMapJson_str)
    {
        $jsondata = json_decode($hashMapJson_str, true);
        $data=$jsondata['info'];
        $election_code=$jsondata['electioncode'];
        $keys = array_keys($data);
        $valuesArray = [];
        foreach ($data as $values) {
            $valuesArray = array_merge($valuesArray, array_values($values));
        }
        $profilesname=Profiles::whereIn('profile_code', $valuesArray)->pluck('full_name','profile_code')->toarray();
        $listsname=CandidatesGroup::where('election_code',$election_code)
        ->whereIn('group_code', $keys)->pluck('group_name','group_code')->toarray();
        $resdata=array();
        $gcounter=0;
        foreach ($data as $key => $values) {
            $countervar=0;
            foreach ($values as $value) {
                $resdata[$gcounter]['listname']=isset($listsname[$key]) ? $listsname[$key] : '';
                $resdata[$gcounter]['full_name']=isset($profilesname[$values[$countervar]]) ? $profilesname[$values[$countervar]] : '';
                $countervar++;
                $gcounter++;
            }
            
        }
        return $resdata;
    }

    public function saveVote(Request $request)
    {
        $jsonData      = $request->json()->all();
        $userCode      = $jsonData["usercode"];
        $electionCode  = $jsonData["electioncode"];
        $roundNumber   = $jsonData["round_number"];
        $voteDetailIn  = $jsonData["info"] ?? [];
        $sessionHandle = Session::getId();

        // Replaces the previous `LOCK TABLES profiles WRITE` (which also blocked
        // logins). Concurrency now relies on:
        //   - AUTO_INCREMENT on vote_master.idvote_master for unique IDs
        //   - lockForUpdate() to serialize only the same (user, election, round)
        //   - 3 retries on deadlock victim under burst load
        return DB::transaction(function () use (
            $userCode, $electionCode, $roundNumber, $voteDetailIn, $sessionHandle
        ) {
            // Idempotency: a second submit for the same (user, election, round)
            // returns success without inserting a duplicate vote.
            $alreadyVoted = DB::table('vote_master')
                ->where('user_code', $userCode)
                ->where('election_code', $electionCode)
                ->where('round_number', $roundNumber)
                ->lockForUpdate()
                ->exists();

            if ($alreadyVoted) {
                return 1;
            }

            // vote_code is kept equal to idvote_master to preserve the prior
            // invariant; let AUTO_INCREMENT assign the ID, then mirror it.
            $idVoteMaster = DB::table('vote_master')->insertGetId([
                'user_code'     => $userCode,
                'election_code' => $electionCode,
                'round_number'  => $roundNumber,
            ]);
            DB::table('vote_master')
                ->where('idvote_master', $idVoteMaster)
                ->update(['vote_code' => $idVoteMaster]);

            $detailRows = [];
            foreach ($voteDetailIn as $listCode => $candidates) {
                foreach ($candidates as $candidateCode) {
                    $detailRows[] = [
                        'election_list_code' => $listCode,
                        'candidate'          => $candidateCode,
                        'round_number'       => $roundNumber,
                    ];
                }
            }
            if (!empty($detailRows)) {
                DB::table('vote_detail')->insert($detailRows);
            }

            DB::table('event_table')
                ->where('session_handle', $sessionHandle)
                ->where('event_description', 'login')
                ->update(['vote_description' => 'vote']);

            return 1;
        }, 3);
    }

    public function savevotergroup(Request $request)
    {
        $jsonData = $request->input('input_voters_group');
        $votersgroupinfo  = json_decode($jsonData, true);
        $electioncode     = $votersgroupinfo['electioncode'];
        $voter_group_name = $votersgroupinfo['voter_group_name'];
        $votersgrouparray = json_decode($votersgroupinfo['votersgroup']);

        // Replaces `LOCK TABLES voters_group WRITE`. lockForUpdate on the MAX
        // query serializes only concurrent admins running this same flow,
        // without blocking other tables.
        DB::transaction(function () use ($electioncode, $voter_group_name, $votersgrouparray) {
            $max_votergroupcode = DB::table('voters_group')->lockForUpdate()->max('voter_group_code') + 1;

            $array_to_insert = [];
            foreach ($votersgrouparray as $voter) {
                Voter::where('election_code', $electioncode)
                    ->where('profile_code', $voter)
                    ->update(['voter_group_code' => $max_votergroupcode]);
                $array_to_insert[] = [
                    'voter_group_code' => $max_votergroupcode,
                    'election_code'    => $electioncode,
                    'voter_group_name' => $voter_group_name,
                    'description'      => '',
                ];
            }
            if (!empty($array_to_insert)) {
                DB::table('voters_group')->insert($array_to_insert);
            }
        }, 3);

        return back()->with('Success', 'Save success');
    }
    public function savevoterinfo(Request $request)
    {
        $jsonData     = $request->input('input_voters_codes');
        $votersinfo   = json_decode($jsonData, true);
        $electioncode = $votersinfo['electioncode'];
        $votersarray  = json_decode($votersinfo['voters']);

        // Replaces `LOCK TABLES voters WRITE` (which also wrongly excluded the
        // users-table writes below). The whole replace-voters operation is now
        // a single atomic transaction — partial failures roll back fully.
        DB::transaction(function () use ($electioncode, $votersarray) {
            Voter::where('election_code', $electioncode)->delete();
            users::where('election_code', $electioncode)
                ->where('isvoter', 1)
                ->where('profile_code', '!=', 'admin')
                ->whereNotIn('profile_code', $votersarray)
                ->delete();

            $array_to_insert = [];
            foreach ($votersarray as $voter) {
                $array_to_insert[] = [
                    'profile_code'  => $voter,
                    'election_code' => $electioncode,
                    'voter_status'  => 1,
                ];
            }
            if (!empty($array_to_insert)) {
                DB::table('voters')->insert($array_to_insert);
            }

            $this->GenerateUsersForElections($electioncode, $votersarray);
        }, 3);

        return back()->with('Success', 'Save success');
    }

    public function genearteresults($electioncode)
    {
        // Replaces `LOCK TABLES profiles WRITE` — this method never writes to
        // `profiles`, only to `candidates` and `election_rounds`. A standard
        // transaction is sufficient and doesn't block concurrent logins.
        return DB::transaction(function () use ($electioncode) {
            $candidateObj = new candidates();
            $active_round = ElectionRound::where("election_code", $electioncode)
                ->where("round_status", 1)
                ->select("round_number", "win_percentage", "min_win_percentage", "win_sign")
                ->first();
            $lastround = ElectionRound::where("election_code", $electioncode)
                ->select("round_number")
                ->orderBy('round_number', 'desc')
                ->first();
            $win_percentage = 0;
            $win_sign = 1;
            if (isset($active_round->round_number)) {
                $win_percentage = $active_round->win_percentage;
                $min_win_percentage = $active_round->min_win_percentage;
                $win_sign = $active_round->win_sign;
                $candidates_by_election_array = candidates::where(
                    "elections_code",
                    $electioncode
                )
                    ->where("round_number", $active_round->round_number)
                    ->where("candidate_status", '!=',3)
                    ->get();
                $candidates_groups_array = CandidatesGroup::where(
                    "election_code",
                    $electioncode
                )->pluck("win_number", "group_code");


                $voters_who_vote_number = Votemaster::where(
                    "election_code",
                    $electioncode
                )
                    ->where(
                        "round_number",
                        $active_round->round_number
                    )
                    ->count();
                $Vote_codes = Votemaster::where("election_code", $electioncode)
                    ->where(
                        "round_number",
                        $active_round->round_number
                    )
                    ->pluck("vote_code")
                    ->toArray();
                $voters_vote_perc = VoteDetail::selectRaw(
                    "candidate,count(candidate) as num_vote"
                )
                    //->whereIn("vote_code", $Vote_codes)//===stoped for testing #991
                    ->where(
                        "round_number",
                        $active_round->round_number
                    )
                    ->groupBy("candidate")
                    ->pluck("num_vote", "candidate")
                    ->toArray();
                $candidatesarray_result = [];
                foreach ($candidates_by_election_array
                    as $key => $candidates_by_election) {
                    $candidatesarray_result[$key]["candidate_code"] =
                        $candidates_by_election->profile_code;
                    $candidatesarray_result[$key]["election_list_code"] =
                        $candidates_by_election->group_code;
                    $candidatesarray_result[$key]["win_percentage"] = $win_percentage;
                    $votes_he_got_var = 0;
                    if (
                        isset($voters_vote_perc[$candidates_by_election->profile_code])
                    ) {
                        $votes_he_got_var =
                            $voters_vote_perc[$candidates_by_election->profile_code];
                        $candidatesarray_result[$key]["votes_he_got"] = $votes_he_got_var;
                    } else {
                        $candidatesarray_result[$key]["votes_he_got"] = 0;
                    }
                    if ($voters_who_vote_number > 0) {
                        $candidatesarray_result[$key]["votes_he_got_percentage"] =
                            ($votes_he_got_var / $voters_who_vote_number) * 100;
                    } else {
                        $candidatesarray_result[$key]["votes_he_got_percentage"] = 0;
                    }
                    $candidatesarray_result[$key]["ispassed"] = $this->isGreaterThanOrEqualPercentOf(
                        $votes_he_got_var,
                        $voters_who_vote_number,
                        $win_percentage
                    );
                }
                // Convert the sorted array to a collection
                $collection = new Collection($candidatesarray_result);
                // Sort the collection by the 'election_list_code' field from high to low
                $sorted = $collection->sortByDesc("election_list_code");
                // Group the collection by the 'election_list_code' key
                $groupedByElectionList = $sorted->groupBy("election_list_code");
                // Convert the grouped collection back to an array
                $groupedArray = $groupedByElectionList->toArray();
                // Filter the array based on the desired field value and highest votes percentage for each list
                $winning_array_Data = [];
                foreach ($groupedArray as $keys => $group) { //====loop groups with vote percentage and candidates
                    $candidates_winning = collect($candidatesarray_result)
                        ->where("ispassed", true)
                        ->where("election_list_code", '=', $keys)
                        ->all();
                    $candidate_remaining = $candidates_groups_array[$keys] - sizeof($candidates_winning);
                    $winning_array_Data[$keys] = collect($group)
                        ->where("ispassed", true)
                        ->sortByDesc("votes_he_got_percentage")
                        //->take($candidate_remaining)
                        //->take($candidates_groups_array[$keys])
                        ->all();

                    if ($active_round->round_number < $lastround->round_number) {
                        $non_winning_array_Data[$keys] = collect($group)
                            ->where("ispassed", false)
                            ->where("votes_he_got_percentage", '>', $min_win_percentage)
                            ->sortByDesc("votes_he_got_percentage")
                            ->all();
                    } else {
                        $non_winning_array_Data[$keys] = collect($group)
                            ->where("ispassed", false)
                            ->sortByDesc("votes_he_got_percentage")
                            ->take($candidate_remaining)
                            ->all();
                    }
                }

                //=====================update winning candidates
                foreach ($winning_array_Data as $key => $winning_data) {
                    // Extract candidate codes from the  key
                    if (isset($winning_data)) {
                        $candidateCodestowin = collect($winning_data)
                            ->pluck("candidate_code")
                            ->toArray();
                        DB::table('candidates')
                            ->whereIn("profile_code", $candidateCodestowin)
                            ->where("round_number", $active_round->round_number)
                            ->update(['candidate_status' => "2"]);
                    }
                    $count_by_key = count($winning_data);
                    $winning_number = $candidates_groups_array[$key];
                    $rest_candidate_number = 0;
                    $candidate_remaining = $candidates_groups_array[$key] - sizeof($candidateCodestowin);
                    //if ($count_by_key < $winning_number) {
                    if (1==1) {//===no limit for next round
                        //$rest_candidate_number = $winning_number - $count_by_key;
                        //============non winning array
                        $candidateCodestonextround = collect($non_winning_array_Data[$key])
                            ->pluck("candidate_code")
                            ->toArray();
                        if ($active_round->round_number < $lastround->round_number) {
                            $cand = candidates::whereIn("profile_code", $candidateCodestonextround)
                                ->where(['round_number' => $active_round->round_number])->get();
                            foreach ($cand as $candobj) {
                                $newcandidate = new candidates();
                                $newcandidate->profile_code = $candobj->profile_code;
                                $newcandidate->elections_code = $candobj->elections_code;
                                $newcandidate->round_number = $active_round->round_number + 1;
                                $newcandidate->group_code = $candobj->group_code;
                                $newcandidate->candidate_status = $candobj->candidate_status;
                                $newcandidate->save();
                            }
                            if (sizeof($candidateCodestonextround) == $candidate_remaining) {
                                DB::table('candidates')
                                    ->whereIn("profile_code", $candidateCodestonextround)
                                    ->where("round_number", $active_round->round_number + 1)
                                    ->update(['candidate_status' => "2"]);
                            }
                        }
                    }
                }
                ElectionRound::where("election_code", $electioncode)
                    ->where("round_number", $active_round->round_number)
                    ->update(['round_status' => '2']);
            }
            return 1;
        }, 3);
    }

    function isGreaterThanOrEqualPercentOf($number1, $number2, $percentage)
    {
        // Calculate 50% of the second number
        $PercentOfNumber2 = $number2 * ($percentage / 100);

        // Check if the first number is greater than or equal to 50% of the second number
        return $number1 >= $PercentOfNumber2;
    }

    function showguestresults($electioncode)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $Profiles = Profiles::all();
            $Elections = Election::all();
            $user = users::Join('profiles', 'users.profile_code', '=', 'profiles.profile_code')
                ->select('user_code', 'full_name', 'users.profile_code', 'election_code', 'isconnected')
                ->where('user_code', session('guest_usercode', ''))
                ->first();
                $candidategroup = CandidatesGroup::where('election_code', $electioncode)
                ->select('group_name', 'group_code')->get();
            $electionobj = Election::where('election_code', $electioncode)->get()->first();
            $election_rounds = ElectionRound::where('election_code', $electioncode)->select('round_number')->get();
            $curr_election_rounds = ElectionRound::where('election_code', $electioncode)
            ->where('round_status', 2)
    ->orderBy('round_number', 'desc')
    ->first();
            return view('guestresults', [
                'full_name' => $full_name, 'users' => $user,
                'electionobj' => $electionobj, 'election_rounds' => $election_rounds,
                'candidategroup'=>$candidategroup,'curr_election_rounds'=>$curr_election_rounds
            ]);
        } else {
            return view('welcome');
        }
    }

    function getelectionresults($electioncode, $roundnumber)
    {
        DB::statement("SET sql_mode=''");
        $votersTotal = Votemaster::selectRaw("round_number,count(distinct(user_code)) as totalvoters")
            ->where('election_code', $electioncode)
            ->groupby('round_number')
            ->pluck("totalvoters", "round_number");
        $candidategroup = CandidatesGroup::where('election_code', $electioncode)->pluck('group_name', 'group_code')->toArray();
        $candidategroup_winnumber = CandidatesGroup::where('election_code', $electioncode)->pluck('win_number', 'group_code')->toArray();
        $Vote_codes = Votemaster::where("election_code", $electioncode)
            ->where("round_number", '<=', $roundnumber)
            ->pluck("vote_code")
            ->toArray();
        $voters_vote_perc = VoteDetail::selectRaw("candidate,count(candidate) as num_vote")
            //->whereIn("vote_code", $Vote_codes)//===stoped for testing #991
            ->where("round_number", '=', $roundnumber)
            ->groupBy("candidate")
            ->pluck("num_vote", "candidate")
            ->toArray();
        $old_voters_vote_perc=VoteDetail::selectRaw("candidate,count(candidate) as num_vote")
        //->whereIn("vote_code", $Vote_codes)//===stoped for testing #991
        ->where("round_number", '<', $roundnumber)
        ->groupBy("candidate")
        ->pluck("num_vote", "candidate")
        ->toArray();
        //===all candidates not just winning
        $candidates_winning = Profiles::select('profiles.profile_code', 'full_name', 'mobile', 'candidates.group_code','round_number')
            ->leftjoin('candidates', 'profiles.profile_code', '=', 'candidates.profile_code')
            ->where("candidate_status", '!=',3)
            ->where(function ($query) use ($roundnumber) {
                $query->where('round_number', '=', $roundnumber) // if round_number = $roundnumber
                      ->orWhere(function ($query) use ($roundnumber) {
                          $query->where('round_number', '<', $roundnumber) // if round_number < $roundnumber
                                ->where('candidate_status', 2); // and candidate_status = 2
                      });
            })
            ->get();
        $win_max = ElectionRound::where('election_code', $electioncode)
            ->pluck('win_percentage','round_number')->toarray();
        $win_min = ElectionRound::where('election_code', $electioncode) 
             ->pluck('min_win_percentage','round_number')->toarray();
        $round_status = ElectionRound::where('election_code', $electioncode)
            ->where("round_number",  $roundnumber)->value('round_status');

        $data = array();
        if (($round_status == 2)) {
            $countervar = 0;
            //$win_number_by_groupcode_array=array();
            foreach ($candidates_winning as $candidate) {
                $data[$countervar]['full_name'] = $candidate['full_name'];
                $data[$countervar]['profile_code'] = $candidate['profile_code'];
                if (isset($candidategroup[$candidate['group_code']])) {
                    $data[$countervar]['group_name'] = $candidategroup[$candidate['group_code']];
                } else {
                    $data[$countervar]['group_name'] = 0;
                }
                $elect_perc = 0;
                if($candidate['round_number']==$roundnumber){
                if (isset($voters_vote_perc[$candidate['profile_code']])) {
                    $elect_perc = $voters_vote_perc[$candidate['profile_code']];
                }
                }else{
                    if (isset($old_voters_vote_perc[$candidate['profile_code']])) {
                        $elect_perc = $old_voters_vote_perc[$candidate['profile_code']];
                    } 
                }
                $data[$countervar]['can_round_num'] = $candidate['round_number'];
                $data[$countervar]['elect_perc'] = $elect_perc;
                $data[$countervar]['group_code'] = $candidate['group_code'];
                $data[$countervar]['votersTotal'] = $votersTotal[$candidate['round_number']];
                $data[$countervar]['win_max'] = $win_max[$candidate['round_number']];
                $data[$countervar]['win_min'] = $win_min[$candidate['round_number']];
                //$odd = ($elect_perc / $votersTotal) * 100;
                //$reswin = 0;
               
                
                
                //====bug road
   
                //$data[$countervar]['reswin'] = $reswin;
                $countervar++;
            }
            $countervar = 0;
            $win_number_by_groupcode_array = array();
            // Custom comparison function
           
            $data = collect($data)->sortBy('group_code')->sortByDesc('elect_perc')->values()->all();
            foreach ($data as $dataobj) {
                $odd = ($dataobj['elect_perc'] / $dataobj['votersTotal']) * 100;
                $reswin = 0;
                if ($odd >= $win_max[$candidate['round_number']]) {
                    if (array_key_exists($dataobj['group_code'], $win_number_by_groupcode_array)) {
                        $win_num_key = $win_number_by_groupcode_array[$dataobj['group_code']];
                        $win_number_by_groupcode_array[$dataobj['group_code']] = $win_num_key + 1;
                    } else {
                        $win_number_by_groupcode_array[$dataobj['group_code']] = 1;
                    }
                    if ($dataobj['can_round_num'] == $roundnumber) {
                        if (
                            $win_number_by_groupcode_array[$dataobj['group_code']] <=
                            $candidategroup_winnumber[$dataobj['group_code']]
                        ) {
                            $reswin = 1;
                        } else {
                            $reswin = 2;
                        }
                    } else {
                        $reswin = 1;
                    }
                } else
            if ($odd < $win_max[$candidate['round_number']] && $odd > $win_min[$candidate['round_number']]) {
                    $reswin = 2;
                } else {
                    $reswin = -1;
                }

                $data[$countervar]['reswin'] = $reswin;
                $countervar++;
            }
        }
        
        
        return $data;
    }

    public function GenerateUsersForElections($election_code,$voters_array)
    {
        $userscodes = users::where('isblocked', 0)
        ->where('admin','!=', 1)
        ->pluck('user_code')->toArray();
        //$voters_array = Profiles::pluck('profile_code')->ToArray();
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
}
