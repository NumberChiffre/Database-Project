<?php
	session_start();
	require 'database.php';
    if($_SESSION['user_type'] !== '1') {
        redirect('index.php?');
    }
	
	function redirect($url) {
		ob_start();
		header('Location: '.$url);
        ob_end_flush();
        die();
	}
    if ( !empty($_POST)) {
        // keep track validation errors
        $dateStartError = null;
        $dateEndError = null;
        $dataSizeError = null;
         require('config.php');
        // keep track post values
        $startDate = mysql_real_escape_string($_POST['startDate']);
        $endDate = mysql_real_escape_string($_POST['endDate']);
        $maxData = mysql_real_escape_string($_POST['maxData']);

		 

        // validate input
        $validStart = true;
		$validEnd = true;
        $validSize = true;

        if (!empty($startDate)) {
            if(!validateDate($startDate)){
				$dateStartError = '- Invalid starting date, must be in YYYY-MM-DD';
				$validStart = false;
			}
        }
		else{
			

		    $pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$result = mysql_fetch_array(mysql_query("SELECT * From System_details"));
			$startDate = $result['start_date'];
			Database::disconnect();	
		}
		
		
        if (!empty($endDate)) {
            if(!validateDate($endDate)){
				$dateEndError = '- Invalid end date, must be in YYYY-MM-DD';
				$validEnd = false;
			}
			if($endDate<=$startDate){
				$dateEndError = '- Invalid end date, must be after start date';
				$validEnd = false;
			}
        }
		else{

		    $pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$result  = mysql_fetch_array(mysql_query("SELECT * From System_details"));
			$endDate = $result['end_date'];
			Database::disconnect();	
		}
		

		if (!empty($maxData)) {
			$maxData = (int)$maxData;
            if(!is_int($maxData) ){
				$dataSizeError = '- Invalid input, must be an integer betweem 0 and 9999999';
				$validSize = false;
			}else
			{			
				if($maxData<=0 || $maxData > 9999999){
					$dataSizeError = '- Invalid input, must be an integer betweem 0 and 9999999';
					$validSize = false;

				}
			}
        }
		else{
		    $pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$result  = mysql_fetch_array(mysql_query("SELECT * From System_details"));
			$maxData = $result['max_data'];
			$maxData = (int)$maxData;
			Database::disconnect();	
		}



        // insert data
        if ($validStart && $validEnd && $validSize) {

            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "UPDATE System_details  SET start_date = ?, end_date =?, max_data =?;";
            $q = $pdo->prepare($sql);
            $q->execute(array($startDate,$endDate,$maxData));
            Database::disconnect();
			
			
			$sql3 = "SELECT * from System_details limit 1;";
			$result3 = mysql_query($sql3, $link);
			$value3=mysql_fetch_assoc($result3);
			$_SESSION['max_data'] = $value3["max_data"];
			$_SESSION['start_date']=$value3["start_date"];
            $_SESSION['end_date']=$value3["end_date"];
			
			
        }
        mysql_close($link);

    }	
		function validateDate($date)
		{
			$d = DateTime::createFromFormat('Y-m-d', $date);
			return $d && $d->format('Y-m-d') === $date;
		}
                
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>System Update</title>
   <link rel='shortcut icon' type='image/x-icon' href='images/favicon.ico' />
		<meta charset="utf-8">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
 
<body>
	<div class="container">
		<div class='page-header'>
			<div class='btn-toolbar pull-right'>
				<a href="logout.php" class="btn btn-danger btn-lg">
					<span class="glyphicon glyphicon-log-out"></span> Log out
				</a>
			</div>
			<div class="page-header">
				<h2> System Update</h2>
			</div>
			<div>

			<div>
		
			<div class="row">
				<div class="panel panel-default">
					<div class="panel-body">
							<?php
								if($dateStartError!=null){
									echo "<h4><font color=\"red\">$dateStartError</font></h4>";
								}
								if($dateEndError!=null){
									echo "<h4><font color=\"red\">$dateEndError</font></h4>";
								}
								if($dataSizeError!=null){
									echo "<h4><font color=\"red\">$dataSizeError</font></h4>";
								}

												
							?>
						<form id=" form1" method="post" class="classA" action="systemupdate.php">
						<table class="table table-hover">
						<br/>
						<thead>
							<tr>
								<th>Field</h>
								<th>Set Details</th>
								<th>Current Details</th>
							</tr>
						</thead>
						<tbody>
							<?php
								   $pdo = Database::connect();
								   $sql = 'SELECT * FROM System_details';
								   foreach ($pdo->query($sql) as $row) {
											echo '<tr>
													<td>
														Start date
													</td>';
											
											echo '<td><label for="startDate">
													<input type="text" class="form-control" name="startDate" id ="startDate" placeholder= "Enter new start date"/>
												  </label>
												  </td>';
												  
											echo '<td>'. $row['start_date'] . '</td>';
												
											echo '</tr>';
											
																						echo '<tr>
													<td>
														End date
													</td>';
											
											echo '<td><label for="endDate">
													<input type="text" class="form-control" name="endDate" id ="endDate" placeholder= "Enter new end date"/>
												  </label>
												  </td>';
												  
											echo '<td>'. $row['end_date'] . '</td>';
												
											echo '</tr>';
											
																						echo '<tr>
													<td>
														Data cap(kb) 
													</td>';
											
											echo '<td><label for="maxData">
													<input type="text" class="form-control" name="maxData" id ="maxData" placeholder= "Enter max data"/>
												  </label>
												  </td>';
												  
											echo '<td>'. $row['max_data'] . '</td>';
												
											echo '</tr>';
											
								   }
								   Database::disconnect();
							  ?>

							</tbody>
						</table>
							<input id="button" type="submit" name="submit" class = "btn btn-default" value="Update"></input>
							<a class="btn btn-default" href="systemdetails.php">Back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
    </body>
</html>
<script>
	<?php 
	 if($dateStartError!=null){
		echo "toastr.error('Invalid Start date');";
	 }
	 if($dateEndError!=null){
		echo "toastr.error('Invalid End date');";
	 }
	 if($dataSizeError!=null){
		echo "toastr.error('Invalid data size');";
	 }


	?>
</script>