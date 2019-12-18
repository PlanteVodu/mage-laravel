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

    // public function relatives()
    // {
    //     return $this->hasManyThrough(
    //         // Name of the final model we wish to access
    //         'App\Actor',
    //         // Name of the indermediate model
    //         'App\ActorKinship',
    //         // Name of the foreign key on the intermediate model
    //         'relative_id',
    //         // Name of the foreign key on the final model
    //         'id',
    //         // Local key (Actor's key)
    //         'id',
    //         // Local key of the intermediate model
    //         'actor_id'
    //     );
    // }
}
