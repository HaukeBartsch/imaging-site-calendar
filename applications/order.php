<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CTIPM Scanner Calendar Protocolling</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="//cdn.datatables.net/1.10.10/css/dataTables.bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/agency.css" rel="stylesheet">
    <link href="../css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css">
    <link href="../css/bootstrap-colorselector.css" rel="stylesheet" type="text/css">

    <!-- Custom Fonts -->
    <link href="../font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->


    <link rel='stylesheet' href='../css/fullcalendar.min.css' />
    <link rel='stylesheet' href='../css/clock.css' />
    <link rel='stylesheet' href='../css/circle.css' />

    <link rel='stylesheet' href='../css/style.css' />


<?php
  session_start();

  if (isset($_SESSION['project_name'])) {
     echo('<script type="text/javascript"> project_name = "'.$_SESSION['project_name'].'"; </script>'."\n");
  }

  include("../code/php/AC.php");
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
    $can_order = false;
    if (check_permission( "can-order" )) {
       $can_order = true;
    }
    echo('<script type="text/javascript"> admin = '.($admin?"true":"false").'; can_qc = '.($can_qc?"true":"false").'; can_order = '.($can_order?"true":"false").'; </script>');

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
                        <a class="page-scroll" href="/index.php">Back to Calendar</a>
                    </li>
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

    <!-- Services Section -->
    <section id="schedules">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">Protocol request form <span class="user_name text-muted" style="font-size: 12pt;">???</span></h2>
                </div>
            </div>

                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <!-- Project Details Go Here -->
                            <p class="item-intro text-muted" id="add-event-message">Name your protocol request, make sure you have the correct project. Select an existing protocol from the list to change or delete it.</p>
                            <p id="add-event-project-details" class="text-muted"></p>
                            <form role="form" class="form-horizontal">
                              <div class="form-group has-feedback">
                                <label class="control-label col-sm-3" for="add-event-name">Name</label>
                                <div class="col-sm-9">
                                  <input type="text" class="form-control" id="add-event-name" placeholder="Subject X">
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
                                <label class="control-label col-sm-3" for="add-event-referring">Referring Physician</label>
                                <div class="col-sm-9">
				  <div class="input-group">
				     <input type="text" class="form-control" aria-label="..." id="add-event-referring">
				     <div class="input-group-btn">
				       <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select <span class="caret"></span></button>
				       <ul class="dropdown-menu dropdown-menu-right" id="referring-list">
                                       </ul>
				     </div>
				   </div>
                                   <!-- <select class="form-control projects" id="add-event-referring"></select> -->
                                </div>
                              </div>
                              <div class="form-group has-feedback">
                                <label class="control-label col-sm-3" for="add-event-section" title="Responsible study section">Section</label>
                                <div class="col-sm-9">
				  <div class="input-group">
				     <div class="input-group-btn">
				       <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select <span class="caret"></span></button>
				       <ul class="dropdown-menu dropdown-menu-right multi-level" role="menu" id="section-list">
					  <li><a class="section-selection" href="#">Cardiovascular</a></li>
					  <li><a class="section-selection" href="#">MSK</a></li>
					  <li><a class="section-selection" href="#">Abdomen/Pelvis</a></li>
					  <li><a class="section-selection" href="#">Breast</a></li>
					  <li><a class="section-selection" href="#">Head</a></li>
					  <li><a class="section-selection" href="#">Neck</a></li>
					  <li><a class="section-selection" href="#">WholeBody</a></li>
				       </ul>
				     </div>
				     <input type="text" class="form-control" aria-label="..." id="add-event-section">
                                  </div>
                                </div>
                              </div>
                              <div class="form-group has-feedback">
                                <label class="control-label col-sm-3" for="add-event-protocol">Protocol</label>
                                <div class="col-sm-9">
				  <div class="input-group">
				     <div class="input-group-btn">
				       <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select <span class="caret"></span></button>
				       <ul class="dropdown-menu dropdown-menu-right multi-level" role="menu" id="protocol-list">
					  <li class="dropdown-submenu">
                                             <a tabindex="-1" href="#">Cardiovascular</a>
                                             <ul class="dropdown-menu">
                                                <li><a class="protocol-selection" href="#">MRA Thoracic Outlet + MRI Brachial Plexus</a></li>
                                                <li><a class="protocol-selection" href="#">MRA Pulmonary Vein Flow</a></li>
                                                <li><a class="protocol-selection" href="#">MRI/MRA Pelvis Fibroid Pre-UAE</a></li>
						<li class="dropdown-submenu">
						  <a tabindex="-1" href="#">Cardiac Exams</a>
						  <ul class="dropdown-menu">
                                                    <li><a class="protocol-selection" href="#">MRI Congenital Heart Flow</a></li>
                                                    <li><a class="protocol-selection" href="#">MRI Congenital Heart Flow with MRA</a></li>
                                                    <li><a class="protocol-selection" href="#">MRI Structural Heart (Valves) Flow</a></li>
                                                    <li><a class="protocol-selection" href="#">MRI Pulmonary Hypertension with MRA</a></li>
                                                    <li><a class="protocol-selection" href="#">MRI Cardiac Function and Viability</a></li>
						  </ul>
						</li>
						<li class="dropdown-submenu">
						  <a tabindex="-1" href="#">Vascular Exams</a>
						  <ul class="dropdown-menu">
                                                    <li><a class="protocol-selection" href="#">MRI/MRA Pulmonary Artery Perfusion</a></li>
                                                    <li><a class="protocol-selection" href="#">MRA Thoracic Aortic Flow</a></li>
                                                    <li><a class="protocol-selection" href="#">MRA Abdomen Flow</a></li>
                                                    <li><a class="protocol-selection" href="#">MRA Pelvis Flow</a></li>
                                                    <li><a class="protocol-selection" href="#">MRA Pelvis-Lower Extremity Flow</a></li>
						  </ul>
						</li>
						<li class="dropdown-submenu">
						  <a tabindex="-1" href="#">Rheumatologic Exams</a>
						  <ul class="dropdown-menu">
                                                    <li><a class="protocol-selection" href="#">MRI/MRA Chest Vasculitis</a></li>
                                                    <li><a class="protocol-selection" href="#">MRI/MRA Abdomen Vasculitis</a></li>
                                                    <li><a class="protocol-selection" href="#">MRI/MRA Pelvis Vasculitis</a></li>
						  </ul>
						</li>
						<li class="dropdown-submenu">
						  <a tabindex="-1" href="#">Gynecologic Exams</a>
						  <ul class="dropdown-menu">
                                                    <li><a class="protocol-selection" href="#">MRI/MRA Pelvic Congestion</a></li>
						  </ul>
						</li>
						<li class="dropdown-submenu">
						  <a tabindex="-1" href="#">Abdominal Exams</a>
						  <ul class="dropdown-menu">
                                                    <li><a class="protocol-selection" href="#">MRI/MRA Liver Portal Hypertension</a></li>
                                                    <li><a class="protocol-selection" href="#">MRI/MRA Renal Hypertension</a></li>
						  </ul>
						</li>
                                             </ul>
                                          </li>                             
					  <li class="dropdown-submenu">
                                             <a tabindex="-1" href="#">Head</a>
                                             <ul class="dropdown-menu">
                                                <li><a class="protocol-selection" href="#">General Brain</a></li>
                                             </ul>
                                          </li>
                                          <li><a class="protocol-selection" href="#">Breast</a></li>
 					  <li class="dropdown-submenu">
                                             <a tabindex="-1" href="#">Pelvis</a>
                                             <ul class="dropdown-menu">
                                                <li class="dropdown-submenu">
                                                   <a tabindex="-1" href="#">Male</a>
                                                   <ul class="dropdown-menu">
                                                     <li><a class="protocol-selection" href="#">RSI Pelvis</a></li>
						   </ul>
						</li>
                                                <li class="dropdown-submenu">
                                                   <a tabindex="-1" href="#">Female</a>
                                                   <ul class="dropdown-menu">
                                                     <li><a class="protocol-selection" href="#">RSI Female Pelvis</a></li>
						   </ul>
						</li>
                                                <li><a class="protocol-selection" href="#">General Pelvis</a></li>
                                             </ul>
                                          </li>                             
					  <li><a class="protocol-selection" href="#">Whole Body</a></li>
                                       </ul>
				     </div>
				     <input type="text" class="form-control" aria-label="..." id="add-event-protocol">
				   </div>
                                </div>
                              </div>
                              <div class="form-group has-feedback">
                                <label class="control-label col-sm-3" for="add-event-notes">Protocol Notes</label>
                                <div class="col-sm-9">
				  <div class="input-group">
				     <input type="text" class="form-control" aria-label="..." id="add-event-notes">
				     <div class="input-group-btn">
				       <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select <span class="caret"></span></button>
				       <ul class="dropdown-menu dropdown-menu-right" id="notes-list">
                                       </ul>
				     </div>
				   </div>
                                   <!-- <select class="form-control projects" id="add-event-referring"></select> -->
                                </div>
                              </div>
			      <div class="input-group" style="display: none;">
				 <input type="text" id="add-event-id">
                              </div>

                            </form>
                            <button id="save-event-button" style="margin-top: 50px;" type="button" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
                            <button style="margin-top: 50px;" type="button" class="btn btn-primary" id="new-event-button">Clear</button>
                            <button id="delete-event-button" style="margin-top: 50px;" type="button" class="btn btn-warning pull-right"><i class="fa fa-close"></i> Delete Event</button>
                        </div>
                    </div>
                </div>
        </div>
	<div class="row">&nbsp;</div>
	<div class="row" style="max-height: 1000px; overflow-y=scroll;">
	  <div class="col-lg-8 col-lg-offset-2">
  	    <table class="table-striped table table-condensed" id="protocols-table">
	      <thead>
	        <tr>
		  <th>Name</th>
		  <th>Project</th>
		  <th>Referring</th>
		  <th>Section</th>
		  <th>Protocol</th>
		  <th>Notes</th>
		</tr>
	      </thead>
	      <tbody id="protocols-list"></tbody>
            </table>
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

  </div>
