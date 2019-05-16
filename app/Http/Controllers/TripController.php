<?php

namespace App\Http\Controllers;
use App\CurrentLocationRequest;
use App\GPSDevice;
use App\Http\Middleware\ApiDataLogger;
use App\TrackerTrip;
use http\Client\Curl\User;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Car;
use Spatie\Geocoder\Geocoder;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use Twilio\Rest\Client;
use Twilio\TwiML\MessagingResponse;
use Barryvdh\DomPDF\Facade as PDF;



class TripController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }
    public function postStartingLocation($start = null, Request $request)
    {
        $this->validate($request, [

            'car_id' => 'required',
            'end_point' => 'required',
        ]);

        $gpsTrip              = new TrackerTrip();
        $gpsTrip->car_id      = $request->car_id;
        $gpsTrip->start_point = $start;
        $gpsTrip->waypoint    = $start;
        $gpsTrip->end_point   = $request->end_point;
        $gpsTrip->active = 1;
        $gpsTrip->created_by  = Auth::id();
        $gpsTrip->save();
        $allInactiveTrips = TrackerTrip::where('car_id',$gpsTrip->car_id)->where('id','!=',$gpsTrip->id)->get();
        if ($allInactiveTrips!=null)
        {
            foreach ($allInactiveTrips as $inactiveTrip)
            {

                $inactiveTrip->active = 0;
                $inactiveTrip->save();
            }
        }
        $gpsPhoneNumber = Car::where('id', $gpsTrip->car_id)->first()->gps_phone_number;
        $account_sid = getenv('ACCOUNT_SID');
        $auth_token = getenv('AUTH_TOKEN');
        $twilionumber = getenv('TWILIO_NUMBER');
        $client = new Client($account_sid, $auth_token);

        $client->messages->create(
            $gpsPhoneNumber,
            array(
                'from' => $twilionumber,
                'body' => 'pass webcoords'
            )
        );

        return redirect('/gps_map/'.$gpsTrip->car_id);
    }
    public function sendSMS(Request $request)
    {
        $account_sid = getenv('ACCOUNT_SID');
        $auth_token = getenv('AUTH_TOKEN');
        $twilionumber = getenv('TWILIO_NUMBER');
        $client = new Client($account_sid, $auth_token);

        $client->messages->create(
            '+37126400216',
            array(
                'from' => $twilionumber,
                'body' => 'pass webcoords'
            )
        );

        return $client;
    }
    public function postReceivedSMS($car_id = null, Request $request)
    {
        $phoneNumber = '';
        $account_sid = getenv('ACCOUNT_SID');
        $auth_token = getenv('AUTH_TOKEN');
        $twilio = new Client($account_sid, $auth_token);
        $trip = '';
        $messages = $twilio->messages
            ->read();
        if($car_id!=null)
        {
            $phoneNumber = Car::where('id',$car_id)->first()->gps_phone_number;
        }
        if($phoneNumber == $messages[0]->from)
        {
            $last_message = $messages[0]->body;
            $pieces = explode(' ', $last_message);
            $weblink = $pieces[2];
            $coordspieces = explode('=', $weblink);
            $coords   = rtrim($coordspieces[1],", ");
            $speed    = rtrim($pieces[4],", ");
            $ignition = rtrim($pieces[6],", ");
            if ($car_id!=null)
            {
                $tripID = TrackerTrip::where('car_id', $car_id)->where('active', 1)->max('id');
                $trip   = TrackerTrip::where('id', $tripID)->first();
                if ($trip)
                {
                    if ($trip->start_point==null)
                    {
                        $trip->start_point = $coords;
                        $trip->waypoint    = $coords;
                        $trip->speed       = $speed;
                        $trip->ignition    = $ignition;
                    }
                    else{
                        $trip->waypoint    = $coords;
                    }
                    $trip->save();
                }
            }
        }

        return response(['start_point'=>$trip!=''?$trip->start_point:'',
            'waypoint'=>$trip!=''?$trip->waypoint:'',
            'end_point'=>$trip!=''?$trip->end_point:'',
            'speed'=>$trip!=''?$trip->speed:'',
            'ignition'=>$trip!=''?$trip->ignition:'']);
    }
    public function getSMS($car_id = null, Request $request)
    {
        $message_array = [];
        $count = 0;
        $phoneNumber = '';
        $account_sid = getenv('ACCOUNT_SID');
        $auth_token = getenv('AUTH_TOKEN');
        $twilio = new Client($account_sid, $auth_token);
        if ($car_id!=null)
        {
            $phoneNumber = Car::where('id', $car_id)->first()->gps_phone_number;
        }

        $messages = $twilio->messages
            ->read();
        foreach ($messages as $message)
        {
            if ($message->direction == 'inbound' && $message->from == $phoneNumber)
            {
                array_push($message_array, $message->body);
                $count++;
            }
        }
        return response(['message_count'=>$count, 'messages'=>$message_array]);
    }
    public function gpsTrackerTracking()
    {
        return view('gpstrackers');
    }
    public function routeMap($car_id = null)
    {
        $trips = TrackerTrip::where('created_by', Auth::id())->get();
        $cars         = Car::where('created_by', Auth::id())->pluck('make', 'id');
        $trackerTrip = '';
        if ($car_id!=null)
        {
            $trackerTripId = TrackerTrip::where('car_id', $car_id)->where('active', 1)->max('id');
            $trackerTrip   = TrackerTrip::where('id', $trackerTripId)->first();
        }

        return view('gpsmap', ['trips'=>$trips,
            'cars'=>$cars, 'car_id'=>$car_id,
            'trip'=>$trackerTrip]);
    }
    public function tripHistory()
    {
        $allTrips = TrackerTrip::where('created_by', Auth::id())->get();
        $client = new \GuzzleHttp\Client();
        $geocoder = new Geocoder($client);
        $geocoder->setApiKey(getenv('GOOGLE_MAPS_API'));
        $allTripsArray = [];
        $count = 0;

        if ($allTrips != null)
        {
            foreach ($allTrips as $trip)
            {
                if ($trip->waypoint[0]==='0' ||
                    $trip->waypoint[0]==='1' ||
                    $trip->waypoint[0]==='2' ||
                    $trip->waypoint[0]==='3' ||
                    $trip->waypoint[0]==='4' ||
                    $trip->waypoint[0]==='5' ||
                    $trip->waypoint[0]==='6' ||
                    $trip->waypoint[0]==='7' ||
                    $trip->waypoint[0]==='8' ||
                    $trip->waypoint[0]==='9' ||
                    $trip->waypoint[0]==='-'){
                    $timePassed = (new \Carbon\Carbon($trip->created_at))
                        ->diff(new \Carbon\Carbon($trip->updated_at))
                        ->format('%D dienas %h stundas %I minūtes');
                    $waypoint_coords = explode(',', $trip->waypoint);
                    $start_coords    = explode(',', $trip->start_point);
                    $end_coords    = explode(',', $trip->end_point);
                    $geocode_waypoint = $geocoder->getAddressForCoordinates($waypoint_coords[0], $waypoint_coords[1]);
                    if ($trip->start_point[0]==='0' ||
                        $trip->start_point[0]==='1' ||
                        $trip->start_point[0]==='2' ||
                        $trip->start_point[0]==='3' ||
                        $trip->start_point[0]==='4' ||
                        $trip->start_point[0]==='5' ||
                        $trip->start_point[0]==='6' ||
                        $trip->start_point[0]==='7' ||
                        $trip->start_point[0]==='8' ||
                        $trip->start_point[0]==='9' ||
                        $trip->start_point[0]==='-')
                    {
                        $geocode_start    = $geocoder->getAddressForCoordinates($start_coords[0], $start_coords[1]);
                    }
                    else{
                        $geocode_start = $trip->start_point;
                    }
                    if ($trip->end_point[0]==='0' ||
                        $trip->end_point[0]==='1' ||
                        $trip->end_point[0]==='2' ||
                        $trip->end_point[0]==='3' ||
                        $trip->end_point[0]==='4' ||
                        $trip->end_point[0]==='5' ||
                        $trip->end_point[0]==='6' ||
                        $trip->end_point[0]==='7' ||
                        $trip->end_point[0]==='8' ||
                        $trip->end_point[0]==='9' ||
                        $trip->end_point[0]==='-')
                    {
                        $geocode_end      = $geocoder->getAddressForCoordinates($end_coords[0], $end_coords[1]);
                    }
                    else{
                        $geocode_end = $trip->end_point;
                    }
                    $tripWithGeocode = collect($trip)
                                    ->put('geocode_waypoint', isset($geocode_waypoint['formatted_address'])?$geocode_waypoint['formatted_address']:$geocode_waypoint)
                                    ->put('geocode_startpoint', isset($geocode_start['formatted_address'])?$geocode_start['formatted_address']:$geocode_start)
                                    ->put('geocode_endpoint', isset($geocode_end['formatted_address'])?$geocode_end['formatted_address']:$geocode_end)
                                    ->put('time_passed', $timePassed);
                    $allTripsArray[$count] = $tripWithGeocode;
                }
                else{
                    $timePassed = (new \Carbon\Carbon($trip->created_at))
                        ->diff(new \Carbon\Carbon($trip->updated_at))
                        ->format('%D dienas %h stundas %I minūtes');
                    $geocode_waypoint = $trip->waypoint;
                    $start_coords    = explode(',', $trip->start_point);
                    $end_coords    = explode(',', $trip->end_point);

                    if ($trip->start_point[0]==='0' ||
                        $trip->start_point[0]==='1' ||
                        $trip->start_point[0]==='2' ||
                        $trip->start_point[0]==='3' ||
                        $trip->start_point[0]==='4' ||
                        $trip->start_point[0]==='5' ||
                        $trip->start_point[0]==='6' ||
                        $trip->start_point[0]==='7' ||
                        $trip->start_point[0]==='8' ||
                        $trip->start_point[0]==='9' ||
                        $trip->start_point[0]==='-')
                    {
                        $geocode_start    = $geocoder->getAddressForCoordinates($start_coords[0], $start_coords[1]);
                    }
                    else{
                        $geocode_start = $trip->start_point;
                    }
                    if ($trip->end_point[0]==='0' ||
                        $trip->end_point[0]==='1' ||
                        $trip->end_point[0]==='2' ||
                        $trip->end_point[0]==='3' ||
                        $trip->end_point[0]==='4' ||
                        $trip->end_point[0]==='5' ||
                        $trip->end_point[0]==='6' ||
                        $trip->end_point[0]==='7' ||
                        $trip->end_point[0]==='8' ||
                        $trip->end_point[0]==='9' ||
                        $trip->end_point[0]==='-')
                    {
                        $geocode_end      = $geocoder->getAddressForCoordinates($end_coords[0], $end_coords[1]);
                    }
                    else{
                        $geocode_end = $trip->end_point;
                    }
                    $tripWithGeocode = collect($trip)
                                        ->put('geocode_waypoint', $geocode_waypoint)
                                        ->put('geocode_startpoint', isset($geocode_start['formatted_address'])?$geocode_start['formatted_address']:$geocode_start)
                                        ->put('geocode_endpoint', isset($geocode_end['formatted_address'])?$geocode_end['formatted_address']:$geocode_end)
                                        ->put('time_passed', $timePassed);
                    $allTripsArray[$count] = $tripWithGeocode;
                }
                $count++;
            }
        }

        $trips = collect($allTripsArray)->sortBy('created_at');

        return view('trip_history', ['trips'=>$trips]);
    }
    function GetDrivingDistance($one, $two)
    {
        $start = str_replace(' ', '+', $one);
        $end   = str_replace(' ', '+', $two);
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$start."&destinations=".$end."&mode=driving&key=".getenv('GOOGLE_MAPS_API');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
        $time = $response_a['rows'][0]['elements'][0]['duration']['text'];

        return array('distance' => $dist, 'time' => $time);
    }
    public function tripPDF($trip_id)
    {
        $trip = TrackerTrip::where('id', $trip_id)->first();
        $client = new \GuzzleHttp\Client();
        $geocoder = new Geocoder($client);
        $geocoder->setApiKey(getenv('GOOGLE_MAPS_API'));
        $car = Car::where('id', $trip->car_id)->first();

        if ($trip->waypoint[0]==='0' ||
            $trip->waypoint[0]==='1' ||
            $trip->waypoint[0]==='2' ||
            $trip->waypoint[0]==='3' ||
            $trip->waypoint[0]==='4' ||
            $trip->waypoint[0]==='5' ||
            $trip->waypoint[0]==='6' ||
            $trip->waypoint[0]==='7' ||
            $trip->waypoint[0]==='8' ||
            $trip->waypoint[0]==='9' ||
            $trip->waypoint[0]==='-'){
            $timePassed = (new \Carbon\Carbon($trip->created_at))
                ->diff(new \Carbon\Carbon($trip->updated_at))
                ->format('%D dienas %h stundas %I minūtes');
            $waypoint_coords = explode(',', $trip->waypoint);
            $start_coords    = explode(',', $trip->start_point);
            $end_coords    = explode(',', $trip->end_point);
            $geocode_waypoint = $geocoder->getAddressForCoordinates($waypoint_coords[0], $waypoint_coords[1]);
            if ($trip->start_point[0]==='0' ||
                $trip->start_point[0]==='1' ||
                $trip->start_point[0]==='2' ||
                $trip->start_point[0]==='3' ||
                $trip->start_point[0]==='4' ||
                $trip->start_point[0]==='5' ||
                $trip->start_point[0]==='6' ||
                $trip->start_point[0]==='7' ||
                $trip->start_point[0]==='8' ||
                $trip->start_point[0]==='9' ||
                $trip->start_point[0]==='-')
            {
                $geocode_start    = $geocoder->getAddressForCoordinates($start_coords[0], $start_coords[1]);
            }
            else{
                $geocode_start = $trip->start_point;
            }
            if ($trip->end_point[0]==='0' ||
                $trip->end_point[0]==='1' ||
                $trip->end_point[0]==='2' ||
                $trip->end_point[0]==='3' ||
                $trip->end_point[0]==='4' ||
                $trip->end_point[0]==='5' ||
                $trip->end_point[0]==='6' ||
                $trip->end_point[0]==='7' ||
                $trip->end_point[0]==='8' ||
                $trip->end_point[0]==='9' ||
                $trip->end_point[0]==='-')
            {
                $geocode_end      = $geocoder->getAddressForCoordinates($end_coords[0], $end_coords[1]);
            }
            else{
                $geocode_end = $trip->end_point;
            }
            $tripWithGeocode = collect($trip)
                ->put('geocode_waypoint', isset($geocode_waypoint['formatted_address'])?$geocode_waypoint['formatted_address']:$geocode_waypoint)
                ->put('geocode_startpoint', isset($geocode_start['formatted_address'])?$geocode_start['formatted_address']:$geocode_start)
                ->put('geocode_endpoint', isset($geocode_end['formatted_address'])?$geocode_end['formatted_address']:$geocode_end)
                ->put('time_passed', $timePassed);
        }
        else{
            $timePassed = (new \Carbon\Carbon($trip->created_at))
                ->diff(new \Carbon\Carbon($trip->updated_at))
                ->format('%D dienas %h stundas %I minūtes');
            $geocode_waypoint = $trip->waypoint;
            $start_coords    = explode(',', $trip->start_point);
            $end_coords    = explode(',', $trip->end_point);

            if ($trip->start_point[0]==='0' ||
                $trip->start_point[0]==='1' ||
                $trip->start_point[0]==='2' ||
                $trip->start_point[0]==='3' ||
                $trip->start_point[0]==='4' ||
                $trip->start_point[0]==='5' ||
                $trip->start_point[0]==='6' ||
                $trip->start_point[0]==='7' ||
                $trip->start_point[0]==='8' ||
                $trip->start_point[0]==='9' ||
                $trip->start_point[0]==='-')
            {
                $geocode_start    = $geocoder->getAddressForCoordinates($start_coords[0], $start_coords[1]);
            }
            else{
                $geocode_start = $trip->start_point;
            }
            if ($trip->end_point[0]==='0' ||
                $trip->end_point[0]==='1' ||
                $trip->end_point[0]==='2' ||
                $trip->end_point[0]==='3' ||
                $trip->end_point[0]==='4' ||
                $trip->end_point[0]==='5' ||
                $trip->end_point[0]==='6' ||
                $trip->end_point[0]==='7' ||
                $trip->end_point[0]==='8' ||
                $trip->end_point[0]==='9' ||
                $trip->end_point[0]==='-')
            {
                $geocode_end      = $geocoder->getAddressForCoordinates($end_coords[0], $end_coords[1]);
            }
            else{
                $geocode_end = $trip->end_point;
            }
            $tripWithGeocode = collect($trip)
                ->put('geocode_waypoint', $geocode_waypoint)
                ->put('geocode_startpoint', isset($geocode_start['formatted_address'])?$geocode_start['formatted_address']:$geocode_start)
                ->put('geocode_endpoint', isset($geocode_end['formatted_address'])?$geocode_end['formatted_address']:$geocode_end)
                ->put('time_passed', $timePassed);
        }

        $distanceDriven = $this->GetDrivingDistance($tripWithGeocode['geocode_startpoint'], $tripWithGeocode['geocode_waypoint']);
        $distanceToGo   = $this->GetDrivingDistance($tripWithGeocode['geocode_endpoint'], $tripWithGeocode['geocode_waypoint']);
        if(isset($distanceDriven['distance']))
        {
            $distanceDrivenPieces = explode(' ',$distanceDriven['distance']);
            $consumedFuel = (float)$distanceDrivenPieces[0]*((float)$car->consumption/100);
        }
        else{
            $consumedFuel = '';
        }

        $pdf = PDF::loadView('trip_pdf', ['trip'=>$tripWithGeocode,
                                        'car'=>$car,
                                        'distanceDriven'=>$distanceDriven,
                                        'distanceToGo'=>$distanceToGo,
                                        'consumedFuel'=>$consumedFuel]);
        return $pdf->download('invoice'.$trip['id'].'_'.$trip['created_at'].'.pdf');
    }
}

