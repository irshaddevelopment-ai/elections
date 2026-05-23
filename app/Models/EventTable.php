<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTable extends Model
{
    use HasFactory;
    protected $table = 'event_table';
    public $timestamps = false;

    // Fillable fields if you need mass assignment
    protected $fillable = [
        'prf_code',
        'user_code',
        'connected',
        'loggedin_datetime',
        'loggedout_datetime',
        'event_description',
        'vote_description',
        'session_handle'
    ];
}
