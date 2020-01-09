<?php

namespace Tests\Feature\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasUuid;

    protected $table = 'tags';

    public $timestamps = false;
}
