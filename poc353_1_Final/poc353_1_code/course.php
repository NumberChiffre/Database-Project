<?php
	session_start();
	//if group member student go to group page
	if($_SESSION['user_type'] === '4' && $_SESSION['group_id'] !== NULL) {
        redirect('group.php?');
    }
	//if this page is loaded through post and the post action is joinGroup
	if(isset($_POST['action'])) {
		if($_POST['action'] === 'joinGroup') {
        	joinGroup();
    }
  }
	
	function joinGroup() {
        require('config.php');
	//retrieve id of group to join
	$group = mysql_real_escape_string($_POST['group']);
	//add group id to user in db
        $sql = "UPDATE Users SET group_id = '".$group."' WHERE user_id = '".$_SESSION['user_id']."';";
        if(mysql_query($sql, $link)){
	    //update users session
            $_SESSION['group_id']=$group;
            mysql_close($link);
        }
    }
	
	function redirect($url) {
        ob_start();
        header('Location: '.$url);
        ob_end_flush();
        die();
    }
?>

<script>
    function joinGroup(id) {
		$.ajax({
			type: "POST",
			url: 'course.php',
			dataType: 'text',
			data:{action:'joinGroup', group:id},
			success: function(data) {
				//once post is complete go to the group page
				window.location="group.php";
			}
		});
    }
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Course Page</title>
	<meta charset="utf-8">
	<link rel='shortcut icon' type='image/png' href='images/favicon.ico' />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script
 
</head>
<body>
  <div class="container">
    <div class='page-header'>
      <div class='btn-toolbar pull-right'>
        <a href="logout.php" class="btn btn-danger btn-lg">
          <span class="glyphicon glyphicon-log-out"></span> Log out
        </a>
      </div>
        <h2>Course Page</h2>
    </div>
            <div class="row">
              <div class="panel panel-default">
                <div class="panel-body">
                  <h4>Groups</h4>
				  <?php if($_SESSION['user_type'] === '2') 
					{
					echo "<span><a href='groupadd.php' class='btn btn-default'>Add Group</a></span>";
					echo "     ";
					echo "<span><a href='assignmentadd.php' class='btn btn-default'>Add Assignment/Project</a></span>";
					}
					
					if($_SESSION['user_type'] === '1')
					{
						echo "<span><a href='systemdetails.php' class='btn btn-default'>System Details</a></span>";
						echo "     ";
						echo "<span><a href='usermanagement.php' class='btn btn-default'>User Management</a></span>";
						echo "     ";
						echo "<input type='button' class='btn btn-default' onClick=\"parent.location='zipper.php'\" value='Archive Course' formtarget='_blank'> ";
					}
                  ?>
                  <br/>
                  <table class="table table-hover">
					<br/>
                    <thead>
                      <tr>
                        <th width=50>Group Number</th>
                        <th width=250>Operations</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                       include 'database.php';
                       $pdo = Database::connect();
                       $sql = 'SELECT * FROM Groups ORDER BY group_id ASC';
                       foreach ($pdo->query($sql) as $row) {
                                if(($_SESSION['user_type'] === '4' && $_SESSION['end_date'] > date("Y-m-d H:i:s") && $_SESSION['start_date'] < date("Y-m-d H:i:s")) || $_SESSION['user_type'] === '3' || $_SESSION['user_type'] === '2' || $_SESSION['user_type'] === '1')
								{
								
								echo '<tr>';
									echo '<td><a href="group.php?group='.$row['group_id'].'">'. $row['group_id'] . '</a></td>';
									echo '<td width=250>';
									if($_SESSION['user_type'] === '2') {
										echo '<a class="btn btn-default" href="group.php?group='.$row['group_id'].'">View</a>';
										echo ' ';
										echo '<a class="btn btn-default" href="groupedit.php?id='.$row['group_id'].'">Edit</a>';
										echo ' ';
										echo '<a class="btn btn-default" href="groupdelete.php?id='.$row['group_id'].'">Delete</a>';
										echo ' ';
									}
									if($_SESSION['user_type'] === '3' || $_SESSION['user_type'] ==='1')
                  {
                    echo '<a class="btn btn-default" href="group.php?group='.$row['group_id'].'">View</a>';
                    echo ' ';
                  }
									if($_SESSION['user_type'] === '4') {
									echo "<a class='btn btn-default' href='#' onclick='joinGroup(".$row['group_id'].")'>Join</a>";
										echo ' ';
									}
									echo '</td>';
                                echo '</tr>';
								}
                       }
                       Database::disconnect();
                  ?>
                    </tbody>
                  </table>
                </div><!-- /panel body -->
              </div><!-- /panel -->
            </div>
    </div> <!-- /container -->
  </body>
</html>