</div>

    <script src='../js/moment.min.js'></script>

    <!-- jQuery Version 1.11.0 -->
    <script src="../js/jquery-1.11.0.js"></script>
    <script src="../js/jquery-ui.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.10.10/js/dataTables.bootstrap.min.js"></script>

    <!-- allow users to download tables as csv and excel -->
    <script src="../js/excellentexport.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <script src="../js/bootstrap-datetimepicker.js"></script>
    <script src="../js/bootstrap-datepicker.js"></script>
    <script src="../js/bootstrap-colorselector.js"></script>

    <!-- Plugin JavaScript -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script src="../js/classie.js"></script>
    <script src="../js/cbpAnimatedHeader.js"></script>

    <!-- Contact Form JavaScript -->
    <script src="../js/jqBootstrapValidation.js"></script>
    <script src="../js/contact_me.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../js/agency.js"></script>
    <script src='../js/fullcalendar.min.js'></script>

    <script type="text/javascript">

      function reloadReferring() {
         jQuery('#referring-list').children().remove();
         jQuery.getJSON('/code/php/getReferring.php', function( refs ) {
           for (var i = 0; i < refs.length; i++) {
              jQuery('#referring-list').append('<li><a href="#" onclick="jQuery(\'#add-event-referring\').val(\'' + refs[i] + '\');">' + refs[i] + '</a></li>');
           }
           //jQuery('#add-event-referring').autocomplete({ source: refs });
         }); 
      }

      function reloadNotes() {
         jQuery('#notes-list').children().remove();
         jQuery.getJSON('/code/php/getNotes.php', function( refs ) {
           for (var i = 0; i < refs.length; i++) {
             jQuery('#notes-list').append('<li><a href="#" onclick="jQuery(\'#add-event-notes\').val(\'' + refs[i] + '\');">' + refs[i] + '</a></li>');
           }
           //jQuery('#add-event-notes').autocomplete({ source: refs });
         });
      }

      function reloadProtocols() {
         jQuery('#protocols-list').children().remove();
         jQuery.getJSON('/code/php/getProtocols.php', function( refs ) {
	   refs.sort(function(a,b) { return b.date - a.date; });

           for (var i = 0; i < refs.length; i++) {
	     var d = new Date(refs[i].date*1000);
             jQuery('#protocols-list').append('<tr prot-id="' + refs[i].id + '" title="last changed: ' + d.toDateString() + '"><td>'+ refs[i].name + '</td><td>' + refs[i].project + '</td><td>' + refs[i].referring + '</td><td>' + refs[i].section + '</td><td>' + refs[i].protocol + '</td><td>' + refs[i].notes + '</td></tr>');
           }
	   jQuery('#protocols-table').DataTable();
         });
      }

      jQuery('document').ready(function() {
	 if (typeof user_name != 'undefined') {
           jQuery('.user_name').text(user_name);
         } 

         jQuery(document).on('click', '.protocol-selection', function(event) {
	    event.preventDefault();
            jQuery('#add-event-protocol').val( jQuery(this).text() );
         });

         jQuery(document).on('click', '.section-selection', function(event) { 
 	    event.preventDefault();
            jQuery('#add-event-section').val( jQuery(this).text() );
         });


         jQuery.getJSON('/code/php/getProjects.php', function(data) {
            for (var i = 0; i < data.length; i++) {
		jQuery('#add-event-project-name').append("<option value=\"" + data[i].name + "\">" + data[i].name + "</option>");
            }
         });
	 reloadNotes();
	 reloadReferring();
	 reloadProtocols();

	 jQuery('#new-event-button').click(function() {
	    jQuery('#add-event-id').val("");
	    jQuery('#add-event-name').val("");
	    jQuery('#add-event-project').val("");
	    jQuery('#add-event-referring').val("");
	    jQuery('#add-event-section').val("");
	    jQuery('#add-event-protocol').val("");
	    jQuery('#add-event-notes').val("");	    

	    jQuery('#protocols-list').children().removeClass('highlight');
         });

	 jQuery('#save-event-button').click(function() {
 	    // store the current protocol
            name = jQuery('#add-event-name').val();
	    project = jQuery('#add-event-project-name').val();
	    referring = jQuery('#add-event-referring').val();
	    section = jQuery('#add-event-section').val();
	    protocol = jQuery('#add-event-protocol').val();
            notes = jQuery('#add-event-notes').val();
	    id = jQuery('#add-event-id').val();
            console.log("id is: " + id );
	    if (id == "") {
               jQuery.getJSON('/code/php/getProtocols.php?action=create&name=' + name + '&project=' + project + '&referring=' 
				+ referring + '&section=' + section + '&protocol=' + protocol + '&notes=' + notes, function(data)  {
                 //console.log('saved event, got id: ' + data.id);
                 setTimeout(function() {reloadProtocols();}, 100);
               });
	    } else {
               jQuery.getJSON('/code/php/getProtocols.php?action=change&id=' + id + '&name=' + name + '&project=' + project 
				+ '&referring=' + referring + '&section=' + section + '&protocol=' + protocol + '&notes=' + notes, function(data)  {
                 //console.log('saved event, got: ' + data);
                 setTimeout(function() {reloadProtocols();}, 100);
               });
            }
	    jQuery('#protocols-list').children().removeClass('highlight');
         });
	 jQuery('#delete-event-button').click(function() {
            id = jQuery('#add-event-id').val();
            //console.log('delete now: ' + id);
            jQuery.getJSON('/code/php/getProtocols.php?action=remove&id=' + id, function(data) {
		console.log('result on delete: ' + data.num);
		if (data.num > 0) {
                   setTimeout(function() {reloadProtocols();}, 100);
                }
            });
         });

	 jQuery('#protocols-list').on('click', 'tr', function(row) {
	    d = jQuery(row.target.parentElement).children();
	    id = jQuery(row.target.parentNode).attr('prot-id');
            //console.log('id for this protocol is: ' + id);
	    jQuery('#add-event-id').val( id );
	    jQuery('#add-event-name').val(jQuery(d[0]).text());
	    jQuery('#add-event-project').val(jQuery(d[1]).text());
	    jQuery('#add-event-referring').val(jQuery(d[2]).text());
	    jQuery('#add-event-section').val(jQuery(d[3]).text());
	    jQuery('#add-event-protocol').val(jQuery(d[4]).text());
	    jQuery('#add-event-notes').val(jQuery(d[5]).text());

	    jQuery(this).addClass('highlight').siblings().removeClass('highlight');
         });

      });
    </script>

</body>

</html>
