<nav class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand">
                GPS izsekošanas sistēma
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    
                @else
{{--                    <li class="{{ Request::segment(1) === 'trips' ? 'active' : Request::segment(1) === 'trip' ? 'active' : null }}"><a href="{{ route('trips') }}">Braucieni</a></li>--}}
{{--                    <li class="hidden-sm {{ Request::segment(1) === 'destinations' ? 'active' : Request::segment(1) === 'destination' ? 'active' : Request::segment(1) === 'distance' ? 'active' : null }}"><a href="{{ route('destinations') }}">Galamērķi</a></li>--}}
{{--                    <li class="hidden-sm {{ Request::segment(1) === 'cars' ? 'active' : Request::segment(1) === 'car' ? 'active' : null }}"><a href="{{ route('cars') }}">Mašīnas</a></li>--}}
{{--                    <li class="hidden-sm {{ Request::segment(1) === 'export' ? 'active' : null }}"><a href="{{ route('export') }}">Izdruka</a></li>--}}
                    <li class="dropdown hidden-lg hidden-md hidden-xs">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"  aria-expanded="true">Izvelne <span class="caret"></span></a>
                        <ul class="dropdown-menu">
{{--                            <li class="{{ Request::segment(1) === 'destinations' ? 'active' : Request::segment(1) === 'destination' ? 'active' : Request::segment(1) === 'distance' ? 'active' : null }}"><a href="{{ route('destinations') }}">Galamērķi</a></li>--}}
{{--                            <li class="{{ Request::segment(1) === 'cars' ? 'active' : Request::segment(1) === 'car' ? 'active' : null }}"><a href="{{ route('cars') }}">Mašīnas</a></li>--}}
{{--                            <li class="{{ Request::segment(1) === 'export' ? 'active' : null }}"><a href="{{ route('export') }}">Izdruka</a></li>--}}
                        </ul>
                    </li>
                    <li class=""><a href="{{ url('/gps_map') }}">GPS braucieni</a></li>
                    <li class=""><a href="{{ url('/auto_park') }}">Autoparks</a></li>
                    <li class=""><a href="{{ url('/trip_history') }}">Maršrutu vēsture</a></li>

                    @if (Auth::user()->role == 'admin')
{{--                        <li class=""><a href="{{ route('profile-list') }}">Profili</a></li>--}}
                    @endif
                @endif
                
                
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())                    
                    <li><a href="{{ route('register') }}"><span class="glyphicon glyphicon-user"></span> Reģistrēties</a></li>
                    <li><a href="{{ route('login') }}"><span class="glyphicon glyphicon-log-in"></span> Autorizēties</a></li>
                @else
                    {{--<li><a href="{{ route('suggestions') }}">Ieteikumi</a></li>--}}
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                    Iziet
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    </nav>