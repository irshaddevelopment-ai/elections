<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\users;
use App\Models\Profiles;
use App\Models\Election;
use App\Models\candidates;
use App\Models\Voter;
use App\Models\CandidatesGroup;
use App\Models\ElectionRound;
use App\Models\Admin;
use App\Models\Leader;
use App\Models\EventTable;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Models\Votemaster;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;





//  note: never return view from a post request
class loginController extends Controller
{
    function faTOen($string)
    {
        return strtr($string, array('۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9'));
    }

    public function guesthome(Request $request)
    {
        DB::statement("SET sql_mode=''");
        $User_code =session('guest_usercode'); 
        $user = users::Join('profiles', 'users.profile_code', '=', 'profiles.profile_code')
                ->select('user_code', 'full_name', 'users.profile_code', 'election_code', 'isconnected','picture','session_handle')
                ->where('user_code', $User_code)
                ->first();
            if (isset($user)) {
                $currentSessionId = Session::getId();
                $currentsession_fullname = session('full_name','');
                $previousSessionId = $user->session_handle ?? null;
                $session_chaned=false;
                $session_full_name=false;
                if ($previousSessionId && $currentSessionId !== $previousSessionId) {
                    $session_chaned=true;
                } 
                if($currentsession_fullname&& $currentsession_fullname!=$user->full_name){
                    $session_full_name=true;
                }
                if (($user->isconnected != 1)or(($user->isconnected == 1)and
                (($session_chaned==false)||$session_full_name==false))) {
                    $profiles=new profiles();
                    $profiles->updateconnectedfield($user->profile_code, 1);
                    
                    session(['full_name' => $user->full_name]);
                    session(['profile_code' => $user->profile_code]);
                    $election_type = Election::where('election_code', $user->election_code)->value('election_type');
                    if ($election_type == 2) {
                        $count_round_row = ElectionRound::where('round_status', '!=', 2)->first();
                        if(isset($count_round_row)){
                        $count_round = $count_round_row->round_number;
                        $count_round_status = $count_round_row->round_status;
                        }else{
                            $count_round=0;  
                            $count_round_status = 0;
                        }
                       
                        $electioncodeObj=$user->election_code;
                        $candidate_groups = DB::table('candidates_groups')
                            ->select('candidates_groups.group_code', 'election_code', 'group_name', 'win_number', 
                            DB::raw('COUNT(profile_code) as candidates_number'))
                            ->join('candidates', function ($join) use ($count_round,$electioncodeObj) {
                                $join->on('candidates_groups.group_code', '=', 'candidates.group_code')
                                    ->where('candidates.elections_code', '=', $electioncodeObj)
                                    ->where('round_number', $count_round)
                                    ->where('candidates_groups.election_code', '=', $electioncodeObj);
                            })
                            ->groupBy('election_code', 'candidates.group_code')
                            ->get();
                        if (isset($candidate_groups[0]->group_code)) {
                            $candidates = profiles::Join('candidates', 'profiles.profile_code', '=', 'candidates.profile_code')
                                ->select('full_name', 'address', 'sex', 'picture', 'attachment', 'profiles.profile_code')
                                ->where('elections_code', $user->election_code)
                                ->where('group_code', $candidate_groups[0]->group_code)
                                ->where('round_number', $count_round)
                                ->get();
                        } else {
                            $candidates = [];
                        }
                    } else {
                        $candidate_groups = [];
                    }
                    $electionobj = Election::where('election_code', $user->election_code)->get()->first();


                    $isleader = Leader::where('profile_code', $user->profile_code)->count();

                    $isvoter = Voter::where('profile_code', $user->profile_code)->count();
                    if (!isset($count_round)) {
                        $count_round = 0;
                    }
                    // Winners in the CURRENT round — used to compute remaining slots per list
                    $results_exists = candidates::select('group_code', \DB::raw('count(profile_code) as count_prf'))
                    ->where('elections_code', $user->election_code)
                    ->where('candidate_status', 2)
                    ->where('round_number', $count_round)
                    ->groupBy('group_code')
                    ->pluck('count_prf','group_code')->toArray();

                    // Enable results button if any round has produced winners
                    $any_results_exist = candidates::where('elections_code', $user->election_code)
                    ->where('candidate_status', 2)
                    ->exists();
                    

                    $profiles = new profiles();
                    $isvotedbefore=Votemaster::where('election_code', $user->election_code)
                    ->where('round_number', $count_round)
                    ->where('user_code', $user->user_code)->exists();
                    /*$profiles->updateconnectedfield($user->profile_code, 1);
                        if (Votemaster::where('user_code', $user->user_code)
                            ->where('election_code', $user->election_code)
                            ->count() === 0
                        ) {
                            return view('guest', ['users' => $user, 'candidates' => $candidates, 'electionobj' => $electionobj, 'candidate_groups' => $candidate_groups, 'electioncode' => $user->election_code]);
                        } else {
                            return back()->with('fail', 'هذا الرمز تم تفعيله من قبل');
                        }*/
                        /*if($isvoter){
                        return view('guest', ['users' => $user, 'candidates' => $candidates, 
                        'electionobj' => $electionobj, 'candidate_groups' => $candidate_groups, 
                        'electioncode' => $user->election_code,"count_round"=>$count_round,
                         'isleader'=>$isleader,'results_exists'=>$results_exists,'isvoter'=>$isvoter,
                         'count_round_status'=>$count_round_status,'isvotedbefore'=>$isvotedbefore]); 
                        }else{
                            return view('leaderdash', [
                                'users' => $user,
                                'isleader' => $isleader, 'electionobj' => $electionobj,
                                'count_round' => $count_round
                            ]);
                        }*/
                        $setting = Setting::where('settings_name', 'sett_idcard')->get()->first();

                        DB::table('event_table')
                        ->where('session_handle',  Session::getId())
                        ->where('user_code',  $user->user_code)
                        ->where('prf_code',  $user->profile_code)
                        ->where('event_description','login')
                        ->update(['loggedout_datetime' => DB::raw('NOW()')]);
                        $eventtab = new EventTable();
                        $eventtab->user_code = $user->user_code;
                        $eventtab->prf_code = isset($user->profile_code)?$user->profile_code:'admin';
                        $eventtab->connected = 1;
                        $eventtab->event_description = 'login';
                        $eventtab->session_handle = Session::getId();
                        $eventtab->save();
                        return view('guest', ['users' => $user, 'candidates' => $candidates,
                        'electionobj' => $electionobj, 'candidate_groups' => $candidate_groups,
                        'electioncode' => $user->election_code,"count_round"=>$count_round,
                         'isleader'=>$isleader,'results_exists'=>$results_exists,'isvoter'=>$isvoter,
                         'count_round_status'=>$count_round_status,'isvotedbefore'=>$isvotedbefore,
                         'setting'=>$setting,'any_results_exist'=>$any_results_exist]);
                } else {
                    return back()->with('fail', 'متصل بالفعل من جهاز آخر');
                }
            } else {
                $error_login = 1;
            }
    }
    public function gotohomepage(Request $request)
    {
        //Session::regenerate();
        DB::statement("SET sql_mode=''");
        $error_login = 0;
        $User_code = $this->faTOen($request->input('user_code')) != "" ?
            $this->faTOen($request->input('user_code')) : session('user_code');
        $prf_code_obj=users::where('user_code', $User_code)->value('profile_code');
        
        // Get the value of the MY_VARIABLE environment variable
        $default_usercode = env('APP_DEFAULTUSER');
        $superadmin_user  = env('SUPERADMIN_USER');
        $superadmin_pass  = env('SUPERADMIN_PASS');
        $input_pass       = $request->input('super_pass', '');

        if ($User_code === $superadmin_user && $input_pass === $superadmin_pass) {
            $full_name_result = "superadmin";
            session(['full_name' => $full_name_result]);
            session(['user_code' => $superadmin_user]);
            session(['profile_code' => $superadmin_user]);
            $eventtab = new EventTable();
            $eventtab->user_code = $superadmin_user;
            $eventtab->prf_code = 'superadmin';
            $eventtab->connected = 1;
            $eventtab->event_description = 'login';
            $eventtab->session_handle = Session::getId();
            $eventtab->save();
            return redirect()->route('dashboard', ['full_name' => $full_name_result, 'prfcode' => '']);
        }

        if ($User_code === $default_usercode) {
            $full_name_result = "admin";
            session(['full_name' => $full_name_result]);
            session(['user_code' => $default_usercode]);
            session(['profile_code' => $default_usercode]);
            $eventtab = new EventTable();
            $eventtab->user_code = $User_code;
            $eventtab->prf_code = isset($prf_code_obj)?$prf_code_obj:'admin';
            $eventtab->connected = 1;
            $eventtab->event_description = 'login';
            $eventtab->session_handle = Session::getId();
            $eventtab->save();
            return redirect()->route('dashboard', ['full_name' => $full_name_result, 'prfcode' => '']);
        } else {
            $error_login = 1;
        }

        $exists = Admin::where('user_code', $User_code)->exists();

        if ($exists) {
            $user = Admin::Join('profiles', 'admins.profile_code', '=', 'profiles.profile_code')
                ->select('user_code', 'full_name', 'admins.profile_code', 'isconnected', 'profiles.profile_code')
                ->where('user_code', $User_code)
                ->first();
            
            if (isset($user)) {
                if ($user->isconnected == 0) {
                    $full_name_result = $user->full_name;
                    $profiles = new profiles();
                    $profiles->updateconnectedfield($user->profile_code, 1);
                    session(['full_name' => $full_name_result]);
                    session(['user_code' => $user->user_code]);
                    session(['profile_code' => $user->profile_code]);
                    $current_election = Election::where('election_status', '1')->get()->first();
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
            $election_users_exists=users::where('election_code', $election_code)->exists() ? 1 : 0;

            $eventtab = new EventTable();
        $eventtab->user_code = $user->user_code;
        $eventtab->prf_code = isset($user->profile_code)?$user->profile_code:'admin';
        $eventtab->connected = 1;
        $eventtab->event_description = 'login';
        $eventtab->session_handle = Session::getId();
        $eventtab->save();
                    return view('admin', ['full_name' => $full_name_result, 'prfcode' => $user->profile_code,
                    'current_election' => $current_election,
                    'Elections' => $Elections, 'ElectionRoundsHashMap' => $ElectionRoundsHashMap,'election_users_exists'=>$election_users_exists]);
                } else if (session('full_name') != $user->full_name) {
                    return back()->with('fail', 'متصل بالفعل من جهاز آخر');
                }
            } else {
                $error_login = 1;
            }
        } else {
            $user = users::Join('profiles', 'users.profile_code', '=', 'profiles.profile_code')
                ->select('user_code', 'full_name', 'users.profile_code', 'election_code', 'isconnected')
                ->where('user_code', $User_code)
                ->first();
                $election_code_obj=isset($user->election_code) ? $user->election_code : '';
                $iselectionactivated=Election::where('election_code',$election_code_obj)->value('election_status');
            if (isset($user)) {
            if($iselectionactivated!=0){
                if ($user->isconnected == 1) {
                    return back()->with('fail', 'المستخدم متصل بالفعل من جهاز آخر');
                }
                session(['guest_usercode' => $User_code]);
                return redirect()->route('guesthome');
            }else{
                $error_login = 10;
            }
            }else{
                $error_login = 1;
            }
        }


        if ($error_login === 1) {
            return back()->with('fail', 'المقترع غير موجود');
        }
        if ($error_login === 10) {
            return back()->with('fail', 'العملية الانتخابية غير مفعلة');
        }
    }

