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
            $prefix = 'kinships.' . $i;

            // Set the Kinship in the right order / way / direction
            if (array_key_exists('actor_id', $kinship)) {
                $kinship['relative_id'] = $actor->getKey();
            } else if (array_key_exists('relative_id', $kinship)) {
                $kinship['actor_id'] = $actor->getKey();
            }

            $updatedKinship = ActorKinship::updateOrCreate(
                ['actor_id' => $kinship['actor_id'],
                 'relative_id' => $kinship['relative_id']],
                ['kinship_id' => $kinship['kinship_id']]
            );

            $updatedKinship->setReferences($request, $prefix);

            $updatedActorKinships[]= $updatedKinship->getKey();
        }

        // Remove the old Actor's Kinships
        $modelsToRemove = array_diff($existingKinships->modelKeys(), $updatedActorKinships);
        ActorKinship::destroy($modelsToRemove);
    }
}
