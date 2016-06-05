<?php

 ob_start();
   session_start();
   if(isset($_SESSION['username']))
      echo "Logged in as: ".$_SESSION['username'];

$conn = oci_connect("admin", "parola1234", "//localhost:1521/xe");
if (!$conn) {
   $m = oci_error();
   echo $m['message'], "\n";
   exit;
}
else {
   echo "Connected to Oracle!";
}
                       
                           $stid = oci_parse($conn, "DELETE from USER_SAVED where u_id= ".$_SESSION['username']);  
                           oci_execute($stid);
?>