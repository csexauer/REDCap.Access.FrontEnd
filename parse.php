<?php
	/* BASIC PROGRAM FLOW
	 * 	REQUEST TO DELETE? 
	 * 	yes										     no
	 *  clear session, set supress					 do nothing
	 * 	v							<---------------|
	 *  MAIN FORM VALUES SENT?
	 * 	yes											 no
	 * 	FULL SCRIPT?							     print form
	 *  yes						no			         v
	 * 	DIRECT UPLOAD?			print func		     v
	 * 	yes						no					 v
	 * 	ADV SETTINGS SET		print script		 v
	 * 	yes		no				v  					 v
	 *  UPLOAD	ERROR			v					 v
	 *  
	 */
	session_start();
?>

<!--WRITTEN BY CRAIG SEXAUER-->
<!--FOR THE UNIVERSITY OF CHICAGO BIOLOGICAL SCIENCES DIVISION-->
<!--APRIL 2012-->
<!--https://github.com/csexauer/REDCap.Access.FrontEnd-->

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <?php
  if(!isset($_REQUEST['redcap']))
	 echo '<title>Access->REDCap Transfer</title>';
  else if(isset($_REQUEST['fullprgm']) and $_REQUEST['fullprgm'])
  	echo '<title>Import Script</title>';
  else 
  	echo '<title>Import Function</title>';
  ?>
  <meta name="description" content="Access to REDCap parser">
  <meta name="author" content="Craig Sexauer">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>
</script>
<style type="text/css">
	body{
		background-color: F0F0F0;
	}
  	p{
  		margin-bottom: 1.5em;
  	}
  	label{
  		font-weight: bold;
  		margin-bottom: 5px;
  	}
  	.smt{
  		margin-top: 5px;
  		margin-left:0;
  		display: block;
  		margin-bottom: 15px;
  		width: 300px;
  		border-radius: 3px;
    	-moz-border-radius: 3px;
 	    border: 1px solid black;
  	}
  	textarea{
  		resize: none;
  		margin-top: 10px;
  		width: 800px;
  		display: block;
 	    border-radius: 3px;
     	-moz-border-radius: 3px;
  	}
  	#container{
  		background-color: #F8F8F8;
  		margin-top: 50px;
  		margin-bottom: 50px;
  		width: 800px;
  		margin-left: auto;
  		margin-right: auto;
  		padding-left: 50px;
  		padding-right: 50px;
  		padding-top: 25px;
  		padding-bottom: 50px;
  		border: 2px dotted grey;
  		 border-radius: 10px;
    	-moz-border-radius: 10px;
  	}
  	input{
  		display: block;
  		margin-top: 30px;
  		width: 100px;
  		margin-left: 350px;
  	}
  	#usage{
  		display: none;
  		margin-bottom: 35px;
  	}
  	#tools{
  		display: none;
  		margin-bottom: 35px;
  	}
  	h1{
  		margin-bottom: 0px;
  	}
  	#usagebutton{
  		margin-left: 0;
  		margin-bottom: 10px;
  	}
  	.radiobut{
  		display: inline-block;
  		width: 15px;
  		margin:0;
  	}
  	#sbut{
  		margin-bottom:20px;
  		margin-top: -20px;
  	}
  	#errordump{
  		clear:both;
  		display: none;
   		margin-bottom: 50px;
  	}
  	#col{
  		vertical-align: top;
  		display: inline-block;
  		margin-right: 200px;
  	}
  	a{
  		clear: both;
  	}
  </style>
