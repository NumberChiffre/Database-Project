<?php
require 'database.php';
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

  $gid = $_SESSION['id'];
    $id = null;
    if ( !empty($_GET['id'])) {
        $id = $_REQUEST['id'];
        $_SESSION['uid'] = $id;
    }
     
     if ( null==$id ) {
        //header("Location: admin.php");
    } 

    if(isset($_POST['group'])) {
        $u_id = $_SESSION['uid'];
        $g_id = $_POST['group'];
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
        $sql = "UPDATE Users set group_id = ? WHERE user_id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($g_id, $u_id));
		$sql2 = "UPDATE Groups set leader_id = NULL WHERE leader_id = ?";
		$q2 = $pdo->prepare($sql2);
        $q2->execute(array($u_id));
      
      
      Database::disconnect();
      header("Location: groupedit.php?id=$gid");
    } else {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $id = null;
        $sql = "SELECT * FROM Groups";
        $q = $pdo->prepare($sql);
        $q->execute();
        $group_data = $q->fetchALL(PDO::FETCH_ASSOC);
         Database::disconnect();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Add Member</title>
	<link rel='shortcut icon' type='image/x-icon' href='images/favicon.ico' />
  <meta charset="utf-8">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
  <div class='page-header'>
      <div class='btn-toolbar pull-right'>
        <a href="logout.php" class="btn btn-danger btn-lg">
          <span class="glyphicon glyphicon-log-out"></span> Log out
        </a>
      </div>
        <h2>Add Member</h2>
    </div>
<div class="row">
			<div class="panel panel-default">
			<div class="panel-body">
<form action="modifygroup.php" method="post">
  <p>List of all groups:</p>
    <div class="form-group">
      <label for="group">Group list (select one):</label>
      <select class="form-control" name="group">
        <?php
        foreach ($group_data as $key => $value) {
          echo '<option value="'.$value['group_id'].'">'.$value['group_id'].'</option>';
        }
        ?>
      </select>
    </div>
  <div>
    <button type="submit" class="btn btn-default">Add Member</button>
    <a class="btn btn-default" href="course.php">Back to course</a> 
  </div>
<form>
</div>
</div>
</div>
</body>
</html>