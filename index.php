<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CTIPM Scanner Calendar</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/agency.css" rel="stylesheet">
    <link href="css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap-colorselector.css" rel="stylesheet" type="text/css">

    <!-- Custom Fonts -->
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->


    <link rel='stylesheet' href='css/fullcalendar.min.css' />
    <link rel='stylesheet' href='css/clock.css' />
    <link rel='stylesheet' href='css/circle.css' />


<?php
  session_start();

  if (isset($_SESSION['project_name'])) {
     echo('<script type="text/javascript"> project_name = "'.$_SESSION['project_name'].'"; </script>'."\n");
  }

  include("code/php/AC.php");
  $user_name = check_logged(); /// function checks if visitor is logged.
  $admin = false;
  $adminuser = false;

  if ($user_name == "") {
    // user is not logged in

  } else {
    echo('<script type="text/javascript"> user_name = "'.$user_name.'"; </script>'."\n");
    // print out all the permissions
    $permissions = list_roles($user_name);
    //print_r($permissions);
    $p = "<script type=\"text/javascript\"> permissions = [";
    foreach($permissions as $perm) {
      $p = $p."\"".$perm."\",";
    }
    echo ($p."]; </script>\n");
    if (check_role( "admin" )) {
       $admin = true;
    }
    if ($user_name == "admin") {
       $adminuser = true;
    }
    $can_qc = false;
    if (check_permission( "can-qc" )) {
       $can_qc = true;
    }
    echo('<script type="text/javascript"> admin = '.($admin?"true":"false").'; can_qc = '.($can_qc?"true":"false").'; </script>');

    // get the user information
    $us = list_user_contacts();
    echo('<script type="text/javascript"> contacts = {');
    foreach($us as $u) {
       echo(" \"".$u["name"]."\": { \"email\": \"".$u["email"]."\", \"fullname\": \"".$u["fullname"]."\" },");
    }
    echo('}; </script>');
  }
?>


</head>


<body id="page-top" class="index">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top">
                  <div class="hero-circle pull-right" style="height: 60px; width: 60px; top: 0px; right: 5px; -ms-transform: scale(1,1); -webkit-transform: scale(1,1); transform: scale(1,1);">
                    <div class="hero-face">
                      <div id="hour" class="hero-hour"></div>
                      <div id="minute" class="hero-minute"></div>
                      <div id="second" class="hero-second"></div>
                    </div>
                  </div>
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand page-scroll" href="#page-top" title="Center for Translational Imaging and Precision Medicine">CTIPM Calendar
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#calendar">Order</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#projects">Projects</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#about">About</a>
                    </li>
<!--                     <li>
                        <a class="page-scroll" href="#contact">Contact</a>
                    </li> -->
<?php if ($user_name == "") : ?>
                    <li>
                        <a id="login-button" href="/User/login.php">Login</a>
                    </li>
<?php else: ?>
                    <li class="dropdown">    
                        <a id="logout" class="dropdown-toggle" data-toggle="dropdown" href="#" title="You are logged in as the <?php echo ($user_name); ?> user."><?php echo ($user_name); ?></a>
                        <ul class="dropdown-menu" role="menu" style="background-color: #555;">
                           <li class="divider"></li>
                           <li><a href="/code/php/logout.php">Logout</a></li>
<?php if ($admin) : ?>
                           <li><a id="admin-report" data-toggle="modal">Admin Report</a></li>
                           <li><a id="admin-report-month-overview" data-toggle="modal">Admin Report By Month</a></li>
<?php endif; ?>
<?php if ($adminuser) : ?>
                           <li class="divider"></li>
                           <li><a href="/User/admin.php">user administration</a></li>
                           <li><a id="startChangeProject" data-toggle="modal" href="#changeProject">project administration</a></li>
<?php endif; ?>
                        </ul>
                    </li>
<?php endif; ?>
                    <li>
                        <a class="page-scroll" href="#">&nbsp;</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

    <!-- Header -->
    <header>

        <div class="container">
            <div class="intro-text">
                <!-- <div class="intro-lead-in">CTIPM Calendar</div>  -->
                <div class="intro-heading">Center for Translational Imaging and Precision Medicine</div>
                <!-- <a href="#calendar" class="page-scroll btn btn-xl"><span class="fa fa-calendar"></span> Order a scan</a> -->
                <div id="history" style="position: relative;"></div>
            </div>
         </div>
    </header>

    <section id="dashboard">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading" id="dashboard">Dashboard</h2>
                    <h3 class="section-subheading text-muted">Scanner News.</h3>
                </div>
            </div>          
            <div class="row-fluid">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="panel panel-default">
                       <div class="panel-heading">Latest News</div>
                       <div class="panel-body">
                         <ul class="list-group">
                            <li class="list-group-item list-group-item-info">Report page shows now scans performed.</li>
                            <li class="list-group-item list-group-item-success">Scanner is operational.</li>
                            <li class="list-group-item list-group-item-default">Experiment in <a href="/experiment/index.php">event visualization</a>.</li>
                         </ul>
                       </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="panel panel-default">
                       <div class="panel-heading">Plan for Today</div>
                       <div class="panel-body">
                         <ul class="list-group" id="today-list">
                         </ul>
                       </div>
                    </div>                    
                </div>
            </div>
         </div>
    </section>


    <!-- Portfolio Grid Section -->
    <section id="calendar-top" class="bg-light-gray">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading" id="calendar">Scan Times</h2>
                    <h3 class="section-subheading text-muted"><span class="small" id="timeSinceLastUpdate"></span></h3>
                </div>
            </div>          
            <!-- <div class="row">
                <div class="col-lg-12 text-center" style="margin-bottom: 20px;">
                   <div id='project-event-list' class="row" style="height: 70px;"></div>
                </div>
            </div> -->
            <div class="row-fluid">
<?php if ($user_name != "bigscreen") : ?>
                <div class="col-lg-3 col-md-6 col-sm-12 hidden-xs" style="margin-bottom: 20px; margin-left: 20px; max-width: 250px;">
                    <div class="hidden-sm hidden-xs hidden-md" id="month-view">
                    </div>
                    <div id='project-event-list' class="hidden-sm hidden-xs hidden-md" style="height: 520px; overflow-y: auto;"></div>
                </div>
                <div class="col-lg-9 col-md-12 col-sm-12 text-center">
<?php else : ?>
                <div class="col-lg-12 col-md-12 col-sm-12 text-center">
<?php endif; ?>
                    <center><div id='calendar-loc' class="row"></div></center>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="projects">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">Projects for <span class="user_name">???</span>
<?php if ($admin) : ?>
                    <a class="glyphicon glyphicon-plus" data-toggle="modal" data-target="#add-project-dialog"></a>
<?php endif; ?>
                    </h2>
<?php if ($user_name == "") : ?>
                    <h3 class="section-subheading text-muted">You have to be logged in to see your projects.</h3>
<?php else : ?>
                    <h3 class="section-subheading text-muted">Control the progress of your scanning projects.</h3>
<?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                   <div class="text-muted" id="projectlist"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="bg-light-gray">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">About</h2>
                    <h3 class="section-subheading text-muted">Center for Translational Imaging and Precision Medicine.</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <ul class="timeline">
                        <li>
                            <div class="timeline-image">
                                <img class="img-circle img-responsive" src="img/about/1.jpg" alt="">
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h4>2015</h4>
                                </div>
                                <div class="timeline-body">
                                    <p class="text-muted">We run a GE 3T MR scanner at the University of California San Diego. Contact us if you are interested in our services.</p>
				    <p class="text-muted">Source code and documentation for this application is available at: <a href="https://github.com/HaukeBartsch/imaging-site-calendar">https://github.com/HaukeBartsch/imaging-site-calendar</a></p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>


    <section id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">Contact Us</h2>
                    <h3 class="section-subheading text-muted">If you have questions with regards to this service, or if you need to report a problem.</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form name="sentMessage" id="contactForm" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Your Name *" id="name" required data-validation-required-message="Please enter your name.">
                                    <p class="help-block text-danger"></p>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control" placeholder="Your Email *" id="email" required data-validation-required-message="Please enter your email address.">
                                    <p class="help-block text-danger"></p>
                                </div>
                                <div class="form-group">
                                    <input type="tel" class="form-control" placeholder="Your Phone *" id="phone" required data-validation-required-message="Please enter your phone number.">
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <textarea class="form-control" placeholder="Your Message *" id="message" required data-validation-required-message="Please enter a message."></textarea>
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-12 text-center">
                                <div id="success"></div>
                                <button type="submit" class="btn btn-xl">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <ul class="list-inline quicklinks">
                      <li><a href="#" data-target="#about-dialog" data-toggle="modal">Copyright &copy; Hauke Bartsch 2015</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul class="list-inline social-buttons">
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul class="list-inline quicklinks">
                        <li><a href="#" data-target="#termsofuse" data-toggle="modal">Privacy Policy and Terms of Use</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- terms of use -->
    <div class="portfolio-modal modal fade" id="termsofuse" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">


