<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
</head>
<style>
    body {
        font-family: DejaVu Sans, sans-serif;
    }
    th{
        font-size: 15px;
    }
    .normal-text{
        font-weight: normal !important;
    }
</style>
<body>
<div style="text-align: center; width: 100%"><h1>Brauciena atskaite</h1></div>
<img src="index.png" alt="Logo" width="150px" height="110px" style="display: block">
<table style="width:100%">
    <tr>
        <th><span class="normal-text">Lietotājs:</span><br>
            {!! \App\User::where('id', \Illuminate\Support\Facades\Auth::id())->first()->name !!}
            <br><br>
            <span class="normal-text">Brauciena datums:</span><br>
            {!! $trip['created_at'] !!}<br>
        </th>
        <th>
            <span class="normal-text">Auto:</span><br>
            {!! $car->make !!}<br>
            <span class="normal-text">Reģistrācijas numurs:</span><br>
            {!! $car->reg_num !!}<br>
            <span class="normal-text">Auto izlaiduma gads:</span><br>
            {!! $car->year !!}<br>
            <span class="normal-text">Vidējais degvielas patēriņš:</span><br>
            {!! $car->consumption !!} L<br>
        </th>
    </tr>
</table>
<table style="width:80%; padding-top: 5%">
    <tr>
        <td>
            Brauciena sākumpunkts
        </td>
        <th>
            {!! $trip['geocode_startpoint'] !!}
        </th>
    </tr>
    <tr>
        <td>
            Brauciena galamērķis
        </td>
        <th>
            {!! $trip['geocode_endpoint'] !!}
        </th>
    </tr>
    <tr>
        <td>
            Tagadējā auto atrašanās vieta
        </td>
        <th>
            {!! $trip['geocode_waypoint'] !!}
        </th>
    </tr>
    <tr>
        <td>
            Braucienā patērētā degviela
        </td>
        <th>
            {!! $consumedFuel !!} L
        </th>
    </tr>
    <tr>
        <td>
            Atlikušais attālums
        </td>
        <th>
            {!! $distanceToGo['distance'] !!}
        </th>
    </tr>
    <tr>
        <td>
            Nobrauktais attālums
        </td>
        <th>
            {!! $distanceDriven['distance'] !!}
        </th>
    </tr>
    <tr>
        <td>
            Laiks līdz galamērķim
        </td>
        <th>
            {!! $distanceDriven['time'] !!}
        </th>
    </tr>
</table>
</body>
</html>