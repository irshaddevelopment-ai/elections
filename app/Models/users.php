<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class users extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'idusers';
    public $timestamps = false; // If you don't want to use created_at and updated_at columns

    protected $fillable = [
        'user_code',
        'election_code',
        'profile_id',
        'elections_code',
        'isblocked',
        'remember_token',
        'admin',
        'isleader',
        'isvoter',
    ];

    // If you have date columns, you can define them as Carbon instances
    protected $dates = ['created_at', 'updated_at'];
}
