<?php
  if(empty($_SESSION["authenticated"])){ 
    header("LOCATION:loginvalidate.php"); /* REDIRECT USER TO LOGIN PAGE */
  }
?>