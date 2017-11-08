<?php
// AWS documentation was used as a guide for this code:
// docs.aws.amazon.com/ses/latest/DeveloperGuide/send-using-smtp-php.html

// files required by PHPMailer class
require '/var/www/html/vendor/autoload.php';

// sender name is static and needs to be validated by AWS. using my OSU address.
$senderName = '*******************';

// setup variables from webpage
$email = $_REQUEST['email'];
$employeeName = $_REQUEST['nomineeName'];
$awardType = $_REQUEST['awardType'];
$awardMessage = $_REQUEST['reason'];

// awards are stored with a text name, change this based on award ID in database
switch ($awardType) {
	case 3:
		$awardType = "week";
		break;
	case 4:
		$awardType = "month";
		break;
	case 5:
		$awardType = "custom";
		break;
}

// variables to display after email attempt
$error = "";
$successMessage = "";

// create new PHPMailer object and set to SMTP
$mail = new PHPMailer;
$mail->isSMTP();

// set email sender info
$mail->setFrom($senderName, 'OSU Employee Recognition');

// set email recipient info
$mail->addAddress($email, $employeeName);

// smtp username and password from AWS
$mail->Username = '*******************';
$mail->Password = '*******************';
    
// set the SMTP host
$mail->Host = '*******************';

// contents of the email
$mail->Subject = 'Employee award';
$mail->Body = '<h1>Congratulations!</h1>
    <h3>Hello, '.$employeeName.'. You have received an award.</h3><p>'.$awardMessage.'</p>';

// attach the pdf here
$mail->AddAttachment('/var/www/html/pdf/'.$awardType.'.pdf');

// more SMTP and port setup
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

// set mail format to HTML
$mail->isHTML(true);

// alternative body content for users without HTML-capable email
$mail->AltBody = "Congratulations. You have received an award.";

// send the email and echo success/failure
if($mail->send()) {
	$successMessage = '<div class="alert alert-success" role="alert">Award has been sent!</div>';
} else {
	$error = '<div class="alert alert-danger" role="alert"><p><strong>Award could not be sent :(</div>';
}
?>

<!-- HTML to display after email is sent -->
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
		</div>

		<div id="bodyDiv">
			<div id="centerContainer" style="height:200px;">
				<div id="error"><?php echo $error.$successMessage; ?></div>
			</div>
		</div>
	</body>
</html>