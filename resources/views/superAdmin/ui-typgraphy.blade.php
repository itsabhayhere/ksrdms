

<?php
// include '/header.blade.php';
// include '/sidebar.blade.php' ;
?>
@include('superAdmin.header')
@include('superAdmin.sidebar')    

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
                            <li class="active">Typography</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">
            <div class="animated fadeIn">

                <div class="ui-typography">
                    <div class="row">
                        <div class="col-md-12">


                            <div class="card">
                                <div class="card-header">
                                    <strong class="card-title" v-if="headerText">Typography</strong>
                                </div>

                                <div class="card-body">

                              <div class="typo-headers">
                                <h1 class="pb-2 display-4">Very Important Information H1</h1>
                                <h2 class="pb-2 display-5">Sections & Modal Names H2</h2>
                                <h3 class="pb-2 display-5">Articles & Block Headings H3</h3>
                                <h4 class="pb-2 display-5">Random Tiny Heading H4</h4>
                                <h5 class="pb-2 display-5">Random Tiny Heading H5</h5>
                                <h6 class="pb-4 display-5">Random Tiny Heading H6</h6>
                            </div>
                            <div class="typo-articles">
                                <p>
                                  The unique stripes of zebras make them one of the animals most familiar to people. They occur in a variety of habitats, such as grasslands, savannas, <span
                                  class="bg-flat-color-1 text-light">woodlands</span>, thorny scrublands, <span
                                  class="clickable-text">mountains</span>
                                  , and coastal hills. However, various anthropogenic factors have had a severe impact on zebra populations, in particular hunting for skins and habitat destruction. Grévy's zebra and the mountain <mark>highlighted text</mark> zebra are endangered.</p>
                                  <blockquote class="blockquote mt-3 text-right">
                                      <p>
                                      Blockquotes. However, various anthropogenic factors have had a severe impact on zebra populations, in particular hunting for skins. </p>
                                      <footer class="blockquote-footer">Jefferey Lebowski</footer>
                                  </blockquote>
                                  <p>
                                      lthough zebra species may have overlapping ranges, they do not interbreed. In captivity, plains zebras have been crossed with mountain zebras. The hybrid foals <span
                                      class="bg-flat-color-1 text-light">selected text</span> lacked a dewlap and resembled their
                                  </p>
                              </div>
                              <div class="vue-lists">
                                <h2 class="mb-4">Lists</h2>
                                <div class="row">
                                  <div class="col-md-6">
                                    <h3>Unordered</h3>
                                    <ul >
                                      <li>
                                        A wide variety of hypotheses have been proposed to account for the evolution of the striking stripes of zebras.
                                    </li>
                                    <li>The more traditional of these (1 and 2, below) relate to camouflage.</li>
                                    <li>The vertical striping may help the zebra hide in the grass by disrupting its outline.</li>
                                    <li>
                                        In addition, even at moderate distances, the striking striping merges to an apparent grey.
                                        <ul class="vue-list-inner">
                                          <li>However, the camouflage has been contested with arguments that most of a zebra's predator.</li>
                                          <li>Such as lions and hyenas cannot see well at a distance.</li>
                                          <li>More likely to have smelled or heard a zebra.</li>
                                      </ul>
                                  </li>
                                  <li>Before seeing it from a distance, especially at night.</li>
                              </ul>
                          </div>
                          <div class="col-md-6 text-left">
                            <div>
                              <h3>Ordered</h3>
                              <ol class="vue-ordered">
                                <li>
                                  A wide variety of hypotheses have been proposed to account for the evolution of the striking stripes of zebras.
                              </li>
                              <li>The more traditional of these (1 and 2, below) relate to camouflage.</li>
                              <li>The vertical striping may help the zebra hide in the grass by disrupting its outline.</li>
                              <li>
                                  In addition, even at moderate distances, the striking striping merges to an apparent grey.
                                  <ul class="vue-list-inner">
                                    <li>However, the camouflage has been contested with arguments that most of a zebra's predator.
                                    </li>
                                    <li>Such as lions and hyenas cannot see well at a distance.</li>
                                    <li>More likely to have smelled or heard a zebra.</li>
                                </ul>
                            </li>
                            <li>Before seeing it from a distance, especially at night.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="vue-misc">
            <h2 class="display-5 my-3">Misc</h2>
            <div class="row">
              <div class="col-md-6">
                <h3>Address</h3>
                <address class="mt-3">
                  <strong>SJØNNA</strong><br>
                  Nezalezhnasti Ave, 13 - 28A<br>
                  Minsk, Belarus, 220141<br>
                  +375 29 319-53-98<br>
                  <br>
                  <b>Vasili Savitski</b><br>
                  <a href="mailto">hello@examplemail.com</a>

              </address>
          </div>
          <div class="col-md-6">
            <h3 class="mb-3">Well</h3>
            <div class="jumbotron">
              Zebras have excellent eyesight. It is believed that they can see in color. Like most ungulates, the zebra's eyes are on the sides of its head, giving it a wide field of view.
          </div>
      </div>
  </div>
</div>

                    </div>
                </div>


</div>
</div>
</div>



            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->


<?php
// include 'footer.blade.php';
?>
@include('superAdmin.footer')