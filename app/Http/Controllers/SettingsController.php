<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profiles;
use App\Models\Setting;

class SettingsController extends Controller
{
    function showsettingsform(Request $request){
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $profilecode = $request->profile_code;
            $settings = Setting::all();
            // Retrieve the profile where profile_code equals a specific value
            $current_profile = Profiles::where('profile_code', $profilecode)->first();
            return view('settings', ['full_name' => $full_name, 
            'current_profile' => $current_profile,'settings'=>$settings]);
        } else {
            return view('welcome');
        }
    }

    function savesettings(Request $request){
        try {
            $jsonData = $request->json()->all();
            $settings_name=$jsonData['sett_name'];
            $settings_value=$jsonData['sett_value'];
            $exists = Setting::where('settings_name', $settings_name)->exists();
            if ($exists) {
            Setting::where('settings_name', $settings_name)
            ->update(['settings_value' => $settings_value]);
            }else{
                $setting = new Setting();
             $setting->settings_name = $settings_name;
            $setting->settings_value = $settings_value;
              $setting->save();
            }
           
            return response()->json(['message' => 'Data saved successfully'], 200);
        } catch (\Exception $e) {
            // Exception handling
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
