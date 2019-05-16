@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Maršrutu vēsture
                    </div>
                    <div class="panel-body">
                        <table class="table" id="trip-table">
                            <thead class="thead-dark bg-primary">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">No</th>
                                    <th scope="col">Uz</th>
                                    <th scope="col">Atrašanās vieta</th>
                                    <th scope="col">Mašīnas modelis</th>
                                    <th scope="col">Reģistrācijas numurs</th>
                                    <th scope="col">Brauciena datums</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $count = 0 ?>
                            @if($trips!=null)
                                @foreach($trips as $trip)
                                    <?php $car = \App\Car::where('id', $trip['car_id'])->first() ?>
                                    <tr>
                                        <th>{!! $count !!}</th>
                                        <td>{!! $trip['geocode_startpoint'] !!}</td>
                                        <td>{!! $trip['geocode_endpoint'] !!}</td>
                                        <td>{!! $trip['geocode_waypoint'] !!}</td>
                                        <td>{!! $car->make !!}</td>
                                        <td>{!! $car->reg_num !!}</td>
                                        <td>{!! $trip['created_at'] !!}</td>
                                        <td>
                                            <a href="/trip_pdf/{{ $trip['id'] }}">
                                                <button type="button" class="btn btn-warning">PDF</button>
                                            </a>
                                        </td>
                                    </tr>
                                <?php $count++; ?>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script
            src="https://code.jquery.com/jquery-3.4.1.js"
            crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#trip-table').DataTable();
    } );
</script>
@endsection
