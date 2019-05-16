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
    <!--<link href="{{ asset('css/app.css') }}" rel="stylesheet">-->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="/js/jquery-3.2.1/jquery.min.js"></script>
    <style>
        #right-panel {
            font-family: 'Roboto','sans-serif';
            line-height: 30px;
            padding-left: 10px;
        }
        #right-panel select, #right-panel input {
            font-size: 15px;
        }
        #right-panel select {
            width: 100%;
        }
        #right-panel i {
            font-size: 12px;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #outerdiv {width: 90%; height: 90%; text-align:center}
        #map {width: 70%; height: 100%; margin:0px auto; display:inline-block}
        #right-panel {
            margin: 20px;
            border-width: 2px;
            width: 20%;
            height: 400px;
            float: left;
            text-align: left;
            padding-top: 0;
        }
        #directions-panel {
            margin-top: 10px;
            background-color: #FFEE77;
            padding: 10px;
            overflow: scroll;
            height: 174px;
        }
    </style>
</head>
<body>
@include('layouts.navigation')
{!! Form::open(['url' => '/post_start', 'id'=>'trip_form'])!!}
    <div class="container">

        @if($errors->get('car_id') == null)
            <div class="form-group ">
                {{ Form::label('car_id', 'Mašīna', ['class'=>'offset-lg-3']) }}
                {{ Form::select('car_id', $cars, null ,array_merge(['class' => 'form-control selectpicker'], ['id' => 'car_id'],['data-size' => config('app.dropdown')], ['placeholder' => 'Izvēlēties..']))}}
            </div>
        @else
            <div class="form-group has-error">
                {{ Form::label('car_id', 'Mašīna', ['class'=>'offset-lg-3']) }}
                {{ Form::select('car_id', $cars, null ,array_merge(['class' => 'form-control selectpicker'], ['id' => 'car_id'],['data-size' => config('app.dropdown')], ['placeholder' => 'Izvēlēties..']))}}
                <span class="help-block">{{ $errors->get('car_id')[0] }}</span>
            </div>
        @endif
            @if($errors->get('end_point') == null)
                <div class="form-group ">
                    <label class="control-label" for="end_point">Gala mērķis</label>
                    {{ Form::text('end_point', '', array_merge(['class' => 'form-control'],['id' => 'end_point'], ['placeholder' => '22.528796,8.173233']))}}
                </div>
            @else
                <div class="form-group has-error">
                    <label class="control-label" for="end_point">Gala mērķis</label>
                    {{ Form::text('end_point', '', array_merge(['class' => 'form-control'],['id' => 'end_point'], ['placeholder' => '22.528796,8.173233']))}}
                    <span class="help-block">{{ $errors->get('end_point')[0] }}</span>
                </div>
            @endif

        {{Form::button('Izveidot braucienu', array_merge(['class' => 'btn btn-success'], ['type' => 'submit'], ['name' => 'action'], ['value' => 'Save']))}}
    </div>
{!! Form::close() !!}
<div id="outerdiv">
    <div id="map"></div>
    <div id="right-panel">
        <div>
            <h3 id="car">{!! $car_id!=null?'Mašīna : <strong>'.\App\Car::where('id', $car_id)->first()->make.'</strong>':'' !!}</h3>
            <h3 id="trip_speed">{!! $trip?'Ātrums : <strong>'.$trip->speed.'</strong>':'' !!}</h3>
            @if($trip)
                @if($trip->ignition=='ON')
                    <h3 id="trip_ignition">{!! 'Motors : <strong> Ieslēgts </strong>' !!}</h3>
                @else
                    <h3 id="trip_ignition">{!! 'Motors : <strong> Izslēgts </strong>' !!}</h3>
                @endif
            @endif
            <input type="hidden" name="car_id" id="car_id" value="{{ $car_id!=null?$car_id:'' }}">
            <input type="hidden" id="start" value="{{$trip!=null?$trip->start_point:''}}">
            <input type="hidden" id="waypoints" value="{{$trip?$trip->waypoint:''}}">
            <input type="hidden" id="end" value="{{$trip?$trip->end_point:''}}">
            <input type="hidden" id="speed" value="{{$trip?$trip->speed:''}}">
            <input type="hidden" id="ignition" value="{{$trip?$trip->ignition:''}}">
        </div>
        <div id="directions-panel" style="height: 100%">

        </div>
    </div>
