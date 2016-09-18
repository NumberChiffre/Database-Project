<?php
	session_start();
    if($_SESSION['user_type'] !== '1') {
        redirect('index.php?');
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
    <title>System Details</title>
    <link rel='shortcut icon' type='image/x-icon' href='images/favicon.ico' />
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
        <h2>System Details</h2>
    </div>
            <div class="row">
              <div class="panel panel-default">
                <div class="panel-body">
                  <br/>
                  <table class="table table-hover">
					<br/>
                    <thead>
                      <tr>
                        <th>Start date</th>
                        <th>End date</th>
						<th>Upload Limit</th>
                      </tr>
                    </thead>
                    <tbody>
                    <tbody>
                      <?php
                       include 'database.php';
                       $pdo = Database::connect();
                       $sql = 'SELECT * FROM System_details';
                       foreach ($pdo->query($sql) as $row) {
                                echo '<tr>';
									echo '<td>'. $row['start_date'] . '</td>';
									echo '<td>'.  $row['end_date'] . '</td>';
									echo '<td>'.  $row['max_data'] . 'kb</td>';
                                echo '</tr>';
                       }
                       Database::disconnect();
                  ?>
                    </tbody>
                  </table>
				 <a class="btn btn-default" href="systemupdate.php?">Change</a>
				 <a class="btn btn-default" href="course.php">Back</a>
                </div><!-- /panel body -->
              </div><!-- /panel -->
            </div>
    </div> <!-- /container -->
  </body>
</html>