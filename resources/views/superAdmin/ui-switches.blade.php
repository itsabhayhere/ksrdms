
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
                            <li class="active">Switches</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">
            <div class="animated fadeIn">



                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="mr-2 fa fa-check-square-o"></i>
                            <strong class="card-title">3d Switch</strong>
                        </div>
                        <div class="card-body">
                            <label class="switch switch-3d switch-primary mr-3"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                            <label class="switch switch-3d switch-secondary mr-3"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                            <label class="switch switch-3d switch-success mr-3"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                            <label class="switch switch-3d switch-warning mr-3"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                            <label class="switch switch-3d switch-info mr-3"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                            <label class="switch switch-3d switch-danger mr-3"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                        </div>
                    </div>
                </div><!--/.col-->

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Switch Default</strong>
                        </div>
                        <div class="card-body">
                            <label class="switch switch-default switch-primary mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                            <label class="switch switch-default switch-secondary mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                            <label class="switch switch-default switch-success mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                            <label class="switch switch-default switch-warning mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                            <label class="switch switch-default switch-info mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                            <label class="switch switch-default switch-danger mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                        </div>
                    </div>
                </div><!--/.col-->


                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Switch Default - Pills</strong>
                        </div>
                        <div class="card-body">
                            <label class="switch switch-default switch-pill switch-primary mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-pill switch-secondary mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-pill switch-success mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-pill switch-warning mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-pill switch-info mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-pill switch-danger mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                        </div>
                    </div>
                </div><!--/.col-->


                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Switch Outline</strong>
                        </div>
                        <div class="card-body">
                            <label class="switch switch-default switch-primary-outline mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-secondary-outline mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-success-outline mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-warning-outline mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-info-outline mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-danger-outline mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                        </div>
                    </div>
                </div><!--/.col-->


                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Switch Outline - Pills</strong>
                        </div>
                        <div class="card-body">
                            <label class="switch switch-default switch-primary-outline switch-pill mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-secondary-outline switch-pill mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-success-outline switch-pill mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-warning-outline switch-pill mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-info-outline switch-pill mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-danger-outline switch-pill mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                        </div>
                    </div>
                </div><!--/.col-->




                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Switch Outline Alternative</strong>
                        </div>
                        <div class="card-body">
                            <label class="switch switch-default switch-primary-outline-alt mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-secondary-outline-alt mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-success-outline-alt mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-warning-outline-alt mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-info-outline-alt mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-danger-outline-alt mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                        </div>
                    </div>
                </div><!--/.col-->


                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Switch Outline Alternative - Pills</strong>
                        </div>
                        <div class="card-body">
                            <label class="switch switch-default switch-primary-outline-alt switch-pill mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-secondary-outline-alt switch-pill mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-success-outline-alt switch-pill mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-warning-outline-alt switch-pill mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-info-outline-alt switch-pill mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-default switch-danger-outline-alt switch-pill mr-2"><input type="checkbox" class="switch-input" checked="true"> <span class="switch-label"></span> <span class="switch-handle"></span></label>
                        </div>
                    </div>
                </div><!--/.col-->


                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Switch with Text</strong>
                        </div>
                        <div class="card-body">
                            <label class="switch switch-text switch-primary"><input type="checkbox" class="switch-input" checked="true"> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-text switch-secondary"><input type="checkbox" class="switch-input" checked="true"> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-text switch-success"><input type="checkbox" class="switch-input" checked="true"> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-text switch-warning"><input type="checkbox" class="switch-input" checked="true"> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-text switch-info"><input type="checkbox" class="switch-input" checked="true"> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-text switch-danger"><input type="checkbox" class="switch-input" checked="true"> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>

                        </div>
                    </div>
                </div><!--/.col-->


                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Switch with Text - Pills</strong>
                        </div>
                        <div class="card-body">

                            <label class="switch switch-text switch-primary switch-pill"><input type="checkbox" class="switch-input" checked="true"> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-text switch-secondary switch-pill"><input type="checkbox" class="switch-input" checked="true"> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-text switch-success switch-pill"><input type="checkbox" class="switch-input" checked="true"> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-text switch-warning switch-pill"><input type="checkbox" class="switch-input" checked="true"> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-text switch-info switch-pill"><input type="checkbox" class="switch-input" checked="true"> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>

                            <label class="switch switch-text switch-danger switch-pill"><input type="checkbox" class="switch-input" checked="true"> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>

                        </div>
                    </div>
                </div><!--/.col-->


                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title" v-if="headerText">Sizes</strong>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover table-striped table-align-middle mb-0">
                                <thead>
                                    <th>Size</th>
                                    <th>Example</th>
                                    <th>CSS Class</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            Large
                                        </td>
                                        <td>
                                            <basix-switch type="3d" variant="primary" size="lg" :checked="true"/>
                                        </td>
                                        <td>
                                            Add following code <code>size="lg"</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Default
                                        </td>
                                        <td>
                                            <basix-switch type="3d" variant="primary" :checked="true"/>
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Small
                                        </td>
                                        <td>
                                            <basix-switch type="3d" variant="primary" size="sm" :checked="true"/>
                                        </td>
                                        <td>
                                            Add following code <code>size="sm"</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Extra small
                                        </td>
                                        <td>
                                            <basix-switch type="3d" variant="primary" size="xs" :checked="true"/>
                                        </td>
                                        <td>
                                            Add following code <code>size="xs"</code>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!--/.col-->



            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->

<?php
// include 'footer.blade.php';
?>
@include('superAdmin.footer')