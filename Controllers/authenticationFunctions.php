<?php 
function checkEmail($email)
{
    global $mySQL;
    $error = array('status'=>false,'userID'=>0);
    if (isset($email) && trim($email) != '') {
        //email was entered
        if ($SQL = $mySQL->prepare("SELECT `ID` FROM `Employees` WHERE `Email` = ? LIMIT 1"))
        {
            $SQL->bind_param('s',trim($email));
            $SQL->execute();
            $SQL->store_result();
            $numRows = $SQL->num_rows();
            $SQL->bind_result($userID);
            $SQL->fetch();
            $SQL->close();
            if ($numRows >= 1) return array('status'=>true,'userID'=>$userID);
        } else { return $error; }
    } else {
        //nothing was entered;
        return $error;
    }
}

function sendPasswordEmail($email)
{
    global $mySQL;
    if ($SQL = $mySQL->prepare("SELECT `fName`,`Password` FROM `Employees` WHERE `Email` = ? LIMIT 1"))
    {
        $SQL->bind_param('s',$email);
        $SQL->execute();
        $SQL->store_result();
        $SQL->bind_result($fname,$pword);
        $SQL->fetch();
        $SQL->close();
        $expFormat = mktime(date("H"), date("i"), date("s"), date("m")  , date("d")+3, date("Y"));
        $expDate = date("Y-m-d H:i:s",$expFormat);
        $key = md5($fname . '_' . $email . rand(0,10000) .$expDate . PASSWORD_DEFAULT);
        if ($SQL = $mySQL->prepare("INSERT INTO `Recovery_Email` (`Employee_Email`,`Key`,`expDate`) VALUES (?,?,?)"))
        {
            $SQL->bind_param('sss',$email,$key,$expDate);
            $SQL->execute();
            $SQL->close();
            $passwordLink = "<a href=\"?a=recover&email=" . $key . "&u=" . urlencode(base64_encode($userID)) . "\">http://34.223.203.66/recoverPassword.php?a=recover&email=" . $key . "&u=" . urlencode(base64_encode($userID)) . "</a>";
            $message = "Dear $fname,\r\n";
            $message .= "Please visit the link below to reset your password:\r\n";
            $message .= "-----------------------\r\n";
            $message .= "$passwordLink\r\n";
            $message .= "-----------------------\r\n";
            $message .= "Be sure to copy the entire link into your browser. This link will expire after 3 days for security reasons.\r\n\r\n";
            $message .= "If you did not request this forgotten password email, no action is needed, your password will not be reset as long as the link above is not visited. However, you may want to log into your account and change your password, as someone may have attempted to guess it.\r\n\r\n";
            $message .= "Thanks,\r\n";
            $message .= "-- Gemini team";
            $headers .= "From: Gemini Employee Recognition Website <kuenstir@oregonstate.edu> \n";
            $headers .= "To-Sender: \n";
            $headers .= "X-Mailer: PHP\n"; // mailer
            $headers .= "Reply-To: kuenstir@oregonstate.edu\n"; // Reply address
            $headers .= "Return-Path: kuenstir@oregonstate.edu\n"; //Return Path for errors
            $headers .= "Content-Type: text/html; charset=iso-8859-1"; //Enc-type
            $subject = "Your Lost Password";
            @mail($email,$subject,$message,$headers);
            return str_replace("\r\n","<br/ >",$passwordLink);
        }
    }
}

function checkEmailKey($key)
{
    global $mySQL;
    $curDate = date("Y-m-d H:i:s");
    if ($SQL = $mySQL->prepare("SELECT `Employee_Email` FROM `Recovery_Email` WHERE `Key` = ? AND `expDate` >= ?"))
    {
        $SQL->bind_param('ss',$key,$curDate);
        $SQL->execute();
        $SQL->execute();
        $SQL->store_result();
        $numRows = $SQL->num_rows();
        $SQL->bind_result($email);
        $SQL->fetch();
        $SQL->close();
        if ($numRows > 0 && $email != '')
        {
            return array('status'=>true,'email'=>$email);
        }
    }
    return false;
}
 
function updateEmployeePassword($empID,$password,$key)
{
    global $mySQL;
    if (checkEmailKey($key) === false) return false;
    if ($SQL = $mySQL->prepare("UPDATE `Employees` SET `Password` = ? WHERE `ID` = ?"))
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $SQL->bind_param('ss',$password,$empID);
        $SQL->execute();
        $SQL->close();
        $SQL = $mySQL->prepare("DELETE FROM `Recovery_Email` WHERE `Key` = ?");
        $SQL->bind_param('s',$key);
        $SQL->execute();
    }
}

function getEmployeeID($key)
{
    global $mySQL;
    if ($SQL = $mySQL->prepare("SELECT `Employee_Email` FROM `Recovery_Email` WHERE `Key` = ?"))
    {
        $SQL->bind_param('s',$key);
        $SQL->execute();
        $SQL->store_result();
        $SQL->bind_result($email);
        $SQL->fetch();
        $SQL->close();
    }
	if ($SQL = $mySQL->prepare("SELECT `ID` FROM `Employees` WHERE `Email` = ?"))
    {
        $SQL->bind_param('s',$email);
        $SQL->execute();
        $SQL->store_result();
        $SQL->bind_result($empID);
        $SQL->fetch();
        $SQL->close();
    }
    return $empID;
}
?>