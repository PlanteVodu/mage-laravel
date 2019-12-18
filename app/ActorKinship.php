<?php

namespace App;

use App\Actor;
use App\Kinship;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ActorKinship extends Pivot
{
    public function kinship()
    {
        return Kinship::find($this->kinship_id);
    }

    public function actor()
    {
        if ($this->pivotParent->id == $this->actor_id) {
            return Actor::find($this->actor_id);
        }
        return Actor::find($this->relative_id);
    }

    public function relative()
    {
        if ($this->pivotParent->id == $this->actor_id) {
            return Actor::find($this->relative_id);
        }
        return Actor::find($this->actor_id);
    }
}
