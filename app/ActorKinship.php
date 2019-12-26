<?php

namespace App;

use App\Actor;
use App\Kinship;
use App\Traits\SetReferences;
use Illuminate\Database\Eloquent\Model;

class ActorKinship extends Model
{
    use SetReferences;

    protected $guarded = [];

    public $actor;

    // Observe this model being deleted and delete its children
    // https://laracasts.com/discuss/channels/eloquent/laravel-delete-model-with-all-relations
    // https://laracasts.com/discuss/channels/laravel/remove-pivot-table-entries-when-deleting-records?page=1
    public static function boot ()
    {
        parent::boot();

        self::deleting(function (ActorKinship $actorKinship) {
            $actorKinship->references()->detach();
        });
    }

    public function kinship()
    {
        return Kinship::find($this->kinship_id);
    }

    /**
     *  Return the actor from the perspective of $actor_id (if any),
     *  or the one defined as actor in the record.
     */
    public function actor($actorId = '')
    {
        if ($actorId == $this->relative_id) {
            return Actor::find($this->relative_id);
        }
        return Actor::find($this->actor_id);
    }

    /**
     *  Return the relative actor from the perspective of $actor_id (if any),
     *  or the one defined as relative in the record.
     */
    public function relative($actorId = '')
    {
        if ($actorId == $this->relative_id) {
            return Actor::find($this->actor_id);
        }
        return Actor::find($this->relative_id);
    }

    public function references()
    {
        return $this->morphToMany('App\Reference', 'referencable');
    }
}
