<?php
/* Un program PHP care proceseaza un document XML folosind modelul DOM
   Functioneaza pentru PHP versiunea 5.x
   
   Autor: Sabin-Corneliu Buraga (2004, 2007, 2011)
   Ultima actualizare: 05 aprilie 2011
*/     
// locul unde sunt stocate fisierele
define ("PATH", 'C:\\Users\\radur\\Documents\\'); //DE MODIFICAT ;) locul unde e .xml -ul 
# De exemplu, pentru Windows cu EasyPHP instalat in 'Program Files\EasyPHP':
# define ("PATH", 'c:\\Program Files\\EasyPHP\\www\\');

// variabila globala indicand daca a fost modificat documentul
$modified = 0;
$conn = oci_connect("admin", "parola1234", "//localhost:1521/xe");
if (!$conn) {
   $m = oci_error();
   echo $m['message'], "\n";
   exit;
}
else {
   print "Connected to Oracle!";
}
// Close the Oracle connection

// instantiem un obiect reprezentand arborele DOM
set_time_limit ( 600 );
$doc = new DomDocument;
try {

  $doc->load (PATH . "mers-trensntfc2015-2016.xml");
  echo PATH . "mers-trensntfc2015-2016.xml";

  $trenuri = $doc->getElementsByTagName("XmlMts")->item(0);
  $trenuri = $trenuri->getElementsByTagName("Mt")->item(0);
  $trenuri = $trenuri->getElementsByTagName("Trenuri")->item(0);
  $trenuri = $trenuri->getElementsByTagName("Tren");
  
  
  
  $i=0;
  foreach ($trenuri as $tren) {
    $i=$i+1;

    $categorie = $tren->getAttribute("CategorieTren");
    $tr_id = $tren->getAttribute("Numar");
    if (!is_numeric($tr_id) )continue;

    $stid = oci_parse($conn, "insert into trains(tr_id,category) values(:tr_id, :category)");
    oci_bind_by_name($stid, ':tr_id', $tr_id);
    oci_bind_by_name($stid, ':category', $category);
    oci_execute($stid);




    $trase = $tren->getElementsByTagName("Trase")->item(0);

    $trasa = $trase->getElementsByTagName("Trasa")->item(0);

    $dest_id = $trasa->getAttribute("CodStatieFinala");
    $source_id = $trasa->getAttribute("CodStatieInitiala");
    echo $dest_id. ' '. $source_id.'\n';
     $stid = oci_parse($conn, "insert into mainroute(mr_id, tr_id,source_id,dest_id,internals) values(13,:tr_id, :source_id, :dest_id,0)");

    oci_bind_by_name($stid, ':tr_id', $tr_id);
    oci_bind_by_name($stid, ':source_id', $source_id);
    oci_bind_by_name($stid, ':dest_id', $dest_id);
    oci_execute($stid);

    $stid = oci_parse($conn, "select mr_id from mainroute  where tr_id=".$tr_id."");  
    oci_execute($stid);

    $mr_id = oci_fetch_row($stid);
    $mr_id = $mr_id[0];
    $elementTrasa = $trasa->getElementsByTagName("ElementTrasa");

    foreach ($elementTrasa as $internals) {

      $dest_id = $internals->getAttribute("CodStaDest");
      $source_id = $internals->getAttribute("CodStaOrigine");
      $source_name = $internals->getAttribute("DenStaOrigine");
      echo $source_name;
      $source_name = str_replace(array("ş","ţ","ă","â","î","Ă","Â","Ş","Ţ","Î"),array("s","t","a","a","i","A","A","S","T","I"),$source_name);
      $dest_name = $internals->getAttribute("DenStaDestinatie");
      
      $dest_name = str_replace(array("ş","ţ","ă","â","î","Ă","Â","Ş","Ţ","Î"),array("s","t","a","a","i","A","A","S","T","I"),$dest_name);
      
      $dep_hour = $internals->getAttribute("OraP");
      $arr_hour = $internals->getAttribute("OraS");
      $delay = $internals->getAttribute("StationareSecunde");
      $secventa = $internals->getAttribute("Secventa");
      $length = $internals->getAttribute("Km");
      $time_to = $arr_hour-$dep_hour;

      if(!isset($mr_id)) echo "DA";
      if($mr_id=="") echo "da1";
      if(!isset($dest_id)) echo "DA2";
      if($dest_id=="") echo "da2";
      if(!isset($source_id)) echo "DA3";
      if($source_id=="") echo "da3";

      oci_free_statement($stid);
      $stid = oci_parse($conn, "insert into internalroute(ir_id,mr_id,dest_id,source_id,source_name,dest_name,leni,time_to) values(1,:mr_id,:dest_id,:source_id,:source_name,:dest_name,:length,:time_to)");

      oci_bind_by_name($stid, ':mr_id', $mr_id);   
      oci_bind_by_name($stid, ':source_id', $source_id);   
      oci_bind_by_name($stid, ':dest_id', $dest_id);
      oci_bind_by_name($stid, ':source_name', $source_name);   
      oci_bind_by_name($stid, ':dest_name', $dest_name); 
      oci_bind_by_name($stid, ':length', $length);
      oci_bind_by_name($stid, ':time_to',$time_to );      
      oci_execute($stid);
    }		

  }
} 
catch (Exception $e) {
	die ("A survenit o exceptie...");
}

echo $i;

oci_close($conn);
?>
