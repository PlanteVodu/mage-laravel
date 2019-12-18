<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    protected $guarded = [];

    public function references()
    {
        return $this->belongsToMany('App\Reference');
    }

    public function kinships()
    {
        return $this->belongsToMany('App\Kinship');
    }
}
