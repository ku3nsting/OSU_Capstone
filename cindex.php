<?php
    //Turn on error reporting
    ini_set('display_errors', 'On');

	//TEMP value (database from a previous class)
	//we'll change this to connect to Employee recognition db
    //Connects to the database
    require_once __DIR__ . '/Config/database.php';
	include("header.php");
    $mysqli = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

	//get current date to figure out awards
	$curDate = date("Y-m-d H:i:s");
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
					<tr><td>
					<b>Welcome, </td></b>
					
				<?php
				$empID = $_SESSION["authenticated"];
				if(!($stmt = $mysqli->prepare("SELECT fname, lname FROM Employees WHERE Employees.ID = ?"))){
					echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
				}
				if(!($stmt->bind_param("s", $empID))){
					echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
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
			<!-- Get Employee of the Month awardee name - rationale here is most recently added employee of the month for the current month is the intended recipient -->
		<?php
		if(!($stmt = $mysqli->prepare("SELECT Employees.ID, Employees.fName, Employees.lName, Employees.Bio FROM Employees
										INNER JOIN Awards_Given ON Awards_Given.EmployeeID = Employees.ID
										INNER JOIN Awards ON Awards_Given.AwardID = Awards.ID
										WHERE Awards.AwardLabel = 'Employee of the Month' AND
											  MONTHNAME(Awards_Given.AwardDate) = MONTHNAME(?) AND
											  YEAR(Awards_Given.AwardDate) = YEAR(?)
										ORDER BY Awards_Given.ID DESC
										LIMIT 1"))){
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!($stmt->bind_param("ss", $curDate, $curDate))){
		echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($id, $fname, $lname, $bio)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		while($stmt->fetch()){
		}
		$stmt->close();
		
		if($fname != ""){
			echo "\n<b>\n" . $fname . "\n\n" . $lname . "\n</b>\n";
		}
		else{
			echo "\nNo winner this month!\n";
			$id = 'x';
		}
		?>
		<!-- /Get Employee of the Month -->		
		
		<p>
		<img src="uploads/profilePhotoEmployeeId<?php echo $id ?>.png" style="max-width:180px;">
		<p>
		Employee of the Month
	</td>
	<td width="65%" id="EM" valign="top" style="padding-right:25px;padding-top:35px">
				
				
		<?php echo $bio ?>
		<p>
		Employee of the Month is a monthly honor awarded through peer nominations. Winners are chosen for outstanding team contributions,
					dependability, and consistent performance.
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
					<!-- Get Employee of the Week awardee name - rationale here is most recently added employee of the week for the current week is the intended recipient -->
				<?php
				if(!($stmt = $mysqli->prepare("SELECT Employees.ID, Employees.fName, Employees.lName, Employees.Bio FROM Employees
												INNER JOIN Awards_Given ON Awards_Given.EmployeeID = Employees.ID
												INNER JOIN Awards ON Awards_Given.AwardID = Awards.ID
												WHERE Awards.AwardLabel = 'Employee of the Month' AND
													  WEEK(Awards_Given.AwardDate) = WEEK(?) AND
													  YEAR(Awards_Given.AwardDate) = YEAR(?)
												ORDER BY Awards_Given.ID DESC
												LIMIT 1"))){
					echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
				}
				if(!($stmt->bind_param("ss", $curDate, $curDate))){
					echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
				}
				if(!$stmt->execute()){
					echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				if(!$stmt->bind_result($id, $fname, $lname, $bio)){
					echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				while($stmt->fetch()){
				}
				$stmt->close();
				
				if($fname != ""){
					echo "\n<b>\n" . $fname . "\n\n" . $lname . "\n</b>\n";
				}
				else{
					echo "\n<b>No winner this week!\n";
					$id = 'x';
				}
				?>
				
				<!-- /Get awardee name -->		
					<br>
					<img src="uploads/profilePhotoEmployeeId<?php echo $id ?>.png" style="max-height:110px;width:110px">
					<br>
					Employee of the Week
				</td>
				
				<td width="40%" style="padding-right: 15px">
					<?php echo $bio ?>
					<p>
					Employee of the Week is an honor awarded through peer nominations. Winners are chosen for performing tasks above and beyond their regular duties, or contributing significantly to the team mission.
				</td>
				
			</tr>
			</table>
			
			<p>
			<img src="resources/divider.png" style="max-width:250px">
			<p>
			
			
			<table width="100%" id="table">
			<tr>
			Previous Winners <p>
			
			<td width="30%">
				
			<br>
					<!-- Employee of the week before last -->
				<?php
				if(!($stmt = $mysqli->prepare("SELECT Employees.ID, Employees.fName, Employees.lName, Employees.Bio FROM Employees
												INNER JOIN Awards_Given ON Awards_Given.EmployeeID = Employees.ID
												INNER JOIN Awards ON Awards_Given.AwardID = Awards.ID
												WHERE Awards.AwardLabel = 'Employee of the Month' AND
													  WEEK(Awards_Given.AwardDate) = (WEEK(?)-1) AND
													  YEAR(Awards_Given.AwardDate) = YEAR(?)
												ORDER BY Awards_Given.ID DESC
												LIMIT 1"))){
					echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
				}
				if(!($stmt->bind_param("ss", $curDate, $curDate))){
					echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
				}
				if(!$stmt->execute()){
					echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				if(!$stmt->bind_result($id, $fname, $lname, $bio)){
					echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				while($stmt->fetch()){
				}
				$stmt->close();
				
				if($fname != ""){
					echo "\n<b>\n" . $fname . "\n\n" . $lname . "\n</b>\n";
				}
				else{
					echo "\nNo winner this week!\n";
					$id = 'x';
				}
				?>
				
				<!-- /Get awardee name -->	
					
					<br>
					<img src="uploads/profilePhotoEmployeeId<?php echo $id ?>.png" style="max-height:110px;width:110px">
					<br>
				</td>
				
				<td width="40%" style="padding-right: 15px">
					<?php echo $bio ?>
			
				</td>
				
			</tr>
			</table>
			
						<table width="100%" id="table">
			<tr>
			
			<td width="30%">
				
			<br>
					<!-- Employee of the month before last -->
				<?php
				if(!($stmt = $mysqli->prepare("SELECT Employees.ID, Employees.fName, Employees.lName, Employees.Bio FROM Employees
												INNER JOIN Awards_Given ON Awards_Given.EmployeeID = Employees.ID
												INNER JOIN Awards ON Awards_Given.AwardID = Awards.ID
												WHERE Awards.AwardLabel = 'Employee of the Month' AND
													  MONTHNAME(Awards_Given.AwardDate) = (MONTHNAME(?)-1) AND
													  YEAR(Awards_Given.AwardDate) = YEAR(?)
												ORDER BY Awards_Given.ID DESC
												LIMIT 1"))){
					echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
				}
				if(!($stmt->bind_param("ss", $curDate, $curDate))){
					echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
				}
				if(!$stmt->execute()){
					echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				if(!$stmt->bind_result($id, $fname, $lname, $bio)){
					echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				while($stmt->fetch()){
				}
				$stmt->close();
				
				if($fname != ""){
					echo "\n<b>\n" . $fname . "\n\n" . $lname . "\n</b>\n";
				}
				else{
					echo "\nNo winner this month!\n";
					$id = 'x';
				}
				?>
				
				<!-- /Get awardee name -->	
					
					<br>
					<img src="uploads/profilePhotoEmployeeId<?php echo $id ?>.png" style="max-height:110px;width:110px">
					<br>
				</td>
				
				<td width="40%" style="padding-right: 15px">
					<?php echo $bio ?>
			
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