<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actor;
use App\Reference;
use App\ActorKinship;
use App\Dates;
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
        $kinships = $request->input('kinships', []);

        // Retrieve previous Actor's Kinships
        $existingKinships = $actor->kinships;

        // Update or create the Actor's Kinships
        $updatedActorKinships = [];
        foreach(array_values($kinships) as $i => $kinship) {
            $kinship['actor_id'] = $actor->getKey();

            $actorKinship = ActorKinship::
                where(function($query) use ($actor, $kinship) {
                    $query
                        ->where('relative_id', $kinship['relative_id'])
                        ->where('actor_id', $kinship['actor_id']);
                })
                ->orWhere(function($query) use ($actor, $kinship) {
                    $query
                        ->where('actor_id', $kinship['relative_id'])
                        ->where('relative_id', $kinship['actor_id']);
                })
                ->first();

            if ($actorKinship && $actorKinship->kinship_id != $kinship['kinship_id']) {
                $actorKinship->kinship_id = $kinship['kinship_id'];
                $actorKinship->save();
            } else {
                $actorKinship = new ActorKinship;

                $actorKinship->kinship_id = $kinship['kinship_id'];
                $actorKinship->actor_id = $kinship['actor_id'];
                $actorKinship->relative_id = $kinship['relative_id'];

                $actorKinship->save();
            }

            $prefix = 'kinships.' . $i;
            $actorKinship->setReferences($request, $prefix);

            $updatedActorKinships[]= $actorKinship->getKey();
        }

        // Remove the old Actor's Kinships
        $modelsToRemove = array_diff($existingKinships->modelKeys(), $updatedActorKinships);
        ActorKinship::destroy($modelsToRemove);
    }
}
