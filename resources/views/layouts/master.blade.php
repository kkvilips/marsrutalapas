<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Bakalaura darbs</title>
    <!-- Styles -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD8ouIhejC09fOuuWnU4N-eAatMNRTg7Kw&callback=initMap" type="text/javascript"></script>
    
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
    
</head>
<body>
    <div id="app">
        @include('layouts.navigation')
        <!--@yield('nav')-->
        @yield('content')

        @include('layouts.footer')
    </div>
    
    
    
    <!-- 3th Scripts-->
    <!--/--> 
    
    
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('js/locale/lv.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.js') }}"></script>
    

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/i18n/defaults-*.min.js"></script>-->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/editodometer.js') }}"></script>
    <!-- view yield script -->


    <script>
        $( document ).ready(function() {
            var email = $('.build-email');
            jQuery.each( email, function( i, val ) {
                var mail = $(val).attr('email-name') + '@' + $(val).attr('email-domain')
                $(val).attr('href','mailto:' + mail ).text(mail);
            });
        });
    </script>
    @yield('script')
</body>
</html>
