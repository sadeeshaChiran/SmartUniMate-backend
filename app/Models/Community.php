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
    ];

    // Relationship (optional but good)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}