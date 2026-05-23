<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidatesGroup  extends Model
{
    use HasFactory;
    protected $table = 'candidates_groups';
    protected $primaryKey = 'idgroups';
    public $timestamps = true;

    protected $fillable = [
        'group_code',
        'election_code',
        'group_name',
        'win_number',
        // You may exclude 'created_at' and 'updated_at' if you don't intend to manage them manually
    ];
}
