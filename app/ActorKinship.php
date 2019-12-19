<?php

namespace App;

use App\Actor;
use App\Kinship;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ActorKinship extends Pivot
{
    protected $guarded = [];

    public function kinship()
    {
        return Kinship::find($this->kinship_id);
    }

    public function actor()
    {
        // dump($this->getParentKey());
        // dump(get_class_methods(get_class($this)));
        // dump($this->getRelatedKey());
        // dump($this->getOtherKey());
        // dump(get__methods($this));
        // if ($this->actor_id == $this->actor_id) {
            // return Actor::find($this->actor_id);
        // }
        return Actor::find($this->relative_id);
    }

    public function relative()
    {
        // dump($this->getParentKey());
        // if ($this->actor_id == $this->actor_id) {
            // return Actor::find($this->relative_id);
        // }
        return Actor::find($this->actor_id);
    }
}
