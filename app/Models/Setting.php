<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';
    protected $primaryKey = 'idsettings';
    public $timestamps = true; // Laravel will manage created_at and updated_at columns
    protected $fillable = ['settings_name', 'settings_value']; // Fillable fields
}
