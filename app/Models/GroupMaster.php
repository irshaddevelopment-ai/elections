<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMaster extends Model
{
    use HasFactory;
    protected $table = 'groups_master';

    protected $primaryKey = 'idgroups';

    protected $fillable = [
        'election_code',
        'group_code',
        'group_name',
        'group_member',
    ];

    protected $casts = [
        'group_member' => 'json',
    ];
}
