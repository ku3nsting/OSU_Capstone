<?php
//used https://code.tutsplus.com/tutorials/creating-an-advanced-password-recovery-utility--net-4741 as reference
include("Config/database.php"); 
include("Controllers/authenticationFunctions.php");

$show = 'emailForm'; //which form step to show by default

if ($_SESSION['lockout'] == true && (mktime() > $_SESSION['lastTime'] + 900))
{
    $_SESSION['lockout'] = false;
    $_SESSION['badCount'] = 0;
}
if (isset($_POST['subStep']) && !isset($_GET['a']) && $_SESSION['lockout'] != true)
{
    switch($_POST['subStep'])
    {
        case 1:
            //we submitted an email or username to check against database
            $result = checkEmail($_POST['email']);
            if ($result['status'] == false )
            {
                $error = true;
                $show = 'userNotFound';
            } else {
                $error = false;
                $show = 'successPage';
                $passwordMessage = sendPasswordEmail($_POST['email']);
                $_SESSION['badCount'] = 0;
            }
        break;
        case 2:
            //we are setting a new password
            if ($_POST['key'] == '') header("location: loginvalidate.php");
            if (strcmp($_POST['pw0'],$_POST['pw1']) != 0 || trim($_POST['pw0']) == '')
            {
                $error = true;
                $show = 'recoverForm';
            } else {
                $error = false;
                $show = 'recoverSuccess';
                updateEmployeePassword($_POST['empID'],$_POST['pw0'],$_POST['key']);
            }
        break;
    }
}
elseif (isset($_GET['a']) && $_GET['a'] == 'recover' && $_GET['email'] != "") {
    $show = 'invalidKey';
    $result = checkEmailKey($_GET['email'],urldecode(base64_decode($_GET['u'])));
    if ($result == false)
    {
        $error = true;
        $show = 'invalidKey';
    } elseif ($result['status'] == true) {
        $error = false;
        $show = 'recoverForm';
        $securityUser = $result['userID'];
    }
}
if ($_SESSION['badCount'] >= 3)
{
    $show = 'speedLimit';
    $_SESSION['lockout'] = true;
    $_SESSION['lastTime'] = '' ? mktime() : $_SESSION['lastTime'];
}
?>

<html>
<head>
 
	<title>Employee Recognition Application</title>

	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />  
   
</head>

<body>

<div id="bodyDiv">
 
		

 <div id="middle">
	 
	 <?php switch($show) {
    case 'emailForm': ?>
		<h2>Password Recovery</h2>
		<p> To recover your password, please type your email address in the field below.<br>
			A recovery link will be sent to that address.<p>
			Problems with this page? Please contact your <a href="mailto:adminemail@admin.imaginary"> Administrator</a>.
			<?php if ($error == true) { ?><span class="error">You must enter a valid email address to continue.</span><?php } ?>
			<form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
			
			<div class="fieldGroup"><label for="email">Email</label>
			<div class="field"><input type="text" name="email" id="email" value="" maxlength="255"></div></div>
			<input type="hidden" name="subStep" value="1" />
			<div class="fieldGroup"><input type="submit" value="Submit" /></div>
			<div class="clear"></div>
			</form>
 
     <?php break; case 'userNotFound': ?><br>    
		<h2>Password Recovery</h2><br>    
		<p>The email you entered was not found in our database.<br /><br />
		<a href="?">Click here</a> to try again.</p><br>    
		
	 <?php break; case 'successPage': ?><br>    
	 <h2>Password Recovery</h2><br>    
	 <p>An email has been sent to you with instructions on how to reset your password. <strong>(Mail will not send unless you have an smtp server running locally.)</strong><br /><br />
	 <a href="loginvalidate.php">Return</a> to the login page. </p><br>    <p>
	 This is the message that would appear in the email:</p><br>    <div class="message"><?= $passwordMessage;?></div><br>    
	 
	 <?php break;
	case 'recoverForm': ?>
    <h2>Password Recovery</h2>
	
    <?php 
	$key = $_GET['email'];
	echo $key;
	$securityUser = getEmployeeID($key); echo $securityUser; ?>.</p>
    <p>In the fields below, enter your new password.</p>
    <?php if ($error == true) { ?><span class="error">The new passwords must match and must not be empty.</span><?php } ?>
    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="fieldGroup"><label for="pw0">New Password</label><div class="field"><input type="password" class="input" name="pw0" id="pw0" value="" maxlength="20"></div></div>
        <div class="fieldGroup"><label for="pw1">Confirm Password</label><div class="field"><input type="password" class="input" name="pw1" id="pw1" value="" maxlength="20"></div></div>
        <input type="hidden" name="subStep" value="2" />
        <input type="hidden" name="empID"  value="<?php echo $securityUser ?>" />
        <input type="hidden" name="key" value="<?php echo $key ?>" />
        <div class="fieldGroup"><input type="submit" value="Submit" /></div>
        <div class="clear"></div>
    </form>
	
    <?php break; case 'invalidKey': ?>
		<h2>Invalid Key</h2>
		<p>The key that you entered was invalid. Either you did not copy the entire key from the email, you are trying to use the key after it has expired (3 days after request), or you have already used the key in which case it is deactivated.<br /><br /><a href="loginvalidate.php">Return</a> to the login page. </p>
		
    <?php break; case 'recoverSuccess': ?>
		<h2>Password Reset</h2>
		<p>Congratulations! your password has been reset successfully.</p><br /><br /><a href="loginvalidate.php">Return</a> to the login page. </p>
    
	<?php break; case 'speedLimit': ?>
		<h2>Warning</h2>
		<p>You have answered the security question wrong too many times. You will be locked out for 15 minutes, after which you can try again.</p><br /><br /><a href="loginvalidate.php">Return</a> to the login page. </p>
    
	<?php break; }
		ob_flush();
		$mySQL->close();
?>

</div>
</div>

</body>
</html>