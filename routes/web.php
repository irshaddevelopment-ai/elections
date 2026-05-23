<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\VoterController;
use App\Http\Controllers\SettingsController;
use FastRoute\RouteCollector;



// Include the FastRoute dispatcher


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::fallback(function () {
    return redirect('/');
});
Route::post("/home",[loginController::class,'gotohomepage'])
    ->name('home')->middleware('checkSessionExpiry');

Route::post("/savesettings",[SettingsController::class,'savesettings'])->name('savesettings');
Route::get("/settings",[SettingsController::class,'showsettingsform'])->name('settings');

Route::get("/guesthome",[loginController::class,'guesthome'])->name('guesthome');
Route::get("/dashboard",[loginController::class,'gotodashboard'])->name('dashboard');
Route::get("/activeelection",[loginController::class,'getactiveelection'])->name('activeelection');
Route::get("/logout/{profile_code}",[loginController::class,'logout'])->name('logout');
Route::get("/resetdata",[loginController::class,'resetdata'])->name('resetdata');
Route::get("/resetlogin",[loginController::class,'resetlogin'])->name('resetlogin');
//====================user controller
Route::get("/usermanager/{prfcode}",[UsersController::class,'ShowEditUserManagerForm'])->name('editusermanager');
Route::get("/usermanager",[UsersController::class,'ShowUserManagerForm'])->name('usermanager');
Route::get("/userslist",[UsersController::class,'ShowUsersListForm'])->name('userslist');
Route::post("/deleteuser",[UsersController::class,'deleteUser'])->name('deleteuser');
Route::post("/saveuserinfo",[UsersController::class,'saveuserinfo'])->name('saveuserinfo');
Route::post('/importexcel', [UsersController::class,'importexcel'])->name('importexcel');
Route::get("/adminresults",[UsersController::class,'showadminresults'])->name('adminresults');
Route::post("/uploadusersfolder",[UsersController::class,'uploadusersfolder'])->name('uploadusersfolder');
Route::put("/resetusercode/{prfcode}",[UsersController::class,'resetusercode'])->name('resetusercode');
Route::get("/idcard",[UsersController::class,'showidentificationcard'])->name('idcard');
Route::get("/getidcard/{prfcode}/{election_code}",[UsersController::class,'getidcard'])->name('getidcard');
Route::get("/getevents/{prfcode}",[UsersController::class,'getevents'])->name('getidcard');
Route::post("/saveprofileextrainfo",[UsersController::class,'saveprofileextrainfo'])->name('saveprofileextrainfo');
Route::post("/importcardinfo",[UsersController::class,'importCardInfo'])->name('importcardinfo');

//====================subject controller
Route::get("/subjectmanager",[SubjectController::class,'ShowSubjectManagerForm'])->name('subjectmanager');
Route::get("/subjectslist",[SubjectController::class,'ShowSubjectsListForm'])->name('subjectslist');
Route::post("/savesubjectinfo",[SubjectController::class,'saveSubjectInfo'])->name('savesubjectinfo');
//====================================================================
//====================election controller
Route::get("/electionmanager/{electioncode}",[ElectionController::class,'ShowEditManagerForm'])->name('editelectionmanager');
Route::get("/electionmanager",[ElectionController::class,'ShowElectionManagerForm'])->name('electionmanager');
Route::get("/electionwithlistmanager",[ElectionController::class,'ShowElectionwithlistManagerForm'])->name('electionwithlistmanager');
Route::get("/electionlauncher",[ElectionController::class,'ShowElectionLauncherForm'])->name('electionlauncher');
Route::get("/electionslist",[ElectionController::class,'ShowElectionListForm'])->name('electionslist');
Route::post("/saveelectioninfo",[ElectionController::class,'saveElectionInfo'])->name('saveelectioninfo');
Route::get("/electioninfo/{eleccode}/{roundnumber}",[ElectionController::class,'getElectionInfo'])->name('electioninfo');
Route::get("/getProfiles/{eleccode}",[ElectionController::class,'getProfiles'])->name('getProfiles');
Route::put('/updatestatus/{election_code}/{election_status}/{codingvar}', [ElectionController::class, 'updateElectionStatus'])->name('updatestatus');
Route::put('/updateLaunchstatus/{election_code}/{round_number}/{round_status}', [ElectionController::class, 'updateLaunchstatus'])->name('updateLaunchstatus');
Route::post('/deleteelection', [ElectionController::class, 'deleteelection'])->name('deleteelection');

