<?php
  session_start(); /// initialize session
  include("AC.php");
  $user_name = check_logged(); /// function checks if visitor is logged.
  if (!$user_name)
     return; // nothing

  $ref_file = "/data/Calendar/data/referring.json";
  function loadReferrer() {
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

  function saveReferrer( $refs ) {
    global $ref_file;

     // parse permissions
     if (!file_exists($ref_file)) {
        echo ('error: referrer file '.$ref_file.' does not exist');
        return;
     }
     if (!is_writable($ref_file)) {
        echo ('Error: cannot write projects file ('.$ref_file.')');
        return;
     }

     // lets sort the referrers alphabetically first
     sort($refs);

     echo ("{ \"message\": \"save these values: " . join($refs) . "\"}");

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

  function addReferring( $name ) {
     $d = loadReferrer();
     if ( in_array($name, $d) ) {
        return; // already present
     }
     array_push($d, $name);
     saveReferrer($d);
  }
  function removeReferring( $name ) {
     $d = loadReferrer();
     if ( in_array($name, $d) == False) {
        echo("{ \"message\": \"key ".$name." not found in list of referrers\"}"); 
        return; // nothing to do
     }
     unset($d[array_search($name,$d)]);
     saveReferrer(array_values($d));
  }

  if (isset($_GET['action']))
    $action = $_GET['action'];
  else
    $action = null;
  if (isset($_GET['value']))
    $value = $_GET['value'];
  else
    $value = null;

  if ($action == "create") {
    $value = str_replace('"', '', $value);
    if ($value == "") {
       return;
    }
    $id = addReferring( $value );
    return;
  } else if ($action == "remove") {
    removeReferring( $value );
    echo(json_encode(array("message" => "done")));
    return;  
  } else {
    $d = loadReferrer();
    echo(json_encode( $d ));
    return;
  }
 ?>
 