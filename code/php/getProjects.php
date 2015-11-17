<?php
  session_start(); /// initialize session
  include("AC.php");
  $user_name = check_logged(); /// function checks if visitor is logged.
  if (!$user_name || $user_name == "") {
     echo (json_encode ( array( "message" => "no user name" ) ) );
     return; // nothing
  }

  $project_file = "/data/Calendar/data/projects.json";

  function loadProjects() {
     global $project_file;

     // parse permissions
     if (!file_exists($project_file)) {
        echo ('error: project file does not exist');
        return;
     }
     if (!is_readable($project_file)) {
        echo ('error: cannot read file...');
        return;
     }
     $d = json_decode(file_get_contents($project_file), true);
     if ($d == NULL) {
        echo('error: could not parse the password file');
     }

     return $d;
  }

  function saveProjects( $d ) {
     global $project_file;

     // parse permissions
     if (!file_exists($project_file)) {
        echo ('error: project file does not exist');
        return;
     }
     if (!is_writable($project_file)) {
        echo ('Error: cannot write project file ('.$project_file.')');
        return;
     }
     // be more careful here, we need to write first to a new file, make sure that this
     // works and copy the result over to the pw_file
     $testfn = $project_file . '_test';
     file_put_contents($testfn, json_encode($d, JSON_PRETTY_PRINT));
     if (filesize($testfn) > 0) {
        // seems to have worked, now rename this file to pw_file
        rename($testfn, $project_file);
     } else {
        syslog(LOG_EMERG, 'ERROR: could not write file into '.$testfn);
     }
  }

  if (isset($_GET['action']))
    $action = $_GET['action'];
  else
    $action = null;

  if (isset($_GET['value']))
    $value = $_GET['value'];
  else
    $value = null;

  if (isset($_GET['value2']))
    $value2 = $_GET['value2'];
  else
    $value2 = null;

  if (isset($_GET['value3']))
    $value3 = $_GET['value3'];
  else
    $value3 = null;
  
  if (isset($_GET['value4']))
    $value4 = $_GET['value4'];
  else
    $value4 = null;

  if (isset($_GET['value5']))
    $value5 = urldecode($_GET['value5']);
  else
    $value5 = "#FF8C00";

  if (isset($_GET['value6']))
    $value6 = urldecode($_GET['value6']);
  else
    $value6 = "#";


  if ($action == "create") { // create a project
    if (!check_role( "admin" )) {
       return;
    }
    $d = loadProjects();

    $d[] = array( 'name' => $value, 
                  'description' => $value2, 
                  'scantime' => array( 'initial' => $value3, 'current' => 0), 
                  'users' => array( $user_name ), 
                  'admin' => array( $user_name ),
                  'timeperscan' => $value4,
                  'color' => $value5,
		  'irb' => $value6 );

    saveProjects($d);

    return;
  } else if ($action == "update") {
    if (!check_role( "admin" )) {
       return;
    }
    $d = loadProjects();
    $name            = $value;
    $description     = $value2;
    $scantimeinitial = $value3;
    $scantimecurrent = $value4;
    $color           = $value5;
    $irb             = $value6;

    foreach ($d as &$project) {
       if ($name == $project['name']) {
         $project['description'] = $description;
         $project['irb'] = $irb;
	 $project['scantime']['initial'] = $scantimeinitial;
	 $project['scantime']['current'] = $scantimecurrent;
  	 $project['color'] = $color;
       }
    }

    saveProjects($d);

    return;
  } else if ($action == "remove") {
    if (!check_role( "admin" )) {
       return;
    }


    echo(json_encode(array("message" => "done")));
    return;
  } else if ($action == "addUser") {
    if (!check_role( "admin" )) {
       return;
    }

    return;
  } else if ($action == "removeUser") {
    if (!check_role( "admin" )) {
       return;
    }

    return;
  } else {
    // use the current user and return the projects from that user
    $d = loadProjects();
    $projects = array();
    foreach ($d as $project) {
       foreach($project['users'] as $user) {
          if ($user == $user_name) {
             $projects[] = $project;
             break;
          }
       }
    }
    echo(json_encode($projects));
  }
?>
 