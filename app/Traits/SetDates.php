<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait SetDates
{
    public function setDates(Request $request, $prefix = '')
    {
        if (strlen($prefix) > 0) {
            $prefix = $prefix . '.';
        }
        $this->date_start = $request->input($prefix . 'date_start', null);
        $this->date_end = $request->input($prefix . 'date_end', null);
        $this->date_start_accuracy = $request->input($prefix . 'date_start_accuracy', null);
        $this->date_end_accuracy = $request->input($prefix . 'date_end_accuracy', null);
    }
}
