<?php
    //Turn on error reporting
    ini_set('display_errors', 'On');

    //Connects to the database
    require_once __DIR__ . '/Config/database.php';
	include("header.php");
    $mysqli = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

?>

<html>
<head>
 
	<title>Employee Recognition Application</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" /> 
   
</head>

<body>

<div id="bodyDiv">

<?php
$empID = $_SESSION["authenticated"];
$empBio = $_POST['bio'];
if(!($stmt = $mysqli->prepare("UPDATE Employees SET Bio = ? WHERE ID = ?"))){
	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!($stmt->bind_param("si", $empBio, $empID))){
	echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!$stmt->execute()){
	echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
else{
	echo "Updated Bio:" . $empBio ;
}

$stmt->close();
?>


				<p><div id="center">
		<form action="account.php">
					<input class="button" type="submit" value="Back to your account overview">
					</form>
				</div>
				
				</div>

</body>
</html>