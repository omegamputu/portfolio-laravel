<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        'title',
        'slug',
        //'excerpt',
        'content',
        'cover_image',
        'status',
        'published_at',
        'author_id',
        'reading_time',
        'seo_title',
        'seo_description',
        'views',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
