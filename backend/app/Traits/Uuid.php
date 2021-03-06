<?php

namespace App\Traits;

trait Uuid
{
    public static function boot()
    {
        parent::boot();
        static::creating(function($obj){
            $obj->id = \Ramsey\Uuid\Uuid::uuid4();
        });
    }
}
