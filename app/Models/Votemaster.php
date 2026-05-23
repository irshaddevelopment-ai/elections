<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Votemaster extends Model
{
    use HasFactory;

    protected $table = 'vote_master';
    protected $primaryKey = 'idvote_master';
    public $timestamps = false; // Set to false if you don't want to use Laravel's automatic timestamp handling

    protected $fillable = [
        'vote_code', 'user_code', 'election_code','round_number', 'vote_date'
    ];

    // You can add any additional model logic or relationships here
}
