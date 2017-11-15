<?php
    //Turn on error reporting
    ini_set('display_errors', 'On');

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
		
		
		
<!-- NOMINATION -->
		<div id="centerContainer">
		
		
			<form method="post" action="sendAward.php">
				<div id="nominationForm">
					<h2>Nominate a Co-worker</h2>
					<p>
					
					<label>Recipient Name:	</label>
					<select name="empID">
					<?php
					if(!($stmt = $mysqli->prepare("SELECT id, fname, lname FROM Employees"))){
						echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
					}
					if(!$stmt->execute()){
						echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
					}
					if(!$stmt->bind_result($empID, $fname, $lname)){
						echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
					}
					while($stmt->fetch()){
					 echo '<option value="'.$empID.'"> ' . $fname .' '. $lname .'</option>\n';
					}
					$stmt->close();

					?>
					</select>

					<p>

					<label for="awardType">Choose an award type: </label>
					<div>
						<input type="radio" name="awardType" value="1"; checked="checked" \>Employee of the Week <a href="/pdf/week.pdf">(preview pdf)</a><br>
						<input type="radio" name="awardType" value="2";">Employee of the Month <a href="/pdf/month.pdf">(preview pdf)</a><br>
						<input type="radio" name="awardType" value="3">Custom <a href="/pdf/custom.pdf">(preview pdf)</a><br>
					</div>
				
					<p>
					</div>
					
					<label>Send your nominee a message:	</label><p>

					<textarea name="reason" placeholder="Type your message here" style="width:50%;height:150px;"></textarea>

					<p>
		
				<div id="submitButton">
					<input class="button" type="submit" value="Nominate">
				</div>
		
			</form>

		</div>


	
	
	</div> <!-- bodydiv -->
</body>
</html>