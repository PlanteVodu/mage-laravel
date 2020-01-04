<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Dates;
use App\Http\Requests\References;

class StoreActor extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required',
            'note' => '',
            'kinships.*.kinship_id' => [
                'exists:kinships,id',
                'required',
            ],
            'kinships.*.relative_id' => [
                'exists:actors,id',
                'required',
            ],
            'kinships.*.primary' => [
                'required',
                'boolean',
            ],
        ];

        $rules = array_merge(
            $rules,
            Dates::rules(''),
            References::rules(''),
            References::rules('kinships.*')
        );

        return $rules;
    }
}
