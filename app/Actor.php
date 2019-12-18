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
        return $this->belongsToMany('App\Kinship')
            ->using('App\ActorKinship')
            ->as('kinship')
            ->wherePivot('actor_id', $this->id)
            ->orWherePivot('relative_id', $this->id)
            ->withPivot([
                'relative_id',
            ]);
    }
}