</head>
<body>
<div id="container">
<?php
	$errcond = 0;
	$supress = 0;
	$hideform = 0;
	/* Clears session memory.
	 * supress supresses the printing of other forms.
	 */
	if(isset($_REQUEST['delete'])){
		$_SESSION = array();
		$supress = 1;
	}		
	/* Prints forms, prepopulating when there are set session variables.
	 * Those variables clear when memory is manually cleared, or when
	 * there is a successful program-style update to the session's program
	 * note that supress isn't necessary here, but it exists for adding more
	 * functionalities.
	 */
	if(isset($_REQUEST['upload'])){
		$supress = 1;
		$hideform = 1;
		echo '<a href="parse.php">Back</a></br>'; 
		echo '<p>Attempting to upload.</p>';
		print_program($_SESSION['factors']);
	}
	if(isset($_REQUEST['show'])){
		$supress = 1;
		$hideform = 1;
		if(isset($_SESSION['factors'])){
			echo '<a href="parse.php">Back</a></br>'; 
			print_program($_SESSION['factors']);
		}
		else echo '<h1>Error:</h1><p>No information stored so far.</p>';
		echo '<a href="parse.php">Back</a></br>'; 
	}
	if(!isset($_REQUEST['redcap']) and !$hideform){
		$supress = 1;
		print_usage();
		echo '<form action="parse.php" method="POST">
				<label>Form Name (raw):';
				if(isset($_SESSION['refname']))
					echo '<input class="smt" type="text" name="fname" value="'.$_SESSION['refname'].'"/>';
				else
					echo '<input class="smt" type="text" name="fname"/>';
				echo '</label><label>Table Name:';
				if(isset($_SESSION['retname']))
					echo '<input class="smt" type="text" name="tname" value="'.$_SESSION['retname'].'"/>';
				else
					echo '<input class="smt" type="text" name="tname"/>';
				echo '</label></br><label>Redcap Names:';
				if(isset($_SESSION['rerdc']))
					echo '<textarea class="textfield" rows=10 name="redcap"/>'.$_SESSION['rerdc'].'</textarea>';
				else
					echo '<textarea class="textfield" rows=10 name="redcap"/></textarea>';
				echo '</label></br><label>Access';
				if(isset($_SESSION['reacc']))
					echo '<textarea class="textfield" rows=10 name="access"/>'.$_SESSION['reacc'].'</textarea>';
				else
					echo '<textarea class="textfield" rows=10 name="access"/></textarea>';
				echo '</label></br>';
				if(isset($_SESSION['refullprgm'])){
					if($_SESSION['refullprgm'])
						echo '<input class="radiobut" type="radio" name="fullprgm" value="1" checked/> Write full php script</br>
						<input class="radiobut" type="radio" name="fullprgm" value="0" /> Write one function';
					else
						echo '<input class="radiobut" type="radio" name="fullprgm" value="1" /> Write full php script</br>
						<input class="radiobut" type="radio" name="fullprgm" value="0" checked/> Write one function';
				}
				else
					echo '<input class="radiobut" type="radio" name="fullprgm" value="1" /> Write full php script</br>
						<input class="radiobut" type="radio" name="fullprgm" value="0" checked/> Write one function';
				echo '</br></br><label>Advanced options for script construction.</br>
						Leaving these blank is allowable. You can set them in the script\'s header.</br></br>
						Access File:';
				if(isset($_SESSION['relocal_db']))
					echo '<input class="smt" type="text" value='.$_SESSION['relocal_db'].' name="local_db"/>';
				else 
					echo '<input class="smt" type="text" name="local_db"/>';
				echo '</label><label>Your API Token:';
				if(isset($_SESSION['reapi_tok']))
					echo '<input class="smt" type="text" value='.$_SESSION['reapi_tok'].' name="api_tok"/>';
				else 
					echo '<input class="smt" type="text" name="api_tok"/>';
				echo '</label><label>The URL of your REDCap Server:';
				if(isset($_SESSION['rehosturl']))
					echo '<input class="smt" type="text" value='.$_SESSION['rehosturl'] .' name="hosturl"/>';
				else
					echo '<input class="smt" type="text" name="hosturl"/>';
				echo '</label><input class="radiobut" type="checkbox" name="upload" value="1"/> Upload from this script (only suitable for small databases due to timeout)<br/>';
				echo '<input class="submitbutton" type="submit" value="GO!" /></form>';
				}
	/* Handling submissions:
	 * if no supressors are on, the script either
	 *  - prints a function
	 *  - prints a program
	 *  - attempts to update the server, listing
	 *    validation errors, etc.
	 */
	if(!$supress){
		$redcap = trim($_REQUEST['redcap']);
		$access = trim($_REQUEST['access']);
		$form = $_REQUEST['fname'];
		$table = $_REQUEST['tname'];
		if(strlen($redcap) == 0){
			echo '<h1>Errors:</h1>
			<p>REDCap values not specified</p>';
			$errcond++;
			unset($_SESSION['rerdc']);
		}
		else{$_SESSION['rerdc'] = $redcap;}
		
		if(strlen($access) == 0){
			if(!$errcond){	echo '<h1>Errors:</h1>'; }
			echo '<p>Access values not specified</p>';
			$errcond++;
			unset($_SESSION['reacc']);
		}
		else{$_SESSION['reacc'] = $access;}
		
		if(strlen($form) == 0){
			if(!$errcond){	echo '<h1>Errors:</h1>'; }
			echo '<p>Form name not specified</p>';
			$errcond++;
			unset($_SESSION['refname']);
		}
		else{$_SESSION['refname'] = $form;}
		if(strlen($table) == 0){
			if(!$errcond){	echo '<h1>Errors:</h1>'; }
			echo '<p>Table name not specified</p>';
			$errcond++;
			unset($_SESSION['retname']);
		}
		else{$_SESSION['retname'] = $table;}
		if(isset($_REQUEST['fullprgm']))
			$_SESSION['refullprgm'] = $_REQUEST['fullprgm'];
		else { $_SESSION['refullprgm'] = 0;}
		if(!isset($_REQUEST['fullprgm']) || !$_REQUEST['fullprgm'])
			$_SESSION['fullprgm'] = 0;
		if(strlen($_REQUEST['local_db']) > 0)
			$_SESSION['relocal_db'] = $_REQUEST['local_db'];
		else
			unset($_SESSION['relocal_db']);
		if(strlen($_REQUEST['api_tok']) > 0)
			$_SESSION['reapi_tok'] = $_REQUEST['api_tok'];
		else
			unset($_SESSION['relocal_db']);
		if(strlen($_REQUEST['hosturl']) > 0)
			$_SESSION['rehosturl'] = $_REQUEST['hosturl'];
		else
			unset($_SESSION['rehosturl']);
			
		/* This is where the magic happens */
		
		$redcap = makerc($redcap);
		$access = makeac($access);
		$factors = array($redcap, $access, $form, $table);
			if(!$errcond and !errorcheck($factors)){
				if($_SESSION['refullprgm']){
					if(isset($_SESSION['factors'])){
						echo '<a href="parse.php">Back</a></br>'; 
						unset($_SESSION['retname']);
						unset($_SESSION['refname']);
						unset($_SESSION['rerdc']);
						unset($_SESSION['reacc']);
						array_push($_SESSION['factors'],$factors);
					}
					else{
						$_SESSION['factors'] = array($factors);
					}
					print_program($_SESSION['factors']);
				}
				else{
					print_function($factors);
				}
			}
		echo '<a href="parse.php">Back</a>';
	}
	

