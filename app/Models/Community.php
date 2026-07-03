<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_content',
        'description',
        'is_admin',
        'image_path',
    ];

    // Relationship (optional but good)
    public function student()
    {
        return $this->belongsTo(Student::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}