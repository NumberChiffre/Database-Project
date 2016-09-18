<?php 
	
	session_start();
	require 'database.php';
	
	//only allow admins to access this page
	if($_SESSION['user_type'] !== '1') {
        redirect('index.php?');
    }
	
	function redirect($url) {
		ob_start();
		header('Location: '.$url);
        ob_end_flush();
        die();
	}
	
	$id = 0;
	if ( !empty($_GET['id'])) {
		$id = $_REQUEST['id'];
	}
	
	if ( !empty($_POST)) {
	
		// keep track validation errors
		$fnameError = null;
        $lnameError = null;
        $unameError = null;
        $passError = null;
        $typeError = null;
		
        // keep track post values
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $uname = $_POST['uname'];
        $password = $_POST['password'];
        $id = $_POST['id'];
        $type = $_POST['type'];
		
        // validate input
        $valid = true;
        if (empty($fname)) {
            $fnameError = 'Please enter First Name';
            $valid = false;
        }
        if (empty($lname)) {
            $lnameError = 'Please enter Last Name';
            $valid = false;
        }
        if (empty($uname)) {
            $unameError = 'Please enter User Name';
            $valid = false;
        }
        if (empty($password)) {
            $passError = 'Please enter Password';
            $valid = false;
        }
		if (empty($type)) {
            $typeError = 'Please enter Type';
            $valid = false;
        }
		
		global $duplicate;
		
		// edit user data and only allow such action if username does not exist in the database
		if ($valid) {
			require('config.php');
            $uname = mysql_real_escape_string($uname);
            $id = mysql_real_escape_string($id);
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE Users set firstname = ?, last = ?, username = ?,password = ?, user_type = ? WHERE user_id = ?;";
			$q = $pdo->prepare($sql);
			$result = mysql_query("SELECT * From Users WHERE username = '".$uname."' AND user_id != '".$id."';");
			
			// only allow editing if username does not exist
			if (mysql_num_rows($result) == 0){
				$duplicate = 0;
				$q->execute(array($fname,$lname,$uname,$password,$type,$id));
				Database::disconnect();
				header("Location: usermanagement.php");
			}
			
			//if duplicate, do not allow
			else{
				$duplicate = 1;
				Database::disconnect();
			}
		}
	} 
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <title>Edit User</title>
   <link rel='shortcut icon' type='image/x-icon' href='images/favicon.ico' />
		<meta charset="utf-8">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
</head>

<body>
    <div class="container">
		<div class="span10 offset1">
			<div class="page-header">
			<div class='btn-toolbar pull-right'>
			<a href="logout.php" class="btn btn-danger btn-lg">
			  <span class="glyphicon glyphicon-log-out"></span> Log out
			</a>
		</div>
			<h2>Edit User</h2>
			</div>
			<div class="row">
			<div class="panel panel-default">
			<div class="panel-body">
			<form class="form-horizontal" action="edituser.php" method="post">
				<input type="hidden" name="id" value="<?php echo $id ;?>">
				<div class="control-group <?php echo !empty($fnameError)?'error':'';?>">
				<label class="control-label">First Name:</label>
				<div class="controls">
					<input name="fname" type="text" class="form-control" placeholder="First Name" value="<?php echo !empty($fname)?$fname:'';?>">
					<?php if (!empty($fnameError)): ?>
						<span class="help-inline"><?php echo $fnameError;?></span>
					<?php endif; ?>
				</div>
			</div>
			<div class="control-group <?php echo !empty($lnameError)?'error':'';?>">
				<label class="control-label">Last Name:</label>
				<div class="controls">
					<input name="lname" type="text" class="form-control" placeholder="Last Name" value="<?php echo !empty($lname)?$lname:'';?>">
					<?php if (!empty($lnameError)): ?>
						<span class="help-inline"><?php echo $lnameError;?></span>
					<?php endif; ?>
				</div>
			</div>
			<div class="control-group <?php echo !empty($unameError)?'error':'';?>">
				<label class="control-label">Username:</label>
				<div class="controls">
					<input name="uname" type="text" class="form-control" placeholder="Username" value="<?php echo !empty($uname)?$uname:'';?>">
					<?php if (!empty($unameError)): ?>
						<span class="help-inline"><?php echo $unameError;?></span>
					<?php endif; ?>
				</div>
			</div>
			<div class="control-group <?php echo !empty($passError)?'error':'';?>">
				<label class="control-label">Password:</label>
				<div class="controls">
					<input name="password" type="password" class="form-control" placeholder="Password" value="<?php echo !empty($password)?$password:'';?>">
					<?php if (!empty($passError)): ?>
						<span class="help-inline"><?php echo $passError;?></span>
					<?php endif;?>
				</div>
			</div>
			<br/>
			<div class="span10 offset1">
				<form action = "edituser.php" method = "post">
				  <input type="radio" name="type" value=4 checked> Student<br>
				  <input type="radio" name="type" value=3> Teaching Assistant<br>
				  <input type="radio" name="type" value=2> Teacher<br>
				  <input type="radio" name="type" value=1> Administrator<br>
				  <br/>
				  <input type="submit" name="submit" class= "btn btn-default" value="Update">
				</form>
				<a class="btn btn-default" href="usermanagement.php">Back</a>
			</div>
			</div>
			</div>
			<br>
			</form>
			</div>
			</div>
			</div>
		</div>
    </div> <!-- /container -->
  </body>
</html>
<script>
	<?php 
	 if($duplicate === 1){
		echo "toastr.error('duplicate User!!');";
	 }
	?>
</script>