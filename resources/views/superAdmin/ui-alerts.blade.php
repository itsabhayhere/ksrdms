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
                            <li><a href="#">UI Elements</a></li>
                            <li class="active">Alerts</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">
            <div class="animated fadeIn">


                    <div class="alerts">
                        <div class="row">
                            <div class="col-md-6">

                                <div class="card">
                                    <div class="card-header">
                                        <strong class="card-title">Alerts</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-primary" role="alert">
                                            This is a primary alert—check it out!
                                        </div>
                                        <div class="alert alert-secondary" role="alert">
                                            This is a secondary alert—check it out!
                                        </div>
                                        <div class="alert alert-success" role="alert">
                                            This is a success alert—check it out!
                                        </div>
                                        <div class="alert alert-danger" role="alert">
                                            This is a danger alert—check it out!
                                        </div>
                                        <div class="alert alert-warning" role="alert">
                                            This is a warning alert—check it out!
                                        </div>
                                        <div class="alert alert-info" role="alert">
                                            This is a info alert—check it out!
                                        </div>
                                        <div class="alert alert-light" role="alert">
                                            This is a light alert—check it out!
                                        </div>
                                        <div class="alert alert-dark" role="alert">
                                            This is a dark alert—check it out!
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <strong class="card-title">Link Color Alerts</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-primary" role="alert">
                                            This is a primary alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
                                        </div>
                                        <div class="alert alert-secondary" role="alert">
                                            This is a secondary alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
                                        </div>
                                        <div class="alert alert-success" role="alert">
                                            This is a success alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
                                        </div>
                                        <div class="alert alert-danger" role="alert">
                                            This is a danger alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
                                        </div>
                                        <div class="alert alert-warning" role="alert">
                                            This is a warning alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
                                        </div>
                                        <div class="alert alert-info" role="alert">
                                            This is a info alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
                                        </div>
                                        <div class="alert alert-light" role="alert">
                                            This is a light alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
                                        </div>
                                        <div class="alert alert-dark" role="alert">
                                            This is a dark alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
                                        </div>
                                    </div>
                                </div>


                            </div>


                            <div class="col-md-6">

                                <div class="card">
                                    <div class="card-header">
                                        <strong class="card-title">Dismissing Alerts</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="sufee-alert alert with-close alert-primary alert-dismissible fade show">
                                            <span class="badge badge-pill badge-primary">Success</span>
                                                You successfully read this important alert.
                                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="sufee-alert alert with-close alert-secondary alert-dismissible fade show">
                                            <span class="badge badge-pill badge-secondary">Success</span>
                                                You successfully read this important alert.
                                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="sufee-alert alert with-close alert-success alert-dismissible fade show">
                                            <span class="badge badge-pill badge-success">Success</span>
                                                You successfully read this important alert.
                                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                            <span class="badge badge-pill badge-danger">Success</span>
                                                You successfully read this important alert.
                                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="sufee-alert alert with-close alert-warning alert-dismissible fade show">
                                            <span class="badge badge-pill badge-warning">Success</span>
                                                You successfully read this important alert.
                                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="sufee-alert alert with-close alert-info alert-dismissible fade show">
                                            <span class="badge badge-pill badge-info">Success</span>
                                                You successfully read this important alert.
                                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="sufee-alert alert with-close alert-light alert-dismissible fade show">
                                            <span class="badge badge-pill badge-light">Success</span>
                                                You successfully read this important alert.
                                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="sufee-alert alert with-close alert-dark alert-dismissible fade show">
                                            <span class="badge badge-pill badge-dark">Success</span>
                                                You successfully read this important alert.
                                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <strong class="card-title">Contents</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-success" role="alert">
                                            <h4 class="alert-heading">Well done!</h4>
                                            <p>Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p>
                                            <hr>
                                            <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and tidy.</p>
                                        </div>

                                        <div class="alert alert-warning" role="alert">
                                            <h4 class="alert-heading">Well done!</h4>
                                            <p>Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p>
                                            <hr>
                                            <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and tidy.</p>
                                        </div>

                                        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Well done!</h4>
                                            <p>Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p>
                                            <hr>
                                            <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and tidy.</p>
                                        </div>
                                    </div>
                                </div>



                            </div>
                        </div>
                    </div> <!-- .alerts -->


            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->

<?php
// include 'footer.blade.php';
?>
@include('superAdmin.footer')