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

    // public function kinshipss()
    // {
    //     // $kinships = ActorKinship::join('actors', 'actor_kinship.actor_id', '=', 'actors.id')
    //     //     ->where('actor_kinship.actor_id', $this->getKey())
    //     //     ->orWhere('actor_kinship.relative_id', $this->getKey())
    //     //     ->get(['actor_kinship.*', 'actors.*'])
    //     //     ;

    //     $kinships = ActorKinship::where('actor_id', $this->getKey())
    //         ->orWhere('relative_id', $this->getKey())->get();

    //     dump($kinships);
    // }

    // public function kinshipsss()
    // {
    //     return $this->belongsToMany('App\ActorKinship',
    //                                 'actor_kinship',
    //                                 'actor_id',
    //                                 'id')
    //         // ->wherePivot('actor_id', $this->getKey())
    //         ->orWherePivot('relative_id', $this->getKey())
    //         ->withPivot([
    //             'relative_id',
    //         ]);
    // }

    public function kinships()
    {
        $relation = $this->hasMany('App\ActorKinship')
            ->orWhere('relative_id', $this->getKey());
        // dump($relation);
        return $relation;
    }
}
