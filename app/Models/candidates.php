<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class candidates extends Model
{
    use HasFactory;
    protected $table = 'candidates';
    public $timestamps = false; // Assuming you don't need timestamps or adjust as needed

    protected $fillable = [
        'profile_code',
        'elections_code',
        'round_number',
        'group_code',
        'candidate_status',
        // Add other fields as needed
    ];
}
