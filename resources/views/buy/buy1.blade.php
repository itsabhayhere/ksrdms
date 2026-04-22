<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/png" href="{{asset('favicon.png')}}" />
    <title>Dairy Management System </title>

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    {{-- <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>  --}}
    {{-- <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> --}}
        
    {{-- <link href="{{asset("css/bootstrap-3.2.0.min.css")}}" rel="stylesheet" id="bootstrap-css"> --}}
    <script src="{{asset("js/jquery-1.11.1.min.js")}}"></script>
    <script src="{{asset("js/bootstrap-3.2.0.min.js")}}"></script>
    
    <link href="{{asset('css\4.7.0-font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
    <link rel="stylesheet" href="{!! asset('css/bootstrap-select.css') !!}">


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
    
    <link href="{!! asset('css/11.css') !!}" rel="stylesheet">

    <link href="{!! asset('css/style1.css') !!}" rel="stylesheet" type="text/css">
    <link href="{!! asset('css/style2.css') !!}" rel="stylesheet" type="text/css"> @yield('styles')

    <link rel="stylesheet" href="{{ asset("css/jquery-ui-1.12.1.css")}}">
    <script src="{{ asset('js/jquery-ui-1.12.1.js')}}"></script>
    
    <style>
        .navbar-brand img.img.img-responsive {
            height: 78px;
        }
        .navbar-default {
            background-color: #fff;
        }
    </style>
</head>

<body>

        <nav class="navbar navbar-default navbar-static-top" style="border: none;">
                <div class="container">
                    <div class="navbar-header">
                        
                        <!-- Branding Image -->
                        <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{asset("images/logo_220x110.jpg")}}" alt="Dairy Management System" class="img img-responsive">
                        </a>
                    </div>
    
                    <div class="collapse navbar-collapse" id="app-navbar-collapse">
                        <!-- Left Side Of Navbar -->
                        <ul class="nav navbar-nav">
                            &nbsp;
                        </ul>
    
                        <!-- Right Side Of Navbar -->
                        <ul class="nav navbar-nav navbar-right">
                            <!-- Authentication Links -->
                            @guest
                            {{-- <li> <a href="dairy-login">Dairy Login</a></li>
                            <li> <a href='my-login'>User Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li> --}}
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
    

<br>



<div class="container mb-5 mt-5">
    <div class="pricing card-deck flex-column flex-md-row mb-3">
        
    @foreach($pp as $d) 

        <div class="card card-pricing shadow text-center px-3 mb-4">
            <span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white shadow-sm">
                {{strtoupper($d->name)}}
            </span>
            <div class="bg-transparent card-header pt-4 border-0">
                <h1 class="h1 font-weight-normal text-primary text-center mb-0" data-pricing-value="15">
                    &#8377;<span class="price">{{$d->monthlyPrice}}</span>
                    <span class="h6 text-muted ml-2">/ per month</span>
                </h1>
            </div>
            <div class="card-body pt-0">
                <ul class="list-unstyled mb-4">
                    <li>
                        @if($d->noOfMem < 5000)
                            {{" Member Limit ".$d->noOfMem}}
                        @else
                            Member: Unlimited
                        @endif
                    </li>
                    <li>
                        {{" SMS Limit ".$d->noOfSms}}
                    </li>
                    <li>
                        {{$d->trial_time}} Days Free Trial
                    </li>
                    {{-- <li>Free cancelation</li> --}}
                </ul>
                <button type="button" class="btn btn-outline-primary mb-3" onclick="openPlanFormModel({{$d->id}})">Order now</button>
            </div>
        </div>

        @endforeach
    </div>
</div>




