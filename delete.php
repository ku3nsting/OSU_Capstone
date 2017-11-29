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
if(!($stmt = $mysqli->prepare("DELETE FROM Awards_Given WHERE ID = ?"))){
	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!($stmt->bind_param("i",$_GET['id']))){
	echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!$stmt->execute()){
	echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
else{
	echo "Deleted award.";
}

$stmt->close();
?>


				<p><div id="center">
		<form action="awards.php">
					<input class="button" type="submit" value="Back to your Awards overview">
					</form>
				</div>
				
				</div>

</body>
</html>