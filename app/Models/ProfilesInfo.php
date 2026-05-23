<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfilesInfo extends Model
{
    protected $table = 'profiles_infos';
    protected $primaryKey = 'profile_code';
    public $timestamps = false; // Set to true if you want to use timestamps
    use HasFactory;

    protected $fillable = [
        'profile_code',
        'residence',
        'joiningdate',
        'category',
        'nominate',
        'candidate',
        'nationality',
        'social_situation',
        'children',
        'children_age',
        'education',
        'current_work',
        'word_about_association',
        'work_at_association',
        'identifiers'
    ];

    
}
