<?php
require 'database.php';
session_start();
  $gid = $_SESSION['id'];
    $id = null;
    if ( !empty($_GET['id'])) {
        $id = $_REQUEST['id'];
    }
     
     if ( null==$id ) {
        //header("Location: admin.php");
    } 

    if(isset($_POST['chkbox'])) {
      foreach ($_POST['chkbox'] as $key => $value) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $sql = "UPDATE Users  set group_id = ? WHERE user_id = ?";
          $q = $pdo->prepare($sql);
          $q->execute(array($gid, $value));
      }
      
      Database::disconnect();
      header("Location: groupedit.php?id=$gid");
    } else {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $id = null;
        $sql = "SELECT * FROM Users where group_id IS NULL AND user_type = 4";
        $q = $pdo->prepare($sql);
        $q->execute();
        $user_data = $q->fetchALL(PDO::FETCH_ASSOC);
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
	<form action="adduser.php" method="post">
    <table class="table table-hover">
      <thead>
        <tr>
          <th></th>
          <th>User Name</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>User Type</th>
        </tr>
      </thead>
      <tbody>
        <?php
          foreach ($user_data as $row) {
            echo '<tr>';
            echo '<td><input type="checkbox" name="chkbox[]" value="' . $row["user_id"] . '"></td>';
            echo '<td>'. $row['username'] . '</td>';
            echo '<td>'. $row['firstname'] . '</td>';
            echo '<td>'. $row['last'] . '</td>';
            if($row['user_type'] == 2) {
              echo '<td>'. 'Teacher' . '</td>';
            } else {
              echo '<td>'. 'Student' . '</td>';
            }
            echo '</tr>';
          }
        ?>
      </tbody>
    </table>
    <form>
	<button type="submit" class="btn btn-default">Add Member</button> 
    <a class="btn btn-default" href="course.php">Back to course</a>
</div>  
    	

  </div>
    </div>
</body>
</html>