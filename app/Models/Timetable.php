<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $fillable = [
        'student_id',
        'subject',
        'lecturer',
        'room',
        'day',
        'start_time',
        'end_time',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
