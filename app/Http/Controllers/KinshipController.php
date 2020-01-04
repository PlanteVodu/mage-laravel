<?php

namespace App\Http\Controllers;

use App\Kinship;
use Illuminate\Http\Request;

class KinshipController extends Controller
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
    public function store(Request $request)
    {
        Kinship::create($this->validateRequest($request));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kinship  $kinship
     * @return \Illuminate\Http\Response
     */
    public function show(Kinship $kinship)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kinship  $kinship
     * @return \Illuminate\Http\Response
     */
    public function edit(Kinship $kinship)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kinship  $kinship
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kinship $kinship)
    {
        $kinship->update($this->validateRequest($request));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kinship  $kinship
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kinship $kinship)
    {
        //
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required',
            'coefficient' => 'integer',
        ]);
    }
}
