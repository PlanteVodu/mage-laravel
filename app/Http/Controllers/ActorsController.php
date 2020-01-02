<?php

namespace App\Http\Controllers;

use App\Actor;
use App\ActorKinship;
use App\Http\Requests\StoreActor;

class ActorsController extends Controller
{
    public function store(StoreActor $request)
    {
        $actor = new Actor;
        $this->setActorData($actor, $request);
    }

    public function update(StoreActor $request, Actor $actor)
    {
        $this->setActorData($actor, $request);
    }

    protected function setActorData(Actor $actor, StoreActor $request)
    {
        $actor->name = $request->name;
        $actor->note = $request->note;
        $actor->setDates($request);

        $actor->save();

        $actor->setReferences($request);
        $this->setKinships($actor, $request);
    }

    protected function setKinships(Actor $actor, StoreActor $request) {
        $oldActorKinships = $actor->kinships->modelKeys();

        $kinships = $request->input('kinships', []);
        $actorKinships = [];
        foreach(array_values($kinships) as $i => $kinship) {
            $actorKinships[]= $this->setKinship($actor, $request, $i, $kinship)->id;
        }

        ActorKinship::destroy(array_diff($oldActorKinships, $actorKinships));
    }

    protected function setKinship(Actor $actor, StoreActor $request, $i, $kinship)
    {
        $actorKinship = $actor->getKinshipWith($kinship['relative_id']);

        if (!$actorKinship) {
            $actorKinship = new ActorKinship;

            $actorKinship->kinship_id = $kinship['kinship_id'];
            $actorKinship->actor_id = $actor->id;
            $actorKinship->relative_id = $kinship['relative_id'];

            $actorKinship->save();
        } else if ($actorKinship->kinship_id != $kinship['kinship_id']) {
            $actorKinship->kinship_id = $kinship['kinship_id'];
            $actorKinship->save();
        }

        $actorKinship->setReferences($request, 'kinships.' . $i);

        return $actorKinship;
    }
}
