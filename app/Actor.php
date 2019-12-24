<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    protected $guarded = [];

    public function references()
    {
        return $this->morphToMany('App\Reference', 'referencable');
    }

    // public function kinships()
    // {
    //     return $this->belongsToMany('App\Kinship')
    //         ->using('App\ActorKinship')
    //         ->as('kinship')
    //         ->wherePivot('actor_id', $this->getKey())
    //         ->orWherePivot('relative_id', $this->getKey())
    //         ->withPivot([
    //             'relative_id',
    //         ]);
    // }

    public function kinships()
    {
        return $this->hasMany('App\ActorKinship')
            ->orWhere('relative_id', $this->getKey());
    }
}
