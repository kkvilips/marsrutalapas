<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    public function tracker_trips()
    {
        return $this->hasMany('App\TrackerTrip');
    }
    public function getDestinationDefault() {
        return Destination::find($this)->name;
    }
    
}
