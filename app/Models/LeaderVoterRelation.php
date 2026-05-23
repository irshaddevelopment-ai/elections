<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderVoterRelation extends Model
{
    use HasFactory;
    protected $table = 'leader_voter_rel';

    protected $fillable = [
        'leader_profile_code',
        'voter_group_code',
        'voter_profile_code',
        'election_code'
    ];

    public $timestamps = true;
}
