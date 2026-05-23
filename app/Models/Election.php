<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    use HasFactory;
    protected $table = 'elections';
    protected $primaryKey = 'idelection';
    public $timestamps = true;

    // Define your fillable columns if you want to use mass assignment
    protected $fillable = [
        'election_code',
        'election_name',
        'election_type',
        'election_status',
        'election_date',
        'logo',
        'win_number',
        // Add other columns as needed
    ];
}
