<?php
    //Turn on error reporting
    ini_set('display_errors', 'On');

	//TEMP value (database from a previous class)
	//we'll change this to connect to Employee recognition db
    //Connects to the database
    require_once __DIR__ . '/Config/database.php';
	include("header.php");
    $mysqli = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

?>

<!DOCTYPE html>

<html>
<head>
 
	<title>Employee Recognition Application</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" /> 
   
</head>

<body>

<div id="bodyDiv">
	
		<table id="spacingTable">
		<tr>
		<td width="10%"; id="logo">
		<a href="cindex.php">
		<img src = "resources/fakelogo.png" alt="Company Logo" style="width:100%;height:100%;"></a>
		</td>
		
		<td width="88%" id="navBar">
		
		<ul>
		  <li><a href="account.php">Account</a></li>
		  <li><a href="awards.php">My Awards</a></li>
		  <li><a href="nominate.php">Nominate</a></li>
		  <li style="float:right"><a class="active" href="loginvalidate.php">Sign Out</a></li>
		</ul>
		
		</td>
		</tr>
		</table>
		
		<p>		
		
		
<!-- NOMINATION CONFIRMARION -->
		<div id="nomContainer">
		
		
			
			<form method="post" action="sendAward.php">
				<div  id="nominationForm">
					
					<h2>Success</h2>
					<p>
					Your nomination was sent sucessfully
					
                   <a href= "cindex.php">Back to Homepage </a>
				</div>
		
			</form>

		</div>
		
<p>

	
	
	</div> <!-- bodydiv -->
</body>
</html>