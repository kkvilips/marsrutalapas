@extends('layouts.master')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title pull-left">
                        <a href="{{ url('auto_park') }}">{{ $car->make }}</a>
                    </h3>
                    <a style="float: right" href="/remove_car/{{ $car->id }}"><button class="btn btn-danger">Dzēst</button></a>
                   <div class="clearfix"></div>
                </div>
                {{ Form::open(array('url' => route('car-data-entry'))) }}

                    <div class="panel-body">
                        <div class="form-group hidden">
                            <label for="id">ID:</label>
                            {{ Form::text('id', $car->id, array_merge(['class' => 'form-control'],['id' => 'id']))}}
                        </div>
                        @if($errors->get('make') == null)
                            <div class="form-group ">
                                <label for="make">Modelis</label>
                                {{ Form::text('make', $car->make, array_merge(['class' => 'form-control'],['id' => 'make'], ['placeholder' => '']))}}
                            </div>
                        @else
                            @php($myModal = true)
                            <div class="form-group has-error">
                                <label class="control-label" for="make">Modelis</label>
                                {{ Form::text('make', $car->make, array_merge(['class' => 'form-control'],['id' => 'make'], ['placeholder' => '']))}}
                                <span class="help-block">{{ $errors->get('make')[0] }}</span>
                            </div>
                        @endif
                        @if($errors->get('year') == null)
                            <div class="form-group ">
                                <label for="year">Izlaiduma gads</label>
                                {{ Form::text('year', $car->year, array_merge(['class' => 'form-control'],['id' => 'year'], ['placeholder' => '']))}}
                            </div>
                        @else
                            @php($myModal = true)
                            <div class="form-group has-error">
                                <label class="control-label" for="year">Izlaiduma gads</label>
                                {{ Form::text('year', $car->year, array_merge(['class' => 'form-control'],['id' => 'year'], ['placeholder' => '']))}}
                                <span class="help-block">{{ $errors->get('year')[0] }}</span>
                            </div>
                        @endif

                        @if($errors->get('reg_num') == null)
                            <div class="form-group ">
                                <label for="reg_num">Registrācijas numurs</label>
                                {{ Form::text('reg_num', $car->reg_num, array_merge(['class' => 'form-control'],['id' => 'reg_num'], ['placeholder' => '']))}}
                            </div>
                        @else
                            @php($myModal = true)
                            <div class="form-group has-error">
                                <label class="control-label" for="reg_num">Registrācijas numurs</label>
                                {{ Form::text('reg_num', $car->reg_num, array_merge(['class' => 'form-control'],['id' => 'reg_num'], ['placeholder' => '']))}}
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
                                {{ Form::text('gps_phone_number', $car->gps_phone_number, array_merge(['class' => 'form-control'],['id' => 'gps_phone_number'], ['placeholder' => '']))}}
                            </div>
                        @else
                            @php($myModal = true)
                            <div class="form-group has-error">
                                <label class="control-label" for="gps_phone_number">GPS sim numurs</label>
                                {{ Form::text('gps_phone_number', $car->gps_phone_number, array_merge(['class' => 'form-control'],['id' => 'gps_phone_number'], ['placeholder' => '']))}}
                                <span class="help-block">{{ $errors->get('gps_phone_number')[0] }}</span>
                            </div>
                        @endif
                        @if($errors->get('consumption') == null)
                            <div class="form-group ">
                                <label for="consumption">Degvielas patēriņš</label>
                                {{ Form::text('consumption', $car->consumption, array_merge(['class' => 'form-control'],['id' => 'consumption'], ['placeholder' => '']))}}
                            </div>
                        @else
                            @php($myModal = true)
                            <div class="form-group has-error">
                                <label class="control-label" for="consumption">Degvielas patēriņš</label>
                                {{ Form::text('consumption', $car->consumption, array_merge(['class' => 'form-control'],['id' => 'consumption'], ['placeholder' => '']))}}
                                <span class="help-block">{{ $errors->get('consumption')[0] }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="panel-footer">
                        <p class="text-right">
                            {{Form::button('Saglabāt', array_merge(['class' => 'btn btn-primary'], ['type' => 'submit']))}}
                            <!--{{Form::button('Apstiprināt', ['class' => 'btn btn-default'])}}-->
                        </p>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<div class="container">
    <table class="table table-striped">
        <thead class="thead-dark bg-primary">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Sākuma atrašanās vieta</th>
                <th scope="col">Pēdējā atrašanās vieta</th>
                <th scope="col">Galamērķis</th>
                <th scope="col">Braukšanas ātrums</th>
                <th scope="col">Motora statuss</th>
                <th scope="col">Brauciena statuss</th>
                <th scope="col">Brauciena datums</th>
            </tr>
        </thead>
        <tbody>
        @if($trips!=null)
            @foreach($trips as $trip)
            <tr>
                <th>{!! $trip->id !!}</th>
                <td>{!! $trip->start_point !!}</td>
                <td>{!! $trip->waypoint !!}</td>
                <td>{!! $trip->end_point !!}</td>
                <td>{!! $trip->speed !!}</td>
                <td>{!! $trip->ignition == 'ON'?'Ieslēgts':'Izslēgts' !!}</td>
                <td>{!! $trip->active == 1?'Aktīvs':'Neaktīvs' !!}</td>
                <td>{!! $trip->updated_at !!}</td>
            </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
     <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Edit car</h4>
        </div>
        <div class="modal-body">
           
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>

@endsection