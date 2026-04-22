<!DOCTYPE html>

<html lang="en">

<?php $__ver = "?v=1.1239"; ?>



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('images/logo_90x45.png')); ?>" />

    <title>Dairy Management System </title>

    
    
    <link href="<?php echo e(asset("css/bootstrap-3.2.0.min.css")); ?>" rel="stylesheet" id="bootstrap-css">

    

    

    

    <link href="<?php echo e(asset('css\4.7.0-font-awesome.min.css')); ?>" rel="stylesheet" type="text/css">

    

    

    

    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

    

    

      

    <link rel="stylesheet" href="<?php echo asset('css/bootstrap-select.css'); ?>"> 

    

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

    

    <script src="<?php echo asset('js/bootstrap-select.js'); ?>"></script>

    



    <script src="<?php echo asset('js/addon/moment.min.js'); ?>"></script>

    <link href="<?php echo asset('css/addon/bootstrap-datetimepicker.min.css'); ?>" rel="stylesheet">

    <script src="<?php echo asset('js/addon/bootstrap-datetimepicker.min.js'); ?>"></script>



    <link href="<?php echo e(asset('css/addon/jquery.dataTables.min.css')); ?>" rel="stylesheet">

    <link href="<?php echo e(asset('css/buttons.dataTables.min.css')); ?>" rel="stylesheet">

    <script src="<?php echo e(asset('js/addon/jquery.dataTables.min.js')); ?>"></script>

    <script src="<?php echo e(asset('js/dataTables.buttons.min.js')); ?>"></script>

    <script src="<?php echo e(asset('js/buttons.print.min.js')); ?>"></script>

    <script src="<?php echo e(asset('js/pdfmake-0.1.36-pdfmake.min.js')); ?>"></script>

    <script src="<?php echo e(asset('js/pdfmake-0.1.36-vfs_fonts.js')); ?>"></script>

    <script src="<?php echo e(asset('js/buttons.html5.min.js')); ?>"></script>

    <script src="<?php echo e(asset('js/3.1.3-jszip.min.js')); ?>"></script>



    <script src="<?php echo asset('js/addon/datepicker/dateTimePickerCustom.js'); ?>"></script>



    <link rel="stylesheet" href="<?php echo e(asset('css/jquery-confirm.min.css')); ?>">

    <script src="<?php echo e(asset('js/jquery-confirm.min.js')); ?>"></script>



    <link href="<?php echo asset('css/style1.css'). $__ver; ?>" rel="stylesheet" type="text/css">

    <link href="<?php echo asset('css/style2.css'). $__ver; ?>" rel="stylesheet" type="text/css"> 

    

    <?php echo $__env->yieldContent('styles'); ?>



    <link rel="stylesheet" href="<?php echo e(asset("css/jquery-ui-1.12.1.css")); ?>">

    <script src="<?php echo e(asset('js/jquery-ui-1.12.1.js')); ?>"></script>



    <style>.navbar-default .navbar-brand {

        font-size: 22px;

        padding: 15px 15px;

    }</style>

</head>



