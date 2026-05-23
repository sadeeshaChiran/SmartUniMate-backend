<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicModule extends Model
{
    use HasFactory;

    protected $table = 'academic_modules';

    protected $fillable = [
        'code',
        'name',
        'credits',
        'faculty',
        'desc',
        'prereq',
    ];
}
