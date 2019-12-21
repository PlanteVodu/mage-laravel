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
                'required_with:kinships.*.actor_id',
            ],
            'kinships.*.relative_id' => [
                'exists:actors,id',
                'required_with:kinships.*.kinship_id',
            ],
            'kinships.*.inversed' => 'boolean|required',
        ]);

        $existingKinships = $actor->kinships;

        $kinships = [];
        if (array_key_exists('kinships', $data)) {
            $kinships = $data['kinships'];
        }

        $kinshipss = ActorKinship::where('actor_id', $actor->getKey())
            ->orWhere('relative_id', $actor->getKey())
            ->get();

        $kinshipsss = [];
        foreach($kinships as $kinship) {
            // Set the Kinship in the right order / way / direction
            if ($kinship['inversed'] == true) {
                $kinship['actor_id'] = $kinship['relative_id'];
                $kinship['relative_id'] = $actor->getKey();
            } else {
                $kinship['actor_id'] = $actor->getKey();
            }

            $updatedKinship = ActorKinship::updateOrCreate(
                ['actor_id' => $kinship['actor_id'],
                 'relative_id' => $kinship['relative_id']],
                ['kinship_id' => $kinship['kinship_id']]
            );

            $kinshipsss[]= $updatedKinship->getKey();
        }

        ActorKinship::destroy($kinshipss->diffKeys($kinshipsss)->modelKeys());
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required',
            'note' => '',
        ]);
    }
}
