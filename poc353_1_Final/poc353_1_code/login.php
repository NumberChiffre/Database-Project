<?php
   session_start();
   function userCheck(){

   	require_once('config.php');
   	

	$username=mysql_real_escape_string($_POST["username"]);
   	$password=mysql_real_escape_string($_POST["password"]);

	$sql  = "SELECT user_id, username, password, user_type, group_id FROM Users WHERE username='".$username."' AND password='".$password."';";

	$result = mysql_query($sql, $link);
	global $failedLogin;

	if (! $result) {
	   die('Could not get data: ' . mysql_error());
	} 
	else if(mysql_num_rows($result)==0)
	{
		$failedLogin=true;
	}
	else{
            $value=mysql_fetch_assoc($result);
	    //create the user's session
            $_SESSION['user_id']=$value["user_id"];
            $_SESSION['username']=$value["username"];
            $_SESSION['user_type']=$value["user_type"];
            $_SESSION['group_id']=$value["group_id"];
            $failedLogin=false;
            //update the users last login to trigger the db file deletion
            $sql2 = "UPDATE Users SET lastlogin = now() WHERE username='".$username."' AND password='".$password."';";
            mysql_query($sql2, $link);
			
			$sql3 = "SELECT * from System_details limit 1;";
			$result3 = mysql_query($sql3, $link);
			$value3=mysql_fetch_assoc($result3);
			$_SESSION['max_data'] = $value3["max_data"];
			$_SESSION['start_date']=$value3["start_date"];
            $_SESSION['end_date']=$value3["end_date"];
			
            mysql_close($link);
            if($_SESSION['user_type'] !== '4' || $_SESSION['group_id'] === NULL) {
                redirect('course.php');
            } else {
                redirect('group.php');
            }
        }
   }
   
   function redirect($url) {
        ob_start();
        header('Location: '.$url);
        ob_end_flush();
        die();
    }
    
   if(isset($_POST["submit"]))
   {
        userCheck();
   }

?>

<html>
    <head>
		<title>Login</title>
		<link rel='shortcut icon' type='image/x-icon' href='images/favicon.ico' />
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    </head>    
    <body>
	<section id="login">
	<div class="container">
	<div class="page-header">
            <h2>Login</h2>
		</div>
		
		<div class="row">
			<div class="panel panel-default">
			<div class="panel-body">
            <form id=" form1" method="post" class="classA" action="login.php">
		<label for="username">
            Username: <br>
            <input type="text" class="form-control" name="username" id ="username" placeholder= "Username"/>
		</label>
		<br>
		<label for= "password">
            Password: <br>
            <input type="password" class="form-control" name="password" id ="password" placeholder= "Password"/>
		</label>
		<br>
		<input id="button" type="submit" name="submit" class = "btn btn-default" value="Login"></input>
		<?php
			if($failedLogin)
				echo "<br><br><strong><div id='failedLogin' class = 'text-danger'><p>*Error. Please Enter Valid Credentials.</p></div></strong>"
		?>
            </form>
			</div>
			</div>
			</div>
			</div>
	</section>
    </body>
</html>
