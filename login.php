<?php
session_start();
//Turn on error reporting
ini_set('display_errors', 'On');
//Connects to the database
$newx = new mysqli("oniddb.cws.oregonstate.edu","kuenstir-db","596vXbwYMGgNDk7e","kuenstir-db");

if(!($stmt = $newx->prepare("SELECT Employees.Password, Employees.Email FROM Employees WHERE Employees.Email = ?"))){
	//echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}

$password = $_POST['password'];

if(!($stmt->bind_param("s",$_POST['email']))){
	//echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
	//echo "Execute failed: "  . $newx->connect_errno . " " . $newx->connect_error;
}
if(!$stmt->bind_result($dbpassword, $dbemail)){
	//echo "Bind failed: "  . $newx->connect_errno . " " . $newx->connect_error;
}
while($stmt->fetch()){
}
$stmt->close();

	//make hash with stored password
	$hash = password_hash($password, PASSWORD_DEFAULT);
	//$hash = $dbpassword;
	//echo $hash;
	
	//compare user-input to stored hash
	if($dbpassword == $hash){
		
		$_SESSION["authenticated"] = "true";
		header('Location: cindex.php');
	}

?>