<body style="margin-top: 18px;">



    <?php if(isset(Auth::user()->name)) $brand = Auth::user()->name; elseif(Session::get('loginUserTpe') == 'dairy') $brand =

        Session::get('dairyInfo')->dairyName; elseif(Session::get('loginUserType') == 'user') $brand = Session::get('loginUserInfo')->userName;

        else $brand = "DMS";

    ?>



    <div class="span-fixed response-alert" id="response-global-alert"></div>



    <div id="wrapper" class="clearfix">



        <div class="upper-info-line">

            <div class="fl strcmlcase" style="padding-left:10px">

                <?php echo e("Society Code: ".Session::get('dairyInfo')->society_code. " | Society Name: ". Session::get('dairyInfo')->dairyName

                ." | User: ". Session::get("colMan")->userName); ?>


            </div>

            <div class="fr" style="padding-right:50px">

                <a href="<?php echo e(url("contactUs")); ?>"> <span class="fa fa-life-ring fa-fw" style="margin-left: 2px;"></span> Contact Us</a>

            </div>

        </div>



        <div id="dropdown-noti" class="panel-collapse collapse">

            <span class="close notidropdown" data-toggle="collapse" data-target="#dropdown-noti">x</span>

            <div class="panel-body">

                <img src="<?php echo e(asset("images/loading.gif")); ?>" alt="Loading..." width="150">

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

                                <img src="<?php echo e(asset('images/logo_220x110.png')); ?>" class="brand-img" />

                                

                            </a>

                        </div>



                        <div class="notificationBellNav">

                            <li class="" id="notificationli" class="panel panel-default users">

                                <span class="bedge-number"></span>

                                <a data-toggle="collapse" href="#dropdown-noti" id="notificationBtn">

                                    <span class="fa fa-bell"></span>

                                    

                                </a>

                            </li>

                        </div>



                        <!-- Search -->

                        <a data-toggle="collapse" href="#menu-l" class="btn btn-default" id="menu-l-trigger">

                        

                        <div class="bar-container" onclick="openMenu(this)">

                            <div class="bar1"></div>

                            <div class="bar2"></div>

                            <div class="bar3"></div>

                        </div>

                    </a>



                        <!-- Search body -->

                        <div id="menu-l" class="panel-collapse collapse">

                            <div class="panel-body">

                                



                                <li>

                                    <a href="<?php echo e(url('dairy-settings')); ?>"><span class="fa fa-cog fa-fw" style="margin-left: 2px;"></span> Settings</a>

                                </li>

                                <li>

                                    <a href="<?php echo e(url("contactUs")); ?>"><span class="fa fa-life-ring fa-fw" style="margin-left: 2px;"></span> Contact Us</a>

                                </li>

                                <li>

                                    <a href="<?php echo e(url('my-logout')); ?>"><span class="fa fa-sign-out fa-fw" style="margin-left: 2px;"></span> Logout</a>

                                </li>        

                            </div>

                        </div>



                    </div>



                </div>



                <!-- Main Menu -->

                <div class="side-menu-container">

                    <ul class="nav navbar-nav">



                        <!-- Dropdown-->

                        <li class="panel panel-default users" id="dropdown">

                            <a data-toggle="collapse" href="#dropdown-users">

                            <span class="glyphicon glyphicon-user"></span>Users <span class="caret"></span>

                        </a>



                            <!-- Dropdown level 1 -->

                            <div id="dropdown-users" class="panel-collapse collapse">

                                <div class="panel-body">

                                    <ul class="nav navbar-nav">

                                        <li class="members"><a href="<?php echo e(url('memberList')); ?>">Members</a></li>

                                        <li class="suppliers"><a href="<?php echo e(url('supplierList')); ?>">Suppliers</a></li>

                                        <li class="customers"><a href="<?php echo e(url('customerList')); ?>">Customers</a></li>

                                    </ul>

                                </div>

                            </div>

                        </li>

                        

                        <li class="milkPlants">

                            <a href="<?php echo e(url('milkPlantList')); ?>">

                                <i class="fa fa-industry"></i> Milk Plants

                            </a>

                        </li>



                        <?php if(Session::get('colMan')->userName == "DAIRYADMIN"): ?>

                            <li class="colMans"><a href="<?php echo e(url('colMans')); ?>"><span class="glyphicon glyphicon-user"></span>Collection Manager</a></li>

                        <?php endif; ?>



                        

                        <!-- Dropdown-->
    <!--                  <li class="panel panel-default category" id="dropdown">

                            <a data-toggle="collapse" href="#dropdown-category">

                            <span class="fa fa-product-hunt"></span>Category <span class="caret"></span>

                        </a>



    

                            <!- Dropdown level 1 --

                            <div id="dropdown-category" class="panel-collapse collapse">

                                <div class="panel-body">

                                    <ul class="nav navbar-nav">

                                        <li class="ProductForm"><a href="<?php echo e(url('CategoryForm')); ?>">Add New</a></li>

                                        <li class="productList"><a href="<?php echo e(url('categoryList')); ?>">Categories</a></li>

                                    </ul>

                                </div>

                            </div>

                        </li> -->

                        <li class="panel panel-default product" id="dropdown">

                            <a data-toggle="collapse" href="#dropdown-product">

                            <span class="fa fa-product-hunt"></span>Product <span class="caret"></span>

                        </a>



    

                            <!-- Dropdown level 1 -->

                            <div id="dropdown-product" class="panel-collapse collapse">

                                <div class="panel-body">

                                    <ul class="nav navbar-nav">

                                        <li class="ProductForm"><a href="<?php echo e(url('ProductForm')); ?>">Add New</a></li>

                                        <li class="productList"><a href="<?php echo e(url('productList')); ?>">Products</a></li>

                                        <li class="productStock"><a href="<?php echo e(url('productSupply')); ?>">Purchase History</a></li>

                                    </ul>

                                </div>

                            </div>

                        </li>



                        <li class="rateCard"><a href="<?php echo e(url('fatSnfRateCardShow')); ?>"><span class="glyphicon glyphicon-send"></span> RateCard</a></li>



                        <li class="milkcollection"><a href="<?php echo e(url('DailyTransactionForm')); ?>"><span class="glyphicon glyphicon-cloud"></span>Milk Collection</a></li>

                        <li class="milkRequest"><a href="<?php echo e(url('milkRequest')); ?>"><span class="glyphicon fa fa-truck"></span>Milk Collection Request</a></li>

                        <li class="prodRequest"><a href="<?php echo e(url('prodRequest')); ?>"><span class="glyphicon fa fa-truck"></span>Product Delivery Request</a></li>



                        



                        <!-- Dropdown-->

                        <li class="panel panel-default sale" id="dropdown">

                            <a data-toggle="collapse" href="#dropdown-sale">

                            <span class="sale-icon">S</span> Sale<span class="caret"></span>

                        </a>



                            <!-- Dropdown level 1 -->

                            <div id="dropdown-sale" class="panel-collapse collapse">

                                <div class="panel-body">

                                    <ul class="nav navbar-nav">

                                        <li class="memberSale"><a href="memberSaleForm">Product Sale</a></li>

                                        <li class="plantSale"><a href="plantSaleForm">Plant sale</a></li>



                                        

                                    </ul>

                                </div>

                            </div>

                        </li>



                        <!-- Dropdown-->

                        <li class="panel panel-default lsale" id="dropdown">

                            <a data-toggle="collapse" href="#dropdown-lsale">

                            <span class="sale-icon">L</span> Local Sale<span class="caret"></span>

                        </a>



                            <!-- Dropdown level 1 -->

                            <div id="dropdown-lsale" class="panel-collapse collapse">

                                <div class="panel-body">

                                    <ul class="nav navbar-nav">

                                        <li class="localSale" title="Header" data-toggle="popover" data-trigger="hover" data-content="Some content">

                                            <a href="localSaleForm">Add Local Sale</a>

                                        </li>

                                        <li class="categoryList"><a href="<?php echo e(url('categoryList')); ?>">Categories</a></li>


                                    </ul>

                                </div>

                            </div>

                        </li>

                        <li class="expenses"><a href="expenseSetupList"><span class="glyphicon glyphicon-plane"></span> Expenses</a></li>



                        





                        

                        <li class="panel panel-default advance-credit" id="dropdown">

                            <a data-toggle="collapse" href="#dropdown-advance">

                                <span class="fa fa-credit-card-alt"></span>Advance/Credit <span class="caret"></span>

                            </a>



                            <!-- Dropdown level 1 -->

                            <div id="dropdown-advance" class="panel-collapse collapse">

                                <div class="panel-body">

                                    <ul class="nav navbar-nav">

                                        <li class="advanceForm"><a href="advanceForm">Advance</a></li>

                                        <li class="creditForm"><a href="creditForm">Credit</a></li>

                                    </ul>

                                </div>

                            </div>

                        </li>



                        <li class="panel panel-default report" id="dropdown">

                            <a data-toggle="collapse" href="#dropdown-report">

                                <span class="glyphicon glyphicon-stats"></span>Report <span class="caret"></span>

                            </a>



                            <!-- Dropdown level 1 -->

                            <div id="dropdown-report" class="panel-collapse collapse">

                                <div class="panel-body">

                                    <ul class="nav navbar-nav">

                                        <li class="sales"><a href="getReport?type=sales">Sales Report</a></li>

                                        <li class="memberList"><a href="getReport?type=memberList">Member List</a></li>

                                        <li class="rateChart"><a href="getReport?type=rateChart">Rate Chart</a></li>

                                        <li class="shiftSummary"><a href="getReport?type=shiftSummary">Shift Summary</a></li>

                                        <li class="memberPassbook"><a href="getReport?type=memberPassbook">Member Passbook</a></li>

                                        <li class="memStatement"><a href="getReport?type=memStatement">Member Account Statement</a></li>
                                        <li class="memStatementDetail"><a href="getReport?type=memStatementDetail">Member Account Statement Detail</a></li>

                                        <li class="custSalseReport"><a href="getReport?type=custSalseReport">Customer Account Statement</a></li>

                                        <li class="payemntRegister"><a href="getReport?type=payemntRegister">Payment Register</a></li>

                                        <li class="cmSubsidiary"><a href="getReport?type=cmSubsidiary">CM Subsidiary</a></li>

                                        <li class="profitLoss"><a href="getReport?type=profitLoss">Profit Loss Report</a></li>

                                    </ul>

                                </div>

                            </div>

                        </li>



                        <li class="dairyBal"><a href="dairyBal"><span class="glyphicon glyphicon-plane"></span> Dairy Balance</a></li>
                        




                          <li class="panel panel-default deleted" id="dropdown">
    <a data-toggle="collapse" href="#dropdown-deleted">
        <span class="fa fa-trash"></span> Deleted Transactions<span class="caret"></span>
    </a>

    <!-- Dropdown level 1 -->
   <div id="dropdown-deleted" class="panel-collapse collapse">
    <div class="panel-body">
        <ul class="nav navbar-nav">
            <li class="productsale_deleted">
                <a href="<?php echo e(url('ProductSale_deleted')); ?>">Deleted Product Sale</a>
            </li>
            <li class="dailyTransactionListAjax_delete">
                <a href="<?php echo e(url('dailyTransactionListAjax_delete')); ?>">Deleted Milk Collection Data</a>
            </li>
            <li class="localSaleForm_delete">
                <a href="<?php echo e(url('localSaleForm_delete')); ?>">Deleted Local Sale Form Data</a>
            </li>
        </ul>
    </div>
