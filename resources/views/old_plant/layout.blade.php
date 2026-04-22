<!DOCTYPE html>
<html lang="en">

@php $__ver = "?v=1.2"; @endphp

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
        .table-data-area {
        position: relative;
    }

    #printBTN {
        margin-left: 178px;
        top: 16px;
        position: absolute;
        z-index: 109;
    }

    .txtr {
        text-align: right;
    }

    .getButton {
        background-color: #329df9;
        color: #fff;
        border: 0;
        transition: 0.5s;
    }

    .getButton:hover {
        box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.4);
    }



    form {
        margin-top: 20px;
        float: right;
        background: #f9f9f9;
        text-align: right;
    }



    .table {
        margin-top: 80px;
    }

    .getPdfButton {
        background-color: #3199f3;
        color: #fff;
        width: 98px;
        height: 29px;
        border-radius: 15px;
        float: left;
        border: 2px solid #0089ff;
        box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.4);
        margin: 10px
    }

    .printPdfReportP {
        background-color: #3199f3;
        color: #fff;
        width: 98px;
        height: 29px;
        border-radius: 15px;
        float: left;
        border: 2px solid #0089ff;
        box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.4);
        margin: 10px
    }

    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0;
    }

    #pdfBTNCmSubs {
        margin-left: 235px;
        top: 16px;
        position: absolute;
        z-index: 109;
        margin: 20px 0 0 50px;
    }


    @media print {

        html,
        body {
            height: 99%;
        }
    }
    </style>
</head>

<body style="margin-top:18px">

    @php if(isset(Auth::user()->name)) $brand = Auth::user()->name; elseif(isset(Session::get('plantInfo')->plantName))
    $brand = Session::get('plantInfo')->plantName; else $brand = "DMS"; // dd(Session::all()); 
@endphp

    <div id="wrapper" class="clearfix">

        <div class="upper-info-line">
            <div class="fl strcmlcase" style="padding-left:10px">
                {{"Plant Code: ".Session::get('plantInfo')->plantCode. " | Plant Name: ". Session::get('plantInfo')->plantName}}
            </div>
        </div>

        <div class="fr">
        </div>
                
        <div id="dropdown-noti" class="panel-collapse collapse">
            <span class="close notidropdown" data-toggle="collapse" data-target="#dropdown-noti">x</span>
            <div class="panel-body">
                <img src="{{asset("images/loading.gif")}}" alt="Loading..." width="150">
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
                            <a class="navbar-brand" href="DairyAdminDashbord">
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

                        <div class="notificationBellNav">
                            <li class="" id="notificationli" class="panel panel-default users">
                                <span class="bedge-number"></span>
                                <a data-toggle="collapse" href="#dropdown-noti" id="notificationBtn">
                                    <span class="fa fa-bell"></span>
                                    {{-- <span class="caret"></span> --}}
                                </a>
                            </li>
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

                        <li class="dashboard"><a href="{{url('plant/dashboard')}}"><span class="fa fa-dashboard"></span> Home</a></li>
                        
                        <li class="request"><a href="{{url('plant/requestToAdd')}}"><span class="fa fa-plus-square"></span>Verify Dairies</a></li>
                        @if(session()->get("plantInfo")->isMainPlant)
                            <li class="plants"><a href="{{url('plant/plants')}}"><span class="fa fa-plus-square"></span>Plants</a></li>
                        @endif
                        {{-- <li class="dairies"><a href="{{url('plant/dairies')}}"><span class="fa fa-plus-square"></span>Dairies</a></li> --}}
                        <li class="dairies"><a href="{{url('plant/alldairies')}}"><span class="fa fa-plus-square"></span>Dairies</a></li>
                        {{-- <li class="dairies"><a href="{{url('plant/dairies')}}"><span class="fa fa-plus-square"></span>Reports</a></li> --}}

                        <li class="panel panel-default report" id="dropdown">

                            <a data-toggle="collapse" href="#dropdown-report">

                                <span class="glyphicon glyphicon-stats"></span>Report <span class="caret"></span>

                            </a>



                            <!-- Dropdown level 1 -->

                            <div id="dropdown-report" class="panel-collapse collapse">

                                <div class="panel-body">

                                    <ul class="nav navbar-nav">

                                        <li class="sales"><a href="{{url('plant/allmember')}}">Member Report</a></li>
                                        <li class="sales"><a href="{{url('plant/payment_register')}}">Payment Register</a></li>
                                        <li class="sales"><a href="{{url('plant/shift_summary')}}">Shift Summary</a></li>
                                        <li class="sales"><a href="{{url('plant/cm_subsidiary')}}">CM Subsidiary</a></li>

                                        

                                    </ul>

                                </div>

                            </div>

                        </li>

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
        if(activepage=="members" || activepage=="suppliers" || activepage=="customers" || activepage == "milkPlants" || activepage == "payment_reigtser"){
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

            setTimeout(function(){ $(".flash-alert").slideDown("slow") }, 14000);

            setTimeout(function(){
                checkNotification();
            }, 3000);
        });

        // $(".notidropdown").on("click", function(){
        //     $("#dropdown-noti").toggle();
        // })

        function checkNotification(){
            $.ajax({
                type:"GET",
                url:'{{url('plant/checkNotification')}}',
                data: {},
                success:function(res){
                    if(res.error){
                        $("#dropdown-noti .panel-body").html("<div class='msg'>"+res.msg+"</div>");
                    }else{
                        count=0;
                        data = "<div class='all-notification'>";
                        res.data.forEach(noti => {
                            data += "<div class='single-noti-"+noti.id+"'>"
                            +"<div class='close' onclick='removeNoti("+noti.id+")'>x</div><div class='content'>"+noti.notification+"</div>"
                            +"<div class='time'>"+noti.created_at+"</div></div>";
                            count++;
                        });
                        data += '</div>';
                        $("#dropdown-noti .panel-body").html(data);
                        $("#notificationli .bedge-number").html(count+'').show();
                    }
               }
            });
        }

        function removeNoti(id){
            $.ajax({
                type:"GET",
                url:'{{url('plant/deleteNotification')}}',
                data: {
                    notiId: id
                },
                success:function(res){
                    if(res.error){
                        alert(res.msg);                        
                    }else{
                        $(".single-noti-"+id).fadeOut();
                    }
               }
            });
        }

        // function createTable(){
        // $('#MyTable').DataTable({

        //     initComplete: function (){

        //         this.api().columns().every( function () {

        //             var column = this;

        //             var select = $('<select><option value=""></option></select>')

        //                 .appendTo( $(column.footer()).empty())

        //                 .on('change', function () {

        //                     var val = $.fn.dataTable.util.escapeRegex(

        //                         $(this).val()

        //                     );

        //             //to select and search from grid

        //                     column

        //                         .search( val ? '^'+val+'$' : '', true, false )

        //                         .draw();

        //                 });

    

        //             column.data().unique().sort().each( function (d, j) {

        //                 select.append( '<option value="'+d+'">'+d+'</option>')

        //             });

        //         });

        //     }

    //     });
    // }
   
    </script>
</body>

</html>