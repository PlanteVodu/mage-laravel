<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actor;
use App\Reference;

class ActorsController extends Controller
{
    public function store(Request $request)
    {
        $actor = Actor::create($this->validateRequest($request));
        Reference::setReferences($request, $actor);

        // Handling Kinships
        $data = $request->validate([
            'kinships.*' => '',
        ]);

        $kinships = [];
        if (array_key_exists('kinships', $data)) {
            $kinships = $data['kinships'];
        }

        $actor->kinships()->sync($kinships);
    }

    public function update(Request $request, Actor $actor)
    {
        $actor->update($this->validateRequest($request));
        Reference::setReferences($request, $actor);
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required',
            'note' => '',
        ]);
    }
}
