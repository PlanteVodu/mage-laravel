<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actor;
use App\Reference;
use App\ActorKinship;

class ActorsController extends Controller
{
    public function store(Request $request)
    {
        $actor = Actor::create($this->validateRequest($request));
        Reference::setReferences($request, $actor);
        $this->setKinships($request, $actor);
    }

    public function update(Request $request, Actor $actor)
    {
        $actor->update($this->validateRequest($request));
        Reference::setReferences($request, $actor);
        $this->setKinships($request, $actor);
    }

    protected function setKinships(Request $request, Actor $actor) {
        $data = $request->validate([
            'kinships.*.kinship_id' => [
                'exists:kinships,id',
                'required',
            ],
            'kinships.*.actor_id' => [
                'exists:actors,id',
                'required_without:kinships.*.relative_id',
            ],
            'kinships.*.relative_id' => [
                'exists:actors,id',
                'required_without:kinships.*.actor_id',
            ],
        ]);

        $existingKinships = $actor->kinships;

        $kinships = [];
        if (array_key_exists('kinships', $data)) {
            $kinships = $data['kinships'];
        }

        $updatedActorKinships = [];
        foreach($kinships as $kinship) {
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

            $updatedActorKinships[]= $updatedKinship->getKey();
        }

        $modelsToRemove = array_diff($existingKinships->modelKeys(), $updatedActorKinships);
        ActorKinship::destroy($modelsToRemove);
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required',
            'note' => '',
        ]);
    }
}
