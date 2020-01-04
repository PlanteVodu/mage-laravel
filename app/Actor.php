<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\SetDates;
use App\Traits\SetReferences;

class Actor extends Model
{
    use SetDates, SetReferences;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::deleting(function (Actor $actor) {
            $actor->references()->detach();
            foreach($actor->kinships as $actorKinship) {
                // We must delete ActorKinships one by one to
                // delete their References
                $actorKinship->delete();
            }
        });
    }

    public function references()
    {
        return $this->morphToMany('App\Reference', 'referencable');
    }

    public function kinships()
    {
        return $this->hasMany('App\ActorKinship')
            ->orWhere('relative_id', $this->id);
    }

    public function getRelatives()
    {
        return $this->kinships->map(function ($item, $key) {
            return $item->relative($this->id);
        });
    }

    public function getKinshipWith($actorId)
    {
        if ($actorId === $this->id) {
            return null;
        }
        return $this->kinships
            ->filter(function ($actorKinship) use ($actorId) {
                return $actorId === $actorKinship->relative($this->id)->id;
            })->first();
    }

    public function hasRelative($actor)
    {
        if ($actor === $this->id) {
            return false;
        }
        return $this->getRelatives()->contains($actor);
    }
}
