<?php
namespace App\Http\Requests;

abstract class References
{
    public static function rules($prefix = '')
    {
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
}
