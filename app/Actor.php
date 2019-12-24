<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Dates;

class Actor extends Model
{
    use Dates;

    protected $guarded = [];

    public function references()
    {
        return $this->morphToMany('App\Reference', 'referencable');
    }

    public function kinships()
    {
        return $this->hasMany('App\ActorKinship')
            ->orWhere('relative_id', $this->getKey());
    }
}
