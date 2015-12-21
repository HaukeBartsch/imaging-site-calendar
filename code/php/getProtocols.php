<?php
  date_default_timezone_set('America/Los_Angeles');
  session_start(); /// initialize session
  include("AC.php");
  $user_name = check_logged(); /// function checks if visitor is logged.
  if (!$user_name)
     return; // nothing
  if (!check_permission("can-order")) {
     echo ("{ \"message\": \"no permission to order for this user\" }");
     return; // do nothing
  }

  $ref_file = "/data/Calendar/data/protocols.json";
  function loadProtocols() {
     global $ref_file;

     // parse permissions
     if (!file_exists($ref_file)) {
        echo ('error: referring file does not exist');
        return;
     }
     if (!is_readable($ref_file)) {
        echo ('error: cannot read file '.$ref_file.'...');
        return;
     }
     $d = json_decode(file_get_contents($ref_file), true);
     if (!is_array($d)) {
        echo('error: could not parse the ref file, contained: '.$d.'\n');
     }
     return $d;
  }

  function saveProtocols( $refs ) {
    global $ref_file;

     // parse permissions
     if (!file_exists($ref_file)) {
        echo ('error: notes file '.$ref_file.' does not exist');
        return;
     }
     if (!is_writable($ref_file)) {
        echo ('Error: cannot write projects file ('.$ref_file.')');
        return;
     }

     // lets sort the referrers alphabetically first
     //sort($refs);

     //echo ("{ \"message\": \"save these values: " . join($refs) . "\"}");

     // be more careful here, we need to write first to a new file, make sure that this
     // works and copy the result over to the pw_file
     $testfn = $ref_file . '_test';
     file_put_contents($testfn, json_encode($refs, JSON_PRETTY_PRINT));
     if (filesize($testfn) > 0) {
        // seems to have worked, now rename this file to pw_file
        rename($testfn, $ref_file);
     } else {
        syslog(LOG_EMERG, 'ERROR: could not write file into '.$testfn);
     }
  }

  function findNewID( $d ) {
     for ($id = 0; $id < 1000000; $id++) {
       $found = 0;
       foreach ( $d as $d1 ) {
          if ($d1['id'] == $id) {
	     $found = 1;
          }
       }
       if ($found == 0) {
          return $id;
       }
     }
     echo ("no id found");
  }

  function addProtocol( $name, $project, $referring, $section, $protocol, $notes ) {
     $d = loadProtocols();
     // create a new id
     $id = findNewID( $d );
     array_push($d, array( 'id' => $id, 'name' => $name, 'project' => $project, 'referring' => $referring, 'section' => $section, 'protocol' => $protocol, 'notes' => $notes, 'date' => time() ) );
     saveProtocols($d);
     return $id;
  }
  function changeProtocol( $id, $name, $project, $referring, $section, $protocol, $notes ) {
     $d = loadProtocols();
     foreach ( $d as &$prot ) {
        if ($prot['id'] == $id ) {
	   // found what we are looking for, replace this entry with the updated one
	   $prot['name'] = $name;
	   $prot['project'] = $project;
	   $prot['referring'] = $referring;
	   $prot['section'] = $section;
	   $prot['protocol'] = $protocol;
	   $prot['notes'] = $notes;
	   $prot['date'] = time(); // time last changed
	   break;
        }
     }
     saveProtocols($d);
  }
  function removeProtocol( $id ) {
     $d = loadProtocols();
     $found = 0;
     foreach ($d as $key => $ds ) {
        if ( $ds['id'] == $id ) {
           unset($d[$key]);
	   $found = $found + 1;
        }
     }
     if ($found == 1) {
       saveProtocols(array_values($d));
     }
     return $found;
  }

  if (isset($_GET['action']))
    $action = $_GET['action'];
  else
    $action = null;

  if (isset($_GET['id']))
    $id = $_GET['id'];
  else
    $id = null;

  if (isset($_GET['name']))
    $name = $_GET['name'];
  else
    $name = null;

  if (isset($_GET['project']))
    $project = $_GET['project'];
  else
    $project = null;

  if (isset($_GET['referring']))
    $referring = $_GET['referring'];
  else
    $referring = null;

  if (isset($_GET['section']))
    $section = $_GET['section'];
  else
    $section = null;

  if (isset($_GET['protocol']))
    $protocol = $_GET['protocol'];
  else
    $protocol = null;

  if (isset($_GET['notes']))
    $notes = $_GET['notes'];
  else
    $notes = null;


  if ($action == "create") { // create a new id add to protocols
    $id = addProtocol( $name, $project, $referring, $section, $protocol, $notes ); 
    echo ("{ \"id\": ".$id."}");
    return;
  } else if ($action == "remove") {
    $num = removeProtocol( $id );
    echo ("{ \"num\": ".$num."}");
    return;  
  } else if ($action == "change") {  
    changeProtocol( $id, $name, $project, $referring, $section, $protocol, $notes );
    echo ("{ \"message\": \"done\" }");
    return;  
  } else {
    $d = loadProtocols();
    echo(json_encode( $d ));
    return;
  }
 ?>
 