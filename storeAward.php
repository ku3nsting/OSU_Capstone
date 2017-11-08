<?php
session_start();

//Turn on error reporting
ini_set('display_errors', 'On');

//Connects to the database
require_once __DIR__ . '/Config/database.php';
$mysqli = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

if(!$mysqli || $mysqli->connect_errno){
 	echo "Connection error " . $mysqli2->connect_errno . " " . $mysqli->connect_error;
}
	
if(!($stmt = $mysqli->prepare("INSERT INTO Awards_Given(AwardedByID, AwardID, EmployeeID, AwardDate, AwardTime) VALUES (?,?,?,?,?)"))){
 	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}

if(!($stmt->bind_param("iiiss", $_SESSION["userID"], $_POST['awardType'], $_POST['nomineeID'], $_POST['chosenDate'], $_POST['chosenTime']))){
 	echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}

include 'sendAward.php';

if(!$stmt->execute()){
 	echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
} else {
 	echo "Nomination completed sucessfully";
}
?>