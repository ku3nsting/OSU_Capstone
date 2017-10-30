<?php
	
	include("header.php");
	
	function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
	}
	if(!empty($_POST["email"]) && !empty($_POST["password"])) {
		$email = $_POST["email"];
		$password = $_POST["password"];
		
		$conn = mysqli_connect("$dbservername, $dbusername, $dbpassword, $dbname");
		$query = $connection->prepare("SELECT 'Password' FROM Employees WHERE Email='" . $_POST["email"] . "'");
		$query->bind_param("s", $email);
		$query->execute();
		$query->bind_result($dbpassword);
		$query->fetch();
		$query->close();
		
		//make hash with stored password
		$hash = password_hash($password, PASSWORD_DEFAULT);
		echo $hash;
		
		//compare user-input to stored hash
		if($dbpassword == $hash){
			debug_to_console( "Authentication Successful!");
			$_SESSION["authenticated"] = 'true';
			header('Location: cindex.php');
		}
		else {
			header('Location: login.html');
		}
	}
	else {
		//no credentials given
		header('Location: login.html');
	}
?>