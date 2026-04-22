<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">

    <meta name="author" content="">



    <title>DNS</title>

    <!-- jquery.min file  -->
        <script type="text/javascript" src="{!! asset('js/jquery.min.js') !!}"></script>
    

    <!-- Bootstrap Core CSS -->

    <link href="{!! asset('public/theme/vendor/bootstrap/css/bootstrap.min.css') !!}" rel="stylesheet">



    <!-- MetisMenu CSS -->

    <link href="{!! asset('public/theme/vendor/metisMenu/metisMenu.min.css') !!}" rel="stylesheet">



    <!-- Custom CSS -->

    <link href="{!! asset('public/theme/dist/css/sb-admin-2.css') !!}" rel="stylesheet">



    <!-- Morris Charts CSS -->

    <link href="{!! asset('public/theme/vendor/morrisjs/morris.css') !!}" rel="stylesheet">



    <!-- Custom Fonts -->

    <link href="{!! asset('public/theme/vendor/font-awesome/css/font-awesome.min.css') !!}" rel="stylesheet" type="text/css">

   <link href="{!! asset('public/css/addon/tabs.css') !!}" rel="stylesheet">

<style>
.btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
    margin-left: 10px;
}

.btn:hover {
  color:#fff;
}


.dtp div.dtp-date, .dtp div.dtp-time {
    background: #0337ac !important;
    text-align: center;
    color: #fff;
    padding: 10px;
}


.dtp > .dtp-content > .dtp-date-view > header.dtp-header {
      background: #0337ac !important;
    color: #fff;
    text-align: center;
    padding: 0.3em;
}
/*.dtp-select-hour {
  fill: #0337ac !important;
}
*/
</style>
  
</head>

<body>



    <div id="wrapper">



        <!-- Navigation -->

        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">

            @include('theme.header')

            @include('theme.sidebar')

        </nav>



        <div id="page-wrapper">

            @yield('content')

        </div>

        <!-- /#page-wrapper -->



    </div>

    <!-- /#wrapper -->

<script src="{!! asset('public/theme/vendor/jquery/jquery.min.js') !!}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>

    <script src="{!! asset('js/addon/moment.min.js') !!}"></script>  
    <link href="{!! asset('css/addon/bootstrap-datetimepicker.min.css') !!}" rel="stylesheet">
    <link href="{!! asset('css/addon/jquery.dataTables.min.css') !!}" rel="stylesheet">
    <script src="{!! asset('js/addon/bootstrap-datetimepicker.min.js') !!}"></script>  
    <script src="{!! asset('js/addon/jquery.dataTables.min.js') !!}"></script>  
    
     <!-- date Time Picker Custom js file -->

    <script src="{!! asset('js/addon/datepicker/dateTimePickerCustom.js') !!}"></script>
      
    <!-- jQuery -->

    



    <!-- Bootstrap Core JavaScript -->

    <script src="{!! asset('public/theme/vendor/bootstrap/js/bootstrap.min.js') !!}"></script>



    <!-- Metis Menu Plugin JavaScript -->

    <script src="{!! asset('public/theme/vendor/metisMenu/metisMenu.min.js') !!}"></script>



    <!-- Morris Charts JavaScript -->

    <script src="{!! asset('public/theme/vendor/raphael/raphael.min.js') !!}"></script>

    <script src="{!! asset('public/theme/vendor/morrisjs/morris.min.js') !!}"></script>

    <script src="{!! asset('public/theme/data/morris-data.js') !!}"></script>



    <!-- Custom Theme JavaScript -->

    <script src="{!! asset('public/theme/dist/js/sb-admin-2.js') !!}"></script>




</body>



</html>