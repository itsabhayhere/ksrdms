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
                            <li><a href="#">Map</a></li>
                            <li class="active">Vector map</li>
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
                                    <h4>World</h4>
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Algeria</h4>
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap2" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Argentina</h4>
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap3" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Brazil</h4>
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap4" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap5" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Germany</h4>
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap6" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Greece</h4>
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap7" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Russia</h4>
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap10" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Tunasia</h4>
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap11" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Europe</h4>
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap12" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>USA</h4>
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap13" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Turkey</h4>
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap14" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Inactive Regions</h4>
                                </div>
                                <div class="Vector-map-js">
                                    <div id="vmap15" class="vmap"></div>
                                </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        <!-- /# column -->



                    </div>
                    <!-- /# row -->


            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->


<?php
// include 'footer.blade.php';
?>
@include('superAdmin.footer')


  <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/jquery.vmap.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/jquery.vmap.min.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/jquery.vmap.sampledata.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.world.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.algeria.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.argentina.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.brazil.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.france.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.germany.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.greece.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.iran.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.iraq.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.russia.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.tunisia.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.europe.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.usa.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/country/jquery.vmap.turkey.js') !!}"></script>
    <!-- scripit init-->
    <script src="{!! asset('public/superAdmin/assets/js/lib/vector-map/vector.init.js') !!}"></script>


