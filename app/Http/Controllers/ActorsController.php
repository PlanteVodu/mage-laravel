<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $accuracies = Rule::in(['exactly', 'circa', 'before', 'after']);

        return $request->validate([
            'name' => 'required',
            'note' => '',
            'date_start' => [
                'date',
                'required_with:date_start_accuracy'
            ],
            'date_end' => [
                'date',
                'required_with:date_end_accuracy',
            ],
            'date_start_accuracy' => [$accuracies],
            'date_end_accuracy' => [$accuracies],
        ]);
    }
}
