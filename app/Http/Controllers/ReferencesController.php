<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Reference;

class ReferencesController extends Controller
{
    public function store(Request $request)
    {
        Reference::create($this->validateRequest($request));
    }

    public function update(Request $request, Reference $reference)
    {
        $reference->update($this->validateRequest($request));
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'category' => [
                'required',
                Rule::in([
                    'source',
                    'bibliography'
                ]),
            ],
            'name' => 'required',
            'note' => '',
        ]);
    }
}
