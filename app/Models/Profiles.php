<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Profiles extends Model
{
    // Specify the table associated with the model
    protected $table = 'profiles';

    // Specify the primary key for the table
    protected $primaryKey = 'idprofile';

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'profile_code',
        'full_name',
        'sex',
        'age',
        'mobile',
        'address',
        'picture',
        'session_handle',
        'attachment',
        'isconnected',
    ];

    
    public function profile()
    {
        return $this->belongsTo(profiles::class, 'profile_code', 'profile_code');
    }

    public function updateconnectedfield($profilecode,$updateval){
        profiles::where('profile_code', $profilecode)->update(['isconnected' => $updateval,'session_handle'=>Session::getId()]);
    }
}
