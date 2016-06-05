<?php

   ob_start();
   session_start();
   echo "Logged in as: ".$_SESSION['username'];
// Fetch the results of the query




?>

<html>
<body>

<form action="testvarray.php" method="get">
    <input type="text" name="src" id="autocomplete1" />
    <input type="text" name="dest" id="autocomplete2" />
    <input type="submit" name="select" value="select" />
    <input type="checkbox" name="rapid" value="2">
</form>
\

Click here to clean <a href = "logout.php" tite = "Logout">Session.

<script   src="https://code.jquery.com/jquery-2.2.4.min.js"   integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="   crossorigin="anonymous"></script>
<script src="jquery.autocomplete.js"></script>

<script type="text/javascript">






var countries = [

<?php 
$conn = oci_connect("admin", "parola1234", "//localhost:1521/xe");
if (!$conn) {
   $m = oci_error();
   echo $m['message'], "\n";
   exit;
}



// Prepare the statement
$stid = oci_parse($conn, 'SELECT NAME, S_ID FROM STATII');
if (!$stid) {
    $e = oci_error($conn);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Perform the logic of the query
$r = oci_execute($stid);
if (!$r) {
    $e = oci_error($stid);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$t = true;
$a=0;
$t= ($res = oci_fetch_assoc($stid));

while( $t != FALSE){
    if(isset($res))
    echo "{ value: '".$res["NAME"]."', data: '".$res["S_ID"]."'}";    

    $t= ($res = oci_fetch_assoc($stid));

    if($res!=FALSE) echo ",";
    else $t = false;
    $a=$a+1;

}

oci_free_statement($stid);
oci_close($conn);
?>
];

$(document).ready(function(){
    

$('#autocomplete1').autocomplete({
    lookup: countries,
    onSelect: function (suggestion) {
        alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
        $('#autocomplete1').val(suggestion.data);
    }
});

$('#autocomplete2').autocomplete({
    lookup: countries,
    onSelect: function (suggestion) {
        alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
        $('#autocomplete2').val(suggestion.data);
    }
});
});
</script>
</body>
<html>
