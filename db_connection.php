<?php
function OpenCon()
 {
 $dbhost = "	sql304.epizy.com";
 $dbuser = "epiz_29918512";
 $dbpass = "Pranay1996@@";
 $db = "epiz_29918512_doc_app_db";
 $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);
 
 return $conn;
 }
 
function CloseCon($conn)
 {
 
 $conn -> close();
 }
   
?>