?>
</div>
</body>

<?php
/* makerc parses the input for the redcap field.
 * This amounts to tokenizing around carriage returns 
 * and setting up the repeat syntax for the checkboxes.
 * The handling is very simple, because this is the exact
 * format the fieldnames will be in when copied from the csv
 * data dictionary.
 */
function makerc($redc){
	$tokens = preg_split('/\r/', $redc);
	$numtok = sizeof($tokens);
	$i=0;
	$addin = 0;
	for($i; $i<$numtok; $i++){
		$tokens[$i] = ltrim($tokens[$i]);
		if(intval($tokens[$i])){
			$addin += intval($tokens[$i])-1;
		}
	}
	$addin = $addin+$numtok;
	$tofill = array_fill(0, $addin, 0);
	$j = 0;
	$k = 0;
	for($i=0; $i<$numtok; $i++){
		if(intval($tokens[$i])){
			$numchk = intval($tokens[$i]);
			for($k=0; $k<$numchk; $k++){	
				$tokens[$i] = ltrim($tokens[$i], "0123456789");
				$tofill[$j++] = $tokens[$i] . '___' . $k;
				}
			}
		else{
			$tofill[$j++] = ltrim($tokens[$i]);
		}
	}
	return $tofill;
}
/* This is easier, just tokenizing around crs and
 * tabs. If copied out of access->excel->here, this 
 * should be the correct format. Tabs are for if they 
 * don't make it vertical first.
 * Hopefully this will become depreciated once I set up the functionality
 * to access an access server and pull the column names. 
 */
function makeac($ac){
	$tokens = preg_split('/\r|\t/', $ac);
	for($i=0; $i<sizeof($tokens); $i++){
		$tokens[$i] = ltrim($tokens[$i]);
	} 

	return $tokens;
}

