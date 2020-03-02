<?php

namespace Tests\Feature\Models;

use Apitizer\JsonApi\Resource;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Resource
{
    use HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email'];

    /**
     * Get the value that should be used for the "type" field.
     */
    public function getResourceType(): string
    {
        return 'users';
    }

    /**
     * Get the value that should be used for the "id" field.
     */
    public function getResourceId(): string
    {
        return $this->id;
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'author_id');
    }
}
