<?php
	session_start();
  	//echo "Logout Successfully ";
	unset($_SESSION);
	session_destroy();
	session_write_close();
  	ob_start();
  	header("Location: login.php");
  	ob_end_flush();
  	die;
?>