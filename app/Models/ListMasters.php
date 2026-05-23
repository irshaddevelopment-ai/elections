<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListMasters extends Model
{

    use HasFactory;

    protected $table = 'list_master';
    protected $primaryKey = 'idgroups';
    public $timestamps = true;

    protected $fillable = [
        'election_code',
        'list_code',
        'list_name',
        'list_info',
        'list_members',
    ];

    protected $casts = [
        'list_members' => 'json',
    ];
}
