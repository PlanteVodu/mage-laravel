<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Reference extends Model
{
    protected $guarded = [];

    static public function setReferences(Request $request, $item, $prefix = '')
    {
        $field = $prefix . 'references';

        $data = $request->validate([
            $field . '.*' => 'exists:references,id',
        ]);

        $references = self::getDeepValue(explode('.', $field), $data);

        $item->references()->sync($references);
    }

    static protected function getDeepValue($keys, $array)
    {
        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];
            if (isset($array[$key]) && array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return null;
            }
        }
        return $array;
    }
}
