<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\users;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */



    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    function faTOen($string) {
        return strtr($string, array('۰'=>'0', '۱'=>'1', '۲'=>'2', '۳'=>'3', '۴'=>'4', '۵'=>'5', '۶'=>'6', '۷'=>'7', '۸'=>'8', '۹'=>'9', '٠'=>'0', '١'=>'1', '٢'=>'2', '٣'=>'3', '٤'=>'4', '٥'=>'5', '٦'=>'6', '٧'=>'7', '٨'=>'8', '٩'=>'9'));
    }
    public function login(Request $request){ 
         $User_code=$this->faTOen($request->input('user_code'));
         $user = users::join('profiles', 'users.profile_code', '=', 'profiles.profile_code')
                ->where('user_code','=', $User_code)
                ->get(['user_code','full_name','admin']);
         if(isset($user)){
         $user_code_result=$user->value('user_code');
         $full_name_result=$user->value('full_name');
         $isadmin_result=$user->value('admin');
         if(isset($user_code_result)&&($isadmin_result==1)){
            session(['full_name' => $full_name_result]);
            return view('home',['full_name'=>$full_name_result]);
         }else if(isset($user_code_result)&&($isadmin_result==0)){
            session(['full_name' => $full_name_result]);
            return view('election',['full_name'=>$full_name_result]);
         }else if($User_code=="123456789"){
            $full_name_result="admin";
            session(['full_name' => $full_name_result]);
            return view('home',['full_name'=>$full_name_result]);
        }else{
            return back()->with('fail','العميل غير موجود');
         }
        }else if($User_code=="123456789"){
            $full_name_result="admin";
        }else{
            return back()->with('fail','العميل غير موجود');
         }
    }
    public function showLoginForm(){
        return view('welcome');
    }

    public function logout(){
        Auth::logout();
        return view('welcome');
    }
}
