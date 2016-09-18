<?php
     session_start();
	if($_SESSION['user_type'] !== '2') {
        redirect('index.php?');
    }
	
    if ( !empty($_POST)) {
        // keep track validation errors
        $group_idError = null;
         
        // keep track post values
        $group_id =$_POST['group_id'];
         
        // validate input
        $valid = true;    
        /*if (empty($group_id)) {
            $group_idError = 'Please enter group_id';
            $valid = false;
        } */
         
        // insert data
        if ($valid) {
            //$pdo = Database::connect();
			require('config.php');
			
            $sql = "INSERT INTO Groups (data_taken) values(0.0)";
           mysql_query($sql, $link);

		   //$q = $pdo->prepare($sql);
            //$q->execute();
			
			$last_id = mysql_insert_id($link);
			$sql2 = 'select assignment_id from Assignments ORDER BY assignment_id ASC';
			 $result = mysql_query($sql2, $link);
			 while ($row = mysql_fetch_assoc($result)){
							$sql3= "insert into Files (is_archived, group_id, assignment_id) values (0, ".$last_id.", ".$row['assignment_id'].")";  
						    mysql_query($sql3, $link);
			 }
			 //foreach (mysql_query($sql3, $link) as $row){
			//foreach ($pdo->query($sql2) as $row) {
                          // $sql3= "insert into Files (is_archived, group_id, assignment_id) values (0, ".$last_id.", ".$row['assignment_id'].")";  
						   // mysql_query($sql3, $link);
							
							//$q = $pdo->prepare($sql3);
							//$q->execute();
            //}
			
			mysql_close($link);
			
			redirect ('course.php');
			
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
    <title>Create Group</title>
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
                        <h2>Create Group</h2>
                    </div>
                    <div class="row">
					 <p class="alert alert-success">Are you sure you want to create group?</p>
			<div class="panel panel-default">
			<div class="panel-body">
					<form class="form-horizontal" action="groupadd.php" method="post">
                        <input name="group_id" type="hidden" placeholder="group Id " value="<?php echo !empty($group_id)?$group_id:'';?>">
                        <?php if (!empty($group_idError)): ?>
                            <span class="help-inline"><?php echo $group_idError;?></span>
                        <?php endif;?>
					  <div class="form-actions">
                          <button type="submit" class="btn btn-default">Create</button>
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