<h3>Term of Use</h3>

<h4>Legal Notices and Disclaimer</h4>

<p>Welcome to the UC San Diego Center for Translational Imaging and Personalized Medicine website. This website is operated and managed by the Center for Translational Imaging and Personalized Medicine at the University of California.</p>

<p>PLEASE READ THESE TERMS AND CONDITIONS OF USE CAREFULLY BEFORE USING THIS SITE. By using this site and all other Official UCSD Web Sites, referred to as "these sites," you agree to these terms of use. If you do not agree to these terms of use, please do not use this site. These Sites are owned and operated by UCSD (referred to as "UCSD," "we," "us," or "our" herein). We reserve the right, at our discretion, to change, modify, add, or remove portions of these terms at any time. Please check these terms periodically for changes. Your continued use of these Sites following the posting of changes to these terms (including the UCSD Web Policy) will mean you accept those changes.
</p>
<h3>Health Information</h3>
<p>All health and health-related information contained within UC San Diego Center for Translational Imaging and Personalized Medicine website is intended to be general in nature and should not be used as a substitute for medical treatment by a health care professional. Your health care provider should be consulted regarding matters concerning the medical condition, treatment and needs of you and your family.</p>

<p>Every effort is made to ensure that the material within this website is accurate and timely, however, this should not be considered official and should be confirmed with other reliable sources. This information is provided without warranty for quality or accuracy. The Regents of the University of California; the University of California, San Diego; the University of California, San Diego Health System nor any other contributing author is responsible for any errors or omissions in any information provided or the results obtained from the use of such information.</p>

<h3>Privacy</h3>

<p>UC San Diego Center for Translational Imaging and Personalized Medicine ctipm.ucsd.edu respects the privacy of its users.</p>

<p>Our goal is to provide you with a personalized Internet experience that delivers the information, resources, and services that are most relevant and helpful to you. In order to achieve this goal, we sometimes collect information during your visits to understand what differentiates you from each of our other users.</p>

To demonstrate our commitment to your privacy, we have prepared this statement disclosing our website privacy practices.

Here, you will learn what personally identifiable information of yours is collected, how and when we might use your information, how we protect your information, who has access to your information, and how you can correct any inaccuracies in the information.

<h3>Collection of Data</h3>

UC San Diego Center for Translational Imaging and Personalized Medicine collects certain information from and about its users in two ways: directly from the user and from our web server logs.

    <h4>User-Supplied Information</h4>
    <p>When you submit a form we may ask you for your name, phone number, and e-mail address. The more accurate information you volunteer, the better we are able to respond to your request.</p>


    <h4>Log Information</h4>
    <p>Web servers typically collect, at least temporarily, the following information: Internet Protocol (IP) address of computer being used; web pages requested; referring web page; browser used; date and time. UCSD Medical Center may collect statistics identifying particular IP addresses from which Medical Center websites are accessed.</p>

<h3>Use of Data</h3>

    <h4>User-Supplied Information</h4>
    <p>The UC San Diego Health System appointment request and seminar enrollment processes require users to provide personal, demographic and insurance information. The information is used to process the request and to contact patients when necessary. UC San Diego Center for Translational Imaging and Personalized Medicine may also use personal information for the purpose of future communication on new offerings but only if the opportunity to opt out of that type of use is provided.</p>


    <h4>Log Information</h4>
    <p>Cookies are used to remember users who return to the site. The browser-IP-address information and anonymous-browser history is used to report information about site accesses, for profiling purposes, and for troubleshooting. This information is generally used to improve website presentation and utilization.</p>

<p>UC San Diego Center for Translational Imaging and Personalized Medicine does not sell, rent, or exchange any personal information collected or gathered online about visitors unless (i) provided for otherwise in this Privacy Policy; (ii) we obtain your consent, such as when you choose to opt-in or opt-out to the sharing of information; (iii) a service provided on our site requires the interaction with or is provided by a third party; (iv) pursuant to legal process or law enforcement; or (v) we find that your use of this site violates this Policy or other usage guidelines or as deemed reasonably necessary by us to protect our legal rights and/or property.</p>

<h3>Security: How We Protect Your Information</h3>

<p>This site has security measures in place to protect the loss, misuse and alteration of the information under our control. We work to protect the security of your information during transmission by using Secure Sockets Layer (SSL) software, which encrypts information you input.</p>

<h3>No Guarantees</h3>

<p>While this Privacy Policy states our standards for maintenance of personal information and we will make efforts to meet them, we are not in a position to guarantee these standards. There may be factors beyond our control that may result in disclosure of data. As a consequence, we disclaim any warranties or representations relating to maintenance or nondisclosure of data.</p>

<h3>Updating Your Information</h3>

<p>If you would like to update your personal information or stop receiving communication from UC San Diego Center for Translational Imaging and Personalized Medicine, please call (858) 736-7028.


<h3>Restrictions of Use of Material</h3>

<p>All trademarks, service marks, and trade names are proprietary to UCSD and the Regents of the University of California, unless noted otherwise.  No material from InfoPath or any Official UCSD Web Site may be copied, reproduced, republished, uploaded, posted, transmitted, or distributed in any way, without explicit permission, except that you may download one copy of the materials on any single computer for your personal, non-commercial home use only, provided you keep intact all copyright and other proprietary notices. Modification of the materials or use of the materials for any other purpose is a violation of UCSD's copyright and other proprietary rights. The use of any such material on any other Web Site or networked computer environment or in any other medium is prohibited.</p>



                            <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Report -->
    <div class="portfolio-modal modal fade" id="adminReport" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <h2>Admin Report</h2>
                            <p class="item-intro text-muted">Regardless of project this report lists all scans performed on the scanner.</p>
                            <div id="adminreport-details"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="portfolio-modal modal fade" id="adminReportMonthOverview" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <h2>Admin Report by Month</h2>
                            <p class="item-intro text-muted">Regardless of project sort scans by study description by month.</p>
                            <div id="adminreport-month-overview-details"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <!-- Project Report -->
    <div class="portfolio-modal modal fade" id="projectReport" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <!-- Project Details Go Here -->
                            <h2>Project Report</h2>
                            <p class="item-intro text-muted" id="project-event-message">The following table lists all reserved scan times for the current project per month.</p>
                            <p id="add-event-project-details" class="text-muted"><span class="report-project-name"></span></p>

                            <div id="report"></div>
 <!--                            <table id="report" class="report-table">
                               <thead>
                                    <tr>
                                        <th>Nr</th>
                                        <th>Title</th>
                                        <th>Duration (hours)</th>
                                        <th>Start/End</th>
                                        <th>Total (hours)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table> -->

                            <button style="margin-top: 50px;" type="button" class="btn btn-success" id="printButton"><i class="fa fa-print"></i> Print</button>
                            <button style="margin-top: 50px;" type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- add event -->
    <div class="portfolio-modal modal fade" id="addEvent" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <!-- Project Details Go Here -->
                            <h2>Event Details</h2>
                            <p class="item-intro text-muted" id="add-event-message">Name your event, make sure you have the correct project.</p>
                            <p id="add-event-project-details" class="text-muted"></p>
                            <form role="form" class="form-horizontal">
                              <div class="form-group has-feedback">
                                <label class="control-label col-sm-3" for="add-event-name">Event name</label>
                                <div class="col-sm-9">
                                  <input type="text" class="form-control" id="add-event-name" placeholder="Subject X, PING protocol">
                                </div>
                                <!-- <input type="text" class="form-control project-name" id="add-event-project-name" placeholder="Project 01"> -->
                              </div>
                              <div class="form-group has-feedback">
                                <label class="control-label col-sm-3" for="add-event-project-name">Project name</label>
                                <div class="col-sm-9">
                                  <select class="form-control projects" id="add-event-project-name"></select>
                                </div>
                              </div>
                              <div class="form-group has-feedback">
                                <label class="control-label col-sm-3" for="add-event-start-time">Start Time</label>
                                <div class="col-sm-9">
                                  <div class='input-group date' id='datetimepicker1'>
                                      <input type='text' data-format="MM/dd/yyyy HH:mm:ss PP" id="add-event-start-time" class="form-control" />
                                      <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                      </span>
                                  </div>
                                </div>
                              </div>                              
                              <div class="form-group has-feedback">
                                <label class="control-label col-sm-3" for="add-event-end-time">End Time</label>
                                <div class="col-sm-9">
                                  <div class='input-group date' id='datetimepicker2'>
                                      <input type='text' data-format="MM/dd/yyyy HH:mm:ss PP" id="add-event-end-time" class="form-control" />
                                      <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                      </span>
                                  </div>
                                </div>
                              </div>
                              <div class="form-group has-feedback">
                                <div class="col-sm-offset-3 col-sm-9">
				  <div class="checkbox">
  				    <label for="add-event-noshow">
                                       <input id="add-event-noshow" type="checkbox" value="">Patient No-Show
                                    </label>
                                  </div>
                                </div>
                              </div>
                            </form>
                            <p class="text-muted">Cancelation policy: Scan times can be canceled up to 72 hours before the scheduled time.</p>
                            <!-- <img class="img-responsive img-centered" src="img/portfolio/escape-preview.png" alt=""> -->
                            <button id="save-event-button" style="margin-top: 50px;" type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-save"></i> Save</button>
                            <button style="margin-top: 50px;" type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                            <button id="delete-event-button" style="margin-top: 50px;" type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Delete Event</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php if ($admin) : ?>
  <div id="changeProject" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     <div class="modal-dialog">
        <div class="modal-content">
           <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Change Projects</h4>
        </div>
        <div class="modal-body">
           <p class="validateTips">Project information is used to allow changes to groups of events.</p>
           <div id="editor" style="height: 600px; width="80%"">load project information<br/></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button id="setupSaveChanges" type="button" data-dismiss="modal" class="btn btn-primary">Save changes</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
