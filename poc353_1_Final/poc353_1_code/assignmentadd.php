<?php
    session_start();
	
	//only allow teachers to see this page
	if($_SESSION['user_type'] !== '2') {
        redirect('index.php?');
    }
	
    if ( !empty($_POST)) {
        // keep track validation errors
        $ass_idError = null;
         
        // keep track post values
        $ass_id = $_POST['assignment_id'];
         
        // validate input
        $valid = true;    
         
        // insert assignment data once the user submits the result from the radio buttons
        if ($valid) {
            require('config.php');
			
			if (isset($_POST['submit'])){
				if (isset($_POST['assignment_type'])){
					$assVal = mysql_real_escape_string($_POST['assignment_type']);
					mysql_query("INSERT INTO Assignments (assignment_type) values('$assVal')");
				}
			}
			
			//make sure that new assignments are then provided for each group enrolled in the course
			$last_id = mysql_insert_id($link);
			$sql2 = 'select group_id from Groups ORDER BY group_id ASC';
			$result = mysql_query($sql2, $link);
			
			//do this for every single group so that each group has the same assignment
			while ($row = mysql_fetch_assoc($result)){
				$sql3= "insert into Files (is_archived, group_id, assignment_id) values (0, ".$row['group_id'].", ".$last_id.")";  
				mysql_query($sql3, $link);
			}
			mysql_close($link);
			redirect('course.php');
        }
    }
	
	function redirect($url) {
        ob_start();
        header('Location: '.$url);
        ob_end_flush();
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Assignment/Project</title>
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
        <h2>Add Assignment/Project</h2>
        </div>
		<div class="row">
		<p class="alert alert-success">Are you sure you want to add assignment/project?</p>
		<div class="panel panel-default">
			<div class="panel-body">
				<form class="form-horizontal" action="assignmentadd.php" method="post">
					<input name="assignment_id" type="hidden" placeholder="Assignment ID " value="<?php echo !empty($assignment_id)?$assignment_id:'';?>">
					<?php if (!empty($ass_idError)): ?>
						<span class="help-inline"><?php echo $ass_idError;?></span>
					<?php endif;?>
				<div class="span10 offset1">
					<form action = "assignmentadd.php" method = "post">
					  <input type="radio" name="assignment_type" value="1" checked> Assignment<br>
					  <input type="radio" name="assignment_type" value="2"> Project<br>
					  <br/>
					  <input type="submit" name="submit" class= "btn btn-default" value="Create">
					</form>
					<a class="btn btn-default" href="course.php">Back</a>
					</div>
					</div>
					</div>
			</div>	  
        </div>	
		<?php echo "<span><a href='course.php'</span>";?>   
    </div> <!-- /container -->
  </body>
</html>