<?php

namespace App;

use Illuminate\Validation\Rule;

class Dates
{
    public static $possible_accuracies = [
        'exactly',
        'circa',
        'before',
        'after',
    ];

    public static function getValidationRules($prefix = '')
    {
        if (strlen($prefix) > 0) {
            $prefix = $prefix . '.';
        }

        $accuracies = Rule::in(self::$possible_accuracies);

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