<?php endif; ?>

    <!-- Define your project -->
    <div class="portfolio-modal modal fade" id="add-project-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <!-- Project Details Go Here -->
                            <h2>Define A New Project</h2>
                            <form role="form">
                              <div class="form-group">
                                <label for="name">Project name</label>
                                <input type="text" class="form-control" id="project-name" placeholder="Project 01">
                              </div>
                              <div class="form-group">
                                <label for="colorselector">Project color</label>
                                <select id="colorselector" class="form-control">
                                  <option value="106" data-color="#A0522D">sienna</option>
                                  <option value="47" data-color="#CD5C5C" selected="selected">indianred</option>
                                  <option value="87" data-color="#FF4500">orangered</option>
                                  <option value="17" data-color="#008B8B">darkcyan</option>
                                  <option value="18" data-color="#B8860B">darkgoldenrod</option>
                                  <option value="68" data-color="#32CD32">limegreen</option>
                                  <option value="42" data-color="#FFD700">gold</option>
                                  <option value="77" data-color="#48D1CC">mediumturquoise</option>
                                  <option value="107" data-color="#87CEEB">skyblue</option>
                                  <option value="46" data-color="#FF69B4">hotpink</option>
                                  <option value="47" data-color="#CD5C5C">indianred</option>
                                  <option value="64" data-color="#87CEFA">lightskyblue</option>
                                  <option value="13" data-color="#6495ED">cornflowerblue</option>
                                  <option value="15" data-color="#DC143C">crimson</option>
                                  <option value="24" data-color="#FF8C00">darkorange</option>
                                  <option value="78" data-color="#C71585">mediumvioletred</option>
                                  <option value="123" data-color="#000000">black</option>
                                </select>
                              </div>
                              <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" class="form-control" id="project-description" placeholder="Short Description">
                              </div>
                              <div class="form-group">
                                <label for="description">Time per scan (minutes)</label>
                                <input type="number" class="form-control" id="project-scan-duration" placeholder="45">
                              </div>
                              <div class="form-group">
                                <label for="description">Scantime available (hours)</label>
                                <input type="text" class="form-control" id="project-scantime" placeholder="10">
                              </div>
                            </form>

                            <button type="submit" id="send-project-definition" class="btn btn-success" data-dismiss="modal"><i class="fa fa-check"></i> Submit</button>
                            <button type="button" style="margin-top: 20px;" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- What is this all about -->
    <div class="portfolio-modal modal fade" id="about-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <h2>MR scanner scheduling application</h2>

			    <p>
			      This application provides scheduling services for scan time on our GE 750, measures scanner utilization and provides project specific scan time reports. It enables you to improve procedural workflows and the quality of care by easily and quickly providing detailed insights into how the scanner is being used across all projects.
			    </p>
			    <p>This application has been developed for the Center for Translational Imaging and Personalized Medicine (CTIPM) at the University of San Diego by Hauke Bartsch. Feel free to contact us if you have a suggestion on how to improve this service, or if you are interested in using this service.</p>
                            <button type="button" style="margin-top: 20px;" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src='js/moment.min.js'></script>

    <!-- jQuery Version 1.11.0 -->
    <script src="js/jquery-1.11.0.js"></script>
    <script src="js/jquery-ui.custom.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <script src="js/bootstrap-datetimepicker.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/bootstrap-colorselector.js"></script>

    <!-- Plugin JavaScript -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script src="js/classie.js"></script>
    <script src="js/cbpAnimatedHeader.js"></script>

    <!-- Contact Form JavaScript -->
    <script src="js/jqBootstrapValidation.js"></script>
    <script src="js/contact_me.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/agency.js"></script>
    <script src='js/fullcalendar.min.js'></script>

    <script src="js/d3.v3.min.js"></script>
<?php if ($admin) : ?>
    <script src="js/ace/ace.js" type="text/javascript" charset="utf-8"></script>

    <script src="//code.highcharts.com/highcharts.js"></script>
    <script src="//code.highcharts.com/modules/exporting.js"></script>

