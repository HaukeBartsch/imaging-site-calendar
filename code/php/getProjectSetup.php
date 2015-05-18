<?php

  $action = "";
  if (isset($_GET['action'])) {
     $action = $_GET['action'];
  }
  $content = "";
  if (isset($_POST['content'])) {
     $content = $_POST['content'];
  } 

  $dbfile = "../../data/projects.json";
  $dbfiletmp = "../../data/projects_temp.json";

  if ($action == "save") {
    # check if the file system is full first by writing to a temporary file
    file_put_contents($dbfiletmp, $content);
    # check if the file is not empty
    if (filesize($dbfiletmp) > 0) {
       $ok = rename($dbfiletmp, $dbfile);
       if (!ok) {
	  echo ("{ \"message\": \"Error on writing db file ".$dbfile." \" }");
       }
    }
  } else {
    echo(file_get_contents($dbfile));
  }
?>
