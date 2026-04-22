<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="YRU2M8Ago7VVVYDOXj1SQ6IdiTpj4aKH8x1ydR85gs4" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" type="image/png" href="favicon.png" />
    <link rel="shortcut icon" type="image/png" href="{{asset('/favicon.png')}}" />

    <title>{{ config('app.name', 'DMS') }}</title>

    <link href="{{asset('css\4.7.0-font-awesome.min.css')}}" rel="stylesheet" type="text/css">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.2.1.min.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <link href="{{ asset('css/app.css') }}?v=123" rel="stylesheet">
    <link href="{{ asset('css/style1.css') }}?v=123" rel="stylesheet">
    {{-- <link href="{{ asset('css/style2.css') }}" rel="stylesheet"> --}}

    <style type="text/css">
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            margin: 0;
        }

        .errmsg {
            color: red;
        }
        .nav>li>a {
            padding: 22px 15px;
            height: 64px;
        }
        .navbar-default .navbar-brand{
            padding: 5px 0;
            height: 64px;
        }
    </style>
</head>

<body>

    <div class="flash-alert alert" style="display:none">
        <span class="close" aria-label="close">X</span>
        <div class="flash-msg">
        </div>
    </div>

    <div id="app">
        <nav class="navbar navbar-default navbar-static-top" style="border: none;">
            <div class="container">
                <div class="navbar-header fl">

                    <!-- Collapsed Hamburger -->
                    {{-- <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button> --}}

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{asset("images/logo_220x110.png")}}" alt="Dairy Management System" class="img img-responsive">
                    </a>
                </div>

                @if(session()->get('loginUserType') != "dairy")
                    @php if($activepage == "login"){
                            $lurl = "#login-form";
                        }else{
                            $lurl = url('dairy-login');
                        }
                    @endphp
                    <div class="fr">
                        <ul class="nav navbar-right">
                            <li class="fl"><a href="{{$lurl}}"> <i class="fa fa-sign-in"></i> Login</a></li>
                            <li class="fl"><a href="{{ url('buy') }}"> <i class="fa fa-plus"></i> Register</a></li>
                            <li class="fl"><a href="{{ url('contactUs') }}"> <i class="fa fa-life-ring"></i> Contact</a></li>
                        </ul>
                    </div>
                @endif

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    {{-- <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul> --}}

                    <!-- Right Side Of Navbar -->
                    <ul class="nav  navbar-right">
                        <!-- Authentication Links -->
                        @guest
                            {{-- <li><a href="{{url('dairy-login')}}">Login</a></li>
                            <li><a href="{{ url('buy') }}">Register</a></li> --}}
                        @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu">
                                <li>
                                    <a href="my-home">
                                            Dashbord
                                        </a>
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <div class="loader"></div>


    @if(Session::has('msg'))
    @if(Session::get('alert-class') == "alert-success")
    <div class="flash-alert alert {{ Session::get('alert-class') }}">
            <span class="close" aria-label="close">X</span>
            <div class="ani_check_mark">
                <div class="ani_sa-icon ani_sa-success ani_animate">
                    <span class="ani_sa-line ani_sa-tip ani_animateSuccessTip"></span>
                    <span class="ani_sa-line ani_sa-long ani_animateSuccessLong"></span>
                    <div class="ani_sa-placeholder"></div>
                    <div class="ani_sa-fix"></div>
                </div>
            </div>

            <div class="flash-msg">
                {{ Session::get('msg') }}
            </div>
        </div>
    @else
        <div class="flash-alert alert {{ Session::get('alert-class') }}">
            <span class="close" aria-label="close">X</span>
            <div class="flash-msg">
                {{ Session::get('msg') }}
            </div>
        </div>
    @endif
@elseif ($errors->any())
    <div class="flash-alert alert alert-danger">
        <span class="close" aria-label="close">X</span>
        <div class="flash-msg">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@else
    <div class="flash-alert alert hide">
        <span class="close" aria-label="close">X</span>
        
        <div class="ani_check_mark">
            <div class="ani_sa-icon ani_sa-success ani_animate">
                <span class="ani_sa-line ani_sa-tip ani_animateSuccessTip"></span>
                <span class="ani_sa-line ani_sa-long ani_animateSuccessLong"></span>
                <div class="ani_sa-placeholder"></div>
                <div class="ani_sa-fix"></div>
            </div>
        </div>

        <div class="flash-msg">
        </div>
    </div>
@endif

@include('layouts.footer')

    <script>

        
        function loader(cmd){
            if(cmd=="show"){
                $("#wrapper").addClass("blur-3");
                $(".loader").show();
            }else{
                $(".loader").hide();
                $("#wrapper").removeClass("blur-3");
            }
        }
        function loaderTime(t){
            loader("show")
            setTimeout( "loader('hide');", t);
        }
        


        $(document).ready(function(){
            setTimeout(function(){ $(".flash-alert").fadeOut("slow"); }, 4000);
          
            $(".flash-alert .close").on("click", function(){
                $(this).closest('.flash-alert').fadeOut();
            })
        });
    </script>
</body>

</html>