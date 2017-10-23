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
		
		
		
<!-- NOMINATION -->
		<div id="centerContainer" style="height:400px;">
		
		
			
			<form method="post" action="cindex.php">
				<div id="center">
					
					<b>Nominate a Co-worker<br></b>
					<p>
					
					<label>Recipient Name:	</label>
					<select name="Coworkers">
					<?php
					if(!($stmt = $mysqli->prepare("SELECT id, fname, lname FROM Employees"))){
						echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
					}

					if(!$stmt->execute()){
						echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
					}
					if(!$stmt->bind_result($id, $fname, $lname)){
						echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
					}
					while($stmt->fetch()){
					 echo '<option value=" '. $id . ' "> ' . $fname .' '. $lname .'</option>\n';
					}
					$stmt->close();
					?>
				</select>
				
					<p>
					
					<label>Reason for Nomination:	</label><p>

					<textarea name="reasons" placeholder="Type your message here" style="width:50%;height:150px;"></textarea>

					<p>
					
				</div>
		
				<div id="center">
					<input class="button" type="submit" value="Nominate">
				</div>
		
			</form>

		</div>
		
<p>

	
	
	</div> <!-- bodydiv -->
</body>
</html>