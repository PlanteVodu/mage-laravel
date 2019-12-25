<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait SetReferences
{
    public function setReferences(Request $request, $prefix = '')
    {
        if (strlen($prefix) > 0) {
            $prefix = $prefix . '.';
        }
        $field = $prefix . 'references';

        $this->references()->sync($request->input($field, []));
    }
}