/* print_function is what it is. It prints a php function out.
 * This will be somewhat depreciated if I set up a direct upload 
 * functionality. However, it could still be useful for people who
 * prefer to keep the php scripts for documentation or future use.
 */
function print_function($factors){
	$redcap = $factors[0];
	$access = $factors[1];
	$form = $factors[2];
	$table = $factors[3];
	echo '<p>function insert_' . $form . '_values($server, $api_token, $db){';
	echo '<p>$REDCapVariables=array(';
	for($i = 0;$i <sizeof($redcap); $i++){
		echo '"' . $redcap[$i] . '"';	
			if(($i + 1) < sizeof($redcap)){echo ', ';}
	}
	echo ');</p>';
	echo '<p>$SQL = "SELECT ';
	for($i = 0; $i<sizeof($access); $i++){
		echo '[' . $access[$i] . ']';
		if(strtolower($redcap[$i]) != strtolower($access[$i])){
			echo ' as ' . $redcap[$i]; 
		}
		if(($i + 1) < sizeof($access)){	echo ', ';}
	}
	echo ' FROM [' . $table . '] ORDER BY ['. $redcap[0].']";</p>';
	echo '<p>$theevent = "'.$form.'";</p>';
	echo '<p>insertGenericValue($REDCapVariables, $SQL, $theevent, $server, $api_token, $db);';
	echo '<p>}</p>';		
}
function run_function($factors){
	$redcap = $factors[0];
	$access = $factors[1];
	$form = $factors[2];
	$table = $factors[3];
	
	$SQL = "SELECT ";
	for($i = 0; $i<sizeof($access); $i++){
		$SQL = $SQL . '[' . $access[$i] . ']';
		if(strtolower($redcap[$i]) != strtolower($access[$i])){
			$SQL = $SQL . ' as ' . $redcap[$i]; 
		}
		if(($i + 1) < sizeof($access)){	$SQL = $SQL .', ';}
	}
	$SQL = $SQL . ' FROM [' . $table . '] ORDER BY ['. $redcap[0].']';
	insertGenericValue($redcap, $SQL, $form, $_REQUEST['hosturl'],
			 $_REQUEST['api_tok'], $_REQUEST['local_db']);
}

/* This prints functions for each form, and also sets up
 * the connection to the two servers.
 */
function print_program($factors){
	$numfacs = sizeof($factors);
	$err = 0;
	if(!isset($_REQUEST['upload'])){
		echo "</br>Put whatever header and style information you want around this and run it.</br>
				You need to run it in the same directory as upload.php and open.php, and you must have php installed.</br>
				Note: there must be beginning and ending php tags around this code</br> <br>";
		echo 'include \'upload.php\';</br>';
		if(isset($_REQUEST['hosturl']) and strlen($_REQUEST['hosturl']) > 0)
			echo '<p>$server = "'.$_REQUEST['hosturl'].'";</p>';
		else
			echo '<p>$server = "SERVER URL HERE" ;</p>';
		if(isset($_REQUEST['api_tok']) and strlen($_REQUEST['api_tok']) > 0)
			echo '<p>$api_token = "'.$_REQUEST['api_tok'].'";</p>';
		else
			echo '<p>$api_token = "YOUR TOKEN NAME HERE" ;</p>';
		if(isset($_REQUEST['api_tok']) and strlen($_REQUEST['local_db']) > 0)
			echo '<p>$db = "'.$_REQUEST['local_db'].'";</p>';
		else
			echo '<p>$db = "FILEPATH TO DATABASE HERE" ;</p>';							
		echo '<p>';
		for($i = 0; $i<$numfacs; $i++){
			echo 'insert_' . $factors[$i][2] . '_values($server, $api_token, $db);</br>';
		}	
		for($i = 0; $i<$numfacs; $i++)
			print_function($factors[$i]);
		}
	else{
		if(!isset($_REQUEST['hosturl']) or strlen($_REQUEST['hosturl']) == 0){
			$err++;
			echo '<p>Error: No url provided.</p>';
		}
		if(!isset($_REQUEST['api_tok']) or strlen($_REQUEST['api_tok']) == 0){
			$err++;
			echo '<p>Error: No token provided.</p>';
		}
		if(!isset($_REQUEST['local_db']) or strlen($_REQUEST['local_db']) == 0){
			$err++;
			echo '<p>Error: No database provided.</p>';
		}
		if($err)
			echo '<p>You MUST fill out all advanced options for a direct upload</p>';
		else{
			$db = $_REQUEST['local_db'];
			$api_token = $_REQUEST['api_tok'];
			$server = $_REQUEST['hosturl'];
			echo '<a href="parse.php">Back</a></br>'; 
			include 'upload.php';
			for($i=0; $i<$numfacs; $i++)
				run_function($factors[$i]);
		}	
		
	}
		
}

