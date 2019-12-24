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

        $actor->name = $request->name;
        $actor->note = $request->note;
        $actor->setDates($request);

        $actor->save();

        Reference::setReferences($request, $actor);
        $this->setKinships($request, $actor);
    }

    public function update(StoreActor $request, Actor $actor)
    {
        $actor->name = $request->name;
        $actor->note = $request->note;
        $actor->setDates($request);

        $actor->save();

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
        foreach(array_values($kinships) as $i => $kinship) {
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

            Reference::setReferences($request, $updatedKinship, 'kinships.' . $i . '.');

            $updatedActorKinships[]= $updatedKinship->getKey();
        }

        $modelsToRemove = array_diff($existingKinships->modelKeys(), $updatedActorKinships);
        ActorKinship::destroy($modelsToRemove);
    }

    protected function validateRequest(Request $request)
    {
        $rules = [
            'name' => 'required',
            'note' => '',
        ];
        $rules = array_merge(Dates::getValidationRules(), $rules);
        return $request->validate($rules);
    }
}
