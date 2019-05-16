<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Trip;
use App\Destination;
use App\Distance;
use App\Car;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index() 
    {
        if (Auth::user()->role == 'admin')
        {
            $profileList = User::where('role','!=','admin')->orderBy('id', 'asc')->paginate(config('app.paginate_default'));;

            return view('profilelist')
                ->with('profileList', $profileList);
        } else {
            return Redirect::to('trips');
        }
    }
    
    public function profileRemove(Request $request) { 
        
        if(Auth::user()->role == 'admin')
        {
            try{
                $trips = Trip::where('user_id', $request->id);
                $distances = Distance::where('created_by', $request->id);
                $destinations = Destination::where('created_by', $request->id);
                $cars = Car::where('created_by', $request->id);
                $user = User::where('id', $request->id);
                
                $trips->delete();
                $distances->delete();
                $destinations->delete();        
                $cars->delete();
                $user->delete();     
                
                return redirect()->back()->with('success','Profils tika veiksmīgi izdzēsts!');
            } 
            catch(\Exception $e){
                return redirect()->back()->with('unsuccess','Nebija iespējams dzēst profilu!');
            }
        }
        else
        {
            return Redirect::to('trips');
        }
        return Redirect::to('error');
    }
    
}
