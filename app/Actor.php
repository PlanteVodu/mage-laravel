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
        dump(get_class_methods(get_class($this)));
        // dump(get_class_methods(get_class($relation)));

        // $relation->initRelation = function() {

        // }
        // dump($relation->initRelation());
        // dump($relation->getResults());
        // dump($this->);
        // dump($relation->getResults());

        return $relation;
    }

    public function newHasMany(\Illuminate\Database\Eloquent\Builder $query, \Illuminate\Database\Eloquent\Model $parent, $foreignKey, $localKey) {
        // $newHasMany = parent::newHasMany($query, $parent, $foreignKey, $localKey);
        // $newHasMany = parent::newHasMany($query, $parent, $foreignKey, $localKey);

        // $newHasMany->initRelation

        // return $newHasMany;
        // return $
    }
}

class HasManyActors extends HasMany
{
    public function newInitRelation(array $models, $relation)
    {
        $init = parent::newInitRelation($models, $relation);
    }
}
