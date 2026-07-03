<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'content',
        'sub_topic',
        'category_id',
        'date'
    ];

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }
}
