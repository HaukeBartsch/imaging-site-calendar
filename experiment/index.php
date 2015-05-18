<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CTIPM Scanner Visualizer</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/agency.css" rel="stylesheet">
    <link href="../css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css">
    <link href="../css/bootstrap-colorselector.css" rel="stylesheet" type="text/css">

    <!-- Custom Fonts -->
    <link href="../font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href='//fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700' rel='stylesheet' type='text/css'>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->


    <link rel='stylesheet' href='../css/fullcalendar.min.css' />
    <link rel='stylesheet' href='../css/clock.css' />
    <link rel='stylesheet' href='../css/circle.css' />
    <style>
        canvas {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
        }
    </style>

<?php
  session_start();

  if (isset($_SESSION['project_name'])) {
     echo('<script type="text/javascript"> project_name = "'.$_SESSION['project_name'].'"; </script>'."\n");
  }

  include("../code/php/AC.php");
  $user_name = check_logged(); /// function checks if visitor is logged.
  $admin = false;

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
    $can_qc = false;
    if (check_permission( "can-qc" )) {
       $can_qc = true;
    }
    echo('<script type="text/javascript"> admin = '.($admin?"true":"false").'; can_qc = '.($can_qc?"true":"false").'; </script>');
  }
?>


</head>


<body id="page-top" class="index">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand page-scroll" href="/index.php" title="Center for Translational Imaging and Personalized Medicine">CTIPM Calendar
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li>
                        <a class="page-scroll" data-toggle="modal" data-target="#about-dialog" id="show-about-text" href="">About</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

    <!-- Header -->
    <header>
        <div class="container" style="height: 1000px; width: 600px;">
            <canvas id='histogramImage'></canvas>
         </div>
    </header>

    <footer style="position: absolute; bottom: 0; width: 100%; height: 70px;">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <ul class="list-inline quicklinks">
                      <li><a href="#" data-target="#about-dialog" data-toggle="modal">Using code from zenphoton.com, 2014</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul class="list-inline social-buttons">
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul class="list-inline quicklinks">
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
			    <p>This is an experiment visualizing events in the CTIPM calendar using a raytracer (<a href="http://zenphoton.com/">zenphoton.com</a>). The spiral time structure shows scheduled scan times starting from the center of the screen (today).</p>
			    <center>
			      <img style="width: 90%;" src="experiment.jpg">
			    </center>
<p>Use shift-mouse to move the sun.</p>
                            <button type="button" style="margin-top: 20px;" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src='../js/moment.min.js'></script>

    <!-- jQuery Version 1.11.0 -->
    <script src="../js/jquery-1.11.0.js"></script>
    <script src="../js/jquery-ui.custom.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <script src="../js/bootstrap-datetimepicker.js"></script>
    <script src="../js/bootstrap-datepicker.js"></script>
    <script src="../js/bootstrap-colorselector.js"></script>

    <!-- Plugin JavaScript -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script src="../js/classie.js"></script>
    <script src="../js/cbpAnimatedHeader.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../js/agency.js"></script>

    <script src="photon.js"></script>

    <script type="text/javascript">

    var projectList;


    Segment = (function() {
        function Segment(x0, y0, x1, y1, diffuse, reflective, transmissive, text) {
            if (typeof(text) === 'undefined')
               text = "";
            this.x0 = x0;
            this.y0 = y0;
            this.x1 = x1;
            this.y1 = y1;
            this.diffuse = diffuse;
            this.reflective = reflective;
            this.transmissive = transmissive;
            this.text = text;
        }
        Segment.prototype.length = function() {
            var dx, dy;
            dx = this.x1 - this.x0;
            dy = this.y1 - this.y0;
            return Math.sqrt(dx * dx + dy * dy);
        };
        return Segment;
    })();


    var sortedEvents = [];
    function startPhotons( events ) {
      sortedEvents = events;
      s = moment(); // today (no events from before)
      e = moment.parseZone(events[events.length-1].end);
      d = -s.diff(e);
      window.ui.renderer.segments = [];

      if (typeof(window.ui) !== 'undefined') {
        // we should have ui now
        var w = jQuery('#histogramImage').width();
        var h = jQuery('#histogramImage').height();
        // draw the next couple of days
        for (var i = 0; i < 21; i++) {
          var dd = moment().add(i,'days').startOf('day');
          var ddd = moment().add(i,'days').endOf('day');
          var t = -s.diff(moment.parseZone(dd))/d * 20 * 3.1415927 + 3.1415927/1.5;
          var t2 = -s.diff(moment.parseZone(ddd))/d * 20 * 3.1415927 + 3.1415927/1.5;
	  if (t < 0) {
	    i--;
	    continue;
          }
          var x1 = w/3.5*Math.log(t)/Math.LN10*Math.cos(t);
          var y1 = w/3.5*Math.log(t)/Math.LN10*Math.sin(t);
          var x2 = w/3.5*Math.log(t2)/Math.LN10*Math.cos(t2);
          var y2 = w/3.5*Math.log(t2)/Math.LN10*Math.sin(t2);
          //var theta = Math.atan2((y2-y1)/(x2-x1));
          
          window.ui.renderer.segments.push(new Segment(w/2+x1, h/2+y1, w/2+x2, h/2+y2, 0.15, 0, 1, "day +"+i));
        }  // diffuse, reflective, transmissive

        // draw the events
        for (var i = 0; i < events.length; i++) {
          if (typeof(events[i].project) == 'undefined')
             continue;
          before = false; // event not before today
          if (s.diff(events[i].start) > 0) {
             before = true; // event before today
	     continue;
          }
	  // lets to to 20pi
          // var t = i/events.length * 20 * 3.1415927 + 3.1415927/4;
          var t = -s.diff(moment.parseZone(events[i].start))/d * 20 * 3.1415927 + 3.1415927/1.5;
          var t2 = -s.diff(moment.parseZone(events[i].end))/d * 20 * 3.1415927 + 3.1415927/1.5;
          if (t < 0)
            continue;
          var x1 = w/4*Math.log(t)/Math.LN10*Math.cos(t);
          var y1 = w/4*Math.log(t)/Math.LN10*Math.sin(t);
          var x2 = w/4*Math.log(t2)/Math.LN10*Math.cos(t2);
          var y2 = w/4*Math.log(t2)/Math.LN10*Math.sin(t2);
          window.ui.renderer.segments.push(new Segment(w/2+x1, h/2+y1, w/2+x2, h/2+y2, 0.9, 0, 0.99, events[i].project));
        }                                // diffuse, reflextive, transmissive
        setTimeout(function( ) { startPhotons(sortedEvents); }, 20000);
      }
    }

    function loadEvents() {
        jQuery.getJSON('../code/php/events.php?action=list', function(data) {
            data.sort(function(a,b) { return moment.parseZone(a.start).diff(moment.parseZone(b.start)); });
            startPhotons( data );
        });        
    }

    jQuery(document).ready(function() {
          loadEvents();
    	  jQuery(window).resize(function() {
            startPhotons();
          });
/*          jQuery('#about').click(function() {
             jQuery('#about-dialog').modal('show');
          });
          jQuery('#show-about-text').click(function() {
             jQuery('#about-dialog').modal('show');
          }); */
    });
   </script>

</body>

</html>
