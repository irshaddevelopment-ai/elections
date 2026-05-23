<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class elections extends Model
{
    protected $table = 'elections';

    protected $fillable = [
        'election_code',
        'election_name',
        'status',
        'election_date',
    ];

    // You can specify other properties, relationships, etc. here
}
