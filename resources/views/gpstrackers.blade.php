@extends('layouts.master')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">
                            Izsekošanas iekārtas konfigurācijas panelis
                            {{--<a href="{{ route('trips') }}"><span class="glyphicon glyphicon-menu-left"></span> Braucieni</a>--}}

                        </h3>
                        <div class="clearfix"></div>
                    </div>


                    <div class="panel-body">
                        {!! Form::open(['url' => '']) !!}
                        <div class="form-group">
                            {{ Form::label('name', 'APN iestatījumi', ['class'=>'offset-lg-3']) }}<br>
                            {{ Form::label('name', 'Vārds', ['class'=>'offset-lg-3']) }}
                            {{ Form::text('name', null, ['class' => 'form-control col-lg-6 offset-lg-3', 'id' => 'name', 'maxlenght'=>'200']) }}
                            <br>{{ Form::label('name', 'Lietotājs', ['class'=>'offset-lg-3']) }}
                            {{ Form::text('name', null, ['class' => 'form-control col-lg-6 offset-lg-3', 'id' => 'name', 'maxlenght'=>'200']) }}
                            <br>{{ Form::label('name', 'Parole', ['class'=>'offset-lg-3']) }}
                            {{ Form::text('name', null, ['class' => 'form-control col-lg-6 offset-lg-3', 'id' => 'name', 'maxlenght'=>'200']) }}
                        </div>
                        {{ Form::button('Labot', ['class' => 'btn btn-primary offset-lg-3', 'type' => 'submit']) }}
                        {{ Form::close() }}

                        {!! Form::open(['url' => '']) !!}
                        <br>
                        <div class="form-group">
                            {{ Form::label('name', 'Parole', ['class'=>'offset-lg-3']) }}
                            {{ Form::text('name', null, ['class' => 'form-control col-lg-6 offset-lg-3', 'id' => 'name', 'maxlenght'=>'200']) }}
                        </div>
                        {{ Form::button('Labot', ['class' => 'btn btn-primary offset-lg-3', 'type' => 'submit']) }}
                        {{ Form::close() }}

                        {!! Form::open(['url' => '']) !!}
                        <br>
                        <div class="form-group">
                            {{ Form::label('name', 'Datu sūtīšana', ['class'=>'offset-lg-3']) }}<br>
                            {{ Form::label('name', 'Min. ieraksti', ['class'=>'offset-lg-3']) }}
                            {{ Form::number('name', null, ['class' => 'form-control col-lg-6 offset-lg-3', 'id' => 'name', 'maxlenght'=>'200']) }}
                            <br>{{ Form::label('name', 'Periods', ['class'=>'offset-lg-3']) }}
                            {{ Form::number('name', null, ['class' => 'form-control col-lg-6 offset-lg-3', 'id' => 'name', 'maxlenght'=>'200']) }}
                        </div>
                        {{ Form::button('Labot', ['class' => 'btn btn-primary offset-lg-3', 'type' => 'submit']) }}
                    {{ Form::close() }}

                        {!! Form::open(['url' => '']) !!}
                        <br>
                        <div class="form-group">
                            {{ Form::label('name', 'Koeficienti', ['class'=>'offset-lg-3']) }}<br>
                            {{ Form::label('name', 'Distance', ['class'=>'offset-lg-3']) }}
                            {{ Form::number('name', null, ['class' => 'form-control col-lg-6 offset-lg-3', 'id' => 'name', 'maxlenght'=>'200']) }}
                            <br>{{ Form::label('name', 'Iedarbināta motora laiks', ['class'=>'offset-lg-3']) }}
                            {{ Form::number('name', null, ['class' => 'form-control col-lg-6 offset-lg-3', 'id' => 'name', 'maxlenght'=>'200']) }}
                        </div>
                        {{ Form::button('Labot', ['class' => 'btn btn-primary offset-lg-3', 'type' => 'submit']) }}
                        {{ Form::close() }}

                    </div>



                </div>
            </div>
        </div>
    </div>
    </div>
    {{--<script type="text/javascript">--}}
        {{--$( document ).ready(function() {--}}
            {{--$.get( "/gps_map/{Jelgava, Dzelzceļa stacija, Stacijas iela, Jelgava}/{56.643736, 23.729850}/{Alunāna parks}", function( data ) {--}}
                {{--$( "#show_map" ).html( data );--}}
            {{--});--}}
        {{--});--}}
    {{--</script>--}}
@endsection

