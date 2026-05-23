<?php
  // app\Models\Leader.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leader extends Model
{
    use HasFactory;
    protected $table = 'leaders';
    protected $primaryKey = 'idleaders';
    public $timestamps = false; // Set to true if you want to use timestamps

    protected $fillable = [
        'profile_code',
        'voter_group_code',
        'election_code',
    ];

    // Add your relationships or other custom logic here
}

?>