
<?php
// include '/header.blade.php';
// include '/sidebar.blade.php' ;
?>
@include('superAdmin.header')
@include('superAdmin.sidebar')

        

    <!-- Left Panel -->

    <!-- Right Panel -->

    <div id="right-panel" class="right-panel">

        <!-- Header-->
        <header id="header" class="header">

            <div class="header-menu">

                <div class="col-sm-7">
                    <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
                    <div class="header-left">
                        <button class="search-trigger"><i class="fa fa-search"></i></button>
                        <div class="form-inline">
                            <form class="search-form">
                                <input class="form-control mr-sm-2" type="text" placeholder="Search ..." aria-label="Search">
                                <button class="search-close" type="submit"><i class="fa fa-close"></i></button>
                            </form>
                        </div>

                        <div class="dropdown for-notification">
                          <button class="btn btn-secondary dropdown-toggle" type="button" id="notification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bell"></i>
                            <span class="count bg-danger">5</span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="notification">
                            <p class="red">You have 3 Notification</p>
                            <a class="dropdown-item media bg-flat-color-1" href="#">
                                <i class="fa fa-check"></i>
                                <p>Server #1 overloaded.</p>
                            </a>
                            <a class="dropdown-item media bg-flat-color-4" href="#">
                                <i class="fa fa-info"></i>
                                <p>Server #2 overloaded.</p>
                            </a>
                            <a class="dropdown-item media bg-flat-color-5" href="#">
                                <i class="fa fa-warning"></i>
                                <p>Server #3 overloaded.</p>
                            </a>
                          </div>
                        </div>

                        <div class="dropdown for-message">
                          <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="message"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ti-email"></i>
                            <span class="count bg-primary">9</span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="message">
                            <p class="red">You have 4 Mails</p>
                            <a class="dropdown-item media bg-flat-color-1" href="#">
                                <span class="photo media-left"><img alt="avatar" src="images/avatar/1.jpg"></span>
                                <span class="message media-body">
                                    <span class="name float-left">Jonathan Smith</span>
                                    <span class="time float-right">Just now</span>
                                        <p>Hello, this is an example msg</p>
                                </span>
                            </a>
                            <a class="dropdown-item media bg-flat-color-4" href="#">
                                <span class="photo media-left"><img alt="avatar" src="images/avatar/2.jpg"></span>
                                <span class="message media-body">
                                    <span class="name float-left">Jack Sanders</span>
                                    <span class="time float-right">5 minutes ago</span>
                                        <p>Lorem ipsum dolor sit amet, consectetur</p>
                                </span>
                            </a>
                            <a class="dropdown-item media bg-flat-color-5" href="#">
                                <span class="photo media-left"><img alt="avatar" src="images/avatar/3.jpg"></span>
                                <span class="message media-body">
                                    <span class="name float-left">Cheryl Wheeler</span>
                                    <span class="time float-right">10 minutes ago</span>
                                        <p>Hello, this is an example msg</p>
                                </span>
                            </a>
                            <a class="dropdown-item media bg-flat-color-3" href="#">
                                <span class="photo media-left"><img alt="avatar" src="images/avatar/4.jpg"></span>
                                <span class="message media-body">
                                    <span class="name float-left">Rachel Santos</span>
                                    <span class="time float-right">15 minutes ago</span>
                                        <p>Lorem ipsum dolor sit amet, consectetur</p>
                                </span>
                            </a>
                          </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-5">
                    <div class="user-area dropdown float-right">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="images/admin.jpg" alt="User Avatar">
                        </a>

                        <div class="user-menu dropdown-menu">
                                <a class="nav-link" href="#"><i class="fa fa- user"></i>My Profile</a>

                                <a class="nav-link" href="#"><i class="fa fa- user"></i>Notifications <span class="count">13</span></a>

                                <a class="nav-link" href="#"><i class="fa fa -cog"></i>Settings</a>

                                <a class="nav-link" href="#"><i class="fa fa-power -off"></i>Logout</a>
                        </div>
                    </div>

                    <div class="language-select dropdown" id="language-select">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown"  id="language" aria-haspopup="true" aria-expanded="true">
                            <i class="flag-icon flag-icon-us"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="language" >
                            <div class="dropdown-item">
                                <span class="flag-icon flag-icon-fr"></span>
                            </div>
                            <div class="dropdown-item">
                                <i class="flag-icon flag-icon-es"></i>
                            </div>
                            <div class="dropdown-item">
                                <i class="flag-icon flag-icon-us"></i>
                            </div>
                            <div class="dropdown-item">
                                <i class="flag-icon flag-icon-it"></i>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </header><!-- /header -->
        <!-- Header-->

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Dashboard</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">Dashboard</a></li>
                            <li><a href="#">Charts</a></li>
                            <li class="active">Peity Chart</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">






                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Pie Chart</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <span class="pie" data-peity='{ "fill": ["#13dafe", "#f2f2f2"]}'>1/5</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="pie" data-peity='{ "fill": ["#6164C1", "#f2f2f2"]}'>226/360</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="pie" data-peity='{ "fill": ["#F96262", "#f2f2f2"]}'>0.52/1.561</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="pie" data-peity='{ "fill": ["#99D683", "#f2f2f2"]}'>1,4</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="pie" data-peity='{ "fill": ["#FFCA4A", "#f2f2f2"]}'>226,134</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="pie" data-peity='{ "fill": ["#4C5667", "#f2f2f2"]}'>0.52,1.041</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /# card -->
                    </div>
                    <!-- /# column -->

                    <!-- /# row -->

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Donut Charts</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <span class="donut" data-peity='{ "fill": ["#13DAFE", "#f2f2f2"]}'>1/5</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="donut" data-peity='{ "fill": ["#6164C1", "#f2f2f2"]}'>226/360</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="donut" data-peity='{ "fill": ["#F96262", "#f2f2f2"]}'>0.52/1.561</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="donut" data-peity='{ "fill": ["#99D683", "#f2f2f2"]}'>1,4</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="donut" data-peity='{ "fill": ["#FFCA4A", "#f2f2f2"]}'>226,134</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="donut" data-peity='{ "fill": ["#4C5667", "#f2f2f2"]}'>0.52,1.041</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /# card -->
                    </div>
                    <!-- /# column -->

                    <!-- /# row -->

                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Line Charts</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <span class="peity-line" data-width="100%">6,2,8,4,3,8,1,3,6,5,9,2,8,1,4,8,9,8,2,1</span>
                                        </div>
                                        <div class="col-lg-4">
                                            <span class="peity-line" data-width="100%">6,2,8,4,-3,8,1,-3,6,-5,9,2,-8,1,4,8,9,8,2,1</span>
                                        </div>
                                        <div class="col-lg-4">
                                            <span class="peity-line" data-width="100%">6,2,8,4,3,8,1,3,6,5,9,2,8,1,4,8,9,8,2,1</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                    <!-- /# row -->

                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Bar Charts</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <span class="bar" data-peity='{ "fill": ["#13DAFE", "#6164C1"]}'>6,2,8,4,3,8,1,3,6,5,9,2,8,1,4,8,9,8,2,1</span>
                                        </div>
                                        <div class="col-lg-4">
                                            <span class="bar" data-peity='{ "fill": ["#F96262", "#f2f2f2"]}'>6,2,8,4,-3,8,1,-3,6,-5,9,2,-8,1,4,8,9,8,2,1</span>
                                        </div>
                                        <div class="col-lg-4">
                                            <span class="bar" data-peity='{ "fill": ["#99D683", "#4C5667"]}'>6,2,8,4,3,8,1,3,6,5,9,2,8,1,4,8,9,8,2,1</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                    <!-- /# row -->

                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Data attributes</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <p class="data-attributes">
                                                <span data-peity='{ "fill": ["#13DAFE", "#eeeeee"],    "innerRadius": 10, "radius": 40 }'>1/7</span>
                                            </p>
                                        </div>
                                        <div class="col-lg-2">
                                            <p class="data-attributes">
                                                <span data-peity='{ "fill": ["#6164C1", "#eeeeee"], "innerRadius": 14, "radius": 36 }'>2/7</span>
                                            </p>
                                        </div>
                                        <div class="col-lg-2">
                                            <p class="data-attributes">
                                                <span data-peity='{ "fill": ["#F96262", "#eeeeee"], "innerRadius": 16, "radius": 32 }'>3/7</span>
                                            </p>
                                        </div>
                                        <div class="col-lg-2">
                                            <p class="data-attributes">
                                                <span data-peity='{ "fill": ["#99D683", "#eeeeee"],  "innerRadius": 18, "radius": 28 }'>4/7</span>
                                            </p>
                                        </div>
                                        <div class="col-lg-2">
                                            <p class="data-attributes">
                                                <span data-peity='{ "fill": ["#FFCA4A", "#eeeeee"],   "innerRadius": 20, "radius": 24 }'>5/7</span>
                                            </p>
                                        </div>
                                        <div class="col-lg-2">
                                            <p class="data-attributes">
                                                <span data-peity='{ "fill": ["indigo", "#eeeeee"], "innerRadius": 18, "radius": 20 }'>6/7</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                    <!-- /# row -->

                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Setting Colours Dynamically</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <span class="bar-colours-1">5,3,9,6,5,9,7,3,5,2</span>
                                        </div>
                                        <div class="col-lg-3">
                                            <span class="bar-colours-2">5,3,2,-1,-3,-2,2,3,5,2</span>
                                        </div>
                                        <div class="col-lg-3">
                                            <span class="bar-colours-3">0,-3,-6,-4,-5,-4,-7,-3,-5,-2</span>
                                        </div>
                                        <div class="col-lg-3">
                                            <span class="pie-colours-2">5,3,9,6,5</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                    <!-- /# row -->
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Updating Charts</h4>
                                </div>
                                <div class="card-body">
                                    <span class="updating-chart">5,3,9,6,5,9,7,3,5,2,5,3,9,6,5,9,7,3,5,2</span>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->







                </div> <!-- .row -->
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->


<?php
// include 'footer.blade.php';
?>
@include('superAdmin.footer')
<script src="{!! asset('public/superAdmin/assets/js/lib/peitychart/jquery.peity.min.js') !!}"></script>
<script src="{!! asset('public/superAdmin/assets/js/lib/peitychart/peitychart.init.js') !!}"></script>
