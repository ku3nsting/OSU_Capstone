<?php
require '/var/www/html/vendor/autoload.php';

// setup variables from webpage
$email = $_REQUEST['email'];
$employeeName = $_REQUEST['fname']." ".$_REQUEST['lname'];
$awardType = $_REQUEST['awardType'];

// new PHPMailer object and to smtp
$mail = new PHPMailer;
$mail->isSMTP();

// email sender info
$mail->setFrom('*****', 'OSU Employee Recognition');

// email recipient info
$mail->addAddress($email, $employeeName);

// smtp username and password from aws
$mail->Username = '*****';
$mail->Password = '*****';
    
// set the smtp host
$mail->Host = '*******m';

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
?>
