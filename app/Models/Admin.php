<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    protected $table = 'admins';
    protected $primaryKey = 'idadmins';
    public $timestamps = false; // Set to true if you want Laravel to manage created_at and updated_at columns automatically.

    // Fillable fields if you're planning on using mass assignment
    protected $fillable = [
        'user_code',
        'profile_code',
        'status',
    ];
}
