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
	
	<div id="pageDiv">
	
		<!-- Get username -->
				<table id="welcome">
					<tr>
					
				<?php
				$empID = $_SESSION["authenticated"];
				if(!($stmt = $mysqli->prepare("SELECT fName, lName, hireDate, Email, CreatedOn FROM Employees WHERE Employees.ID = ?"))){
					echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
				}
				if(!($stmt->bind_param("i", $empID))){
					echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
				}
				if(!$stmt->execute()){
					echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				if(!$stmt->bind_result($fname, $lname, $hiredate, $email, $createdon)){
					echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				while($stmt->fetch()){
				 echo "\n<td>\n" . $fname . " " . $lname;
				}
				$stmt->close();
				?>'s profile </td>
				</tr>
				</table>
		</div>
	
	<p>			
	<p>
	<!-- end get username -->

<div id="centerContainer">
	
	<div id="leftContainer">
	<br>
	<img style="width:20%; height:20%;" src= "resources/profile-icon.png"><br>
	
	<b>Email address:	</b> <?php echo $email ?><p>
	
	<b>Account created on:	</b> <?php echo $createdon ?><p>
	
	<b>Hire date:	</b> <?php echo $hiredate ?><p>  <!-- correct this value according to however we store these -->
	
	<b>Signature on file?	</b> no<p>
	</div>
	<p>

</div>	
	

	</div><!-- bodydiv -->
</body>
</html>