<?php
	session_start();
	//not logged in go to login
	if($_SESSION['user_type'] === NULL) {
		redirect('login.php');
	}
	else if($_SESSION['user_type'] !== '4' || $_SESSION['group_id'] === NULL) {
		//go to course if not a group member
		redirect('course.php');
    } else {
        redirect('group.php');
    }
	
    function redirect($url) {
        ob_start();
        header('Location: '.$url);
        ob_end_flush();
        die();
    }
?>
