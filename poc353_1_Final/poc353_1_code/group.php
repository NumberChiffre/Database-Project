<?php
	session_start();
	require_once('assignment.php');
        require_once('log.php');
        require_once('userstats.php');

	//call appropriate action based on post variable
	if(isset($_POST['action']) && $_POST['action'] === 'upload') {
        	upload();
    }
	if(isset($_POST['action']) && $_POST['action'] === 'archive') {
        	archive();
    }
	if(isset($_POST['action']) && $_POST['action'] === 'unarchive') {
        	unarchive();
    }
        
    loadAssignments();
	
	function loadAssignments() {
        	require('config.php');
       		//set group id depending on if user is a student
		if($_SESSION['user_type'] === '4') {
			$group = $_SESSION['group_id'];
		} else {
			$group = mysql_real_escape_string(htmlspecialchars($_GET['group']));
		}
		//retrieve the list of assignments
		$sql  = "SELECT f.is_archived, f.assignment_id, f.file_id, a.assignment_type, COALESCE(MAX(v.is_current), 0) as 'has_upload' "
                        . "FROM Files f join Assignments a on f.assignment_id = a.assignment_id left join File_versions v on v.file_id = f.file_id "
                        . "where f.group_id =".$group." GROUP BY f.assignment_id;";
        $result = mysql_query($sql, $link);
        global $assignments;
		$assignments = array();
        while ($row = mysql_fetch_assoc($result)) {
            	// Append all rows to an array
	    	$assignment = new Assignment;
	    	$assignment->assignmentid = $row['assignment_id'];
	    	$assignment->fileid = $row['file_id'];
	    	$assignment->assignmentType = $row['assignment_type'];
            $assignment->hasUpload = $row["has_upload"];
			$assignment->is_archived = $row['is_archived'];
            array_push($assignments, $assignment);
        }
	//retrieve group member statistics		
		$sql2 = "select * from (select a.assignment_id, a.assignment_type, CONCAT(u.firstname,\" \", u.last) as 'user', v.version_number, v.name, \"Upload\" as 'action' , v.upload_date as 'date', v.upload_ip, v.checksum, v.sizechange from File_versions v join Files f on f.file_id = v.file_id join Assignments a on a.assignment_id = f.assignment_id join Users u on u.user_id = v.upload_id where f.group_id = ".$group."
				union
				select a.assignment_id, a.assignment_type, CONCAT(u.firstname,\" \", u.last) as 'user', v.version_number, v.name, \"Rollback\" as 'action', r.rollback_date as 'date', v.upload_ip, v.checksum, v.sizechange from Rollbacks r join File_versions v on r.file_id = v.file_id and r.version_number = v.version_number join Files f on f.file_id = v.file_id join Assignments a on a.assignment_id = f.assignment_id join Users u on u.user_id = r.rollback_id where f.group_id = ".$group."
				union
				select a.assignment_id, a.assignment_type, CONCAT(u.firstname,\" \", u.last) as 'user', v.version_number, v.name, \"Delete\" as 'action', d.delete_date as 'date', v.upload_ip, v.checksum, v.sizechange from Deletes d join File_versions v on d.file_id = v.file_id and d.version_number = v.version_number join Files f on f.file_id = v.file_id join Assignments a on a.assignment_id = f.assignment_id join Users u on u.user_id = d.delete_id where f.group_id = ".$group."
				union
				select a.assignment_id, a.assignment_type, CONCAT(u.firstname,\" \", u.last) as 'user', v.version_number, v.name, \"Download\" as 'action', o.download_date as 'date', v.upload_ip, v.checksum, v.sizechange from Downloads o join File_versions v on o.file_id = v.file_id and o.version_number = v.version_number join Files f on f.file_id = v.file_id join Assignments a on a.assignment_id = f.assignment_id join Users u on u.user_id = o.download_id where f.group_id = ".$group."
				union
				select a.assignment_id, a.assignment_type, CONCAT(u.firstname,\" \", u.last) as 'user', v.version_number, v.name, \"Recover\" as 'action', e.recover_date as 'date', v.upload_ip, v.checksum, v.sizechange from Recovers e join File_versions v on e.file_id = v.file_id and e.version_number = v.version_number join Files f on f.file_id = v.file_id join Assignments a on a.assignment_id = f.assignment_id join Users u on u.user_id = e.recover_id where f.group_id = ".$group."
				) log
				group by date desc;";
		$result2 = mysql_query($sql2, $link);
		global $logs;
		$logs = array();
		while ($row = mysql_fetch_assoc($result2)) {
            	// Append all rows to an array
	    	$log = new Log;
	    	$log->assignment = $row['assignment_id'];
			$log->type = $row['assignment_type'];
	    	$log->version = $row['version_number'];
	    	$log->name = $row['name'];
            $log->user = $row["user"];
			$log->action = $row["action"];
			$log->when = $row["date"];
			$log->ip = $row["upload_ip"];
			$log->checksum = $row["checksum"];
		$log->sizechange = $row["sizechange"];
            array_push($logs, $log);
        }
		//retrieve activity log data
		$sql3 = "select CONCAT(u.firstname, \" \", u.last) as 'user', up.uploads, down.downloads, del.deletes, roll.rollbacks, rec.recovers, up.datachange from Users u 
		join (select v.upload_id as 'user_id', count(v.upload_id) as 'uploads', sum(abs(v.sizechange)) as 'datachange' from File_versions v group by v.upload_id) up on up.user_id = u.user_id 
		join (select d.download_id as 'user_id', count(d.download_id) as 'downloads' from Downloads d group by d.download_id) down on down.user_id = u.user_id 
		join (select e.delete_id as 'user_id', count(e.delete_id) as 'deletes' from Deletes e group by e.delete_id) del on del.user_id = u.user_id 
		join (select r.rollback_id as 'user_id', count(r.rollback_id) as 'rollbacks' from Rollbacks r group by r.rollback_id) roll on roll.user_id = u.user_id 
		join (select c.recover_id as 'user_id', count(c.recover_id) as 'recovers' from Recovers c group by c.recover_id) rec on rec.user_id = u.user_id  where u.group_id = ".$group.";";
		$result3 = mysql_query($sql3, $link);
		global $stats;
		$stats = array();
		while ($row = mysql_fetch_assoc($result3)) {
            	// Append all rows to an array
	    	$stat = new UserStats;
	    	$stat->name = $row['user'];
			$stat->uploads = $row['uploads'];
	    	$stat->downloads = $row['downloads'];
	    	$stat->deletes = $row['deletes'];
            	$stat->recovers = $row["rollbacks"];
		$stat->rollbacks = $row["recovers"];
		$stat->datachanged = $row["datachange"];
            array_push($stats, $stat);
        }
		
        mysql_close($link);
    }

	function upload() {
		require('config.php');
		$fileid = mysql_real_escape_string($_POST['fileid']);
		$fileName = $_FILES['file']['name'];
		$fileType = $_FILES['file']['type'];
		$fileSize = $_FILES['file']['size'];
		$fileTmpName = $_FILES['file']['tmp_name'];
		//get the amount of data used by the group
		$sql4 = "select sum(v.size) as 'size' from File_versions v join Files f on v.file_id = f.file_id where v.is_deleted = 0 AND f.group_id = ".$_SESSION['group_id'].";";
		$result4 = mysql_query($sql4, $link);
		$value4 = mysql_fetch_assoc($result4);
		//check for invalid file name
		if(isValidName($fileName)) {
			//check if file wont make group exceed max data
			if(($fileSize + $value4['size']) < ($_SESSION['max_data']+1)) {
				//add slashes to sql breaking file data, calculate checksum and retrieve ip
				$fileData = addslashes(file_get_contents($fileTmpName, FILE_USE_INCLUDE_PATH));
                		$checksum = md5_file($fileTmpName);
                		$ipaddress = $_SERVER['REMOTE_ADDR'];
				//get size of the current version to calculate the size change
				$sql7 = "SELECT COALESCE(SUM(v.size), 0) as size FROM File_versions v where v.is_current = 1 and v.file_id = ".$fileid.";";
				$result7 = mysql_query($sql7, $link);
				$value7 = mysql_fetch_assoc($result7);
                		//retrieve the current highest version number for this groups assignment
                		$sql1  = "SELECT COALESCE(MAX(version_number), 0) AS 'version' FROM File_versions WHERE file_id = ".$fileid.";";
				$result1 = mysql_query($sql1, $link);
                		$value = mysql_fetch_assoc($result1);
                		$versionNum = $value['version'] + 1;
				$sizechange = $fileSize - $value7['size'];
                		//insert the file into the database
				$sql2 = "INSERT INTO File_versions (version_number, is_current, data, checksum, upload_ip, size, upload_date, name, is_deleted, upload_id, file_id, file_type, sizechange)
                                           VALUES (".$versionNum.", 1, '".$fileData."', '".$checksum."', '".$ipaddress."', ".$fileSize.", now(), '".$fileName."', 0, ".$_SESSION['user_id'].", ".$fileid.", '".$fileType."', ".$sizechange.");";
				mysql_query($sql2, $link);
				//make the newly uploaded file the only current version for this groups assignment
				$sql3 = "UPDATE File_versions SET is_current = 0 WHERE file_id = ".$fileid." AND version_number <> ".$versionNum.";";
				mysql_query($sql3, $link);
			} else {
				header("HTTP/1.1 450 Max Size");
			}
		} else {
			header("HTTP/1.1 451 Invalid Name");
		}
			
        mysql_close($link);     
	}
	
	function isValidName($str) {
		return (!preg_match('/[^A-Za-z0-9.#\\-$]/', $str) && $str !== '');
	}
	
	function archive()
	{
		require('config.php');
		$fileid = mysql_real_escape_string($_POST['fileid']);
		$sql = "UPDATE Files SET is_archived = 1 WHERE file_id = ".$fileid.";";
		mysql_query($sql, $link);
        mysql_close($link);    
	}
	
	
	function unarchive()
	{
		require('config.php');
		$fileid = mysql_real_escape_string($_POST['fileid']);
		$sql = "UPDATE Files SET is_archived = 0 WHERE file_id = ".$fileid.";";
		mysql_query($sql, $link);
        mysql_close($link);    
	}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
		<meta charset="utf-8">
		<link rel='shortcut icon' type='image/png' href='images/favicon.ico' />
		<title>Group Page</title>
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
        			<h2>Group Page</h2>
    		</div>
			<div class="row">
			<div class="panel panel-default">
			<div class="panel-body">
			<h4>Assignments/Projects</h4>
			<br/>
			<section id="version">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Assignment/Project</th>
							<th>Operations</th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</thead>
						<tbody>
							<?php foreach($assignments as $assignment):
							
							if((($_SESSION['user_type'] === '2' || $_SESSION['user_type'] === '3') && $assignment->is_archived === '0') || ($_SESSION['user_type'] === '1') || ($_SESSION['user_type'] === '4' && $_SESSION['end_date'] > date("Y-m-d H:i:s") && $_SESSION['start_date'] < date("Y-m-d H:i:s")) )
							{
							echo "<tr>";
								echo "<td>"; 
								
								if($assignment->assignmentType === '1')
									{
										echo "Assignment ";
									} 
									else 
									{
										echo "Project ";
									}
								echo $assignment->assignmentid;
								echo "</td>";
								echo "<td>"; 
								
								if($_SESSION['user_type'] === '4') 
								{
									echo "<label class='btn btn-default btn-file'>Upload<input style='display: none;' onchange='uploadFile(this.files, ".$assignment->fileid.")' type='file' id='".$assignment->fileid."'></label> ";
								}
								
								echo "</td>";
								echo "<td>"; 
								
								if($_SESSION['user_type'] === '4') 
								{
									echo "<button class='btn btn-default' onclick='manageVersions(".$assignment->fileid.")'>Manage Versions</button> ";
								}
								
								echo "</td>";
								echo "<td>";
								
								if($assignment->hasUpload === '1') 
								{
									echo "<input type='button' class='btn btn-default' onClick=\"parent.location='download.php?fileid=".$assignment->fileid."'\" value='Download' formtarget='_blank'> ";
								}
								
								echo "</td>";
								echo "<td>";
								
								if($_SESSION['user_type'] === '1' && $_SESSION['end_date'] < date("Y-m-d H:i:s")) 
								{
									if($assignment->is_archived === '0')
									{
										echo "<button class='btn btn-default' onclick= 'archiveFile(".$assignment->fileid.")'>Archive</button> ";
									}
								}
								
								echo "</td>";
								echo "<td>";

								if($_SESSION['user_type'] === '1' && $_SESSION['end_date'] < date("Y-m-d H:i:s")) 
								{
									if($assignment->is_archived === '1')
									{
										echo "<button class='btn btn-default' onclick= 'unarchiveFile(".$assignment->fileid.")'>Unarchive</button> ";
									}
								}
								
								echo "</td>";
							
							echo "</tr>";
							}
							endforeach; ?>
						</tbody>
					</table>
					</br>
					<?php if($_SESSION['user_type'] === '2' || $_SESSION['user_type'] === 3) {
						echo "<h4>Member Statistics</h4>";
						echo "<br/>";
						echo "<table class='table table-hover'>";
							echo "<thead>";
								echo "<tr>";
									echo "<th>User</th>";
									echo "<th>Uploads</th>";
									echo "<th>Downloads</th>";
									echo "<th>Deletes</th>";
									echo "<th>Rollbacks</th>";
									echo "<th>Recovers</th>";
									echo "<th>Data Changed</th>";
								echo "</tr>";
							echo "</thead>";
							echo "<tbody>";
								foreach($stats as $stat):
								echo "<tr>";
									echo "<td>".$stat->name."</td>";
									echo "<td>".$stat->uploads."</td>";
									echo "<td>".$stat->downloads."</td>";
									echo "<td>".$stat->deletes."</td>";
									echo "<td>".$stat->rollbacks."</td>";
									echo "<td>".$stat->recovers."</td>";
									echo "<td>".$stat->datachanged."</td>";
								echo "</tr>";
								endforeach;
							echo "</tbody>";
						echo "</table>";
						echo "</br>";
						echo "<h4>Activity Log</h4>";
						echo "<br/>";
						echo "<table class='table table-hover'>";
							echo "<thead>";
								echo "<tr>";
									echo "<th>Assignment/Project</th>";
									echo "<th>Version</th>";
									echo "<th>File</th>";
									echo "<th>User</th>";
									echo "<th>Action</th>";
									echo "<th>Date</th>";
									echo "<th>Upload IP</th>";
									echo "<th>Checksum</th>";
									echo "<th>Upload Size Change";
								echo "</tr>";
							echo "</thead>";
							echo "<tbody>";
								foreach($logs as $log):
								echo "<tr>";
									echo "<td>";
									if($log->type === '1'){
										echo "Assignment ";
									} else {
										echo "Project ";
									}
									echo $log->assignment;
									echo "</td>";
									echo "<td>".$log->version."</td>";
									echo "<td>".$log->name."</td>";
									echo "<td>".$log->user."</td>";
									echo "<td>".$log->action."</td>";
									echo "<td>".$log->when."</td>";
									echo "<td>".$log->ip."</td>";
									echo "<td>".$log->checksum."</td>";
									echo "<td>".$log->sizechange."</td>";
								echo "</tr>";
								endforeach;
							echo "</tbody>";
						echo "</table>";
					}
					
					if($_SESSION['user_type'] !== '4') 
						{
							echo "<a class='btn btn-default' href='course.php'>Back</a>";
						}
					?>
					</div>
				</div>
			</div>
		</div>
    </body>
</html>

<script>
	$(document).ready(function(){
         if(localStorage.getItem("Upload"))
         {
              toastr.success("File uploaded");
              localStorage.clear();
         }
		 if(localStorage.getItem("Archive"))
		 {
			toastr.success("File archived");
              localStorage.clear();
		 }
		 if(localStorage.getItem("Unarchive"))
		 {
			toastr.success("File unarchived");
              localStorage.clear();
		 }
    });

    function uploadFile(file, fileid) {
    	var formData = new FormData();
	formData.append('file', file[0]);
	formData.append('fileid', fileid);
	formData.append('action', 'upload');
	$.ajax({
       		url : 'group.php',
       		type : 'POST',
       		data : formData,
       		processData: false,
       		contentType: false,
       		success : function(data) {
				localStorage.setItem("Upload",data.OperationStatus);
                location.reload();
       		}, 
			error: function(xhr, status, text) {
				if(text === 'Max Size') {
					toastr.error("Upload exceeds max group data");
				}
				if(text === 'Invalid Name') {
					toastr.error("File name invalid");
				}
			}
	});
    }
    
    function manageVersions(fileid) {
        window.location="manageversions.php?file="+fileid;
    }
	
	function archiveFile(fileid)
	{
		var formData = new FormData();
		formData.append('fileid', fileid);
		formData.append('action', 'archive');
		
		$.ajax({
       		url : 'group.php',
       		type : 'POST',
       		data : formData,
       		processData: false,
       		contentType: false,
       		success : function(data) {
				localStorage.setItem("Archive",data.OperationStatus);
                location.reload();
       		}
	});
	}
	
	function unarchiveFile(fileid)
	{
		var formData = new FormData();
		formData.append('fileid', fileid);
		formData.append('action', 'unarchive');
		
		$.ajax({
       		url : 'group.php',
       		type : 'POST',
       		data : formData,
       		processData: false,
       		contentType: false,
       		success : function(data) {
				localStorage.setItem("Unarchive",data.OperationStatus);
                location.reload();
       		}
	});
	}
</script>
