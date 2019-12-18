<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kinship;

class KinshipsController extends Controller
{
    public function store(Request $request)
    {
        Kinship::create($this->validateRequest($request));
    }

    public function update(Request $request, Kinship $kinship)
    {
        $kinship->update($this->validateRequest($request));
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required',
            'coefficient' => 'integer',
        ]);
    }
}
