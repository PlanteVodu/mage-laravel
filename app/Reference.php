<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Reference extends Model
{
    protected $guarded = [];

    static public function setReferences(Request $request, $item)
    {
        $data = $request->validate([
            'references.*' => 'exists:references,id',
        ]);

        $references = [];
        if (array_key_exists('references', $data)) {
            $references = $data['references'];
        }

        $item->references()->sync($references);
    }
}
