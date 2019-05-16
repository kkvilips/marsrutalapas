@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Sveicināts!</div>

                <div class="panel-body">
                    Lai dotos lietot <strong>"Maršruta lapas"</strong> nospiediet <a href="{{ route('trips') }}">šeit</a>!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
