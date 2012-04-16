<?php

$conn=odbc_connect($_REQUEST['local_db'],"" ,"");

if($conn){
	print "Connection to the Server opened";
	$openerr = 0;
}
else {
	print "Error opening connection to ". $_REQUEST['local_db'];
	$openerr = 1;
}
	
//include "index.php";
	
//$link=mssql_connect($dbhost, $dbuser, $dbpass) or die(mssql_error());

//if (!$link || !mssql_select_db('php', $link)) 
    //die('Unable to connect or select database!');
//else 
//	print "Connection to the Server opened";

//mssql_select_db($dbname)or die ('Error connecting to mysql');


//$db = 'C:\Inetpub\wwwroot\Noth\Noth.mdb';

//$conn = new COM('ADODB.Connection') or exit('Cannot start ADO.');

// Two ways to connect. Choose one.
//$conn->Open("Provider=Microsoft.Jet.OLEDB.4.0; Data Source=$db") or exit('Cannot open with Jet.');
//$conn->Open("DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$db") or exit('Cannot open with driver.');



?>