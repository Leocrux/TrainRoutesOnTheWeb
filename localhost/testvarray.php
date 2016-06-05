<?php
   ob_start();
   session_start();
   echo "Logged in as: ".$_SESSION['username'];

if(!isset($_GET["src"]))
	$_GET["src"] = 23428;


if(!isset($_GET["dest"]))
	$_GET["dest"] = 10938;

$conn = oci_connect("admin", "parola1234", "//localhost:1521/xe");
if (!$conn) {
   $m = oci_error();
   echo $m['message'], "\n";
   exit;
}
else {
   echo "Connected to Oracle!";
}

$opt = 1;

if (isset($_GET['rapid']))
	$opt = 2;

$array = ARRAY();
$stid = oci_parse($conn, "BEGIN	dijkstra.drum(".$_GET["src"].",".$_GET["dest"].",".$_SESSION['username'].",".$opt.");
	dijkstra.iobind_prev(:c1,1); END;");  
oci_bind_array_by_name($stid, ":c1", $array,1000,-1,SQLT_INT);
 oci_execute($stid);

 //$mr_id = $mr_id[0];
echo "vardump(array):";
echo "<br>";
var_dump($array);
echo "<br>";
?>




Click here to <a href = "delete.php" tite = "Logout">delete records</a>


Click here to clean <a href = "logout.php" tite = "Logout">Session.</a>