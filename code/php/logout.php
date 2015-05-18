<?php
 session_start();

 global $_SESSION;

 if (isset($_SESSION["logged"])) {
    unset($_SESSION["logged"]);
    header("Location: /index.php");
    return;
 } else {
    return;
 }

?>