<div class="wmodel clearfix" id="buyModel" style="width:100%;height:100%;max-height:100%">
        <div class="close" onclick="closePlanFormModel()">X</div>
        <div class="wmodel-body">
            <div class="container">
    
                <form name="form-dairysetup" action="{{url('sa/saveDairyAndPay')}}" method="post">
                <div class="row">
                    <div class="col-sm-7">
                        <h3>Enter Your Details</h3>
                        <hr>
                            <input type="hidden" name="tid" id="tid" readonly />
                            <input type="hidden" name="merchant_id" value="196388"/>
                            <input type="hidden" name="order_id" value="123654789"/>
                            
                            <input type="hidden" name="currency" value="INR"/>
                            <input type="hidden" name="redirect_url" value="{{url('ccavResponseHandler')}}"/>
                            <input type="hidden" name="cancel_url" value="{{url('ccavResponseHandler')}}"/>
                            <input type="hidden" name="language" value="EN"/>
                    
                            <input type="hidden" name="createBySuperAdmin" value="0"/>
                            <input type="hidden" name="pricePlanId" value="" id="pricePlanId">
        
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="name">Dairy Name:</label>
                                    <input type="text" class="form-control" name="name" id="dairyName" required>
                                </div>
                            </div>
        
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="dairyCode">Dairy Code:</label>
                                    <input type="text" class="form-control" name="code" id="dairyCode" required>
                                </div>
                            </div>
        
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="dairyContact">Dairy Contact:</label>
                                    <input type="number" class="form-control" name="mobile" id="dairyContact" required>
                                </div>
                            </div>
        
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="owName">Propritor Name:</label>
                                    <input type="text" class="form-control" name="owname" id="owName" required>
                                </div>
                            </div>
        
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="owMobile">Propritor Mobile No.:</label>
                                    <input type="text" class="form-control" name="owmobile" id="owMobile" required>
                                </div>
                            </div>
        
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="owEmail">Propritor Email:</label>
                                    <input type="text" class="form-control" name="owemail" id="owEmail" required>
                                </div>
                            </div>
        
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">Address</label>
                                    <textarea maxlength="200" required="required" name="owaddress" class="form-control" id="paddress" placeholder="Address"></textarea>
                                </div>
                            </div>
        
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">State</label>
                                    <select name="owstate" class="selectpicker" id="pstate"  title="Select State" onchange="getCity(this, 'pdistrict')" required data-live-search="true">
                                        @foreach ($states as $allStates)
                                            <option value=" {{$allStates->id}} ">{{$allStates->name}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
        
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">District</label>
                                    <select name="owdistrict" class="selectpicker" id="pdistrict"  title="Select District" required data-live-search="true">
                                    </select>
                                </div>
                            </div>
        
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Pin Code</label>
                                    <input maxlength="200" required="required" name="owpin" class="form-control" placeholder="Pin code" id="ppinCode" />
                                </div>
                            </div>
        
                            <div class="clearfix mb-20"></div>
        
                            <div class="col-sm-12">
                                <button type="submit" id="getdashboardbtn" class="btn btn-default fr">Get Your Dairy Dashboard</button>
                            </div>
                    </div>
        
                    <div class="col-md-5">
                        <div class="pt-100"></div>
                        <div class="buy-s-plan">
                            Selected Plan
                            <div class="s-p-name" id="buy-s-name"></div>
                            <br>
                            <br>
                            Select Price
                            <div>
                                <br>
                                <label for="monthlyPrice">
                                    <input type="radio" name="priceMonthlyOrYearly" class="fl m-18-12" id="monthlyPrice" value="monthly" required>
                                    <div class="s-p-price fl" >
                                        <span class="rupee-symb">&#8377;</span>
                                        <span class="" style="margin-left: 15px;" id="buy-sm-price">
                                        </span>
                                        <span class="small-black-585">per Month </span>
                                    </div>
                                </label>
                            </div>
                            <div>
                                <label for="yearlyPrice">
                                    <input type="radio" name="priceMonthlyOrYearly" class="fl m-18-12" id="yearlyPrice" value="yearly" required>
                                    <div class="s-p-price fl">
                                        <span class="rupee-symb">&#8377;</span>
                                        <span class="" style="margin-left: 15px;" id="buy-sy-price">
                                        </span>
                                        <span class="small-black-585">per Year </span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                </form>
                
            </div>
        </div>
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
    <div class="flash-alert alert d-none">
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
    <div class="footer-copyright text-center py-3">© 2018 Copyright:
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
    $(this).closest('.flash-alert').fadeOut();
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
});









        function openPlanFormModel(id){
            $("#buyModel").fadeIn();
    
                $.ajax({
                    type:"POST",
                    url:"{{url('sa/getPricePlanDetails')}}",
                    data: {planId: id},
                    success:function(res){  
                        if(res.error){
                            $.alert(res.msg);
                            closePlanFormModel();
                        }else{
                            setPlanData(res.plan);
                        }
                        console.log(res);
                    },
                    error: function(d){
                        console.log(d);
                    }
                });
        }
    
        function closePlanFormModel(){
            $("#buyModel").fadeOut();
        }
    
        function setPlanData(plan){
            $("#pricePlanId").val(plan.id);
            $("#buy-s-name").html(plan.name);
            $("#buy-sm-price").html(plan.monthlyPrice);
            $("#buy-sy-price").html(plan.yearlyPrice);
        }
    
        
        function getCity(e, cityid){
            var stateID = $(e).val();    
            
            if(stateID){
                $.ajax({
                    type:"GET",
                    url:"{{url('/add-dairy-admin/city')}}?state_id="+stateID,
                    success:function(res){               
                    if(res){
                        $("#"+cityid).empty();
                        $.each(res,function(key,value){
                            $("#"+cityid).append('<option value="'+key+'">'+value['name']+'</option>');
                        });
                        $("#"+cityid).selectpicker("refresh");
                    }else{
                        $("#"+cityid).empty();
                    }
                    }
                });
            }else{
                $("#cityid"+cityid).empty();
            }
        }
    
    
        $("#getdashboardbtn").on("click", function(event){
            event.preventDefault();
    
            // loader("show");
    
            data = $("#form-dairysetup").serializeArray();
            console.log(data);
    
            $.ajax({
                type:"POST",
                url:"{{url('registerNewDairy')}}",
                data: data,
                success:function(res){
                    console.log(res);
                    if(!res.error){
                        // $(".flash-alert").removeClass("hide").addClass("alert-success"); $(".flash-alert .flash-msg").html(res.msg);
                        // window.location.assign("{{url('sa/dairyList')}}");
                    }else{
                        // $.alert(res.msg);
                    }
                },
                error:function(res){
                }
            }).done(function(res){
                // loader("hide");
                console.log(res);
            });
        });
    
    </script>




</body>

</html>