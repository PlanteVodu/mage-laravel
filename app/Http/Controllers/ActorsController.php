<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actor;

class ActorsController extends Controller
{
    public function store(Request $request)
    {
        Actor::create($this->validateRequest($request));
    }

    public function update(Request $request, Actor $actor)
    {
        $actor->update($this->validateRequest($request));
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required',
            'note' => '',
        ]);
    }
}