//====================election controller
//=================Candidate controller
Route::get("/candidatemanager",[CandidateController::class,'ShowCandidateManagerForm'])->name('candidatemanager');
Route::get("/candidatemanager/{election_code}/{group_code}/{round_number}",[CandidateController::class,'getcandidatesbygroup'])->name('candidatemanagerbygroup');
Route::post("/savecandidateinfo",[CandidateController::class,'savecandidateinfo'])->name('savecandidateinfo');
Route::post("/savecandidatelist",[CandidateController::class,'savecandidatelist'])->name('savecandidatelist');
Route::delete("/deletecandidatelist/{group_code}",[CandidateController::class,'deleteCandidateList'])->name('deletecandidatelist');
Route::post("/resetcandidate",[CandidateController::class,'resetcandidate'])->name('resetcandidate');
//=================Candidate controller
//=================Voter controller
Route::get("/votermanager",[VoterController::class,'ShowVoterManagerForm'])->name('votermanager');
Route::post("/savevoterinfo",[VoterController::class,'savevoterinfo'])->name('savevoterinfo');
Route::post("/savevotergroup",[VoterController::class,'savevotergroup'])->name('savevotergroup');
Route::post("/savevote",[VoterController::class,'saveVote'])->name('savevote');
Route::get("/votersperc/{eleccode}",[VoterController::class,'getvotersperc'])->name('votersperc');
Route::get("/loggedinperc/{eleccode}",[VoterController::class,'getloggedinperc'])->name('loggedinperc');
Route::get("/voterprofiles/{eleccode}",[VoterController::class,'getvoterprofiles'])->name('voterprofiles');
Route::get("/voterprofilesforgroups/{eleccode}",[VoterController::class,'getvoterprofilesforgroups'])->name('voterprofilesforgroups');
Route::get("/getvoterstatus/{usercode}/{eleccode}/{roundcount}",[VoterController::class,'getvoterstatus'])->name('getvoterstatus');
Route::get("/getvotersforleaderinfo/{datevar}/{eleccode}",[VoterController::class,'getvotersforleaderinfo'])->name('getvotersforleaderinfo');
Route::get("/getcandidatesstatus/{eleccode}",[VoterController::class,'getcandidatesstatus'])->name('getcandidatesstatus');
Route::put("/genearteresults/{eleccode}",[VoterController::class,'genearteresults'])->name('genearteresults');
Route::get("/guestresults/{eleccode}",[VoterController::class,'showguestresults'])->name('guestresults');
Route::get("/getelectionresults/{eleccode}/{roundnumber}",[VoterController::class,'getelectionresults'])->name('getelectionresults');
Route::get("/getvotersbyelection/{eleccode}/{votestatus}/{clickvar}/{datetosend}",[VoterController::class,'getvotersbyelection'])->name('getvotersbyelection');
Route::get("/getvoterschoosen/{hashMapJson_str}",[VoterController::class,'getvoterschoosen'])->name('getvoterschoosen');
//=================Voter controller
//=================Group controller
Route::get("/groupmanager",[GroupController::class,'ShowGroupManagerForm'])->name('groupmanager');
Route::get("/groupslist",[GroupController::class,'ShowGroupsListForm'])->name('groupslist');
Route::get("/getvotergroups/{electioncode}",[GroupController::class,'getVoterGroups'])->name('getvotergroups');
Route::put("/updatevotergroup/{voter_group_code}",[GroupController::class,'updateVoterGroup'])->name('updatevotergroup');
Route::delete("/deletevotergroup/{voter_group_code}",[GroupController::class,'deleteVoterGroup'])->name('deletevotergroup');
//=================Group controller
//=================Leader controller
Route::get("/leadermanager",[LeaderController::class,'ShowLeaderManagerForm'])->name('leadermanager');
Route::get("/leaderslist",[LeaderController::class,'ShowLeadersListForm'])->name('leaderslist');
Route::get("/getvotersbyleader/{eleccode}/{leader_code}",[LeaderController::class,'getvotersbyleader'])->name('getvotersbyleader');
Route::post("/saveleaderinfo",[LeaderController::class,'saveleaderinfo'])->name('saveleaderinfo');
Route::get("/leaderdash",[LeaderController::class,'showleaderdash'])->name('leaderdash');
Route::get("/getvotersbyelectioncode/{eleccode}",[LeaderController::class,'getvotersbyelectioncode'])->name('getvotersbyelectioncode');


//=================Leader controller
