<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackerTrip extends Model
{
    protected $table = 'tracker_trips';

    public function cars()
    {
        return $this->belongsTo('App\Car');
    }
}
