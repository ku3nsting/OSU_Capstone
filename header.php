<?php
  session_start();
  if(empty($_SESSION["authenticated"])){ 
    header("LOCATION:login.html"); /* REDIRECT USER TO LOGIN PAGE */
  }
?>