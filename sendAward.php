<html>
<head>
 
	<title>Employee Recognition Application</title>

	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />  
   
</head>

<body>

<div id="bodyDiv">

<?php
//Turn on error reporting
//no awarded-by field yet -- I was afraid it would break things since we don't have a signed-in value to pullf rom
ini_set('display_errors', 'On');

//Connects to the database
$mysqli2 = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);
if(!$mysqli2 || $mysqli2->connect_errno){
	echo "Connection error " . $mysqli2->connect_errno . " " . $mysqli2->connect_error;
	}
	
if(!($stmt = $mysqli2->prepare("INSERT INTO Awards_Given(AwardID, EmployeeID, AwardDate) VALUES (?,?,NOW())"))){
	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!($stmt->bind_param("ii",$_POST['awardType'], $_POST['empID']))){
	echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!$stmt->execute()){
	echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
} else {
	echo "Nomination completed sucessfully";
}


?>



				<p><div id="center">
				<form action="cindex.php">
					<input class="button" type="submit" value="Back to Homepage">
					</form>
				</div>
				
				</div>

</body>
</html>