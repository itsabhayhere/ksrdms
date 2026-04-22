<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="">
    <link rel="shortcut icon" type="image/png" href="favicon.png" />
    <link rel="shortcut icon" type="image/png" href="{{asset('/favicon.png')}}" />
    <title>{{ config('app.name', 'DMS') }}</title>
    <!-- jquery.min file  -->
    <script type="text/javascript" src="{!! asset('js/jquery.min.js') !!}"></script>
    <!-- Bootstrap Core CSS -->
    <link href="{!! asset('theme/vendor/bootstrap/css/bootstrap.min.css') !!}" rel="stylesheet" media='all'>
    <!-- MetisMenu CSS -->
    <link href="{!! asset('theme/vendor/metisMenu/metisMenu.min.css') !!}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{!! asset('theme/dist/css/sb-admin-2.css') !!}" rel="stylesheet">
    <!-- Morris Charts CSS -->
    <link href="{!! asset('theme/vendor/morrisjs/morris.css') !!}" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="{!! asset('theme/vendor/font-awesome/css/font-awesome.min.css') !!}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{!! asset('css/style.css') !!}" rel="stylesheet" type="text/css" media='all'>
    
    {{-- --------------------- bootstrap select picker ---------------------- --}} {{--
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> --}}
    <link rel="stylesheet" href="{!! asset('css/bootstrap-select.css') !!}"> {{--
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script> --}}
    <script src="{!! asset('js/bootstrap-select.js') !!}"></script>
    {{-- --------------------- end bootstrap select picker ---------------------- --}}


</head>

<body>
    <div id="wrapper" class="clearfix">
        <!-- Navigation -->
        <!-- <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0"> -->
        <nav class="clearfix" role="navigation" style="margin-bottom: 0">
    @include('theme.header')
    @include('theme.sidebar')
        </nav>
        <div id="page-wrapper" class="clearfix">
            @if(Session::has('msg'))
            <div class="flash-alert alert {{ Session::get('alert-class', 'alert-info') }}">
                <span class="close" aria-label="close">X</span>
                <div class="flash-msg">
                    {{ Session::get('msg') }}
                </div>
            </div>
            @endif
             @yield('content')

            <div class="flash-alert alert hide">
                <span class="close" aria-label="close">X</span>
                <div class="flash-msg">
                </div>
            </div>
        </div>

        <!-- /#page-wrapper -->
        <div class="footer">

        </div>

    </div>

    <div class="loader"></div>

    <!-- /#wrapper -->
    {{--
    <script src="{!! asset('theme/vendor/jquery/jquery.min.js') !!}"></script> --}}
    <script src="{{ asset('js/jquery.js')}}"></script>
    <script src="{!! asset('js/addon/moment.min.js') !!}"></script>
    <link href="{!! asset('css/addon/bootstrap-datetimepicker.min.css') !!}" rel="stylesheet">
    <script src="{!! asset('js/addon/bootstrap-datetimepicker.min.js') !!}"></script>
    <script src="{!! asset('js/addon/bootstrap.min.js') !!} "></script>


    
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}">
    <script src="{{asset('js/jquery-confirm.min.js')}}"></script>

    <!-- date Time Picker Custom js file -->
    <script src="{!! asset('js/addon/datepicker/dateTimePickerCustom.js') !!}"></script>
    <!-- jQuery -->
    <link href="{!! asset('css/addon/jquery.dataTables.min.css') !!}" rel="stylesheet">
    <script src="{!! asset('js/addon/jquery.dataTables.min.js') !!}"></script>
    <!-- Bootstrap Core JavaScript -->
    {{--<script src="{!! asset('public/theme/vendor/bootstrap/js/bootstrap.min.js') !!}"></script> --}}
    <!-- Metis Menu Plugin JavaScript -->
    <script src="{!! asset('theme/vendor/metisMenu/metisMenu.min.js') !!}"></script>
    <!-- Morris Charts JavaScript -->
    <script src="{!! asset('theme/vendor/raphael/raphael.min.js') !!}"></script>
    <!--     <script src="{!! asset('public/theme/vendor/morrisjs/morris.min.js') !!}"></script>
         <script src="{!! asset('public/theme/data/morris-data.js') !!}"></script> -->
    <!-- Custom Theme JavaScript -->
    <script src="{!! asset('theme/dist/js/sb-admin-2.js') !!}"></script>

    <script>
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
            $(this).closest('.flash-alert').addClass("hide");
        })
        
function loader(cmd){
    if(cmd=="show"){
        $(".loader").show();
    }else{
        $(".loader").hide();
    }
}
    </script>

    @yield('scripts')

</body>

</html>