<?php
//Turn on error reporting
//no awarded-by field yet -- I was afraid it would break things since we don't have a signed-in value to pull from
ini_set('display_errors', 'On');
require 'vendor/autoload.php';
require_once __DIR__ . '/Config/database.php';
include("header.php");
$mysqli3 = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

$senderName = "FILL THIS IN";
$dbsuccess = false;
$emailsuccess = false;

// setup variables from webpage
$empID = $_POST['empID'];
$awdDate = $_POST['awdDate'];
$awardType = $_POST['awardType'];
$currentUser = $_SESSION["authenticated"];


//get other variables from database
if(!($stmt = $mysqli3->prepare("SELECT fname, lname, email FROM Employees WHERE Employees.ID = ?"))){
	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!($stmt->bind_param("i", $empID))){
	echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!$stmt->execute()){
	echo "Execute failed: "  . $mysqli3->connect_errno . " " . $mysqli3->connect_error;
}
if(!$stmt->bind_result($fname, $lname, $email)){
	echo "Bind failed: "  . $mysqli3->connect_errno . " " . $mysqli3->connect_error;
}
while($stmt->fetch()){
}
$stmt->close();


//SEND AWARD TO DB
$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);
if(!$conn || $conn->connect_errno){
	echo "Connection error " . $conn->connect_errno . " " . $conn->connect_error;
	}
if(!($stmt = $conn->prepare("INSERT INTO Awards_Given(awardid, employeeid, awarddate, awardedbyid) VALUES (?, ?, ?, ?)"))){
	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!($stmt->bind_param("iisi", $awardType, $empID, $awdDate, $currentUser))){
	echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!$stmt->execute()){
	echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
} else {
	$dbsuccess = true;
}

//finish assigning values to vars
$employeeName = $fname." ".$lname;


//EMAIL AWARD TO WINNER
// new PHPMailer object and to smtp
$mail = new PHPMailer;
$mail->isSMTP();

// email sender info
$mail->setFrom($senderName, 'OSU Employee Recognition');

// email recipient info
$mail->addAddress($email, $employeeName);

// smtp username and password from aws
$mail->Username = $dbusername;
$mail->Password = $dbpassword;
    
// set the smtp host
$mail->Host = $dbservername;

// contents of the email
$mail->Subject = 'Employee award';
$mail->Body = '<h1>Congratulations!</h1>
    <h3>Hello, '.$employeeName.'. You have received an award.</h3>';

// attach the pdf's here
$mail->AddAttachment('/var/www/html/pdf/'.$awardType.'.pdf');

// smtp auth and tls encryption/port setup
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

// set mail format to HTML
$mail->isHTML(true);

// alt body for users not using HTML email
$mail->AltBody = "Congratulations. You have received an award.";

// send the email and echo success
if($mail->send()) {
	$emailsuccess = true;
} else {
	echo "Award was not sent.";
}

//Redirect to success page
if($dbsuccess == true && $emailsuccess == true){
	header("Location: nomSuccess.php"); /* Redirect browser */
	ob_end_flush();
}

?>