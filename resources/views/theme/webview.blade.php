<!DOCTYPE html>

<html lang="en">

@php $__ver = "?v=1.9"; @endphp



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="shortcut icon" type="image/png" href="{{asset('favicon.png')}}" />

    <title>Dairy Management System </title>

    

    <link href="{{asset("css/bootstrap-3.2.0.min.css")}}" rel="stylesheet" id="bootstrap-css">

    {{-- <script src="{{asset("js/jquery-1.11.1.min.js")}}"></script>

    <script src="{{asset("js/bootstrap-3.2.0.min.js")}}"></script> --}}

    

    <link href="{{asset('css\4.7.0-font-awesome.min.css')}}" rel="stylesheet" type="text/css">

    {{-- <link href="{!! asset('theme/vendor/font-awesome/css/font-awesome.min.css') !!}" rel="stylesheet" type="text/css"> --}}

    {{-- <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet"> --}}

    {{-- <link href="{{asset("css/googleQuicksand.css")}}" rel="stylesheet"> --}}
    
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

    {{-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css"> --}}

    {{-- <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> --}}

     {{-- --------------------- bootstrap select picker ---------------------- --}} {{--

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> --}}

    <link rel="stylesheet" href="{!! asset('css/bootstrap-select.css') !!}"> 

    

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

    

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



    <link href="{!! asset('css/style1.css'). $__ver !!}" rel="stylesheet" type="text/css">

    <link href="{!! asset('css/style2.css'). $__ver !!}" rel="stylesheet" type="text/css"> 

    

    @yield('styles')



    <link rel="stylesheet" href="{{ asset("css/jquery-ui-1.12.1.css")}}">

    <script src="{{ asset('js/jquery-ui-1.12.1.js')}}"></script>



    <style>.navbar-default .navbar-brand {

        font-size: 22px;

        padding: 15px 15px;

    }

    

    #page-wrapper {

        margin: 0 ;

        padding: 0;

    }

    @media only screen and (max-width: 767px){

        #page-wrapper {

            margin: 8px 0 0 0;

            padding: 0;

        }

    }

    </style>

</head>



<body style="margin-top: 8px;">



    @php if(isset(Auth::user()->name)) $brand = Auth::user()->name; elseif(Session::get('loginUserTpe') == 'dairy') $brand =

        Session::get('dairyInfo')->dairyName; elseif(Session::get('loginUserType') == 'user') $brand = Session::get('loginUserInfo')->userName;

        else $brand = "DMS";

    @endphp



    <div class="span-fixed response-alert" id="response-global-alert"></div>



    <div id="wrapper" class="clearfix">



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

                <!-- <a href="https://techpathway.com/" target="_blank"> techpathway.com</a> -->
                <a href="{{url('download/getweight.zip')}}" download> techpathway.com</a>

            </div>

            <!-- Copyright -->

    </footer>



    <div class="wmodel clearfix" id="updateCurBal" style="width: 75%;">

        <div class="close">X</div>

        <div class="wmodel-body">



            <div  class="w3-container w3-border clearfix">

                <div class="pt-20"></div>

                <h3 class="light-color">Opening balance Details</h3>

                <div class="col-sm-6">

                    <input placeholder="Opening Balance" value="{{ old('memberPersonalOpeningBalance') }}" class="memberPersonalOpeningBalance form-control"

                        id="memberPersonalOpeningBalance" name="memberPersonalOpeningBalance">

                </div>

                <div class="col-sm-6">

                    <select name="openingBalanceType" class="openingBalanceType selectpicker" name="openingBalanceType" id="openingBalanceType" title="Opening Balance Type" required>

                        <option value="credit">Credit</option>

                        <option value="debit">Debit</option>

                    </select>

                </div>

            </div>



        </div>

    </div>



    @yield('scripts')



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

            $(this).closest('.flash-alert').fadeOut();

        })

        

        function loader(cmd){

            if(cmd=="show"){

                // $("#wrapper").addClass("blur-3");

                $(".loader").show();

            }else{

                $(".loader").hide();

                // $("#wrapper").removeClass("blur-3");

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



        });

        



        @if(Session::get('dairyInfo')->firstTimeBalanceUpdated == "false")

            promptCurrentBal();

            console.log("salf");

        @endif

        

        function promptCurrentBal(){



            jc = $.confirm({

                title: 'Update current dairy balance first.',

                content: '' +

                '<form action="{{url('updateCurrentDairyBal')}}" class="formName">' +

                '<div class="form-group">' +

                '<label>Opening Balance</label>' +

                '<input type="number" placeholder="Enter Opening Balance" value="0" class="bal form-control" required />' +

                '</div>' +



                '<div class="form-group">' +

                '<label>Opening Balance Type</label>' +

                '<select class="baltype form-control" required  title="Select type">' +

                    '<option value="cash" selected>Cash</option>'+

                '</select>'+

                '</div>' +



                '</form>',

				type: 'orange',

				typeAnimated: true,



                columnClass: 'col-md-4 col-md-offset-4',

                containerFluid: true, // this will add 'container-fluid' instead of 'container'



                buttons: {

                    formSubmit: {

                        text: 'Submit',

                        btnClass: 'btn-blue submit',

                        action: function () {

                            this.$content.find('form').submit();

                            return false;

                        }

                    },

                },

                onContentReady: function () {

                    // bind to events

                    var jc = this;

                    this.$content.find('form').on('submit', function (e) {

                        // if the user submits the form by pressing enter in the field.

                        e.preventDefault();



                        // jc.$$formSubmit.trigger('click'); // reference the button and click it



                        var bal = jc.$content.find('.bal').val();

                        var type = jc.$content.find('.baltype').val();



                        $.ajax({

                            type:"POST",

                            data: {bal: bal, type:type},

                            url:"{{url('updateCurrentDairyBal')}}",

                            success:function(res){

                                console.log(res);

                                if(res.error){

                                    $("#response-global-alert").show().html(res.msg);

                                }else{

                                    $("#response-global-alert").hide();

                                    jc.close();

                                    $(".flash-alert").removeClass("hide").addClass("alert-success");

                                    $(".flash-alert .flash-msg").html(res.msg);

                                }

                            },

                            error: function(res){

                                console.log(res);

                            }

                        });



                    });



                }

            });

        }



        

        $(document).keyup(function(e) {

            if(e.key === "Escape") {

                $(".wmodel:visible .close").trigger("click");

            }

        });





    </script>

</body>



</html>