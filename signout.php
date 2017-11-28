<?php
	require_once __DIR__ . '/Config/database.php';
	if(session_destroy())
	{
		header('Location: loginvalidate.php');
	}
	
?>
