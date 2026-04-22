<div class="navbar-header">

    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">

        <span class="sr-only">Toggle navigation</span>

        <span class="icon-bar"></span>

        <span class="icon-bar"></span>

        <span class="icon-bar"></span>

    </button>

        @if(isset(Auth::user()->name))
            <a class="navbar-brand" href="DairyAdminDashbord">{{{ Auth::user()->name }}}</a>
        @elseif(Session::get('loginUserType') == 'dairy')
            <a class="navbar-brand" href="DairyAdminDashbord">{{{ Session::get('loginUserInfo')->dairyPropritorName }}}</a> 
        @elseif(Session::get('loginUserType') == 'user')
            <a class="navbar-brand" href="DairyAdminDashbord">{{{ Session::get('loginUserInfo')->userName }}}</a>   
        @endif
        
</div>

<!-- /.navbar-header -->

<ul class="nav navbar-top-links navbar-right">

    <li class="dropdown">

        <a class="dropdown-toggle" data-toggle="dropdown" href="#">

            <i class="fa fa-envelope fa-fw"></i> <i class="fa fa-caret-down"></i>

        </a>

        <ul class="dropdown-menu dropdown-messages">

            <li>

                <a href="#">

                    <div>
                    
                    @if(isset(Auth::user()->name))
                        <strong>{{{ Auth::user()->name }}}</strong>
                    @elseif(Session::get('loginUserType') == 'dairy')
                        <strong>{{{ Session::get('loginUserInfo')->dairyPropritorName }}}</strong>  
                    @elseif(Session::get('loginUserType') == 'user')
                        <strong>{{{ Session::get('loginUserInfo')->userName }}}</strong>  
                    @endif
                    

                    <span class="pull-right text-muted">

                            <em>Yesterday</em>

                        </span>

                    </div>

                    <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>

                </a>

            </li>

            <li class="divider"></li>

            <li>

                <a class="text-center" href="#">

                    <strong>Read All Messages</strong>

                    <i class="fa fa-angle-right"></i>

                </a>

            </li>

        </ul>

        <!-- /.dropdown-messages -->

    </li>

    <!-- /.dropdown -->

    <li class="dropdown">

        <a class="" data-toggle="" href="#"><i class="fa fa-home fa-fw"></i>   </a>


<!--
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">

            <i class="fa fa-home fa-fw"></i> <i class="fa fa-caret-down"></i>

        </a>

         <ul class="dropdown-menu dropdown-tasks">

            <li>

                <a href="#">

                    <div>

                        <p>

                            <strong>Task 4</strong>

                            <span class="pull-right text-muted">50% Complete</span>

                        </p>

                        <div class="progress progress-striped active">

                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%">

                                <span class="sr-only">50% Complete (danger)</span>

                            </div>

                        </div>

                    </div>

                </a>

            </li>

            <li class="divider"></li>

            <li>

                <a class="text-center" href="#">

                    <strong>See All Tasks</strong>

                    <i class="fa fa-angle-right"></i>

                </a>

            </li>

        </ul> -->

        <!-- /.dropdown-tasks -->

    </li>

    <!-- /.dropdown -->

    <li class="dropdown">

        <a class="dropdown-toggle" data-toggle="dropdown" href="#">

            <i class="fa fa-bell fa-fw"></i> <i class="fa fa-caret-down"></i>

        </a>

        <ul class="dropdown-menu dropdown-alerts">

            <li>

                <a href="#">

                    <div>

                        <i class="fa fa-upload fa-fw"></i> Server Rebooted

                        <span class="pull-right text-muted small">4 minutes ago</span>

                    </div>

                </a>

            </li>

            <li class="divider"></li>

            <li>

                <a class="text-center" href="#">

                    <strong>See All Alerts</strong>

                    <i class="fa fa-angle-right"></i>

                </a>

            </li>

        </ul>

        <!-- /.dropdown-alerts -->

    </li>

    <!-- /.dropdown -->

    <li class="dropdown">

        <a class="dropdown-toggle" data-toggle="dropdown" href="#">

            <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>

        </a>

        <ul class="dropdown-menu dropdown-user">

            <li><a href="editDairyInfo"><i class="fa fa-user fa-fw"></i> User Profile</a>

            </li>

            <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>

            </li>

            <li class="divider"></li>

            <li><a href="my-logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>

            </li>

        </ul>

        <!-- /.dropdown-user -->

    </li>

    <!-- /.dropdown -->

</ul>

<!-- /.navbar-top-links -->