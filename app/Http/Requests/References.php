<?php
namespace App\Http\Requests;

use Illuminate\Support\Str;

abstract class References
{
    public static function rules($prefix = '')
    {
        if (Str::endsWith($prefix, '.*')) {
            return self::getNestedArraysRules($prefix);
        }

        if (strlen($prefix) > 0) {
            $prefix = $prefix . '.';
        }
        $field = $prefix . 'references.*';

        return [
            $field => [
                'distinct',
                'exists:references,id',
            ],
        ];
    }

    /**
     *  Handle nested arrays, ensuring the rules are applied separately on each
     *  of the deepest arrays.
     */
    protected static function getNestedArraysRules($prefix)
    {
        $prefix = Str::beforeLast($prefix, '.*');
        $fields = collect(request()->get($prefix));
        return $fields->map(function ($item, $key) use ($prefix) {
            return self::rules($prefix . '.' . $key);
        })->toArray();
    }
}
