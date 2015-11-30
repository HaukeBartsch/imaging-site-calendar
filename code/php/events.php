<?php
  // TODO: add index number field to each scan in report view

  session_start(); /// initialize session
  include("AC.php");
  $user_name = check_logged(); /// function checks if visitor is logged.
  if (!$user_name || $user_name == "") {
     echo (json_encode ( array( "message" => "no user name" ) ) );
     return; // nothing
  }

  $project_file = "/data/Calendar/data/projects.json";
  $events_file = "/data/Calendar/data/events.json";

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
        echo('error: could not parse the project file');
     }

     return $d;
  }

  function loadEvents() {
     global $events_file;

     // parse permissions
     if (!file_exists($events_file)) {
        //echo ('error: events file does not exist');
        file_put_contents($events_file, json_encode( array( array( 'title' => 'Some Title' )) ));
        // return;
     }
     if (!is_readable($events_file)) {
        echo ('error: cannot read file...');
        return;
     }
     $d = json_decode(file_get_contents($events_file), true);
     if ($d == NULL) {
        //echo('error: could not parse the events file');
     }

     return $d;
  }

  // we need to update the current time on a project is a new event appears
  function saveProjects( $projects ) {
    global $project_file;

     // parse permissions
     if (!file_exists($project_file)) {
        echo ('error: projects file '.$project_file.' does not exist');
        return;
     }
     if (!is_writable($project_file)) {
        echo ('Error: cannot write projects file ('.$project_file.')');
        return;
     }
     // be more careful here, we need to write first to a new file, make sure that this
     // works and copy the result over to the pw_file
     $testfn = $project_file . '_test';
     file_put_contents($testfn, json_encode($projects, JSON_PRETTY_PRINT));
     if (filesize($testfn) > 0) {
        // seems to have worked, now rename this file to pw_file
        rename($testfn, $project_file);
     } else {
        syslog(LOG_EMERG, 'ERROR: could not write file into '.$testfn);
     }
  }

  function saveEvents( $events ) {
     global $events_file;

     // parse permissions
     if (!file_exists($events_file)) {
        echo ('error: events file does not exist');
        return;
     }
     if (!is_writable($events_file)) {
        echo ('Error: cannot write events file ('.$events_file.')');
        return;
     }
     // be more careful here, we need to write first to a new file, make sure that this
     // works and copy the result over to the pw_file
     $testfn = $events_file . '_test';
     file_put_contents($testfn, json_encode($events, JSON_PRETTY_PRINT));
     if (filesize($testfn) > 0) {
        // seems to have worked, now rename this file to pw_file
        rename($testfn, $events_file);
     } else {
        syslog(LOG_EMERG, 'ERROR: could not write file into '.$testfn);
     }
  }

  function getTimeSpendTimeForProject($project, $e) {
    // now update current time for this project
    $ct = 0; // sum of times used up by events for this project
    foreach($e as $event) {
      if ($event['project'] != $project)
        continue;
      try {
        $dateA = DateTime::createFromFormat(DateTime::ATOM, $event['start']); //new DateTime($event['start']);
        $dateB = DateTime::createFromFormat(DateTime::ATOM, $event['end']); //new DateTime($event['end']);
        //if (is_object($dateB)) {
        //  echo ($dateB->format(DateTime::ATOM));
        //} else {
        //  echo ("could not convert ". $event['end'] . " into DateTime. ".$_GET['value3']." ". $value3."\n");
        //}
      } catch (Exception $ex) {
        print_r(DateTime::getLastErrors());
      }
      try {
        if (is_object($dateB) && is_object($dateA)) {
          $interval = $dateB->diff($dateA);
        } else {
          continue; // give up here - should never happen
        }
      } catch (Exception $ex) {
        print_r(DateTime::getLastErrors());        
      }
      $hours    = $interval->h;
      $minutes  = $interval->i;
      $hours    = $hours + ($interval->days*24) + ($minutes/60.0);
      $ct = $ct + $hours;
    }
    return $ct;
  }


  if (isset($_GET['action']))
    $action = $_GET['action'];
  else
    $action = null;

  if (isset($_GET['value']))
    $value = rawurldecode($_GET['value']);
  else
    $value = null;

  if (isset($_GET['value2']))
    $value2 = rawurldecode($_GET['value2']);
  else
    $value2 = null;

  if (isset($_GET['value3']))
    $value3 = rawurldecode($_GET['value3']);
  else
    $value3 = null;
  
  if (isset($_GET['value4']))
    $value4 = rawurldecode($_GET['value4']);
  else
    $value4 = null;

  if (isset($_GET['value5']))
    $value5 = rawurldecode($_GET['value5']);
  else
    $value5 = null;

  if (isset($_GET['value6']))
    $value6 = rawurldecode($_GET['value6']);
  else
    $value6 = null;

  if (isset($_GET['value7']))
    $value7 = rawurldecode($_GET['value7']);
  else
    $value7 = null;

  if (isset($_GET['value8']))
    $value8 = rawurldecode($_GET['value8']);
  else
    $value8 = null;

  if (isset($_GET['value9']))
    $value9 = rawurldecode($_GET['value9']);
  else
    $value9 = null;

  if (isset($_GET['start']))
    $start = rawurldecode($_GET['start']);
  else
    $start = null;

  if (isset($_GET['end']))
    $end = rawurldecode($_GET['end']);
  else
    $end = null;

  if (isset($_GET['project']))
    $project = rawurldecode($_GET['project']);
  else {
    $project = null;
  }



  if ($action == "create") { // TODO: do not create anything that is in the past
    //if (!check_role( "admin" )) {
    //   return;
    //}
    $d = loadProjects();
    // check if the current user is allowed to remove from this project (admin)
    $projects = array();
    foreach ($d as $p) {
       foreach($p['users'] as $user) {
          if ($user == $user_name) {
             $projects[] = $p['name'];
          }
       }
    }
    if (!in_array($project, $projects)) {
      echo(json_encode(array("message" => "user ".$user_name." not allowed to create this event", "ok" => 0, "project" => $project, "projects" => implode(",",$projects))));
      return;
    }

    $e = loadEvents();
    $eid = uniqid();

    $e[] = array('scantitle' => $value, 'start' => $value2, 'end' => $value3, 'project' => $project, 'user' => $user_name, 'eid' => $eid, 'noshow' => $value5, 'referrer' => $value6, 'reader' => $value7, 'protocol' => $value8, 'section' => $value9);
    audit("create event", " -> scantitle: \"". $value. "\", " . $value2 . ", " . $value3 . ", " . $project . ", " . $user_name . ", " . $eid. ", ". $value5 .", ".$value6.", ".$value7.", ".$value8.", ".$value9);
 
    saveEvents($e);

    $ct = getTimeSpendTimeForProject($project, $e);

    foreach ($d as &$p) {
      if ($p['name'] == $project) {
        $p['scantime']['current'] = $ct;
        saveProjects( $d );
        break;
      }
    }

    echo (json_encode( array( 'message' => 'event added', 'eid' => $eid, "ok" => 1)));
    return;
  } else if ($action == "remove") { // TODO: do not remove anything that is in the past
    //if (!check_role( "admin" )) {
    //   return;
    //}
    $scantitle   = $value;
    $start       = $value2;
    $end         = $value3;
    $eid         = $value4;
    audit("remove event", " -> \"". $scantitle. "\", " . $start . ", " . $end . ", " . $project . ", " . $user_name . ", " . $eid);

    $d = loadProjects();
    // check if the current user is allowed to remove from this project (admin)
    $projects = array();
    foreach ($d as $p) {
       foreach($p['users'] as $user) {
          if ($user == $user_name) {
             $projects[] = $p['name'];
          }
       }
    }
    if (!in_array($project, $projects)) {
      echo(json_encode(array("message" => "user not allowed to delete this event")));
      return;
    }

    $e = loadEvents();
    // identify the event just by the event id
    foreach ($e as $key => $event) {
      if ($event['eid'] == $eid) {
        // found the event, delete it now
        //echo("delete the event now\n");
        unset($e[$key]);
        saveEvents(array_values($e)); // this removes keys again

        $ct = getTimeSpendTimeForProject($project, $e);

        foreach ($d as &$p) {
          if ($p['name'] == $project) {
            $p['scantime']['current'] = $ct;
            saveProjects( $d );
            echo(json_encode(array("message" => "event deleted", "ok" => 1)));
            return;
          }
        }
        echo(json_encode(array("message" => "error, could not find correct project", "ok" => 0)));
        return;
      }
    }

    echo(json_encode(array("message" => "event not found", "ok" => 0)));
    return;
  } else if ($action == "update") {
    //if (!check_role( "admin" )) {
    //   return;
    //}
    $scantitle   = $value;
    $start       = $value2;
    $end         = $value3;
    $eid         = $value4;
    $noshow      = $value5;
    $referrer    = $value6;
    $reader      = $value7;
    $protocol    = $value8;
    $section     = $value9;

    $d = loadProjects();
    // check if the current user is allowed to remove from this project (admin)
    $projects = array();
    foreach ($d as $p) {
       foreach($p['users'] as $user) {
          if ($user == $user_name) {
             $projects[] = $p['name'];
          }
       }
    }
    if (!in_array($project, $projects)) {
      echo(json_encode(array("message" => "user not allowed to change this event")));
      return;
    }

    $e = loadEvents();
    // identify the event just by the event id
    foreach ($e as $key => &$event) {
      if ($event['eid'] == $eid) {
        // found the event, change it now
      	$event['scantitle'] = $scantitle;
        $event['start'] = $start;
      	$event['end']   = $end;
        // remember the old project in case this was changed
        $oldProject       = $event['project'];
        $event['project'] = $project; // now set a new project name
        $event['noshow']  = $noshow;
	$event['referrer'] = $referrer;
	$event['reader']   = $reader;
	$event['protocol'] = $protocol;
	$event['section']  = $section;

        //echo(json_encode(array("message" => "save changed events")));
        saveEvents(array_values($e)); // this removes keys from the array

	// this is for the new project, but in case we have changed projects we 
	// need to do the same for the old project (update its global time)
        $ct = getTimeSpendTimeForProject($project, $e); 
        
        $changed = false;
        foreach ($d as &$p) {
          if ($p['name'] == $project) {
            $p['scantime']['current'] = $ct;
	    $changed = true;
          }
          if ($oldProject != $project && $p['name'] == $oldProject) {
            $ct = getTimeSpendTimeForProject($oldProject, $e);
            $p['scantime']['current'] = $ct;
	    $changed = true;
          }
        }
        if ($changed == true) {
            saveProjects( $d );
            audit("update event", " -> ". $scantitle. ", " . $start . ", " . $end . ", " . $project . ", " . $user_name . ", " . $eid. ", ". $noshow);
            echo(json_encode(array("message" => "event changed right now", "ok" => 1)));
            return;
        } else {
            echo(json_encode(array("message" => "error, could not find the correct event", "ok" => 0)));
        }
        return;
      }
    }

    echo(json_encode(array("message" => "event not found", "ok" => 0)));
    return;    
  } else if ( $start != null ) { // called by fullcalendar
    $e = loadEvents();
    $d = loadProjects(); // get color from here

    $startdate = DateTime::createFromFormat('Y-m-d', $start);
    $enddate   = DateTime::createFromFormat('Y-m-d', $end);

    $events = [];
    foreach ($e as $key => $event) {
      $dateA = DateTime::createFromFormat(DateTime::ATOM, $event['start']);
      $dateB = DateTime::createFromFormat(DateTime::ATOM, $event['end']);

      if ( ($dateA >= $startdate && $dateA <= $enddate) ||
           ($dateB >= $startdate || $dateB <= $enddate)) {
        // find out if we the current user is user of this events project (show color)
	$showColor = false;
        foreach( $d as $p ) {
           if ( $p['name'] != $event['project'] ) {
              continue;
           }
	   foreach( $p['users'] as $user ) {
	      if ($user == $user_name) {
	         $showColor = true;
              }
           }
        }
        if ( $showColor == true ) {
          $event['title'] = $event['project'] . ": " . $event['scantitle'] . "(" . $event['user'] . ")";
          foreach ($d as $p) {
  	    if ($p['name'] == $event['project']) {
              $event['backgroundColor'] = $p['color'];
              break;
            }
          }
        } else {
          $event['title'] = "Booked (" . $event['user'] . ")";
        }
        $events[] = $event;
      }
    }
    echo(json_encode(array_values($events)));
  } else {
    $e = loadEvents();

    echo(json_encode($e));
  }
?>
