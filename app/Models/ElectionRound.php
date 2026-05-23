<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElectionRound extends Model
{
    use HasFactory;
    protected $table = 'election_rounds';

    protected $primaryKey = 'idelection_rounds';

    protected $fillable = [
        'election_code',
        'round_number',
        'win_percentage',
        'min_win_percentage',
        'win_sign',
        'round_status',
        // Add other fillable columns here if needed
    ];

    public $timestamps = true;

    // Additional model logic can be added here
}