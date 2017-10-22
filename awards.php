<?php
    //Turn on error reporting
    ini_set('display_errors', 'On');

	//TEMP value (database from a previous class)
	//we'll change this to connect to Employee recognition db
    //Connects to the database
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu","kuenstir-db","4jgIGJ2KQMnNGthS","kuenstir-db");

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
		  <li style="float:right"><a class="active" href="login.html">Sign Out</a></li>
		</ul>
		
		</td>
		</tr>
		</table>
		
		<p>
	
	<div id="pageDiv">
	
		<!-- Get username -->
				<table >
					<tr>
					
				<?php
				if(!($stmt = $mysqli->prepare("SELECT fname, lname FROM Employees WHERE Employees.ID = 3"))){
					echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
				}

				if(!$stmt->execute()){
					echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				if(!$stmt->bind_result($fname, $lname)){
					echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				while($stmt->fetch()){
				 echo "\n<td><b>\n" . $fname . "\n</td>\n<td>\n" . $lname . "'s</b> Award History</td></tr>";
				}
				$stmt->close();
				?>
				
				
				</table>

	</div>
	<p>
	
<div id="centerContainer">
		<table id="boldTable">
			<tr>
			<th>Award Date</th>
				<th>Award Type</th>
			</tr>
			
		<?php
		if(!($stmt = $mysqli->prepare("SELECT Awards_Given.AwardDate, Awards.AwardLabel FROM Awards_Given INNER JOIN Employees ON Employees.ID = Awards_Given.EmployeeID
		INNER JOIN Awards ON Awards.ID = Awards_Given.AwardID	
		WHERE Employees.ID = 3"))){
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}

		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($date, $type)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		while($stmt->fetch()){
		 echo "<tr>\n<td>\n" . $date . "\n</td>\n<td>\n" . $type . "\n</td>\n</tr>\n";
		}
		$stmt->close();
		?>
		</table>

<p>

	</div>


	</div><!-- bodydiv -->
</body>
</html>