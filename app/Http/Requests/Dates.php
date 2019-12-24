<?php
namespace App\Http\Requests;

use Illuminate\Validation\Rule;

trait Dates
{
    public static $possibleAccuracies = [
        'exactly',
        'circa',
        'before',
        'after',
    ];

    public function getDatesValidationRules($prefix = '')
    {
        if (strlen($prefix) > 0) {
            $prefix = $prefix . '.';
        }

        $accuracies = Rule::in(self::$possibleAccuracies);

        return [
            $prefix . 'date_start' => [
                'date',
                'required_with:date_start_accuracy'
            ],
            $prefix . 'date_end' => [
                'date',
                'required_with:date_end_accuracy',
            ],
            $prefix . 'date_start_accuracy' => [$accuracies],
            $prefix . 'date_end_accuracy' => [$accuracies],
        ];
    }
}