</div>

</li>


                    </ul>

                </div>

                <!-- /.navbar-collapse -->

            </nav>



        </div>





        <?php if(Session::has('msg')): ?>

            <?php if(Session::get('alert-class') == "alert-success"): ?>

            <div class="flash-alert alert <?php echo e(Session::get('alert-class')); ?>">

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

                        <?php echo e(Session::get('msg')); ?>


                    </div>

                </div>

            <?php else: ?>

                <div class="flash-alert alert <?php echo e(Session::get('alert-class')); ?>">

                    <span class="close" aria-label="close">X</span>

                    <div class="flash-msg">

                        <?php echo e(Session::get('msg')); ?>


                    </div>

                </div>

            <?php endif; ?>

        <?php elseif(isset($errors) && $errors->any()): ?>

            <div class="flash-alert alert alert-danger">

                <span class="close" aria-label="close">X</span>

                <div class="flash-msg">

                    <ul>

                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <li><?php echo e($error); ?></li>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </ul>

                </div>

            </div>

        <?php else: ?>

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

        <?php endif; ?>





        <div id="page-wrapper" class="clearfix">



            <div class="trialBox clearfix" id="trialBox">

                <span class="close">x</span>

        

                <div class="fl">

                    <div class="trial-text">

                        Trial

                    </div>

                </div>

                <div class="fl">

                    <a href="<?php echo e(url('renewPage')); ?>" class="btn btn-primary btn-sm">Subscribe</a>

                </div>

            </div>

                

            <?php echo $__env->yieldContent('content'); ?>

        </div>



    </div>



    <div class="loader"></div>



    <footer class="footer">

            

            <!-- Copyright -->

            <div class="footer-copyright text-center py-3">© <?php echo e(date("Y")); ?> powered by

                <a href="https://techpathway.com/" target="_blank"> techpathway.com</a>

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

                    <input placeholder="Opening Balance" value="<?php echo e(old('memberPersonalOpeningBalance')); ?>" class="memberPersonalOpeningBalance form-control"

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



    <?php echo $__env->yieldContent('scripts'); ?>



    <script>

        activepage = <?php if(!isset($activepage)): ?> "noclass" <?php else: ?> "<?php echo e($activepage); ?>" <?php endif; ?>;

        if(activepage=="members" || activepage=="suppliers" || activepage=="customers" || activepage == "milkPlants"){

            $("#dropdown-users").addClass("in");

        }

        if(activepage=="localSale" || activepage=="memberSale" || activepage=="plantSale" ){

            $("#dropdown-sale").addClass("in");

        }

        if(activepage=="creditForm" || activepage=="advanceForm" ){

            $("#dropdown-advance").addClass("in");

        }

        if(activepage=="ProductForm" || activepage=="productList" || activepage=="productStock" ){

            $("#dropdown-product").addClass("in");

        }

        if(activepage=="memberList" || activepage=="rateChart" || activepage=="shiftSummary" || activepage=="memberPassbook" 

            || activepage=="sales" || activepage == "payemntRegister" || activepage == "profitLoss"){

            $("#dropdown-report").addClass("in");

        }

        

        

        $(document).ready(function(){

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



            // $('.side-menu').animate({

            //     scrollTop: ($('.'+activepage).offset().top)

            // },500);



            $("."+activepage).addClass("active");



            $('[data-toggle="tooltip"]').tooltip();   



            $('.datepicker').datetimepicker({format: 'DD-MM-YYYY'});

            

            $('[data-toggle="popover"]').popover(); 



            $(".flash-alert .close").on("click", function(){

                $(this).closest('.flash-alert').fadeOut();

            })



            $(document).keyup(function(e) {

                if(e.key === "Escape") {

                    $(".wmodel:visible .close").trigger("click");

                }

            });



            $("#trialBox .close").on('click', function(){

                $("#trialBox").fadeOut();

            })



            $("#notificationli").on('click', function(){

                markasReadNotification();

            })



            _checkSubscription();



            setTimeout(function(){ $(".flash-alert").fadeOut("slow"); }, 4000);



            setTimeout(function(){checkNotification();}, 2000);



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





        function checkNotification(){

            $.ajax({

                type:"GET",

                url:'<?php echo e(url("dairy/checkNotification")); ?>',

                data: {},

                success:function(res){

                    if(res.error){

                        $("#dropdown-noti .panel-body").html("<div class='msg'>"+res.msg+"</div>");

                    }else{

                        count=0;

                        data = "<div class='all-notification'>";

                        res.data.forEach(noti => {

                            if(noti.opened){

                                unread = "";

                            }else{

                                unread = " unread";

                                count++;

                            }

                            data += "<div class='single-noti-"+noti.id+unread+"' data-notiid='"+noti.id+"'>"

                            +"<div class='close' onclick='removeNoti("+noti.id+")'><i class='fa fa-trash'></i></div><div class='content'>"+noti.notification+"</div>"

                            +"<div class='time'>"+noti.created_at+"</div></div>";

                        });

                        data += '</div>';

                        $("#dropdown-noti .panel-body").html(data);

                        if(count > 0){

                            $("#notificationli .bedge-number").html(count+'').show();

                        }

                    }

                }

            });

        }



        function removeNoti(id){

            $.ajax({

                type:"GET",

                url:'<?php echo e(url("dairy/deleteNotification")); ?>',

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



        <?php if(Session::get('dairyInfo')->firstTimeBalanceUpdated == "false"): ?>

            promptCurrentBal();

        <?php endif; ?>

        

        function promptCurrentBal(){



            jc = $.confirm({

                title: 'Update current dairy balance first.',

                content: '' +

                    '<form action="<?php echo e(url('updateCurrentDairyBal')); ?>" class="formName">' +

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

                            url:"<?php echo e(url('updateCurrentDairyBal')); ?>",

                            success:function(res){

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

                                // console.log(res); 

                            }

                        });



                    });



                }

            });

        }



        function _checkSubscription(){

            $.ajax({

                type:"GET",

                data: {},

                url:"<?php echo e(url('checkSubscription')); ?>",

                success:function(res){

                    if(res.error){

                        text = "";

                        url = "";

                        if(res.takeToExp){

                            url="<?php echo e(url('expiredPage')); ?>";

                            text = "Renew Now";

                        }

                        if(res.takeToLogin){

                            url="<?php echo e(url('dairy-login')); ?>";

                            text = "Login";

                        }

                        if(res.takeToDeactivatedDairy){

                            url="<?php echo e(url('dairyDeactivatedPage')); ?>";

                            text = "Proceed";

                        }



                        $.confirm({

                            title: res.msg,

                            content: '',

                            type: 'orange',

                            typeAnimated: true,

                            buttons: {

                                gotoexpired: {

                                    text: text,

                                    btnClass: 'btn-default',

                                    action: function(){

                                        window.location = url;

                                    }

                                }

                            }

                        });

                    }else{

                        if(res.trial){

                            showTrialBox(res);

                        }

                    }

                },

                error: function(res){



                }

            }).done(function(res){

                console.log(res);

            });

        }



        function markasReadNotification(){

            ids = [];

            i = 0

            $(".all-notification").find(".unread").each(function(){

                ids[i] = $(this).data('notiid');

                i++;

            });

            console.log(ids);



            if(ids.length < 1){

                return true;

            }



            $.ajax({

                type:"POST",

                url:'<?php echo e(url("markasReadNotification")); ?>',

                data: {

                    notiIds: ids

                },

                success:function(res){

                    console.log(res);

                    if(res.error){

                        console.log(res);

                    }else{

                        $(".all-notification .unread").removeClass("unread");

                    }

                }

            });

        }



        function showTrialBox(s){

            $("#trialBox").fadeIn('slow');

            $("#trialBox .trial-text").text("Trial Remaining: "+s.trialTime+" Day(s)");

        }



    </script>

</body>



</html>