/* This is a messy function that checks for several
 * potential errors I foresaw.
 */
function errorcheck($factors){
	$errors = 0;
	if(sizeof($factors[0]) != sizeof($factors[1])){
		echo '<h1>Errors:</h1>'; 
		echo '<p>The number of variables for redcap and access do not match.</br>'.
		'REDCap: ' .sizeof($factors[0]) . '</br>Access: ' . sizeof($factors[1]) . '</p>';
		echo '<input id="usagebutton" type="button" value="Show Details" onclick="$(\'#errordump\').toggle()" />';
		echo '<div id="errordump"><div id="col"><p>Redcap '.
					 nl2br(print_r($factors[0], true)) .
			 '</p></div><div id="col"><p>Access ' .
			 nl2br(print_r($factors[1], true)) .
			 '</p></div></div>';
			$errors++;
	}
	else if($_SESSION['refullprgm'] and isset($_SESSION['factors']) and
		$factors == $_SESSION['factors'][sizeof($_SESSION['factors'])-1]){
		echo '<h1>Errors:</h1>';
		echo '<p>It looks like you attempted a repeat submission of form '.$factors[2].'.</p>';
		$errors++;
	}
	else {
		$errfac=array();
		$size = sizeof($factors[0]);
		for($i=0; $i<$size; $i++){
			for($k=0; $k<$size; $k++){
				if(($i!=$k) and ($factors[0][$i] == $factors[0][$k])){
					$errors++;
					array_push($errfac, $factors[0][$i]);
				}
			}
		}
		if(sizeof($errfac)){
			$errfac = array_unique($errfac);
			echo '<h1>Errors:</h1><p>It looks like you have repeated fields from REDCap:</br>';
			foreach($errfac as $repeat)
				echo $repeat.'</br>';
			echo '</p>';
		}
	}
	return $errors;
}

function print_usage(){
	echo '<h1>Upload Helper</h1>
			<input id="usagebutton" type="button" value="Usage" onclick="$(\'#usage\').toggle()" />
			<div id="usage">
				<blockquote>
					<p>Prefix checkboxes the number of options.</br>
					i.e. 5medications if medications has 5 checkboxes</br>
					This should be safe, because REDCap does not allow number-initiated names.</br>
					Checkboxes fill from 0.</p>
					<p>To make an entire program, keep the script button on and keep inputting forms.</br>
					Forms with errors will not be appended. Use the function maker for easier individual \'debugging\'.</br>
					Use the clear all button to delete the stored script as well as the remembered form names.<p>
					<p>Orders by the first element - you\'ll want your primary key here.</p>
					<p>Tokenization occurs at carriage returns for REDCap, so you can copy it straight from data dictonary.</br>
					Tokenization occurs at carriage returns and tabs for Access, So you can copy paste it from the table view.<\br>
					BE NICE. Don\'t leave interstital newlines. Ends should be fine.</p>
					<p>For the advanced options - your Access MDB file should be in the same directory as the php file, or<\br>
					you can provide the full filepath at your own risk.</br>
					If you have qualms about putting the token/filepath/url on the form, you can edit the variables<\br>
					$accessfile, $api_tok, and $redcap_url in the header for the printed php script.<\br>
					For obvious reasons, you will need those to do a direct upload.</br>
					<p>Features I might attempt - autofill of column names from Access</p>
		 		 </blockquote>
		 	</div>
			<form id="sbut" action="parse.php" method="POST">
				<input type="hidden" name="delete" value="1">
				<input id="usagebutton" type="submit" value="Clear All" />
			</form>
			<form id="sbut" action="parse.php" method="POST">
				<input type="hidden" name="show" value="1">
				<input id="usagebutton" type="submit" value="Show Script" />
			</form>';
			
}

?>

