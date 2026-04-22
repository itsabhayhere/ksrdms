<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="YRU2M8Ago7VVVYDOXj1SQ6IdiTpj4aKH8x1ydR85gs4" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <link rel="shortcut icon" type="image/png" href="favicon.png" />
    <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('/favicon.png')); ?>" />

    <title><?php echo e(config('app.name', 'DMS')); ?></title>

    <link href="<?php echo e(asset('css\4.7.0-font-awesome.min.css')); ?>" rel="stylesheet" type="text/css">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.2.1.min.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <link href="<?php echo e(asset('css/app.css')); ?>?v=123" rel="stylesheet">
    <link href="<?php echo e(asset('css/style1.css')); ?>?v=123" rel="stylesheet">
    

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
                    

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
                    <img src="<?php echo e(asset("images/logo_220x110.png")); ?>" alt="Dairy Management System" class="img img-responsive">
                    </a>
                </div>

                <?php if(session()->get('loginUserType') != "dairy"): ?>
                    <?php if($activepage == "login"){
                            $lurl = "#login-form";
                        }else{
                            $lurl = url('dairy-login');
                        }
                    ?>
                    <div class="fr">
                        <ul class="nav navbar-right">
                            <li class="fl"><a href="<?php echo e($lurl); ?>"> <i class="fa fa-sign-in"></i> Login</a></li>
                            <li class="fl"><a href="<?php echo e(url('buy')); ?>"> <i class="fa fa-plus"></i> Register</a></li>
                            <li class="fl"><a href="<?php echo e(url('contactUs')); ?>"> <i class="fa fa-life-ring"></i> Contact</a></li>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    

                    <!-- Right Side Of Navbar -->
                    <ul class="nav  navbar-right">
                        <!-- Authentication Links -->
                        <?php if(auth()->guard()->guest()): ?>
                            
                        <?php else: ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                <?php echo e(Auth::user()->name); ?> <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu">
                                <li>
                                    <a href="my-home">
                                            Dashbord
                                        </a>
                                    <a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                        <?php echo e(csrf_field()); ?>

                                    </form>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <div class="loader"></div>


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
<?php elseif($errors->any()): ?>
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

<?php echo $__env->make('layouts.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

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