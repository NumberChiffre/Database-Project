<?php 
	
	require 'database.php';
	session_start();
	$gid = $_SESSION['id'];
	$id = null;
	if ( !empty($_GET['id'])) {
		$id = $_REQUEST['id'];
	}
	
	//if ( null==$id ) {
	//	header("Location: memberedit.php");
	//}
	
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
		
		// update data
		if ($valid) {
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE Users  set firstname = ?,last = ?,username = ?,password = ? WHERE user_id = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($fname,$lname,$uname,$pass,$id));
			Database::disconnect();
			header("Location: groupedit.php?id=$gid");
		}
	} else {
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM Users where group_id = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($gid));
		$user_data = $q->fetchALL(PDO::FETCH_ASSOC);
		Database::disconnect();
	}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <title>Update Member</title>
   <link rel='shortcut icon' type='image/x-icon' href='images/favicon.ico' />
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
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
			<br/>
			<section id="version">
				<table class="table table-hover">
					<thead>
						<th>Version Number</th>
						<th>File Name</th> 
						<th>File Size</th>
						<th>Operations</th>
						<th></th>
						<th></th>
						<th class='btn-toolbar pull-left'></th>
				</thead>
                <?php foreach($versions as $version): ?>
                <tr>
                    <td class='btn-toolbar pull-left'>
						<?php
						echo $version->versionNumber;
						?></td>
								<td><?php
						echo $version->name;
						?></td>
								<td><?php
						echo $version->size;
						?></td>
								<td><?php
						echo "<input type='button' class='btn btn-default' onClick=\"parent.location='download.php?fileid=".$file."&version=".$version->versionNumber."'\" value='Download' formtarget='_blank'></td>";
								?></td>
								<td><?php
									if($version->is_current === '0' && $leader === $_SESSION['user_id']) {
										echo "<button class='btn btn-default' onclick='rollbackVersion(".$version->versionNumber.", ".$file.")'>Rollback</button>";
									}
								?></td>
								<td><?php
									if($version->is_deleted === '0') {
										echo "<button class='btn btn-default' onclick='deleteVersion(".$version->versionNumber.", ".$file.")'>Delete</button>";
									}
								?></td>
								<td><?php
									if($version->is_deleted === '1') {
										echo "<button class='btn btn-default' onclick='recoverVersion(".$version->versionNumber.", ".$file.")'>Recover</button>";
									}
						?></td>
				</tr>
				<?php endforeach; ?>
            </table>
			
			<a class='btn btn-default' href='group.php'>Back</a>
        
		</div>
		</div>
		</div>
    </body>
</html>
