<?php

namespace App;

use Illuminate\Http\Request;

trait Dates
{
    public function setDates(Request $request)
    {
        $this->date_start = $request->date_start;
        $this->date_end = $request->date_end;
        $this->date_start_accuracy = $request->date_start_accuracy;
        $this->date_end_accuracy = $request->date_end_accuracy;
    }
}
