<?php 
	session_start();
	if($_SESSION['user_type'] !== '2') {
        redirect('index.php?');
    }
	
	function redirect($url) {
		ob_start();
		header('Location: '.$url);
        ob_end_flush();
        die();
	}
	
	require 'database.php';
	$id = 0;
	
	if ( !empty($_GET['id'])) {
		$id = $_REQUEST['id'];
	}
	
	if ( !empty($_POST)) {
		// keep track post values
		$id = $_POST['id'];
		
		// delete data
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "DELETE FROM Groups WHERE group_id = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($id));
		Database::disconnect();
		header("Location: course.php");
		
	} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Delete Group</title>
	<link rel='shortcut icon' type='image/x-icon' href='images/favicon.ico' />
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
					<h2>Delete Group</h2>
		    		</div>
					<div class="row">
					<p class="alert alert-danger">Are you sure you want to delete group?</p>
			<div class="panel panel-default">
			<div class="panel-body">
	    			<form class="form-horizontal" action="groupdelete.php" method="post">
	    			  <input type="hidden" name="id" value="<?php echo $id;?>"/>
					  <div class="form-actions">
						  <button type="submit" class="btn btn-default">Delete</button>
						  <a class="btn btn-default" href="course.php">Back</a>
						</div>
					</form>
					</div>
					</div>
					</div>
				</div>
				
    </div> <!-- /container -->
  </body>
</html>