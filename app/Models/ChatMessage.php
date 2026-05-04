<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'student_id',
        'role',      // 'user' or 'assistant'
        'message',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
