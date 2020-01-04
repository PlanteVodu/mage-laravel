<?php

namespace App\Http\Controllers;

use App\Actor;
use App\ActorKinship;
use App\Http\Requests\StoreActor;

class ActorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreActor $request)
    {
        $actor = new Actor;
        $this->setActorData(new Actor, $request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function show(Actor $actor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function edit(Actor $actor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function update(StoreActor $request, Actor $actor)
    {
        $this->setActorData($actor, $request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Actor  $actor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Actor $actor)
    {
        $actor->delete();
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
