<?php
namespace App\Http\Requests;

use Illuminate\Validation\Rule;

trait References
{
    public function getReferencesValidationRules($prefix = '')
    {
        if (strlen($prefix) > 0) {
            $prefix = $prefix . '.';
        }
        $field = $prefix . 'references.*';

        return [
            $field => 'exists:references,id',
        ];
    }
}
