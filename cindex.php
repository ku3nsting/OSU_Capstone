<?php
    //Turn on error reporting
    ini_set('display_errors', 'On');

	//TEMP value (database from a previous class)
	//we'll change this to connect to Employee recognition db
    //Connects to the database
    require_once __DIR__ . '/Config/database.php';
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
		  <li style="float:right"><a class="active" href="login.html">Sign Out</a></li>
		</ul>
		
		</td>
		</tr>
		</table>
		
		<p>
	
	<div id="pageDiv">
	
		<!-- Get username -->
				<table id="welcome">
					<tr><td>
					<b>Welcome, </td></b>
					
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
				 echo "\n<td>\n" . $fname . "\n</td>\n<td>\n" . $lname . "\n</td>\n</tr>\n";
				}
				$stmt->close();
				?>
				
				</tr>
				</table>

	</div>
	<p>
	
	<table id="spacingTableBody">
	<tr>
	<td text-align="center"; width="40%"; id="EM" style="font-size:16px">

			Congratulations to
			<br>
			
			<!-- Get awardee name -->
		<?php
		if(!($stmt = $mysqli->prepare("SELECT fname, lname FROM Employees WHERE Employees.ID = 4"))){
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}

		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($fname, $lname)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		while($stmt->fetch()){
		 echo "\n<b>\n" . $fname . "\n\n" . $lname . "\n</b>\n";
		}
		$stmt->close();
		?>
		<!-- /Get awardee name -->		
		
		<p>
		<img src="resources/profile-icon.png" style="max-width:180px;">
		<p>
		Employee of the Month
	</td>
	<td width="65%" id="EM" valign="top" style="padding-right:25px;padding-top:35px">
				
				
		Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
		<p>
		Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
	</td>
	</tr>
	</table>
	
	<p>
	
	<div id="centerContainer">
	<div id="scrollBox">
			<table width="100%" id="table">
			<tr>
			
			<td width="30%">
				Congratulations to
			<br>
					<!-- Get awardee name -->
				<?php
				if(!($stmt = $mysqli->prepare("SELECT fname, lname FROM Employees WHERE Employees.ID = 1"))){
					echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
				}

				if(!$stmt->execute()){
					echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				if(!$stmt->bind_result($fname, $lname)){
					echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				while($stmt->fetch()){
				 echo "\n<b>\n" . $fname . "\n\n" . $lname . "\n</b>\n";
				}
				$stmt->close();
				?>
				<!-- /Get awardee name -->		
					<br>
					<img src="resources/profile-icon.png" style="max-height:110px;width:110px">
					<br>
					Employee of the Week
				</td>
				
				<td width="40%" style="padding-right: 15px">
					Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
				</td>
				
				
			</tr>
			</table>
			
			<table width="100%" id="table">	
							
				<img src="resources/divider.png" style="max-width:250px">
				<p>
			
				<tr>
				<td width="30%">


				Previous Winners:<p>
					<img src="resources/profile-icon.png" style="max-height:110px;width:110px">
				</td>
				
				<td width="40%" style="padding-right: 15px">
					Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
				</td>
			</tr>
			
			<tr>
				<td width="30%">
					<img src="resources/profile-icon.png" style="max-height:110px;width:110px">
				</td>
				
				<td width="40%" style="padding-right: 15px">
					Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
				</td>
				
				</tr>

			</table>
	</div>
	
			<table width="45%" valign="middle" id="EM" style="float:right; padding-top:15px;">
			<tr>
				<td valign="middle">
				<img src="resources/Employee-Appreciation-Card.png" width="80%" height="80%">
				</td>
			</tr>
			</table>
	</div>

	</div><!-- bodydiv -->
</body>
</html>