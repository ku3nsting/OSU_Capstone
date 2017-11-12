<?php
	if(!empty($_SESSION["authenticated"])){ /* IF USERNAME IS ALREADY ASSIGNED ON SESSION VARIABLE */
    header("LOCATION:cindex.php"); /* REDIRECT USER TO INDEX PAGE */
	}
	
	//Turn on error reporting
	ini_set('display_errors', 'On');

	//TEMP value (database from a previous class)
	//we'll change this to connect to Employee recognition db
	//Connects to the database
	require_once __DIR__ . '/Config/database.php';
	$mysqli = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);
	
	?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
    <title>Employee Recognition - Login</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap.css" rel="stylesheet">
	
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  </head>
  <body>
  
   <div id="bodyDiv">
 
		

 <div class="wrapper">
    <form class="form-signin" name="signForm" action="login.php" method="post">       
	 <img src = "resources/fakelogo.png" alt="Company Logo" style="width:70%;height:70%;"></a>
      <h2 class="form-signin-heading">
	  Sign in</h2>
      <input type="text" class="form-control" id="email" name="email" placeholder="Email Address" required="" />
      <input type="password" class="form-control" id="password" name="password" placeholder="Password" required="" />      
	  <div id="checkboxThing">
      <label class="checkbox">
        <input type="checkbox" value="remember-me" id="rememberMe" name="rememberMe"> Remember me
      </label>
	  </div>
      <button class="btn btn-lg btn-primary btn-block" id="submit" type="submit">Submit</button>   
	  <p>
	  <p>
	  <a href = "recoverPassword.php">Forgot Password?</a>
    </form>
</form>

	
	
  </div>
	
  </div>
 

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>