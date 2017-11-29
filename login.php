<?php
//Turn on error reporting
ini_set('display_errors', 'On');

//TEMP value (database from a previous class)
//we'll change this to connect to Employee recognition db
//Connects to the database
require_once __DIR__ . '/Config/database.php';
include("header.php");

//Turn on error reporting
ini_set('display_errors', 'On');
//Connects to the database
$newx =  new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

if(!($stmt = $newx->prepare("SELECT Employees.Password, Employees.Email FROM Employees WHERE Employees.Email = ?"))){
	//echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}

$passwordplain = $_POST['password'];

if(!($stmt->bind_param("s",$_POST['email']))){
	//echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
	//echo "Execute failed: "  . $newx->connect_errno . " " . $newx->connect_error;
}
if(!$stmt->bind_result($passwordencr, $dbemail)){
	//echo "Bind failed: "  . $newx->connect_errno . " " . $newx->connect_error;
}
while($stmt->fetch()){
}
$stmt->close();

	//compare the plaintext password the user passed to the hash in the db
	$passwordIsGood = password_verify($passwordplain, $passwordencr);
	
	//compare user-input to stored hash
	if($passwordIsGood){
		
		global $mySQL;
		if ($SQL = $mySQL->prepare("SELECT `ID` FROM `Employees` WHERE `Email` = ?"))
		{
			$SQL->bind_param("s",$_POST['email']);
			$SQL->execute();
			$SQL->bind_result($empID);
			$SQL->fetch();
			$SQL->close();
		}
		$_SESSION["authenticated"] = $empID;
		header('Location: cindex.php');
	}
	else{
		 header('Location: loginvalidate.php');
	}

?>