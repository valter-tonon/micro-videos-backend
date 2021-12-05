<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;


class CastMember extends Model
{
    use Uuid;

    const TYPE_DIRECTOR = 1;
    const TYPE_ACTOR = 2;

    protected $fillable = ['name', 'type'];

    protected $casts = [
        'id' => 'string'
    ];

    public $incrementing = false;
}
