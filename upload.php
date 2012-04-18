
<?php
//phpinfo();

//include 'config.php';
//include ('opendb.php');
include 'open.php';
//include 'closedb.php';

set_time_limit(0);
ini_set ( 'mssql.textlimit' , '65536' );
ini_set ( 'mssql.textsize' , '65536' );

$conn;

function insertGenericValue($variableArray, $SQL, $theevent, $server, $api_token, $db)
{
$conn = connect($db);
if(!connected($conn)) echo "</br>Failed to upload.";
else{
	echo $SQL;
	echo "</br> Uploading data to " .$theevent.".";
	$result = odbc_exec($conn, $SQL);
	$i = 1;

	$myData="";
	$currentRN="";
	$previousRN="";
	$initialEvenName=9;
	$counter=1;
	$eventName="";
	$previousCodeValue="";

	while ($db_field = odbc_fetch_row($result)  ):
	//while ($db_field = mssql_fetch_assoc($result)  ):
 	if(odbc_result($result,"mrn")<>"")
 	{
 	
 		 if(odbc_result($result,"mrn")>=1)
 		 {
 			if($counter==1)
 				$previousRN= odbc_result($result,"mrn");
 			
			$myData="";
		
			$currentRN	= odbc_result($result,"mrn");
				if ($previousRN==$currentRN)
				{
				  $eventName=$initialEvenName + $i;	
				  $i++;
				}
				else 
				{
					$i=1;
					$eventName=$initialEvenName + $i;	
					$i++;
				}
				
				$eventName=$eventName .'_arm_1';	

			//print $currentRN ." ".$eventName."<br>";
			$myData="record,field_name,value,redcap_event_name\r	";
			
			foreach($variableArray as $theVariable)
			{
				$myData=$myData .odbc_result($result,"mrn").",";	
				$myData=$myData .$theVariable;
			
			//	if (($db_field[$theVariable]) )
			//	$myData=$myData .",\"" .$db_field[$theVariable] ."\"";
			$myData=$myData .",\"" .odbc_result($result,$theVariable) ."\"";
			
				//	else 
				//	$myData=$myData ."," ;
				$myData=$myData ."," .$eventName."\r"; 
			
			}
								
			$myData=$myData ."\r";
			$myData=$myData .odbc_result($result,"mrn");
			$myData=$myData .",". $theevent . "_complete";
			$myData=$myData .",2";
			$myData=$myData ."," .$eventName."\r";
		
	
		
	if ($counter>=1)
	{
		
	$YOUR_DATA = <<<DATA
$myData
DATA;

//print $YOUR_DATA;

print "<br><br>Inserting ". $theevent . " record " .$counter ." ). " .$myData ." <br>";


# an array containing all the elements that must be submitted to the API
$data = array('content' => 'record', 'type' => 'eav', 'format' => 'csv', 'token' => $api_token, 
	'data' => $YOUR_DATA);

# create a new API request object
$request = new RestCallRequest($server, 'POST', $data);

# initiate the API request
$request->execute();


print "<pre" . print_r($request, true) . '</pre>';
}


}

	$previousRN=$currentRN;
	

$counter++;
$previousRN=$currentRN;
//$previousCodeValue=$db_field['general_note'];
				
		
 }
 //}
endwhile;

printf ("</br>Completed attempt to insert " . $theevent . " record. </br></br>");
mysql_close($conn);
}}
?>