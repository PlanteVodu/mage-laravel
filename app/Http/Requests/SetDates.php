<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetDates
{
    static public $possible_accuracies = [
        'exactly',
        'circa',
        'before',
        'after',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $accuracies = Rule::in($possible_accuracies);

        return [
            'date_start' => [
                'date',
                'required_with:date_start_accuracy'
            ],
            'date_end' => [
                'date',
                'required_with:date_end_accuracy',
            ],
            'date_start_accuracy' => [$accuracies],
            'date_end_accuracy' => [$accuracies],
        ];
    }
}
