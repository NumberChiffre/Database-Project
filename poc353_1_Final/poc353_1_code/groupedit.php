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

	$id = null;
	if ( !empty($_GET['id'])) {
		$id = $_REQUEST['id'];
		$_SESSION['id'] = $id;
	}
	
	if ( null==$id ) {
		header("Location: course.php");
	} else {
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM Users where group_id = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($id));
		$user_data = $q->fetchALL(PDO::FETCH_ASSOC);

    //Group data
    $sql = "SELECT *  FROM Groups where group_id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($id));
    $group_data = $q->fetch(PDO::FETCH_ASSOC);
		
    Database::disconnect();
	}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Group Members</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

</head>
<body>
    <div class="container">
			<div class="page-header">
				<div class='btn-toolbar pull-right'>
        <a href="logout.php" class="btn btn-danger btn-lg">
          <span class="glyphicon glyphicon-log-out"></span> Log out
        </a>
      </div>
				
				
				<h2>Group Members</h2>
            </div>
		   <div class="row">
			<div class="panel panel-default">
			<div class="panel-body">
			<?php echo '<a class="btn btn-default" href="adduser.php?id='.$id.'">Add Member</a>'; ?>
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>User Id</th>
                      <th>First Name</th>
					  <th>Last Name</th>
                      <th>User Name</th>
                      <th>User Type</th>
                      
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                   foreach ($user_data as $row) {
                    if($group_data['leader_id'] == $row['user_id']) {
                      echo '<tr class="info">';
                    } else {
                      echo '<tr>';
                    }
                            
                            echo '<td>'. $row['user_id'] . '</td>';
                            echo '<td>'. $row['firstname'] . '</td>';
							echo '<td>'. $row['last'] . '</td>';
                            echo '<td>'. $row['username'] . '</td>';
                            if($row['user_type'] == 2) {
                              echo '<td>'. 'Teacher' . '</td>';
                            } else {
                               echo '<td>'. 'Student' . '</td>';
                            }
                                       
                            echo '<td>';
                                echo '<a class="btn btn-default" href="modifygroup.php?id='.$row['user_id'].'">Modify Group</a>';
                                echo ' ';
                                if(isset($group_data)) {
                                  if($group_data['leader_id'] == $row['user_id']) {
                                        echo '<a class="btn btn-default" disabled>Make Leader</a>';
                                  } else {
                                    echo '<a class="btn btn-default" href="addleader.php?id='.$row['user_id'].'">Make Leader</a>';
                                  }
                                echo ' ';
                                }
                                
							               echo '<a class="btn btn-default" href="memberdelete.php?id='.$row['user_id'].'">Remove</a>';
								            echo '</td>';
                            echo '</tr>';
                   }
                  ?>
                  </tbody>
                </table>
				<a class="btn btn-default" href="course.php">Back</a>
            </div>
			</div>
			</div>
    </div> <!-- /container -->
  </body>
</html>