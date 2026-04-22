<!DOCTYPE html>
<html lang="en">

@php $__ver = "?v=1.1.1"; @endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/png" href="{{asset('images/logo_90x45.png')}}" />
    <title>Dairy Management System </title>

    <link href="{{asset("css/bootstrap-3.2.0.min.css")}}" rel="stylesheet" id="bootstrap-css">
    <script src="{{asset("js/jquery-1.11.1.min.js")}}"></script>
    <script src="{{asset("js/bootstrap-3.2.0.min.js")}}"></script>

    <link href="{{asset('css\4.7.0-font-awesome.min.css')}}" rel="stylesheet" type="text/css"> {{--
    <link href="{!! asset('theme/vendor/font-awesome/css/font-awesome.min.css') !!}" rel="stylesheet" type="text/css"> --}} {{--
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet"> --}}
    <link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet"> {{--
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css"> --}} {{--
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> --}} {{-- --------------------- bootstrap select picker ---------------------- --}} {{--
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> --}}
    <link rel="stylesheet" href="{!! asset('css/bootstrap-select.css') !!}"> {{--
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script> --}}
    <script src="{!! asset('js/bootstrap-select.js') !!}"></script>
    {{-- --------------------- end bootstrap select picker ---------------------- --}}

    <script src="{!! asset('js/addon/moment.min.js') !!}"></script>
    <link href="{!! asset('css/addon/bootstrap-datetimepicker.min.css') !!}" rel="stylesheet">
    <script src="{!! asset('js/addon/bootstrap-datetimepicker.min.js') !!}"></script>

    <link href="{{ asset('css/addon/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{asset('css/buttons.dataTables.min.css')}}" rel="stylesheet">
    <script src="{{ asset('js/addon/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('js/buttons.print.min.js')}}"></script>
    <script src="{{ asset('js/pdfmake-0.1.36-pdfmake.min.js')}}"></script>
    <script src="{{ asset('js/pdfmake-0.1.36-vfs_fonts.js')}}"></script>
    <script src="{{ asset('js/buttons.html5.min.js')}}"></script>
    <script src="{{ asset('js/3.1.3-jszip.min.js')}}"></script>

    <script src="{!! asset('js/addon/datepicker/dateTimePickerCustom.js') !!}"></script>

    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}">
    <script src="{{asset('js/jquery-confirm.min.js')}}"></script>

    <link href="{!! asset('css/11.css'). $__ver !!}" rel="stylesheet">

    <link href="{!! asset('css/style1.css'). $__ver !!}" rel="stylesheet" type="text/css">
    <link href="{!! asset('css/style2.css'). $__ver !!}" rel="stylesheet" type="text/css"> @yield('styles')

    <link rel="stylesheet" href="{{ asset('css/jquery-ui-1.12.1.css')}}">
    <script src="{{ asset('js/jquery-ui-1.12.1.js')}}"></script>

    <style>
        .navbar-default .navbar-brand {
            font-size: 22px;
            padding: 15px 15px;
        }

        .navbar-toggle {
            padding: 25px;
        }

        .side-menu-container > .navbar-nav {
            top: 58px;
        }
    </style>
</head>

<body style="margin-top:18px">

    @php if(isset(Auth::user()->name)) $brand = Auth::user()->name; elseif(isset(Session::get('loginUserInfo')->memberPersonalName))
    $brand = Session::get('loginUserInfo')->memberPersonalName; else $brand = "DMS"; // dd(Session::all()); 