    public function gotodashboard(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $current_election = Election::where('election_status', '1')->get()->first();
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
            $election_code_obj=isset($current_election->election_code) ? $current_election->election_code : '';
            $election_users_exists=users::where('election_code', $election_code_obj)->exists() ? 1 : 0;
            return view('admin', ['full_name' => $full_name, 'current_election' => $current_election,
            'Elections' => $Elections, 'ElectionRoundsHashMap' => $ElectionRoundsHashMap,'election_users_exists'=>$election_users_exists]);
        } else {
            return view('welcome');
        }
    }

    public function getactiveelection()
    {
        return [10, 20, 30, 40];
    }

    public function logout($profile_code)
    {
        // Remove the 'full_name' session variable
        Session::forget('full_name');
        Session::forget('user_code');
        Session::forget('profile_code');
        $profiles = new profiles();
        $profiles->updateconnectedfield($profile_code, 0);
        $sessionid=Session::getId();
        DB::table('event_table')
        ->where('session_handle',  $sessionid)
        ->where('event_description','login')
        ->update(['loggedout_datetime' => DB::raw('NOW()')]);
        Session::regenerate();
        return view('welcome');
    }

    public function resetdata(){
        $tables = DB::select('SHOW FULL TABLES WHERE TABLE_TYPE = ?', ['BASE TABLE']);
        foreach ($tables as $key => $value) {
            // Truncate each table
            if (Schema::hasTable($value->Tables_in_election)) {
                DB::table($value->Tables_in_election)->truncate();
            }
        }
        return 1;
    }
    public function resetlogin(){
        DB::table('profiles')->update(['isconnected' => 0, 'session_handle' => '']);
        return response()->json(['success' => true]);
    }
}