</div>
<script type="text/javascript">

        if (document.getElementById('start').value != '') {

            setInterval(function () {
                $.get("/send_sms", function (data) {
                });
            }, 30000);
            $(window).on('resize', function () {
                var currCenter = map.getCenter();
                google.maps.event.trigger(map, 'resize');
                map.setCenter(currCenter);
            })

            function initMap() {

                var directionsService = new google.maps.DirectionsService;
                var directionsDisplay = new google.maps.DirectionsRenderer;
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 6,
                    center: {lat: 56.763288, lng: 23.867292}
                });
                directionsDisplay.setMap(map);
                calculateAndDisplayRoute(directionsService, directionsDisplay);
            }

            function calculateAndDisplayRoute(directionsService, directionsDisplay) {
                if (document.getElementById('waypoints').value != '') {
                    directionsService.route({
                        origin: document.getElementById('start').value,
                        destination: document.getElementById('end').value,
                        waypoints: [{location: document.getElementById('waypoints').value, stopover: true}],
                        optimizeWaypoints: true,
                        travelMode: 'DRIVING'
                    }, function (response, status) {
                        if (status === 'OK') {
                            directionsDisplay.setDirections(response);
                            var route = response.routes[0];
                            var summaryPanel = document.getElementById('directions-panel');
                            summaryPanel.innerHTML = '';
                            // For each route, display summary information.
                            for (var i = 0; i < route.legs.length; i++) {
                                var routeSegment = i + 1;
                                summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
                                    '</b><br>';
                                summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
                                summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
                                summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
                            }
                        } else {
                            window.alert('Maršrutu parādīt nav iespējams. Izveidojiet braucienu!');
                        }
                    });
                } else {
                    directionsService.route({
                        origin: document.getElementById('start').value,
                        destination: document.getElementById('end').value,
                        optimizeWaypoints: true,
                        travelMode: 'DRIVING'
                    }, function (response, status) {
                        if (status === 'OK') {
                            directionsDisplay.setDirections(response);
                            var route = response.routes[0];
                            var summaryPanel = document.getElementById('directions-panel');
                            summaryPanel.innerHTML = '';
                            // For each route, display summary information.
                            for (var i = 0; i < route.legs.length; i++) {
                                var routeSegment = i + 1;
                                summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
                                    '</b><br>';
                                summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
                                summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
                                summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
                            }
                        } else {
                            window.alert('Directions request failed due to ' + status);
                        }
                    });
                }
                var smsarray = [];
                setInterval(function () {
                    $.get("/getsms", function (data) {
                        smsarray.push(data.message_count);
                        var i;
                        for (i = 0; i < smsarray.length; i++) {
                            if (smsarray[smsarray.length - 1] > smsarray[smsarray.length - 2]) {
                                $.get("/post_received_message/{{ $car_id!=null?$car_id:'' }}", function (data2) {
                                    console.log(data2);
                                    document.getElementById('start').value = data2.start_point;
                                    document.getElementById('waypoints').value = data2.waypoint;
                                    document.getElementById('end').value = data2.end_point;
                                    document.getElementById('trip_speed').value = data2.speed;
                                    document.getElementById('trip_ignition').value = data2.ignition;
                                    document.getElementById('speed').value = data2.speed;
                                    document.getElementById('ignition').value = data2.ignition;
                                    initMap();
                                });
                                break;
                            }
                        }
                    });
                }, 1000);
            }
        } else {
            if ('{{ $car_id }}' != null) {
                var smsarray = [];
                setInterval(function () {
                    $.get("/getsms/{{$car_id}}", function (data) {
                        smsarray.push(data.message_count);
                        var i;
                        for (i = 0; i < smsarray.length; i++) {
                            if (smsarray[smsarray.length - 1] > smsarray[smsarray.length - 2]) {
                                $.get("/post_received_message/{{ $car_id!=null?$car_id:'' }}", function (data2) {
                                    document.getElementById('start').value = data2.start_point;
                                    document.getElementById('waypoints').value = data2.waypoint;
                                    document.getElementById('end').value = data2.end_point;
                                    document.getElementById('speed').value = data2.speed;
                                    document.getElementById('ignition').value = data2.ignition;
                                    document.getElementById('trip_speed').value = data2.speed;
                                    document.getElementById('trip_ignition').value = data2.ignition;
                                    initMap();
                                });
                                break;
                            }
                        }
                    });
                }, 1000);
            }

            function initMap() {
                if ('{{ $car_id }}' != null) {
                    var directionsService = new google.maps.DirectionsService;
                    var directionsDisplay = new google.maps.DirectionsRenderer;

                    directionsDisplay.setMap(map);
                    calculateAndDisplayRoute(directionsService, directionsDisplay);
                }
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 6,
                    center: {lat: 56.763288, lng: 23.867292}
                });
            }
            if ('{{ $car_id }}' != null) {
                function calculateAndDisplayRoute(directionsService, directionsDisplay) {
                    if (document.getElementById('waypoints').value != '') {
                        directionsService.route({
                            origin: document.getElementById('start').value,
                            destination: document.getElementById('end').value,
                            waypoints: [{location: document.getElementById('waypoints').value, stopover: true}],
                            optimizeWaypoints: true,
                            travelMode: 'DRIVING'
                        }, function (response, status) {
                            if (status === 'OK') {
                                directionsDisplay.setDirections(response);
                                var route = response.routes[0];
                                var summaryPanel = document.getElementById('directions-panel');
                                summaryPanel.innerHTML = '';
                                // For each route, display summary information.
                                for (var i = 0; i < route.legs.length; i++) {
                                    var routeSegment = i + 1;
                                    summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
                                        '</b><br>';
                                    summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
                                    summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
                                    summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
                                }
                            } else {
                                window.alert('Maršrutu parādīt nav iespējams. Izveidojiet braucienu!');
                            }
                        });
                    } else {
                        directionsService.route({
                            origin: document.getElementById('start').value,
                            destination: document.getElementById('end').value,
                            optimizeWaypoints: true,
                            travelMode: 'DRIVING'
                        }, function (response, status) {
                            if (status === 'OK') {
                                directionsDisplay.setDirections(response);
                                var route = response.routes[0];
                                var summaryPanel = document.getElementById('directions-panel');
                                summaryPanel.innerHTML = '';
                                // For each route, display summary information.
                                for (var i = 0; i < route.legs.length; i++) {
                                    var routeSegment = i + 1;
                                    summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
                                        '</b><br>';
                                    summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
                                    summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
                                    summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
                                }
                            } else {
                                window.alert('Maršrutu parādīt nav iespējams. Izveidojiet braucienu!');
                            }
                        });
                    }
                }
            }
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