@endphp

    <div id="wrapper" class="clearfix">

        <div class="upper-info-line">
            <div class="fl strcmlcase" style="padding-left:10px">
                {{"Society Code: ".Session::get('loginUserInfo')->society_code. " | Society Name: ". Session::get('loginUserInfo')->dairyName. 
                " | Member Name: ".Session::get('loginUserInfo')->memberPersonalName. " | Code: " .Session::get('loginUserInfo')->memberPersonalCode}}
            </div>
        </div>
        
        <div class="side-menu">

            <nav class="navbar navbar-default" role="navigation" style="margin-bottom: 52px;">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <div class="brand-wrapper">
                        <!-- Hamburger -->
                        <button type="button" class="navbar-toggle">
                            <span class="sr-only"> Toggle navigation</span> 
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                        <!-- Brand -->
                        <div class="brand-name-wrapper">
                            <a class="navbar-brand" href="{{url("member/dashboard")}}">
                                <img src="{{asset('images/logo_220x110.png')}}" class="brand-img" />
                                {{-- {{$brand}}
                                @if(isset(Session::get('loginUserInfo')->memberPersonalCode))
                                    <br>
                                    <span style="font-size:13px;">
                                        Member Code: {{Session::get('loginUserInfo')->memberPersonalCode}}
                                    </span>
                                @endif --}}
                            </a>
                        </div>

                        <!-- Search -->
                        <a data-toggle="collapse" href="#menu-l" class="btn btn-default" id="menu-l-trigger">
                        {{-- <span class="glyphicon glyphicon-search"></span> --}}
                        <div class="bar-container" onclick="openMenu(this)">
                            <div class="bar1"></div>
                            <div class="bar2"></div>
                            <div class="bar3"></div>
                        </div>
                    </a>

                        <!-- Search body -->
                        <div id="menu-l" class="panel-collapse collapse">
                            <div class="panel-body">

                                <li style="padding: 15px;list-style: none;float: right;">
                                    <a href="{{url('my-logout')}}">Logout <span class="fa fa-sign-out fa-fw" style="margin-left: 8px;"></span></a>
                                </li>

                            </div>
                        </div>

                    </div>

                </div>

                <!-- Main Menu -->
                <div class="side-menu-container">
                    <ul class="nav navbar-nav">

                        {{--
                        <!-- Dropdown-->
                        <li class="panel panel-default users" id="dropdown">
                            <a data-toggle="collapse" href="#dropdown-users">
                            <span class="glyphicon glyphicon-user"></span>Users <span class="caret"></span>
                        </a>

                            <!-- Dropdown level 1 -->
                            <div id="dropdown-users" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul class="nav navbar-nav">
                                        <li class="members"><a href="memberList">Members</a></li>
                                        <li class="suppliers"><a href="supplierList">Suppliers</a></li>
                                        <li class="customers"><a href="customerList">Customers</a></li>
                                        <li class="milkPlants"><a href="milkPlantList">Milk Plants</a></li>
                                    </ul>
                                </div>
                            </div>
                        </li> --}}

                        <li class="dashboard"><a href="{{url('member/dashboard')}}"><span class="fa fa-dashboard"></span> Home</a></li>
                        <li class="dairyList"><a href="{{url('member/collectionHistory')}}"><span class="fa fa-list-alt"></span> My Passbook </a></li>
                        <li class="purchase"><a href="{{url('member/purchaseHistory')}}"><span class="fa fa-list-alt"></span> Purchase History </a></li>
                        <li class="payments"><a href="{{url('member/payments')}}"><span class="fa fa-list-alt"></span> Payments </a></li>
                        <li class="statement"><a href="{{url('member/statement')}}"><span class="fa fa-list-alt"></span> Statement </a></li>
                        <li class="notification"><a href="{{url('member/notification')}}"><span class="fa fa-list-alt"></span> Request </a></li>
                        <li class="changePassword"><a href="{{url('member/changePassword')}}"><span class="fa fa-list-alt"></span> Change Password </a></li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </nav>

        </div>


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
        @elseif (isset($errors) && $errors->any())
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


        <div id="page-wrapper" class="clearfix">
            @yield('content')
        </div>

    </div>

    <div class="loader"></div>

    <footer class="footer">
        {{-- <span class="text-muted">Place sticky footer content here.</span> --}}
        <!-- Copyright -->
        <div class="footer-copyright text-center py-3">© {{date("Y")}} powered by
            <a href="https://techpathway.com/" target="_blank"> techpathway.com</a>
        </div>
        <!-- Copyright -->
    </footer>

    @yield('scripts')

    <script>
        activepage = "@if(!isset($activepage)){{"noclass"}}@else{{$activepage}}@endif";
        if(activepage=="members" || activepage=="suppliers" || activepage=="customers" || activepage == "milkPlants"){
            $("#dropdown-users").addClass("in");
        }

        $("."+activepage).addClass("active");

        console.log(activepage);

        $(function () {
            $('.navbar-toggle').click(function () {
                $('.navbar-nav').toggleClass('slide-in');
                $('.side-body').toggleClass('body-slide-in');
                $('#search').removeClass('in').addClass('collapse').slideUp(200);

                /// uncomment code for absolute positioning tweek see top comment in css
                //$('.absolute-wrapper').toggleClass('slide-in');
                
            });
        
        // Remove menu for searching
        $('#menu-l-trigger').click(function () {
                $('.navbar-nav').removeClass('slide-in');
                $('.side-body').removeClass('body-slide-in');

                /// uncomment code for absolute positioning tweek see top comment in css
                //$('.absolute-wrapper').removeClass('slide-in');

            });
        });

        function formatDate(date) {
            var monthNames = [
                "Jan", "Feb", "Mar",
                "Apr", "May", "Jun", "Jul",
                "Aug", "Sep", "Oct",
                "Nov", "Dec"
            ];

            var day = date.getDate();
            var monthIndex = date.getMonth();
            var year = date.getFullYear();

            return day + ' ' + monthNames[monthIndex] + ' ' + year;
        }

        $(".flash-alert .close").on("click", function(){
            $(this).closest('.flash-alert').hide();
        })
        
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

        function openMenu(x) {
            x.classList.toggle("change");
        }

        
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   

            $('.datepicker').datetimepicker({
                 format: 'DD-MM-YYYY'
            });
            
            $('[data-toggle="popover"]').popover(); 

            setTimeout(function(){ $(".flash-alert").fadeOut("slow"); }, 4000);
        });
    </script>
</body>

</html>