<?php endif; ?>
    <script type="text/javascript">

    var projectList;

    function updateClock(){
        var now = jQuery.fullCalendar.moment(),
            second = now.seconds() * 6,
            minute = now.minutes() * 6 + second / 60,
            hour = ((now.hours() % 12) / 12) * 360 + 90 + minute / 12;

        $('#hour').css("transform", "rotate(" + hour + "deg)");
        $('#minute').css("transform", "rotate(" + minute + "deg)");
        $('#second').css("transform", "rotate(" + second + "deg)");
    }

    function timedUpdate () {
        updateClock();
        setTimeout(timedUpdate, 1000);
        var dur = moment.duration(Math.round(((new Date).getTime() - lastUpdatedTime)/1000.0), 'seconds').humanize();
        jQuery('#timeSinceLastUpdate').text( "last updated: " + dur + " ago" );
    }

    var lastUpdatedTime;
    function timedRefresh() {
        jQuery('#calendar-loc').fullCalendar('refetchEvents');
        lastUpdatedTime = (new Date).getTime();
        setTimeout(timedRefresh, 1000*30);
    }


    // requires events
    function updateHistogram() {

        jQuery('#history').children().remove(); // remove the prior display

        var events = jQuery('#calendar-loc').fullCalendar('clientEvents');
        lastUpdatedTime = (new Date).getTime();

        // sort events by days
        var numBins = 40;
        var eventByDay = new Array(numBins);

        events.forEach(function(e) {
            if (e.start == null || e.end == null)
                return false;
           var start   = jQuery.fullCalendar.moment(e.start).format();
           var end     = jQuery.fullCalendar.moment(e.end).format();

           if (e.start.zone() == 0)
            start = start  + "-08:00";
           if (e.end.zone() == 0)
            end = end  + "-08:00";

           var minutes = Math.abs(jQuery.fullCalendar.moment(start).diff(end, 'minutes'))/60;
           var dist    = -jQuery.fullCalendar.moment().diff(e.start, 'days');
           if (dist > -Math.floor(numBins/2) && dist <= Math.floor(numBins/2)) {
             var idx   = dist+Math.floor(numBins/2)+1;
             if (typeof(eventByDay[idx]) === 'undefined') {
                eventByDay[idx] = {};
             }
             if (typeof(eventByDay[idx][e['project']]) == 'undefined') {
                eventByDay[idx][e['project']] = minutes;
             } else {
                eventByDay[idx][e['project']] = eventByDay[idx][e['project']] + minutes;                
             }
           }
        });

        for (var i = 0; i < eventByDay.length; i++) {
            if (typeof(eventByDay[i]) === 'undefined')
               eventByDay[i] = {};
            eventByDay[i]['day'] = i - Math.floor(eventByDay.length/2); // jQuery.fullCalendar.moment().day(i - Math.floor(eventByDay.length/2)).day();
            eventByDay[i]['weekday'] = jQuery.fullCalendar.moment().day(i - Math.floor(eventByDay.length/2)).day();
        }

        var margin = {top: 20, right: 20, bottom: 30, left: 40},
          width = jQuery('#history').width() - margin.left - margin.right,
          height = 200 - margin.top - margin.bottom;

        var x = d3.scale.ordinal()
         .rangeRoundBands([0, width], .1);

        var y = d3.scale.linear()
         .rangeRound([height, 0]);

        var xAxis = d3.svg.axis()
         .scale(x)
         .tickFormat(function(d) {
            if (d == 0)
                return "NOW";
            var wd = jQuery.fullCalendar.moment().add(d, 'days').day();
            return moment().isoWeekday(wd).format('ddd');
         })
         .orient("bottom");

        var yAxis = d3.svg.axis()
         .scale(y)
         .orient("left")
         .tickFormat(d3.format(".2f"));

        var svg = d3.select("#history").append("svg")
          .attr("width", width + margin.left + margin.right)
          .attr("height", height + margin.top + margin.bottom)
          .append("g")
          .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        var projs = jQuery.map(events, function(val, i) { return val['project']; });
        var u = {};
        for (var i = 0; i< projs.length; i++)
            u[projs[i]] = projs[i];
        projs = [];
        for (i in u)
            projs.push(u[i]);

        // TODO: get the correct colors for each project
        var color = d3.scale.ordinal()
         .range(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"]);

        color.domain(projs);

        eventByDay.forEach(function(d) {
            var y0 = 0;
            d.hours = color.domain().map(function(name) {
                var uu = 0;
                if (typeof(d[name]) !== 'undefined')
                    uu = d[name];
                return {
                    name: name, 
                    y0: y0, 
                    y1: y0 += uu};
            });
            if (typeof(d.hours) !== 'undefined' && d.hours.length > 0)
              d.total = d.hours[d.hours.length - 1].y1;
            else
                d.total = 0;
        });

        //events.sort(function(a, b) { return b.total - a.total; });

        x.domain(eventByDay.map(function(d) { return d.day; }));
        y.domain([0, d3.max(eventByDay, function(d) { return d.total; })]);

        svg.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate(0," + height + ")")
        .call(xAxis);

        svg.append("g")
        .attr("class", "y axis")
        .call(yAxis)
        .append("text")
        .attr("transform", "rotate(-90)")
        .attr("y", 6)
        .attr("dy", ".71em")
        .style("text-anchor", "end")
        .text("hours");

        var state = svg.selectAll(".state")
        .data(eventByDay)
        .enter().append("g")
        .attr("class", "g")
        .attr("transform", function(d) { return "translate(" + x(d.day) + ",0)"; });

        state.selectAll("rect")
        .data(function(d) { return d.hours; })
        .enter().append("rect")
        .attr("width", x.rangeBand())
        .attr("y", function(d) { return y(d.y1); })
        .attr("height", function(d) { return y(d.y0) - y(d.y1); })
        .style("fill", function(d) { return color(d.name); });

        var legend = svg.selectAll(".legend")
        .data(color.domain().slice().reverse())
        .enter().append("g")
        .attr("class", "legend")
        .attr("transform", function(d, i) { return "translate(0," + i * 20 + ")"; });

        legend.append("rect")
        .attr("x", width - 18)
        .attr("width", 18)
        .attr("height", 18)
        .style("fill", color);

        legend.append("text")
        .attr("x", width - 24)
        .attr("y", 9)
        .attr("dy", ".35em")
        .style("text-anchor", "end")
        .text(function(d) { return d; });

    }

    // add projects for the current user to the web-page
    function addProjects(projects) {
        if ( typeof(projects) === 'undefined')
            return;

        projectList = projects; // global object
        if (projects.length == 0)
            return;

        jQuery('#projectlist').children().remove();
        for (var i = 0; i < projects.length; i++) {
            var finished = Math.round( projects[i].scantime.current/projects[i].scantime.initial * 100 );
            jQuery('#projectlist').append('<div class="col-sm-6 col-lg-3 col-md-4 project clearfix"><div class="project-title portfolio-item">' + 
                '<h4>' + projects[i]['name'] + '</h4>' +
                '<div class="swatch"><div class="c100 p'+ finished + ' small"><span>'+finished+'%</span><div class="slice"><div class="bar"></div><div class="fill"></div></div></div></div>'+
                '<hr>' +
                '<center>' + "<small>total usage (hours)<small><br/><div style=\"font-size: 22pt;margin-top: -10px;\">" + projects[i].scantime.current.toFixed(2) + "/" + projects[i].scantime.initial + '</div><center>' +
                '<hr>' +
                '<div class="portfolio-bottom"> <a href="#" class="show-report" project="'+ projects[i]['name'] +'">Show report</a>' +
                '</div>' +
                '</div></div>');
            jQuery('#project-event-list').append('<div class="event-template ui-draggable ui-draggable-handle" value=\"'+ projects[i]['name'] +'\">' + 
                '<span style="display: relative; width:50px; height:50px; border: 1px solid white; margin-top: 10px;margin-right: 10px; border-radius: 3px; background-color: ' + projects[i].color + '" class="pull-right"></span>' +
                '<h3 class="project-title">' + projects[i]['name'] + '</h3>' +
                '<p class="project-description">' + projects[i]['description'] + '</p>' +
                '<div class="project-scan-time-left">scan time left: ' + (projects[i].scantime.initial-projects[i].scantime.current) + 'h</div>' +
                '<div class="project-available-sessions">approx. sessions left: ' + Math.floor((projects[i].scantime.initial-projects[i].scantime.current)/projects[i].timeperscan) + '</div>' +
                '</div>');
        }

        jQuery('#project-event-list .event-template').each(function() {
        
            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
            // it doesn't need to have a start or end
            var pr = jQuery(this).attr('value');
            var eventObject = {
                title: $.trim(pr) // use the element's text as the event title
            };
            
            // store the Event Object in the DOM element so we can get to it later
            $(this).data('eventObject', eventObject);
            
            // make the event draggable using jQuery UI
            $(this).draggable({
                opacity: 0.5,
                zIndex: 999,
                revert: true,      // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            });
            
        });
        jQuery('.projects').children().remove();
        for (var i = 0; i < projects.length; i++) {
          jQuery('.projects').append("<option value=\"" + projects[i]['name'] + "\">" + projects[i]['name'] + "</option>" );
        }
        // update the details for this project
        jQuery('#add-event-project-name').change(function() {
            var pr = jQuery(this).val();
            updateScanTimeDisplay(pr);
        });

    }

    // project name, sets the string in the interface for the project details
    function updateScanTimeDisplay( pr ) {
            for (var i = 0; i < projectList.length; i++){
                if (projectList[i]['name'] == pr) {
                   jQuery('#add-event-project-details').html( "Project \"" + projectList[i].name + "\" used up " + projectList[i].scantime.initial + " hours. The total scan time allocated to this project is " + projectList[i].scantime.current + " hours.");
                   break;
                }
            }
    }

    function specifyEvent( event ) {
      if (!eventEditable(event).ok) {
        jQuery('#add-event-message').html("This event cannot be edited because " + eventEditable(event).message + ".");
        jQuery('#add-event-project-name').prop('disabled', true);
        jQuery('#add-event-name').prop('disabled', true);
        jQuery('#save-event-button').prop('disabled', true);
        jQuery('#delete-event-button').prop('disabled', true);
        jQuery('#datetimepicker1').prop('disabled', true);
        jQuery('#datetimepicker2').prop('disabled', true);
        jQuery('#add-event-start-time').prop('disabled', true);
        jQuery('#add-event-end-time').prop('disabled', true);
      } else {
        jQuery('#add-event-message').html("Name your event, make sure you have the correct project selected.");
        jQuery('#add-event-project-name').prop('disabled', false);        
        jQuery('#add-event-name').prop('disabled', false);
        jQuery('#save-event-button').prop('disabled', false);
        jQuery('#delete-event-button').prop('disabled', false);
        jQuery('#datetimepicker1').prop('disabled', false);
        jQuery('#datetimepicker2').prop('disabled', false);
        jQuery('#add-event-start-time').prop('disabled', false);
        jQuery('#add-event-end-time').prop('disabled', false);
      }

      jQuery('#addEvent').modal( 'show');
      setTimeout(function() {
        jQuery('#add-event-name').focus();
      }, 200);
      // store the event in the name dom element (we have to move it later if it exists already)
      jQuery('#add-event-project-name').data('origevent', event);
      jQuery('#delete-event-button').data('eid', event.eid);

      if (typeof(event.project) !== 'undefined') {
        jQuery('#add-event-project-name').val(event.project);
      } else {
        jQuery('#add-event-project-name').val(event.title);        
      }
      if (typeof(event.scantitle) !== 'undefined') {
        if (!eventEditable(event).ok) {
	  jQuery('#add-event-name').val("Booked by " + event.user);
        } else {
          jQuery('#add-event-name').val(event.scantitle);
        }
      }
      jQuery('#add-event-project-details').val(event.user);
      
      var cal = $('#calendar-loc').fullCalendar('getCalendar');
      var s = cal.moment(event.start).format();
      var e = cal.moment(event.end).format();

      if (event.noshow == "true")
        jQuery('#add-event-noshow').prop('checked', true);
      jQuery('#save-event-button').attr('event-start', s);
      jQuery('#save-event-button').attr('event-end', e);
      jQuery('#datetimepicker1').data("DateTimePicker").setMinDate(new Date());
      jQuery('#datetimepicker1').data("DateTimePicker").setDate(event.start);

      jQuery('#datetimepicker2').data("DateTimePicker").setMinDate(new Date());
      jQuery('#datetimepicker2').data("DateTimePicker").setDate(event.end);

      updateScanTimeDisplay(event.project);
    }
    
    function loadEvents() {
        jQuery.getJSON('code/php/events.php?action=list', function(data) {
            // alert('got something back: '+ data.message)
            for (var i = 0; i < data.length; i++) {
                var event = new Object();
                event.scantitle = data[i].scantitle;
                event.title = data[i].project + ": " + data[i].scantitle + " (" + data[i].user + ")";
                event.start = moment.parseZone(data[i].start);
                event.end   = moment.parseZone(data[i].end);
                event.project = data[i].project;
                event.user    = data[i].user;
                event.eid     = data[i].eid; // event id
                for (var j = 0; j < projectList.length; j++) {
                    if (projectList[j].name == event.project) {
                      event.backgroundColor = projectList[j].color;
                      break;
                    }
                }
                if (!eventEditable(event).ok)
                    event.editable = false;
                jQuery('#calendar-loc').fullCalendar('renderEvent', event, true);
            }
            updateHistogram();
        });        
    }

    moment.fn.roundNext15Min = function () {
      var intervals = Math.floor((this.minutes()+(15/2.0)) / 15);
      if(intervals == 4) {
         this.add('hours', 1);
         intervals = 0;
      }
      this.minutes(intervals * 15);
      this.seconds(0);
      return this;
    }

    // save a new calendar event
    function storeEvent( event ) {
        if (!eventEditable(event).ok) {
            alert("Error: This event could not be stored. Maybe you don't have permissions, or its in the quarantine zone (past or immediate future).");
            return false; // do nothing
        }

        var cal = jQuery('#calendar-loc').fullCalendar('getCalendar');
        var s = cal.moment(event.start).format();
        var e = cal.moment(event.end).format();
        // round events at 15 minutes
	s = event.start.roundNext15Min().format();
	e = event.end.roundNext15Min().format();

        var url = encodeURI('code/php/events.php?project=' + event.project + 
            '&action=create&value=' + event.scantitle + '&value2=' + 
            encodeURIComponent(s) + '&value3=' + 
            encodeURIComponent(e) + '&value5=' + event.noshow);
        jQuery.getJSON(url, 
            function(data) { // returns the event id
               // alert('got something back: '+ data.message)
               if (typeof(data.eid) !== 'undefined'){
                  event.eid = data.eid;
                  jQuery('#calendar-loc').fullCalendar('renderEvent', event, true);
                  updateHistogram();
               }
            });
        return true;
    }

    function removeEvent( event ) {
        if (!eventEditable(event).ok) {
            alert("Error: This event cannot be removed.");
            return; // do nothing
        }

        var url = encodeURI('code/php/events.php?project=' + event.project + 
            '&action=remove&value=' + event.scantitle + '&value2=' + 
            encodeURIComponent(event.start.format()) + '&value3=' + 
            encodeURIComponent(event.end.format()) + '&value4=' + 
            encodeURIComponent(event.eid));
        jQuery.getJSON(url,
            function(data) {
                if (data.ok == 1) {
                    // now delete the event from the calendar as well
                    var events = jQuery('#calendar-loc').fullCalendar('clientEvents');
                    events.forEach(function(e) {
                       if (typeof(event.eid) !== 'undefined' && e.eid == event.eid)
                          jQuery('#calendar-loc').fullCalendar('removeEvents', e._id);
                    });
                    updateHistogram();
                } else {
                    alert(data.message);
                }
            });
    }

    function updateEvent( event ) {
        if (!eventEditable(event).ok) {
            alert("Error: This event cannot be changed. Maybe you don't have permissions, it is in the past or the immediate future (+72hours).");
            return false; // do nothing
        }
        //var cal = jQuery('#calendar-loc').fullCalendar('getCalendar');
        //var s = cal.moment(event.start).format();
        //var e = cal.moment(event.end).format();
        var s = event.start.format();
        var e = event.end.format();

        // WHY? If we drag-and-drop we don't get the time zone here
        // We add the correct time zone to the string for Los Angeles
        if (event.start.zone() == 0) {
            s = s + "-08:00";
        }
        if (event.end.zone() == 0) {
            e = e + "-08:00";
        }
// https://github.com/eternicode/bootstrap-datepicker/zipball/1.3.0
        var url = encodeURI('code/php/events.php?project=' + event.project + 
            '&action=update&value=' + event.scantitle + '&value2=' + 
            encodeURIComponent(s) + '&value3=' + 
            encodeURIComponent(e) + '&value4=' + 
            encodeURIComponent(event.eid) + '&value5=' +
            event.noshow);
        jQuery.getJSON(url,
            function(data) {
               // alert('got something back: '+ data.message)
               if (data.ok == 0){
                 alert(data.message);
               } else {
                 jQuery('#calendar-loc').fullCalendar('updateEvent', event);
                 updateHistogram();
               }
            });

        return true;
    }

    function eventEditable( event ) {
          //return {ok: true};
        var cal = jQuery('#calendar-loc').fullCalendar('getCalendar');
        var s = cal.moment(event.start).format();
        var e = cal.moment(event.end).format();

        var block = 0; // 3*24*60; // 3 days minutes

        // alert('what is the difference? ' + jQuery.fullCalendar.moment().diff(e));
        // an event is not valid if its in the past (no changing history)
        if (jQuery.fullCalendar.moment().diff(e, 'minutes') > 0-block &&
            jQuery.fullCalendar.moment().diff(s, 'minutes') > 0-block)
            return {ok: false, message: "the event is in a blocked period"};

        // an event is only editable if the current user has permissions
        var found = false;
        for(var i = 0; i < projectList.length; i++) {
            if (projectList[i]['name'] == event.project) {
                found = true;
                break;
            }
        }
        if (found == true)
          return {ok: true};

        return {ok: false, message: "the current user has no project permissions"};
    }

    function updateDayView() {
        var today = moment();
        jQuery('#today-list').children().remove();
        var todayevents = jQuery('#calendar-loc').fullCalendar('clientEvents', function(event) {
                if(event.start.isSame(today,'d')) {
                    // is this event currently going on?
		    /*jQuery('#today-list').append("<li class=\"list-group-item " + col + "\">"
                       + event.start.format('HH:mm')+"-"+event.end.format('HH:mm')
                       + " [<a href=\"mailto:" + contacts[event.user].email + "\">" + event.user + "</a> - " + event.project + "] <span class=\"text-muted\">"
                       + event.scantitle+"</span></li>"); */

                    return true;
                }
                return false;
        });
        todayevents.sort(function(a,b) { return moment.parseZone(a.start).diff(moment.parseZone(b.start)); });
        for (var i = 0; i < todayevents.length; i++) {
  	   event = todayevents[i];
           var col = "list-group-item-info";
           if (today.isAfter(event.start) && event.end.isAfter(today)) {
	     col = "list-group-item-info";
	   }
	   jQuery('#today-list').append("<li class=\"list-group-item " + col + "\">"
              + event.start.format('HH:mm')+"-"+event.end.format('HH:mm')
              + " [<a href=\"mailto:" + contacts[event.user].email + "\">" + event.user + "</a> - " + event.project + "] <span class=\"text-muted\">"
              + event.scantitle+"</span></li>"); 
        }
    }

function strTimeToMinutes(str_time) {
  var arr_time = str_time.split(":");
  var hour = parseInt(arr_time[0]);
  var minutes = parseInt(arr_time[1]);
  return((hour * 60) + minutes);
}

function setTimeline(view) {
  var parentDiv = $('.fc-slats:visible').parent();
  var timeline = parentDiv.children(".timelineCal");
  if (timeline.length == 0) { //if timeline isn't there, add it
    timeline = $("<hr>").addClass("timelineCal");
    parentDiv.prepend(timeline);
  }

  var curTime = new Date();

  var curCalView = $("#calendar-loc").fullCalendar('getView');
  if (curCalView.intervalStart < curTime && curCalView.intervalEnd > curTime) {
    timeline.show();
  } else {
    timeline.hide();
    return;
  }
  var calMinTimeInMinutes = strTimeToMinutes(curCalView.opt("minTime"));
  var calMaxTimeInMinutes = strTimeToMinutes(curCalView.opt("maxTime"));
  var curSeconds = (( ((curTime.getHours() * 60) + curTime.getMinutes()) - calMinTimeInMinutes) * 60) + curTime.getSeconds();
  var percentOfDay = curSeconds / ((calMaxTimeInMinutes - calMinTimeInMinutes) * 60);

  var topLoc = Math.floor(parentDiv.height() * percentOfDay);
  var timeCol = $('.fc-time:visible');
  timeline.css({top: topLoc + "px", left: (timeCol.outerWidth(true))+"px"});

  if (curCalView.name == "agendaWeek") { //week view, don't want the timeline to go the whole way across
    var dayCol = $(".fc-today:visible");
    var left = dayCol.position().left + 1;
    var width = dayCol.width() + 1;
    timeline.css({left: left + "px", width: width + "px"});
  }
}
       var timelineInterval;
       var editor = null;

       jQuery(document).ready(function() {
	jQuery(window).resize(function() {
           updateHistogram();
        });	

        jQuery('#datetimepicker1').datetimepicker({language: 'en' });
        jQuery('#datetimepicker2').datetimepicker({language: 'en' });
        jQuery("#datetimepicker1").on("change.dp",function (e) {
           jQuery('#datetimepicker2').data("DateTimePicker").setMinDate(e.date);
        });
        jQuery("#datetimepicker2").on("change.dp",function (e) {
           jQuery('#datetimepicker1').data("DateTimePicker").setMaxDate(e.date);
        });

        if (typeof(user_name) !== 'undefined')
          jQuery('.user_name').html(user_name);

        jQuery('#delete-event-button').click(function() {
           var ev = new Object();
           ev.project = jQuery('#add-event-project-name').val();
           ev.scantitle = jQuery('#add-event-name').val();
           ev.title = ev.project + ": " + ev.scantitle;
           ev.start = jQuery('#datetimepicker1').data('DateTimePicker').getDate();
           ev.end   = jQuery('#datetimepicker2').data('DateTimePicker').getDate();
           ev.eid   = jQuery('#delete-event-button').data('eid');
	   removeEvent(ev);
        });

        jQuery('#admin-report-month-overview').on('click', function() {
	    jQuery('#adminReportMonthOverview').modal('show');		

            jQuery.getJSON('/code/php/scans.php', function(scans) {
              scans.sort(function(a,b) {
                 a.start = moment(a.StudyDate + a.StudyTime, "YYYYMMDDHHmmss");
                 b.start = moment(b.StudyDate + b.StudyTime, "YYYYMMDDHHmmss");
                 return moment.parseZone(a.start).diff(moment.parseZone(b.start));
              });

              // how to detect the different scans
              var listOfCategories = [ ["Cardiac", ["/cardiac/i", "/CARD/i" ]],
			               ["Pelvis", ["/pelvis/i"]], 
			               ["Brain", ["/brain/i"]],
			               ["Neck", ["/neck/i"]],
                                       ["Lumbar", ["/lumbar/i"]],
			               ["Breast", ["/breast/i"]],
			               ["Chest", ["/chest/i"]],
				       ["Extremity", ["/EXTREMITY/i", "/extr/i"]],
                                       ["Spine", ["/spine/i"]],
			               ["Abdomen", ["/abdomen/i"]]
			    ];
	      str = "<div>";
              var startMonth = moment(scans[scans.length-1].start).startOf('month');
	      var thisMonthData = {};
              var totalThisMonth = 0;
              var month = 0;
              for (var i = scans.length-1; i >= 0; i--) { // collect by month
	         var firstOfThisMonth = moment(scans[i].start).startOf('month');
	         if ( moment(firstOfThisMonth).diff(startMonth, 'months') !== 0  || i == 0 ) { // plot something for this month
console.log(i);
	            l = Object.keys(thisMonthData);

                    str = str + "<br/>" + moment(startMonth).format('MMM YYYY') + ":<br/>";
		    piedata = [];
                    for (var j = 0; j < l.length; j++) {
		       str = str + l[j] + ": " + thisMonthData[l[j]] + "<br/>";
		       piedata.push( { name: l[j], y: thisMonthData[l[j]] } );
                    }

                    jQuery('#adminreport-month-overview-details').append("<div id=\"pie-"+month+"\" style=\"min-width: 410px; height: 500px; max-width: 800px; margin: 0 auto\"></div>");

	            jQuery('#pie-'+month).highcharts({
			   chart: {
			   plotBackgroundColor: null,
			    plotBorderWidth: null,
			       plotShadow: false,
			       type: 'pie'
			   },
			title: {
			    text: moment(startMonth).format('MMM YYYY') + ": " + totalThisMonth
			},
			tooltip: {
			    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
			    pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
				    enabled: true,
				    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
				    style: {
					color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
				    }
				}
			    }
			},
			series: [ {
			    name: "types",
			    colorByPoint: true,
			    data: piedata
			}]
		    });
		    totalThisMonth = 0;
		    month = month + 1;
   	            thisMonthData = {};
		    startMonth = firstOfThisMonth;
                 }
		 // check this scan against all the possible Categories, pick the first and add
		 var cat = "";
	         for ( var j = 0; j < listOfCategories.length; j++) {
		    var g = listOfCategories[j];
		    for (var k = 0; k < g[1].length; k++) {
                      var match = g[1][k].match(new RegExp('^/(.*?)/([gimy]*)$'));
   	              var patt = new RegExp(match[1], match[2]);
                      if (patt.test(scans[i].StudyDescription)) {
                         cat = g[0];
                         break;
                      }
                    }
		    if (cat !== "") {
			break; // only allow the first category
                    }
                 }
                 if (cat == "") {
		    cat = "unknown";
		    console.log("Could not find category for: " + scans[i].StudyDescription);
                 }

                 totalThisMonth = totalThisMonth + 1;
		 if (cat in thisMonthData) {
		    thisMonthData[cat] = thisMonthData[cat] + 1;
                 } else {
                    thisMonthData[cat] = 1;
                 }
              }
              str = str + '</div>';
	      jQuery('#adminreport-month-overview-details').append(str);
            });
        });


        jQuery('#admin-report').on('click', function() {
	    jQuery('#adminReport').modal('show');		

            jQuery.getJSON('/code/php/scans.php', function(scans) {
              scans.sort(function(a,b) {
                 a.start = moment(a.StudyDate + a.StudyTime, "YYYYMMDDHHmmss");
                 b.start = moment(b.StudyDate + b.StudyTime, "YYYYMMDDHHmmss");
                 return moment.parseZone(a.start).diff(moment.parseZone(b.start));
              });
	      
	      str = '<table id=table-admin-report class=\"report-table\">' +
			    "<thead><tr><th>Nr</th><th>PatientID</th><th>PatientName</th><th>StudyDate</th><th>StudyTime</th><th>StudyDescription</th><th>StudyInstanceUID</th></tr></thead>" +
			    "<tbody>";
              for (var i = scans.length-1; i >= 0; i--) {
		 str = str + "<tr><td>" + i + "</td><td>" + scans[i].PatientID + "</td><td>" + scans[i].PatientName + "</td><td>" +
				  scans[i].StudyDate + "</td><td>" + scans[i].StudyTime + "</td><td>" + scans[i].StudyDescription + "</td><td>"
				  + scans[i].StudyInstanceUID + "</td></tr>";
              }
              str = str + '</tbody></table>';
	      jQuery('#adminreport-details').append(str);
            });
        });

        jQuery('#projectlist').on('click', '.show-report', function() {
            jQuery('#projectReport').modal('show');

            var project_name = jQuery(this).attr('project');
            var current = 0;
            var contingent = 0;
            for (var i = 0; i < projectList.length; i++) {
                if (projectList[i]['name'] == project_name) {
                    current    = projectList[i]['scantime']['current'];
                    contingent = projectList[i]['scantime']['initial'];
                }
            }
            jQuery('.report-project-name').html("Project \"" + project_name + "\" used up "+ current + " hours. The total scan time for this project is " + contingent +" hours.");
            
            //jQuery.getJSON('code/php/events.php?action=list', function(data) {
            // we need both the list of events and the actual scans taken
	    jQuery.when( jQuery.getJSON('code/php/events.php?action=list'), jQuery.getJSON('code/php/scans.php') ).done(function(data, scans) {
               // sort by start time
               if (data[1] == "success")
                  data = data[0];
               if (scans[1] == "success")
		  scans = scans[0];
	       scans.sort(function(a,b) {
		 a.start = moment(a.StudyDate + a.StudyTime, "YYYYMMDDHHmmss");
		 b.start = moment(b.StudyDate + b.StudyTime, "YYYYMMDDHHmmss");
                 return moment.parseZone(a.start).diff(moment.parseZone(b.start));
               });
               data.sort(function(a,b) { return moment.parseZone(a.start).diff(moment.parseZone(b.start)); });
               var sum = 0; var count = 0; var crossNow = false; var sumPerMonth = 0;
	       var startMonth; var countMonths = 0;
               jQuery('#report').children().remove();
               for (var i = 0; i < data.length; i++) {
                   var event = new Object();
                   event.scantitle = data[i].scantitle;
                   event.title = data[i].project + ": " + data[i].scantitle;
                   event.start = moment.parseZone(data[i].start);
                   event.end   = moment.parseZone(data[i].end);
                   var duration = event.end.diff(event.start, 'minutes')/60;
                   event.project = data[i].project;
                   event.user    = data[i].user;
                   event.eid     = data[i].eid; // event id
	           event.noshow  = data[i].noshow;

		   if (i==0) {
                     startMonth = moment(event.start).startOf('month');
		     sumPerMonth = 0;
		     var firstOfThisMonth = moment(event.start).startOf('month');
		     jQuery('#report').append('<table id=table-'+ countMonths +' class=\"report-table\">' +
                                              '<thead><tr><th>Nr</th><th>Title</th><th>Duration (hours)</th><th>Start/End '+ moment(firstOfThisMonth).format('MMM YYYY') +'</th><th>Total (hours)</th></tr></thead><tbody></tbody></table>' +
				              '<div>Summary for '+ moment(firstOfThisMonth).format('MMM YYYY') +': <span id=\"summary-' + countMonths + '\"></span>hours</div>');
                   }
                   if (event.project !== project_name)
                      continue;

                   if (i > 0) {
		     // do we have to create a new table?
		     var firstOfThisMonth = moment(event.start).startOf('month');
                     if ( moment(firstOfThisMonth).diff(startMonth, 'months') !== 0 ) {
  		        sumPerMonth = 0;
			countMonths++;
                        // add a new table
			jQuery('#report').append('<table id=table-'+ countMonths +' class=\"report-table\">' +
                                                 '<thead><tr><th>Nr</th><th>Title</th><th>Duration (hours)</th><th title=\"In coordinated universal time (UTC).\">Start/End '+ moment(firstOfThisMonth).format('MMM YYYY') +'</th><th>Total (hours)</th></tr></thead><tbody></tbody></table>' +
				                 '<div>Summary for '+ moment(firstOfThisMonth).format('MMM YYYY') +': <span id=\"summary-' + countMonths + '\"></span>hours</div>');
			startMonth = moment(event.start).startOf('month');
                     }           
                   }

                   if (crossNow == false && moment().diff(event.end) < 0) {
                     crossNow = true;
                     jQuery('#table-'+ countMonths +' tbody').append( '<tr><td>' 
                        + '</td><td>' + "<i>TODAY</i>"
                        + '</td><td>'
                        + '</td><td>' + moment().format() 
                        + '</td><td>'
                        + '</td></tr>' );
                   }

                   sum = sum + duration;
		   sumPerMonth = sumPerMonth + duration;
		   jQuery('#summary-'+countMonths).text(sumPerMonth);
                   noshowstr = "";
                   if (typeof(event.noshow) != 'undefined' && event.noshow == 'true')
                      noshowstr = " (no-show)";
                   jQuery("#table-" + countMonths + " tbody").append( '<tr><td>' + count 
                        + '</td><td>' + data[i].scantitle + "<br/><span class=\"text-muted\">(" + data[i].user + ")</span>"
                        + '</td><td>' + duration + noshowstr
                        + '</td><td>' + event.start.format() + "<br/>" + event.end.format() 
                        + '</td><td>' + sum 
                        + '</td></tr>' );
                   // find a scan that overlaps with this time period
                   for (var j = 0; j < scans.length; j++) {
		      if ( scans[j].start.isAfter(event.start) && scans[j].start.isBefore(event.end) ) {
                        jQuery("#table-" + countMonths + " tbody").append( '<tr><td style="padding: 5px;">'
                           + '</td><td colspan="4" style="padding: 5px;">' + scans[j].start.format() + " PatientID: " + scans[j].PatientID + " PatientName: " + scans[j].PatientName + " SIUID: " + scans[j].SeriesInstanceUID
                           + '</td></tr>' );
                      }
                   }
                   count = count + 1;
               }
	       
            });
        });
        jQuery('#printButton').on('click', function (event) {
          if ($('.modal').is(':visible')) {
              var modalId = $(event.target).closest('.modal').attr('id');
              $('body').css('visibility', 'hidden');
              $("#" + modalId).css('visibility', 'visible');
              $('#' + modalId).removeClass('modal');
              window.print();
              $('body').css('visibility', 'visible');
              $('#' + modalId).addClass('modal');
          } else {
              window.print();
          }
        });

        jQuery('#save-event-button').click(function() {

           var ev = new Object();

           // if we change an existing event this should exist
           var originalEvent = jQuery('#add-event-project-name').data('origevent');
           if (typeof(originalEvent) !== 'undefined')
              ev = originalEvent; // start from here (copies event id in eid)
          
           //event_start = jQuery('#save-event-button').attr('event-start');
           //event_end   = jQuery('#save-event-button').attr('event-end');
           //ev.start = event_start;
           //if (typeof(event_end) !== 'undefined')
           //  ev.end   = event_end;
           ev.project = jQuery('#add-event-project-name').val();
           ev.scantitle = jQuery('#add-event-name').val();
           ev.title = ev.project + ": " + ev.scantitle;
           ev.noshow = jQuery('#add-event-noshow').prop('checked');
           // ev.eid   = jQuery('#delete-event-button').data('eid');

           for (var i = 0; i < projectList.length; i++) {
              if (projectList[i].name == ev.project) {
                 ev.backgroundColor = projectList[i].color;
                 break;
              }
           }

           ////////////////////////////////////
           //
           // This seems to be broken, I get two different dates from picker1 and picker2,
           // in order to fix, replace time zone if its in UTC (== "+00:00")
           //
           ////////////////////////////////////
           var offsetInMinutes = moment().zone();
           ev.start = jQuery('#datetimepicker1').data('DateTimePicker').getDate().format();
           ev.end   = jQuery('#datetimepicker2').data('DateTimePicker').getDate().format();
           ev.start = ev.start.replace("+00:00", "-08:00");
           ev.end   = ev.end.replace("+00:00", "-08:00");
           var cal  = jQuery('#calendar-loc').fullCalendar('getCalendar');
           ev.start = cal.moment(ev.start);
           ev.end   = cal.moment(ev.end);

           if (typeof(ev.eid) !== 'undefined') {
              if (!updateEvent(ev)) {
                 jQuery('#calendar-loc').fullCalendar('refetchEvents');
              }
           } else {
              if (!storeEvent(ev)) {
                 jQuery('#calendar-loc').fullCalendar('refetchEvents');
              }
           }
        });

          jQuery('#calendar-loc').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultView: 'agendaWeek',
            minTime: '06:00:00',
            timezone: 'America/Los_Angeles',
            firstDay: new Date().getDay(),
            droppable: true,
            drop: function(date) { // this function is called when something is dropped
            
                // retrieve the dropped element's stored Event Object
                var originalEventObject = $(this).data('eventObject');
                
                // we need to copy it, so that multiple events don't have a reference to the same object
                var copiedEventObject = $.extend({}, originalEventObject);
                
                // assign it the date that was reported
                copiedEventObject.start = jQuery.extend({}, date);
                copiedEventObject.durationEditable = true;

                //var calendar = $('#calendar-loc').fullCalendar('getCalendar');
                //var m = calendar.moment();
                var dur = moment.duration(2, 'hours');
                for ( var i = 0; i < projectList.length; i++) {
                    if (projectList[i].name == copiedEventObject.title) {
		        copiedEventObject.backgroundColor = projectList[i].color;
                        dur = moment.duration(+projectList[i].timeperscan, 'hours');
                    }
                }
                var hh = dur.hours() + dur.minutes()/60;
                var endDate = jQuery.fullCalendar.moment(date).add(hh, 'hours');
                copiedEventObject.end = endDate; // .format();
                
                // render the event on the calendar
                // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                //$('#calendar-loc').fullCalendar('renderEvent', copiedEventObject, true);
                // after editing we will update this event again
                //jQuery('#calendar-loc').fullCalendar('renderEvent', copiedEventObject, true);
                copiedEventObject.project = copiedEventObject.title;
                jQuery('#add-event-name').val("");

                specifyEvent(copiedEventObject);                            
            },
            eventClick: function(calEvent, jsEvent, view) {
              specifyEvent(calEvent);
            },
            eventDrop: function(calEvent, jsEvent, view) {
              if (!updateEvent(calEvent)) {
                 jQuery('#calendar-loc').fullCalendar('refetchEvents');                 
              }
            },
            eventResize: function(calEvent, jsEvent, view) {
              if (!updateEvent(calEvent)) {
                 jQuery('#calendar-loc').fullCalendar('refetchEvents');                 
              }
            },

            selectable: true,
            selectHelper: true,
            select: function(start, end) {

                if (projectList.length == 0) {
                    // user does not have any events
                    jQuery('#calendar-loc').fullCalendar('unselect');
                    return;
                }
                // var title = prompt('Event Title:');
                var eventData = {
                    title: projectList[0].name, // pick the first project
                    project: projectList[0].name, // pick the first project
                    start: start,
                    end: end
                };
                jQuery('#calendar-loc').fullCalendar('unselect');
                jQuery('#add-event-name').val("");
                specifyEvent( eventData );
            },
            eventRender: function (event, element) {
               element.find('.fc-event-title').html(event.title);
            },
	    viewRender: function(view) {
		if(typeof(timelineInterval) != 'undefined'){
		    window.clearInterval(timelineInterval); 
		}
		timelineInterval = window.setInterval(setTimeline, 1000*60);
		try { 
		   setTimeline();
		} catch(err) {};
            },
            editable: true,
            eventSources: [ "code/php/events.php", [
                {
                    title: 'Scanner setup',
                    start: '2014-10-27'
                },
	        {
	            title: 'Veterans Day',
		    start: '2014-11-11'
                },
	        {
	            title: 'Thanksgiving',
		    start: '2014-11-27'
                },
	        {
	            title: 'Thanksgiving',
		    start: '2014-11-28'
                },
	        {
	            title: 'Christmas Day',
		    start: '2014-12-25'
                },
	        {
	            title: 'New Year\'s Day',
		    start: '2015-01-01'
                },
	        {
	            title: 'Martin Luther King, Jr. Day',
		    start: '2015-01-19'
                },
	        {
	            title: 'President\'s Day',
		    start: '2015-02-16'
                },
	        {
	            title: 'Cesar Chavez Day',
		    start: '2015-03-31'
                },
	        {
	            title: 'Memorial Day',
		    start: '2015-05-25'
                },
	        {
	            title: 'Independence Day',
		    start: '2015-07-03'
                },
	        {
	            title: 'Labor Day',
		    start: '2015-09-07'
                },
	        {
	            title: 'Veterans Day',
		    start: '2015-11-11'
                },
	        {
	            title: 'Thanksgiving',
		    start: '2015-11-26'
                },
	        {
	            title: 'Thanksgiving',
		    start: '2015-11-27'
                },
	        {
	            title: 'Christmas Day',
		    start: '2015-12-25'
                }
            ] ]
            // put your options and callbacks here
          });


          if (typeof(user_name) !== 'undefined') {
            jQuery.getJSON('/code/php/getProjects.php', function(data) {
               addProjects(data);
               //loadEvents();
               timedUpdate();
               if (user_name == "bigscreen")
   	         timedRefresh();
               updateHistogram();
               updateDayView();
               jQuery('#month-view').datepicker({
                 format:'d.m.Y H:i',
                 inline:true,
                 lang:'en',
                 calendarWeeks: true,
                 todayHighlight: true
               }).on("changeDate", function(e){
                   jQuery('#calendar-loc').fullCalendar('gotoDate', e.date );
               });
            });
          }

          jQuery('#colorselector').colorselector();

          jQuery('#send-project-definition').click(function() {

              // collect the information from the form
              var name        = jQuery('#project-name').val();
              var description = jQuery('#project-description').val();
              var scantime    = jQuery('#project-scantime').val();
              var timeperscan = jQuery('#project-scan-duration').val()/60; // now in hours
              var color       = jQuery('#colorselector option:selected').attr('data-color');
              jQuery.getJSON('/code/php/getProjects.php?action=create&value=' + name + '&value2='+ 
                             encodeURIComponent(description) + 
                             '&value3=' + encodeURIComponent(scantime) +
                             '&value4=' + encodeURIComponent(timeperscan) +
                             '&value5=' + encodeURIComponent(color), function(data) {
                  //console.log('created new project');
                  jQuery.getJSON('/code/php/getProjects.php', function(data) {
                    addProjects(data);
                  });
              });
          });

          // get the routing information as well
          jQuery.ajax({
            url: '/code/php/getProjectSetup.php', 
            dataType: 'html',  // we want to show this as text not interprete
            success: function(data) {
		if (typeof(ace) == 'undefined') {
		  return; // don't do anything if we don't import ace
		}

                if (editor == null) {
                    editor = ace.edit("editor");
                }
                editor.setValue(data);
                editor.setTheme("ace/theme/monokai");
                editor.getSession().setMode("ace/mode/javascript");
            },
            cache: false
          });
 
          jQuery('#setupSaveChanges').click(function() {
            jQuery.ajax({
               url: '/code/php/getProjectSetup.php?action=save',
               data: { "content":  editor.getValue() },
               type: 'POST',
               success: function(data){
                   if (data.length > 0)
                      alert('Error: ' + data);
               }
            });
          });

       });
   </script>

</body>

</html>
