<?php
    
	session_start();
    require 'database.php';
	
	//prevent non-admin users from accessing admin's view
	if($_SESSION['user_type'] !== '1') {
        redirect('index.php?');
    }
	
	function redirect($url) {
		ob_start();
		header('Location: '.$url);
        ob_end_flush();
        die();
	}
    $ids = 0;
	
    if ( !empty($_GET['id'])) {
        $ids = $_REQUEST['id'];
    }
 
    if ( !empty($_POST)) {
        // keep track validation errors
        $fnameError = null;
        $lnameError = null;
        $unameError = null;
        $passError = null;
         
        // keep track post values
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $uname = $_POST['uname'];
        $pass = $_POST['pass'];
        $id = $_POST['id'];
         
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
        if (empty($pass)) {
            $passError = 'Please enter Password';
            $valid = false;
        }
         
        // create new user and insert user data into database
        if ($valid) {
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO Users (user_id,firstname,last,username,password,user_type,grade,group_id,lastlogin) values(null,?, ?, ?, ?, 4, null, ?,null)";
            $q = $pdo->prepare($sql);
            $q->execute(array($fname, $lname, $uname, $pass, $id));
            Database::disconnect();
			header("Location: groupedit.php?id=$id");
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <title>User Management</title>
   <link rel='shortcut icon' type='image/x-icon' href='images/favicon.ico' />
		<meta charset="utf-8">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <body>
	    <div class="container">
            <div class="page-header">
			<div class='btn-toolbar pull-right'>
        			<a href="logout.php" class="btn btn-danger btn-lg">
          				<span class="glyphicon glyphicon-log-out"></span> Log out
        			</a>
      			</div>
                <h2>User Management</h2>
			</div>
			<div class="row">
			<div class="panel panel-default">
			<div class="panel-body">
			<?php 
				echo "<span><a href='createuser.php' class='btn btn-default'>Create User</a></span>";
			?>
			<br/>
			<br/>
			<section id="version">
				<table class="table table-hover">
					<thead>
						<th>User ID</th>
						<th>Username</th>
						<th>First Name</th> 
						<th>Last Name</th>
						<th>User Type</th>
						<th></th>
						<th></th>
				</thead>
                 <?php
                       $pdo = Database::connect();
					   //display user information in usermanagement.php
                       $sql = 'SELECT * FROM Users ORDER BY user_id ASC';
                       foreach ($pdo->query($sql) as $row) {
                                echo '<tr>';
									echo '<td>'. $row['user_id'] .'</td>';
									echo '<td>'. $row['username'] .'</td>';
									echo '<td>'. $row['firstname'] .'</td>';
									echo '<td>'. $row['last'] .'</td>';
									echo '<td>'. $row['user_type'] .'</td>';
									echo '<td width=250>';
								echo '<td>';
								//only admins can edit and delete, but only the first admin who was ever created can do this
								if ($row['user_id'] !== "1"){
									echo '<a class="btn btn-default" href="edituser.php?id='.$row['user_id'].'">Edit</a>';
									echo ' ';
									echo '<a class="btn btn-default" href="deleteuser.php?id='.$row['user_id'].'">Delete</a>';
								}
								//hide edit and delete buttons for the original admin, if he is deleted, then no one can add new users
								else{
									echo '<a class="btn btn-default" input style="visibility:hidden;" type="hidden" vhref="#">View</a>';
								}
								echo '</td>';
                            echo '</tr>';
                       }
                       Database::disconnect();
                  ?>
            </table>
			<a class='btn btn-default' href='course.php'>Back</a>
		</div>
		</div>
		</div>
    </body>
</html>
