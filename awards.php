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
		  <li style="float:right"><a class="active" href="signout.php">Sign Out</a></li>
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
				?>'s Award History </td>
				</tr>
				</table>
	</div>

	<!-- end get username -->
	
<div id="centerContainer">
		
		<table id="boldTable">
		Awards Received:
			<tr>
			<th>Award Date</th>
				<th>Award Type</th>
				<th>Awarded By</th>
			</tr>
			
		<?php
		if(!($stmt = $mysqli->prepare("SELECT Awards_Given.AwardDate, Awards.AwardLabel, Awards_Given.AwardedByID FROM Awards_Given INNER JOIN Employees ON Employees.ID = Awards_Given.EmployeeID
		INNER JOIN Awards ON Awards.ID = Awards_Given.AwardID	
		WHERE Employees.ID = ?"))){
		echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!($stmt->bind_param("i", $empID))){
			echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($date, $type, $givenby)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		while($stmt->fetch()){
		 echo "<tr>\n<td>\n" . $date . "\n</td>\n<td>\n" . $type . "\n</td>\n<td>\n" . $givenby . "\n</td>\n</tr>\n";
		}
		$stmt->close();
		?>
		</table>
		<p>
		<br>
		
		<table id="boldTable">
		Awards Given:
			<tr>
			<th>Recipient ID</th>
			<th>Award Date</th>
				<th>Award Type</th>
				<th> </th>
			</tr>
			
		<?php
		if(!($stmt = $mysqli->prepare("SELECT Awards_Given.EmployeeID, Awards_Given.AwardDate, Awards.AwardLabel FROM Awards_Given INNER JOIN Employees ON Employees.ID = Awards_Given.EmployeeID
		INNER JOIN Awards ON Awards.ID = Awards_Given.AwardID	
		WHERE Awards_Given.AwardedByID = ?"))){
		echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!($stmt->bind_param("i", $empID))){
			echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($recipient, $date, $type)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		while($stmt->fetch()){
		 echo "<tr>\n<td>\n" . $recipient . "\n</td>\n<td>\n" . $date . "\n</td>\n<td>\n". $type . "\n</td>\n<td>\n<a href=deleteAward.php>\n</td>\n</tr>\n";
		}
		$stmt->close();
		?>
		</table>

<p>

	</div>


	</div><!-- bodydiv -->
</body>
</html>