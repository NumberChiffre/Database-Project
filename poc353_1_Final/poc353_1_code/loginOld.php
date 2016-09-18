<?php
 	
   function userCheck(){

   	require_once('config.php');
   	

	 $username=$_POST["username"];
   	 $password=$_POST["password"];

	$sql  = "SELECT username, password FROM Users WHERE username='".$username."' AND password='".$password."';";

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
        $_SESSION['username']=$value["username"];
        $failedLogin=false;
        mysql_close($link);
		
    }

   }
   if(isset($_POST["submit"]))
   {
   userCheck();
   }

?>

<html>

    <head>
    </head>    
<body>




	<section id="login">
	<h2> Welcome:</h2>
	<form id=" form1" method="post" class="classA" action="login.php">
		<label for="username">
			user: <br>
			<input type="text" name="username" id ="username" placeholder= "Enter your Username"/>
			</label>

		<label for= "password">
			Password: <br>
			<input type="password" name="password" id ="password" placeholder= "Enter your Password"	/>
			</label>
		<input id="button" type="submit" name="submit"> </input>		
		<?php
			if($failedLogin)
				echo "<div id='failedLogin'><p>*Error. Please Enter Valid Credentials.</p></div>"
		?>
		</form>
		</section>
		



    </body>
</html>




