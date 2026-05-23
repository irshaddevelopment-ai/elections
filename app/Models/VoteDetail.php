<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoteDetail extends Model
{

    use HasFactory;
    
    protected $table = 'vote_detail';
    protected $primaryKey = 'idvote_detail';
    public $timestamps = false; // Set to false if you don't want to use Laravel's automatic timestamp handling

    protected $fillable = [
        //'vote_code',//===stoped for testing #991 
        'election_list_code', 'round_number', 'candidate'
    ];

    // You can add any additional model logic or relationships here
}
