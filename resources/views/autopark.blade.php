<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title></title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" type="image/png" href="/favicon.ico"/>

    <!-- Styles -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!--<link href="{{ asset('css/app.css') }}" rel="stylesheet">-->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="/js/jquery-3.2.1/jquery.min.js"></script>
    <style>
        /* Always set the map height explicitly to define the size of the div
         * element that contains the map. */
        #map {
            height: 100%;
            width: 60%;
        }
        /* Optional: Makes the sample page fill the window. */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .carpark{
            height:100%;
            width:100%;
            display: flex;
            justify-content: space-evenly;
        }
        .carlist{
            width: 30%;
        }
        .car{
            width: 100%;
            padding: 10px;
            height: 100px;
        }
        .fa-car{
            font-size: 20px;
        }
        .search-block {
            display: flex;
            justify-content: flex-start;
        }
    </style>
    @include('layouts.navigation')
</head>
<body>
<div class="carpark">
    <div id="map"></div>
    <div class="carlist">

        <ul class="nav nav-tabs">
            @if($searchedCars==null)
                <li class="active"><a data-toggle="tab" href="#home">Visas mašīnas ({!! $cars->count() !!})</a></li>
                <li><a data-toggle="tab" href="#menu1">Brauc ({!! $drivingCars!=[]?collect($drivingCars)->count():0 !!})</a></li>
                <li><a data-toggle="tab" href="#menu2">Stāv ({!! $standingCars!=[]?collect($standingCars)->count():0 !!})</a></li>
            @endif
        </ul>
        <div class="tab-content">
            @if($searchedCars != null)
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">
                            Automašīnu saraksts
                        </h3>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        {!! Form::open(['url'=>'search', 'method'=>'post']) !!}
                        <div class="search-block">
                            {!! Form::text('search', null, ['id' => 'search', 'class'=>'search', 'placeholder'=>'Meklēt auto modeli']); !!}
                            {!!  Form::select('ignition', [''=>'Statuss'] + $ignitions, null, []) !!}
                            <div class="pointer_">
                                <button type="submit" class="glyphicon glyphicon-search" style="height: 32px;">
                                </button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                        @if($searchedCars!=null)
                            <div class="alert alert-info" role="alert">
                                <strong>Saraksts tukšs</strong> <a href="#/" class="alert-link" data-toggle="modal" data-target="#myModal">pievienot</a>
                            </div>
                        @else
                            <div class="list-group">
                                @foreach ($searchedCars as $car)
                                    <a href="{{ route('car', ['id' => $car['id']]) }}" class="list-group-item list-two-row car">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>{{ $car['make'] }}</strong> - <small>{{ $car['reg_num'] }}</small><br>
                                                    {{ $car['year']!=null?$car['year']:'' }}
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $carTrip = $trips->where('car_id', $car['id'])->first();
                                                    if($carTrip!=null)
                                                    {
                                                        $ignition = $carTrip->ignition;
                                                    }
                                                    else{
                                                        $ignition = '';
                                                    }
                                                    $timeNow = \Carbon\Carbon::now();
                                                    ?>
                                                    <p class="list-group-item-text" style="font-size: 13px; color: #555;">
                                                        @if($ignition != '')
                                                            <strong>{{ $ignition == 'ON'?'BRAUC':'STĀV' }}</strong> <br>
                                                            {{ $ignition == 'OFF'?(new \Carbon\Carbon($timeNow))->diff(new \Carbon\Carbon($carTrip->updated_at))->format('%D dienas %h stundas %I minūtes'):'' }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-1">
                                                    <span class="link-in pull-right fa fa-car"></span>
                                                </div>

                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <div class="text-center">
                            </div>
                        @endif
                        <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#myModal">Pievienot</button>
                    </div>
                </div>
            @else
            <div id="home" class="tab-pane fade in active">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">
                            Automašīnu saraksts
                        </h3>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        {!! Form::open(['url'=>'search', 'method'=>'post']) !!}
                        <div class="search-block">
                            {!! Form::text('search', null, ['id' => 'search', 'class'=>'search', 'placeholder'=>'Meklēt auto modeli']); !!}
                            {!!  Form::select('ignition', [''=>'Statuss'] + $ignitions, null, []) !!}
                            <div class="pointer_">
                                <button type="submit" class="fa fa-search" style="height: 32px;">
                                </button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                        @if($cars->total() == 0)
                            <div class="alert alert-info" role="alert">
                                <strong>Saraksts tukšs</strong> <a href="#/" class="alert-link" data-toggle="modal" data-target="#myModal">pievienot</a>!
                            </div>
                        @else
                            <div class="list-group">
                                @foreach ($cars as $car)
                                    <a href="{{ route('car', ['id' => $car->id]) }}" class="list-group-item list-two-row car">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>{{ $car->make }}</strong> - <small>{{ $car->reg_num }}</small><br>
                                                    {{ $car->year!=null?$car->year:'' }}
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                        $carTrip = $trips->where('car_id', $car->id)->first();
                                                        if($carTrip!=null)
                                                        {
                                                            $ignition = $carTrip->ignition;
                                                        }
                                                        else{
                                                            $ignition = '';
                                                        }
                                                        $timeNow = \Carbon\Carbon::now();
                                                    ?>
                                                    <p class="list-group-item-text" style="font-size: 13px; color: #555;">
                                                        @if($ignition != '')
                                                        <strong>{{ $ignition == 'ON'?'BRAUC':'STĀV' }}</strong> <br>
                                                        {{ $ignition == 'OFF'?(new \Carbon\Carbon($timeNow))->diff(new \Carbon\Carbon($carTrip->updated_at))->format('%D dienas %h stundas %I minūtes'):'' }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-1">
                                                    <span class="link-in pull-right fa fa-car"></span>
                                                </div>

                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <div class="text-center">
                                {!! $cars->render() !!}
                            </div>
                        @endif
                        <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#myModal">Pievienot</button>
                    </div>
                </div>
            </div>
            <div id="menu1" class="tab-pane fade">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">
                            Automašīnu saraksts
                        </h3>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        @if($cars->total() == 0)
                            <div class="alert alert-info" role="alert">
                                <strong>Saraksts tukšs</strong> <a href="#/" class="alert-link" data-toggle="modal" data-target="#myModal">pievienot</a>!
                            </div>
                        @else
                            <div class="list-group">
                                @if($drivingCars!=[])
                                @foreach (collect($drivingCars) as $car)
                                    <a href="{{ route('car', ['id' => $car->id]) }}" class="list-group-item list-two-row car">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>{{ isset($car->make)?$car->make:'' }}</strong> - <small>{{ isset($car->reg_num)?$car->reg_num:'' }}</small><br>
                                                    {{ $car->year!=null?$car->year:'' }}
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $carTrip = $trips->where('car_id', $car->id)->first();
                                                    if($carTrip!=null)
                                                    {
                                                        $ignition = $carTrip->ignition;
                                                    }
                                                    else{
                                                        $ignition = '';
                                                    }
                                                    $timeNow = \Carbon\Carbon::now();
                                                    ?>
                                                    <p class="list-group-item-text" style="font-size: 13px; color: #555;">
                                                        @if($ignition != '')
                                                            <strong>{{ $ignition == 'ON'?'BRAUC':'STĀV' }}</strong> <br>
                                                            {{ $ignition == 'OFF'?(new \Carbon\Carbon($timeNow))->diff(new \Carbon\Carbon($carTrip->updated_at))->format('%D dienas %h stundas %I minūtes'):'' }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-1">
                                                    <span class="link-in pull-right fa fa-car"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                                @else
                                    Tukšs
                                @endif
                            </div>
                            <div class="text-center">
                                {!! $cars->render() !!}
                            </div>
                        @endif
                        <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#myModal">Pievienot</button>
                    </div>
                </div>
            </div>
            <div id="menu2" class="tab-pane fade">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">
                            Automašīnu saraksts
                        </h3>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        @if($cars->total() == 0)
                            <div class="alert alert-info" role="alert">
                                <strong>Saraksts tukšs</strong> <a href="#/" class="alert-link" data-toggle="modal" data-target="#myModal">pievienot</a>!
                            </div>
                        @else
                            <div class="list-group">
                                @if($standingCars!=[])
                                @foreach (collect($standingCars) as $car)
                                    <a href="{{ route('car', ['id' => $car->id]) }}" class="list-group-item list-two-row car">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>{{ $car->make }}</strong> - <small>{{ $car->reg_num }}</small><br>
                                                    {{ $car->year!=null?$car->year:'' }}
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $carTrip = $trips->where('car_id', $car->id)->first();
                                                    if($carTrip!=null)
                                                    {
                                                        $ignition = $carTrip->ignition;
                                                    }
                                                    else{
                                                        $ignition = '';
                                                    }
                                                    $timeNow = \Carbon\Carbon::now();
                                                    ?>
                                                    <p class="list-group-item-text" style="font-size: 13px; color: #555;">
                                                        @if($ignition != '')
                                                            <strong>{{ $ignition == 'ON'?'BRAUC':'STĀV' }}</strong> <br>
                                                            {{ $ignition == 'OFF'?(new \Carbon\Carbon($timeNow))->diff(new \Carbon\Carbon($carTrip->updated_at))->format('%D dienas %h stundas %I minūtes'):'' }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-1">
                                                    <span class="link-in pull-right fa fa-car"></span>
                                                </div>

                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                                @else
                                    Tukšs
                                @endif
                            </div>
                            <div class="text-center">
                                {!! $cars->render() !!}
                            </div>
                        @endif
                        <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#myModal">Pievienot</button>
                    </div>
                </div>
            </div>
            <div id="menu3" class="tab-pane fade">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">
                            Automašīnu saraksts
                        </h3>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        @if($cars->total() == 0)
                            <div class="alert alert-info" role="alert">
                                <strong>Saraksts tukšs</strong> <a href="#/" class="alert-link" data-toggle="modal" data-target="#myModal">pievienot</a>!
                            </div>
                        @else
                            <div class="list-group">
                                @foreach ($cars as $car)
                                    <a href="{{ route('car', ['id' => $car->id]) }}" class="list-group-item list-two-row car">
                                        <span class="link-in pull-right fa fa-car"></span>
                                        <strong>{{ $car->make }}</strong> - <small>{{ $car->reg_num }}</small>
                                        <p class="list-group-item-text" style="font-size: .8em; padding-top: .4em; font-style: normal; color: gray;"></p>
                                    </a>
                                @endforeach
                            </div>
                            <div class="text-center">
                                {!! $cars->render() !!}
                            </div>
                        @endif
                        <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#myModal">Pievienot</button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Pievienot jaunu auto</h4>
            </div>
            {{ Form::open(array('url' => route('car-data-entry'))) }}
            <div class="modal-body">
                @if($errors->get('make') == null)
                    <div class="form-group ">
                        <label for="make">Modelis</label>
                        {{ Form::text('make', '', array_merge(['class' => 'form-control'],['id' => 'make'], ['placeholder' => '']))}}
                    </div>
                @else
                    @php($myModal = true)
                    <div class="form-group has-error">
                        <label class="control-label" for="make">Modelis</label>
                        {{ Form::text('make', '', array_merge(['class' => 'form-control'],['id' => 'make'], ['placeholder' => '']))}}
                        <span class="help-block">{{ $errors->get('make')[0] }}</span>
                    </div>
                @endif
                @if($errors->get('year') == null)
                    <div class="form-group ">
                        <label for="year">Izlaiduma gads</label>
                        {{ Form::text('year', '', array_merge(['class' => 'form-control'],['id' => 'year'], ['placeholder' => '']))}}
                    </div>
                @else
                    @php($myModal = true)
                    <div class="form-group has-error">
                        <label class="control-label" for="year">Izlaiduma gads</label>
                        {{ Form::text('year', '', array_merge(['class' => 'form-control'],['id' => 'year'], ['placeholder' => '']))}}
                        <span class="help-block">{{ $errors->get('year')[0] }}</span>
                    </div>
                @endif

                @if($errors->get('reg_num') == null)
                    <div class="form-group ">
                        <label for="reg_num">Registrācijas numurs</label>
                        {{ Form::text('reg_num', '', array_merge(['class' => 'form-control'],['id' => 'reg_num'], ['placeholder' => '']))}}
                    </div>
                @else
                    @php($myModal = true)
                    <div class="form-group has-error">
                        <label class="control-label" for="reg_num">Registrācijas numurs</label>
                        {{ Form::text('reg_num', '', array_merge(['class' => 'form-control'],['id' => 'reg_num'], ['placeholder' => '']))}}
                        <span class="help-block">{{ $errors->get('reg_num')[0] }}</span>
                    </div>
                @endif

                <div class="form-group ">
                    <label for="gps_tracker">GPS izsekošanas ierīce</label>
                    {{ Form::select('gps_tracker', $gps_trackers, $car->device_id ,array_merge(['class' => 'form-control selectpicker'],['id' => 'gps_tracker'],['data-size' => config('app.dropdown')]))}}
                </div>
                @if($errors->get('gps_phone_number') == null)
                    <div class="form-group ">
                        <label for="gps_phone_number">GPS sim numurs</label>
                        {{ Form::text('gps_phone_number', '', array_merge(['class' => 'form-control'],['id' => 'gps_phone_number'], ['placeholder' => '']))}}
                    </div>
                @else
                    @php($myModal = true)
                    <div class="form-group has-error">
                        <label class="control-label" for="gps_phone_number">GPS sim numurs</label>
                        {{ Form::text('gps_phone_number', '', array_merge(['class' => 'form-control'],['id' => 'gps_phone_number'], ['placeholder' => '']))}}
                        <span class="help-block">{{ $errors->get('gps_phone_number')[0] }}</span>
                    </div>
                @endif
                @if($errors->get('consumption') == null)
                    <div class="form-group ">
                        <label for="consumption">Degvielas patēriņš</label>
                        {{ Form::text('consumption', '', array_merge(['class' => 'form-control'],['id' => 'consumption'], ['placeholder' => '']))}}
                    </div>
                @else
                    @php($myModal = true)
                    <div class="form-group has-error">
                        <label class="control-label" for="consumption">Degvielas patēriņš</label>
                        {{ Form::text('consumption', '', array_merge(['class' => 'form-control'],['id' => 'consumption'], ['placeholder' => '']))}}
                        <span class="help-block">{{ $errors->get('consumption')[0] }}</span>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                {{Form::submit('Pievienot', ['class' => 'btn btn-primary'])}}
            </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>
</div>
    <script>
        var geocoder;
        function initMap() {
            geocoder = new google.maps.Geocoder();
            var myLatLng = {lat: 56.6518804, lng: 23.7229734};
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8,
                center: myLatLng
            });
            var i, count=0;
            $.get('/get_car_locations', function (data) {
                for (i = 0; i < data.active_trips.length; i++) {
                    geocoder.geocode( { 'address': data.active_trips[i].waypoint}, function(results, status) {
                        if (status == 'OK') {
                            map.setCenter(results[0].geometry.location);
                            if(data.active_trips[count].ignition=='ON') {
                                var infowindow = new google.maps.InfoWindow({
                                    content: 'Modelis : ' + data.active_trips[count].car + '<br><br>' +
                                        'Atrašanās vieta : ' + data.active_trips[count].waypoint + '<br><br>' +
                                        'Brauciena sākuma atrašanās vieta : ' + data.active_trips[count].start_point + '<br><br>' +
                                        'Galamērķis : ' + data.active_trips[count].end_point + '<br><br>' +
                                        'Braukšanas ātrums : ' + data.active_trips[count].speed + '<br><br>' +
                                        'Motors : Ieslēgts<br><br>'+
                                        'Laiks atrašanās vietā : ' + data.active_trips[count].updated_at + '<br><br>'+
                                        '<button><a href="/car/'+data.active_trips[count].car_id+'">Skatīt vairāk</a></button>',
                                });
                            }
                            else {
                                var infowindow = new google.maps.InfoWindow({
                                    content: 'Modelis : ' + data.active_trips[count].car + '<br><br>' +
                                        'Atrašanās vieta : ' + data.active_trips[count].waypoint + '<br><br>' +
                                        'Brauciena sākuma atrašanās vieta : ' + data.active_trips[count].start_point + '<br><br>' +
                                        'Galamērķis : ' + data.active_trips[count].end_point + '<br><br>' +
                                        'Braukšanas ātrums : ' + data.active_trips[count].speed + '<br><br>' +
                                        'Motors : Izslēgts<br><br>'+
                                        'Laiks atrašanās vietā : ' + data.active_trips[count].updated_at + '<br><br>'+
                                        '<button><a href="/car/'+data.active_trips[count].car_id+'">Skatīt vairāk</a></button>',
                                });
                            }
                            var marker = new google.maps.Marker({
                                title: 'Hello World!',
                                label: {
                                    text: data.active_trips[count].car,
                                    color: 'white',
                                    fontSize: "10px",
                                },
                                map: map,
                                position: results[0].geometry.location,
                            });
                            marker.addListener('click', function() {
                                infowindow.open(map, marker);
                            });
                            count++;
                        } else {
                            alert('Geocode was not successful for the following reason: ' + status);
                        }
                    });
                };
            });
            var smsarray = [];
            setInterval(function() {
                $.get( "/getsms", function( data ) {
                    smsarray.push(data.message_count);
                    var i;
                    for (i = 0; i < smsarray.length; i++) {
                        if (smsarray[smsarray.length-1]>smsarray[smsarray.length-2])
                        {
                            $.get( "/post_received_message/", function( data2 ) {
                                initMap();
                            });
                            break;

                        }
                    }
                });
            }, 1000);
        }

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcydmAnLruEtB6PxE9kmMljbRw65IfRdk&callback=initMap"
            async defer></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('js/locale/lv.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.js') }}"></script>


    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/i18n/defaults-*.min.js"></script>-->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/editodometer.js') }}"></script>
</body>
</html>

