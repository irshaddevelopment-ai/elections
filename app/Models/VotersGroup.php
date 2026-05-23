<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotersGroup extends Model
{
    use HasFactory;
    protected $table = 'voters_group';

    protected $primaryKey = 'idvoters_group';

    public $timestamps = true;

    protected $fillable = [
        'voter_group_code',
        'voter_group_name',
        'election_code',
        'description'
    ];

    // You can define relationships, additional methods, etc. here
}
