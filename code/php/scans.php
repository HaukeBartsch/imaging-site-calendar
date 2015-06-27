<?php

  #
  # The scans.json file handeled by this script contains a list of actual scans
  # generated on the scanner. The scanner itself is on a secure network but
  # it runs a script (cron job) that tells our service about new scans. 
  # In order to implement this functionality the scanner script executes a 
  # findscu (Study level) and uses curl to call this script with the information 
  # for new scans.
  #

  $scan_file = "/data/Calendar/website/html/code/php/scans.json";

  function loadScans() {
     global $scan_file;

     // parse permissions
     if (!file_exists($scan_file)) {
        echo ('error: events file does not exist');
        // file_put_contents($scan_file, json_encode( array( array( )) ));
        // return;
     }
     if (!is_readable($scan_file)) {
        echo ('error: cannot read file...');
     }
     $d = json_decode(file_get_contents($scan_file), true);
     if ($d == NULL) {
        //echo('error: could not parse the events file');
	$d = array();
     }
     return $d;
  }

  function saveScans( $scans ) {
     global $scan_file;

     // parse permissions
     if (file_exists($scan_file) && !is_writable($scan_file)) {
        echo ('Error: cannot write events file ('.$scan_file.')');
        return;
     }
     // be more careful here, we need to write first to a new file, make sure that this
     // works and copy the result over to the pw_file
     $testfn = $scan_file . '_test';
     file_put_contents($testfn, json_encode($scans, JSON_PRETTY_PRINT));
     if (filesize($testfn) > 0) {
        // seems to have worked, now rename this file to pw_file
        rename($testfn, $scan_file);
     } else {
        syslog(LOG_EMERG, 'ERROR: could not write file into '.$testfn);
     }
  }

  if (isset($_GET['action'])) {
    $action = $_GET['action'];
  } else
    $action = null;

  if (isset($_GET['value']))
    $value = urldecode($_GET['value']);
  else
    $value = null;

  if ($action == "add") { // add a scan to the database
    $d = loadScans();
    $vals = explode(":", $value);
    $patientid=$vals[0];
    $patientname=$vals[1];
    $studydate=$vals[2];
    $studytime=$vals[3];
    $siuid=$vals[4];

    $d[] = array('PatientID' => $patientid, 'PatientName' => $patientname, 'StudyDate' => $studydate, 'StudyTime' => $studytime, 'SeriesInstanceUID' => $siuid);
 
    saveScans($d);

    echo (json_encode( array( 'message' => 'scan added', "ok" => 1)));
    return;
  } else {  // query the database
    // make sure we are allowed to see the results
    session_start(); /// initialize session
    include("AC.php");
    $user_name = check_logged(); /// function checks if visitor is logged.
    if (!$user_name || $user_name == "") {
       echo (json_encode ( array( "message" => "not logged in" ) ) );
       return; // nothing
    }

    $e = loadScans();

    echo(json_encode($e));
  }
?>
