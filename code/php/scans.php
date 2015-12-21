<?php

  #
  # The scans.json file handeled by this script contains a list of actual scans
  # generated on the scanner. The scanner itself is on a secure network but
  # it runs a script (cron job) that tells our service about new scans. 
  # In order to implement this functionality the scanner script executes a 
  # findscu (Study level) and uses curl to call this script with the information 
  # for new scans. Here is an example for the scanner script used to add
  # scans to this database:
  #   #!/bin/bash
  # 
  #   studies=$( { findscu -aet CTIPMUCSD2 -aec CTIPMUCSD1 --study -k 0008,0052=STUDY <scanner IP> 4006; } 2>&1 )
  #   db=/home/processing/bin/listOfStudiesDB.log
  #   count=0
  #   while read line; do
  #     if [[ $line =~ ^W:\ \(0020,0010\) ]]; then
  #       str="$patientName:$patientID:$studyDate:$studyTime:$studyInstanceUID:$accessionNumber:$referringPhysician:$studyDescription"
  #       # only send novel scans
  #       grep "$studyInstanceUID" "$db"
  #       if [[ "$?" > "0" ]]; then
  #         /usr/bin/curl -k -G --data-urlencode "action=add" --data-urlencode "value=$str" "https://<name of calendar website>/code/php/scans.php"
  #         echo $str >> $db
  #       fi
  #     fi
  #     val=`echo $line | cut -d'[' -f2 | cut -d']' -f1`
  #     if [[ $line =~ ^W:\ \(0008,0020\) ]]; then
  #       studyDate=$val
  #     fi
  #     if [[ $line =~ ^W:\ \(0008,0030\) ]]; then
  #       studyTime=$val
  #     fi
  #     if [[ $line =~ ^W:\ \(0010,0010\) ]]; then
  #       patientName=$val
  #     fi
  #     if [[ $line =~ ^W:\ \(0010,0020\) ]]; then
  #       patientID=$val
  #     fi
  #     if [[ $line =~ ^W:\ \(0020,000d\) ]]; then
  #       studyInstanceUID=$val
  #     fi
  #     if [[ $line =~ ^W:\ \(0008,1030\) ]]; then
  #       studyDescription=$val
  #     fi
  #     if [[ $line =~ ^W:\ \(0008,0050\) ]]; then
  #       accessionNumber=$val
  #     fi
  #     if [[ $line =~ ^W:\ \(0008,0090\) ]]; then
  #       referringPhysician=$val
  #     fi
  #
  #     count=$(( count + 1 ))
  #   done < <(echo "$studies")
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
    $patientid=$vals[1];
    $patientname=$vals[0];
    $studydate=$vals[2];
    $studytime=$vals[3];
    $siuid=$vals[4];
    if (count($vals) == 6 ) {
       $studyDescription=$vals[5];
       $accessionNumber='unknown';
       $referringPhysician='unknown';
    } 
    if (count($vals) == 7 ) {
       $accessionNumber=$vals[5];
       $studyDescription=$vals[6];
       $referringPhysician='unknown';
    }
    if (count($vals) == 8 ) {
       $accessionNumber=$vals[5];
       $studyDescription=$vals[7];
       $referringPhysician=$vals[6];
    }

    $d[] = array('PatientID' => $patientid, 'PatientName' => $patientname, 'StudyDate' => $studydate, 'StudyTime' => $studytime, 'StudyInstanceUID' => $siuid, 
    	         'AccessionNumber' => $accessionNumber, 'ReferringPhysician' => $referringPhysician, 'StudyDescription' => $studyDescription );
    audit("create scan", " -> patientid: ".$patientid); 
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
