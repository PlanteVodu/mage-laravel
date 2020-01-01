<?php
namespace App\Http\Requests;

use Illuminate\Validation\Rule;

abstract class Dates
{
    public static $possibleAccuracies = [
        'exactly',
        'circa',
        'before',
        'after',
    ];

    public static function rules($prefix = '')
    {
        if (strlen($prefix) > 0) {
            $prefix = $prefix . '.';
        }

        $isValidAccuracy = Rule::in(self::$possibleAccuracies);

        return [
            $prefix . 'date_start' => [
                'date',
                'required_with:date_start_accuracy'
            ],
            $prefix . 'date_end' => [
                'date',
                'required_with:date_end_accuracy',
            ],
            $prefix . 'date_start_accuracy' => [$isValidAccuracy],
            $prefix . 'date_end_accuracy' => [$isValidAccuracy],
        ];
    }
}
