<?php

namespace App\Http\Controllers;

use App\GPSDevice;
use App\TrackerTrip;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Car;
use App\Trip;
use App\UserCar;
use App\Destination;


class CarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $destinations = Destination::where('created_by', Auth::id() )->pluck('name', 'id');
        $gps_trackers = GPSDevice::all()->pluck('code', 'id');


        return view('cars')
                ->with('gps_trackers', $gps_trackers)
                ->with('destinations', $destinations);
        //->with('trips_count', $car?$car->trips->count():0);
    }

    public function dataEntry(Request $request) {

// validation rules - obligatie lauki būtu aizpildīti!!!!!!!!

        $this->validate($request, [

            'reg_num' => 'required',
            'consumption' => 'required',
            'make' => 'required',
            'year' => 'required',
            'gps_phone_number' => 'required',
        ]);
        
        if($request->input('id'))
        {
            $car                            = Car::find($request->input('id'));
            $car->reg_num                   = $request->input('reg_num'); //$request->reg_num;
            $car->consumption               = str_replace(',', '.', $request->input('consumption'));
            $car->device_id               = $request->gps_tracker;
            $car->gps_phone_number          = $request->gps_phone_number;
            $car->make                      = $request->input('make');
            $car->year                      = $request->input('year');
            $car->updated_by                = $request->user()?$request->user()->id:''; 
            $car->save();
            return redirect('auto_park')
                    ->with('success','Izmaiņas tika veiksmīgi saglabātas!');
        }
        else
        {
            $car                            = new Car();
            $car->reg_num                   = $request->input('reg_num'); //$request->reg_num;
            $car->consumption               = str_replace(',', '.', $request->input('consumption'));
            $car->device_id                 = $request->gps_tracker;
            $car->gps_phone_number          = $request->gps_phone_number;
            $car->make                      = $request->input('make');
            $car->year                      = $request->input('year');
            $car->created_by                = $request->user()?$request->user()->id:'';
            $car->updated_by                = $request->user()?$request->user()->id:'';    
            $car->save();
      
          
            return redirect()->back()->with('success','Automašīna tika veiksmīgi pievienota!');
        }
        
    }
    
    public function car($id, Request $request) {
        
        $car = Car::find($id);
        if($car == null) {
            return view('error')->withErrors(['view' => 'Šāds galamērķis neeksistē!']);
        }
        
        if ($request->user()->id == $car->created_by){
            $gps_trackers = GPSDevice::all()->pluck('code', 'id');
            $trackerTrips = TrackerTrip::where('car_id', $id)->get();

            return view('car')->with('car', $car)
                ->with('gps_trackers', $gps_trackers)
                ->with('trips', $trackerTrips);
        }
        return view('error')->withErrors(['error', 'Jūsu lietotājam nav tiesību redzēt šo mašīnu!']);

    }
    public function getCarLocations()
    {
        $cars = Car::where('created_by', Auth::id())->get();
        $activeTripArray = [];
        $count = 0;
        if ($cars!=null)
        {
            foreach ($cars as $car)
            {
                $activeTripID = collect($car->tracker_trips)->max('id');
                $activeTrip = TrackerTrip::where('id', $activeTripID)->first();
                $activeTrip = collect($activeTrip)->put('car', $car->make);
                $activeTripArray[$count] = $activeTrip;
                $count++;
            }
        }

        return response()->json(['active_trips'=>$activeTripArray]);
    }
    public function autoPark($car_search = null, $ignition = null)
    {
        $cars = Car::where('created_by', Auth::id() )->paginate(config('app.paginate_default'));
        $alltrips = TrackerTrip::all();
        $gps_trackers = GPSDevice::all()->pluck('code', 'id');
        $ignitionsArray = [['ignition'=>'Brauc'], ['ignition'=>'Stāv']];
        $ignitions = collect($ignitionsArray)->pluck('ignition')->all();

        if ($ignition!=null)
        {
            if ($ignition == '0')
            {
                $ignition = 'ON';
            }
            else{
                $ignition = 'OFF';
            }
        }

        $count = 0;
        $carArray = [];

        if ($cars!=null) {
            foreach ($cars as $car) {
                $activeTripID = collect($car->tracker_trips)->max('id');
                $activeTrip = TrackerTrip::where('id', $activeTripID)->first();
                if (isset($activeTrip))
                {
                    if ($activeTrip->ignition == 'ON') {
                        $thisCar = collect($car)->put('ignition', 'ON');
                        $carArray[$count] = $thisCar;

                    } else {
                        $thisCar = collect($car)->put('ignition', 'OFF');
                        $carArray[$count] = $thisCar;
                    }

                }
                else{
                    $thisCar = collect($car);
                    $carArray[$count] = $thisCar;
                }
                $count++;
//            }
            }
        }
        if($car_search!=null)
        {
            $searchedCarByName = Car::where('make', 'like', '%'.$car_search.'%')
                ->where('created_by', Auth::id())->get();
        }
        else{
            $searchedCarByName = null;
        }
        if ($ignition!=null)
        {
            $searchedCars     = collect($carArray)
                ->where('ignition', $ignition)
                ->where('created_by', Auth::id())->all();
        }
        if ($ignition!= null && $searchedCarByName!=null)
        {
            $searchedCars     = collect($carArray)
                ->where('ignition', $ignition)
                ->where('make', 'like', '%'.$car_search.'%')
                ->where('created_by', Auth::id())->all();
        }
        if ($searchedCarByName != null)
        {
            $searchedCars = Car::where('make', 'like', '%'.$car_search.'%')
                ->where('created_by', Auth::id())->get();
        }
        else{
            $searchedCars = null;
        }

        $drivingCarArray       = [];
        $standingCarArray      = [];
        $drivingCarArrayCount  = 0;
        $standingCarArrayCount = 0;
        $tripCount             = 0;
        $drivingTripArray      = [];
        $standingTripArray     = [];
        $tripArray             = [];

        if ($cars!=null)
        {
            foreach ($cars as $car)
            {
                $activeTripID = collect($car->tracker_trips)->max('id');
                $activeTrip = TrackerTrip::where('id', $activeTripID)->first();
                $tripArray[$tripCount] = $activeTrip;
                if (isset($activeTrip))
                {
                    $thisCar = Car::where('id', $activeTrip->car_id)->first();
                    if ($activeTrip->ignition == 'ON')
                    {
                        $drivingTripArray[$drivingCarArrayCount] = $activeTrip;
                        $drivingCarArray[$drivingCarArrayCount] = $thisCar;
                        $drivingCarArrayCount++;
                    }
                    else{
                        $standingTripArray[$standingCarArrayCount] = $activeTrip;
                        $standingCarArray[$standingCarArrayCount] = $thisCar;
                        $standingCarArrayCount++;
                    }
                }


                $tripCount++;
            }
        }

        if ($tripArray != [])
        {
            $trips = collect($tripArray);
        }
        else{
            $trips = '';
        }
        if ($drivingTripArray != [])
        {
            $drivingTrip = collect($drivingTripArray);
        }
        else{
            $drivingTrip = '';
        }
        if ($standingTripArray != [])
        {
            $standingTrip = collect($standingTripArray);
        }
        else{
            $standingTrip = '';
        }
        if ($drivingCarArray != [])
        {
            $drivingCars = collect($drivingCarArray);
        }
        else{
            $drivingCars = '';
        }
        if ($standingCarArray != [])
        {
            $standingCars = collect($standingCarArray);
        }
        else{
            $standingCars = '';
        }


        return view('autopark')->with('cars', $cars)
            ->with('trips', $trips)
            ->with('gps_trackers', $gps_trackers)
            ->with('drivingCars', $drivingCars)
            ->with('standingCars', $standingCars)
            ->with('drivingTrip', $drivingTrip)
            ->with('standingTrip', $standingTrip)
            ->with('searchedCars', $searchedCars!=null?collect($searchedCars):null)
            ->with('ignitions', $ignitions);
    }
    public function search(Request $request)
    {
        $userCars = Car::where('created_by', Auth::id())->get();
        $carName  = $request->search;
        $cars     = Car::where('make', 'like', '%'.$carName.'%')
                        ->where('created_by', Auth::id())->get();
        $ignition = $request->ignition;

        if($cars!=null)
        {
            if ($ignition!=null)
            {
                return redirect('auto_park/'.$carName.'/'.$ignition);
            }
            else{
                return redirect('auto_park/'.$carName);
            }
        }
        else{
            if($ignition!=null)
            {
                if ($cars!=null)
                {
                    return redirect('auto_park/'.$carName.'/'.$ignition);
                }
                else{
                    return redirect('auto_park/'.$ignition);
                }
            }
            else{
                return redirect('auto_park');
            }
        }
    }
    public function remove($car_id)
    {
        $car = Car::where('id', $car_id)->first();
        $trip = TrackerTrip::where('car_id', $car->id)->first();
        if (!$trip)
        {
            $car->delete();
            return redirect('auto_park');
        }
        else{
            return redirect('car/'.$car_id);
        }


    }
}
