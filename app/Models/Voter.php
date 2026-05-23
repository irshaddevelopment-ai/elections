<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    use HasFactory;
    protected $table = 'voters';

    protected $primaryKey = 'idvoters';

    protected $fillable = [
        'profile_code',
        'election_code',
        'voter_status',
        'voter_group_code',
    ];

    // Automatically manage timestamps
    public $timestamps = true;

}
