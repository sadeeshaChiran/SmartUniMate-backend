<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reported_student_id',
        'reported_post_id',
        'reason',
        'details',
        'screenshot_path',
        'status',
        'action_taken',
    ];

    public function reporter()
    {
        return $this->belongsTo(Student::class, 'reporter_id');
    }

    public function reportedStudent()
    {
        return $this->belongsTo(Student::class, 'reported_student_id');
    }

    public function reportedPost()
    {
        return $this->belongsTo(Community::class, 'reported_post_id');
    }
}
