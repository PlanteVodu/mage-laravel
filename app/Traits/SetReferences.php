<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait SetReferences
{
    public function setReferences($data, $prefix = '')
    {
        if (strlen($prefix) > 0) {
            $prefix = $prefix . '.';
        }
        $field = $prefix . 'references';

        $this->references()->sync(Arr::get($data, $field, []));
    }
}
