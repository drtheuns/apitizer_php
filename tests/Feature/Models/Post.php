<?php

namespace Tests\Feature\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasUuid;

    protected $fillable = ['title', 'body'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
