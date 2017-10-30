<?php

require '/var/www/html/vendor/autoload.php';

$senderName = FILL THIS IN;

// setup variables from webpage
$email = $_REQUEST['email'];
$employeeName = $_REQUEST['fname']." ".$_REQUEST['lname'];
$awardType = $_REQUEST['awardType'];

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
	echo "Award was sent!";
} else {
	echo "Award was not sent.";
}

//Turn on error reporting
//no awarded-by field yet -- I was afraid it would break things since we don't have a signed-in value to pull from
ini_set('display_errors', 'On');

//Connects to the database
$mysqli2 = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);
if(!$mysqli2 || $mysqli2->connect_errno){
	echo "Connection error " . $mysqli2->connect_errno . " " . $mysqli2->connect_error;
	}
	
if(!($stmt = $mysqli2->prepare("INSERT INTO Awards_Given(AwardID, EmployeeID, AwardDate) VALUES (?,?,NOW())"))){
	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!($stmt->bind_param("ii",$_POST['awardType'], $_POST['empID']))){
	echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!$stmt->execute()){
	echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
} else {
	echo "Nomination completed sucessfully";
}
?>
