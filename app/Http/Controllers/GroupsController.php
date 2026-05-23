<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GroupMaster;

class GroupsController extends Controller
{


    public function showgrouplist(Request $request){
        $full_name = session('full_name', '');
        $groups=GroupMaster::all();
        if($full_name!=''){
            return view('GroupsList',['full_name'=>$full_name,'groups'=>$groups]);
        }else{
            return view('welcome');
        }
    }
    public function showAddgroupsForm(Request $request){
        $full_name = session('full_name', '');
        if($full_name!=''){
        return view('addGroups',['full_name'=>$full_name]);
        }else{
            return view('welcome');
        }
    }
    public function savegroups(Request $request){
        try {
            DB::raw('LOCK TABLES groups_master WRITE');
            DB::beginTransaction();
            // Lock the table for writing
            //================save profile
        
            $max_GroupMaster_Id = GroupMaster::max('idgroups')+1;
        $choosen_profile_id_arr=$request->input('choosen_profile_id');
        $GroupMaster_var=new GroupMaster();
        $GroupMaster_var->election_code=$request->input('input_election_code');
        $GroupMaster_var->group_code='grp_'.$max_GroupMaster_Id;
        $GroupMaster_var->group_name=$request->input('input_group');
        $countervar=0;
        foreach ($choosen_profile_id_arr as $value) {
            $groups_array[$countervar]["profile_code"]=$value;
            $countervar++;
        }
       
        if(isset($groups_array)){
            $GroupMaster_var->group_member=json_encode( $groups_array);
        }
         $GroupMaster_var->save();  
             DB::commit();
             DB::raw('UNLOCK TABLES');
            } catch (\Exception $e) {
               
                DB::rollBack();
                throw $e;
                
            }
            
           return back()->with('Success','Save success');
    }

    public function getgroups(){
        // Perform the Eloquent query
        $GroupMaster = GroupMaster::all();

        // If you want to get all matching records, use get() instead of first()

        return response()->json($GroupMaster);
    }
}
