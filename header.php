<?php
	session_start();
	ob_start();
  if(empty($_SESSION["authenticated"])){ 
    header("LOCATION:loginvalidate.php"); /* REDIRECT USER TO LOGIN PAGE */
  }
?>