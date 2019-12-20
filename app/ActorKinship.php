<?php

namespace App;

use App\Actor;
use App\Kinship;
use Illuminate\Database\Eloquent\Model;

class ActorKinship extends Model
{
    protected $guarded = [];

    public $actor;

    public function kinship()
    {
        return Kinship::find($this->kinship_id);
    }

    public function actor()
    {
        // if ($this->actor_id == $this->actor_id) {
            // return Actor::find($this->actor_id);
        // }
        return Actor::find($this->actor_id);
    }

    public function relative()
    {
        // if ($this->actor_id == $this->actor_id) {
            // return Actor::find($this->relative_id);
        // }
        return Actor::find($this->relative_id);
    }
}
