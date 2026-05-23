<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = ['title', 'description', 'picture'];

    // Optionally, you can define timestamps if you want
    // protected $timestamps = false;
}
