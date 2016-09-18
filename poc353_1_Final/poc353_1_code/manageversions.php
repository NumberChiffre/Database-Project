<?php
    session_start();
    require_once('version.php');
    //only users can manage versions
    if($_SESSION['user_type'] === '4') {
	require('config.php');
	$version = mysql_real_escape_string($_POST['version']);
        $file = mysql_real_escape_string($_POST['file']);
	//for rollback, set the selected file as current version and set the other versions to be not current
        if($_POST['action'] === 'rollback') {
            $sql2 = "UPDATE File_versions SET is_current = 1 WHERE file_id = ".$file." AND version_number = ".$version.";";
            mysql_query($sql2, $link);
            $sql3 = "UPDATE File_versions SET is_current = 0 WHERE file_id = ".$file." AND version_number <> ".$version.";";
            mysql_query($sql3, $link);
	    //log the rollback activity
            $sql8 = "INSERT INTO Rollbacks (rollback_id, version_number, file_id, rollback_date)
                            VALUES (".$_SESSION['user_id'].", ".$version.", ".$file.", now());";
            mysql_query($sql8, $link);
            mysql_close($link);
        } else if($_POST['action'] === 'delete') {
	    //for delete, update the is_deleted flag
            $sql4 = "UPDATE File_versions SET is_deleted = 1 WHERE file_id = ".$file." AND version_number = ".$version.";";
            mysql_query($sql4, $link);
	    //log the delete activity
            $sql5 = "INSERT INTO Deletes (delete_id, version_number, file_id, delete_date)
                            VALUES (".$_SESSION['user_id'].", ".$version.", ".$file.", now());";
            mysql_query($sql5, $link);
            mysql_close($link);
        } else if($_POST['action'] === 'recover') {
	    //for recover, update the is_deleted flag
            $sql6 = "UPDATE File_versions SET is_deleted = 0 WHERE file_id = ".$file." AND version_number = ".$version.";";
            mysql_query($sql6, $link);
	    //log the recover activity
            $sql7 = "INSERT INTO Recovers (recover_id, version_number, file_id, recover_date)
                            VALUES (".$_SESSION['user_id'].", ".$version.", ".$file.", now());";
            mysql_query($sql7, $link);
            mysql_close($link);
        }
        else {
	    //if it is not any of the above actions, the page needs to be loaded so get the group/assignment and load the versions
            $file = mysql_real_escape_string(htmlspecialchars($_GET['file']));
            $sql  = "SELECT group_id, assignment_id "
                  . "FROM Files "
                  . "WHERE file_id =".$file.";";
            $result = mysql_query($sql, $link);
            $value = mysql_fetch_assoc($result);
            mysql_close($link);
            $assignment = $value['assignment_id'];
            $group = $value['group_id'];
        
            if($_SESSION['group_id'] === $group) {
                loadVersions($file);
            }
        }
        
    }
    
    function loadVersions($file) {
        require('config.php');
	//retrive each version for this group/assignment combo that has not been deleted permanenetly
        $sql1  = "SELECT version_number, name, is_current, size, is_deleted "
              . "FROM File_versions "
              . "WHERE file_id =".$file." and Length(data) > 0;";
        $result1 = mysql_query($sql1, $link);
        global $versions;
		$versions = array();
        while ($row = mysql_fetch_assoc($result1)) {
            // Append all rows to an array
	    $version = new Version;
	    $version->versionNumber = $row['version_number'];
	    $version->name = $row['name'];
	    $version->is_current = $row['is_current'];
            $version->size = $version->filesize_formatted($row["size"]);
            $version->is_deleted = $row["is_deleted"];
            array_push($versions, $version);
        }
		//get the id of the group leader
		global $leader;
		$leader = null;
		$sql2  = "SELECT leader_id "
              . "FROM Groups "
              . "WHERE group_id =".$_SESSION['group_id'].";";
        $result2=mysql_query($sql2, $link);
		$value2=mysql_fetch_assoc($result2);
		$leader = $value2["leader_id"];
        mysql_close($link);
    }

    
?>

<html>
    <head>
        <meta charset="utf-8">
		<link rel='shortcut icon' type='image/png' href='images/favicon.ico' />
		<title>Versions Page</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    </head>    
    <body>
	    <div class="container">
            <div class="page-header">
			
			<div class='btn-toolbar pull-right'>
        			<a href="logout.php" class="btn btn-danger btn-lg">
          				<span class="glyphicon glyphicon-log-out"></span> Log out
        			</a>
      			</div>
                <h2>Versions Page</h2>
			</div>
			<div class="row">
			<div class="panel panel-default">
			<div class="panel-body">
			<br/>
			<section id="version">
				<table class="table table-hover">
					<thead>
						<th>Version Number</th>
						<th>File Name</th> 
						<th>File Size</th>
						<th>Operations</th>
						<th></th>
						<th></th>
						<th></th>
				</thead>
                <?php foreach($versions as $version): ?>
                <?php if ($version->is_current === '1')
                {
                	echo "<tr class='info'>";
                }
                else
                {
                	echo "<tr>";
                }?>
                    <td>
						<?php
						echo $version->versionNumber;
						?></td>
								<td><?php
						echo $version->name;
						?></td>
								<td><?php
						echo $version->size;
						?></td>
								<td><?php
						echo "<input type='button' class='btn btn-default' onClick=\"parent.location='download.php?fileid=".$file."&version=".$version->versionNumber."'\" value='Download' formtarget='_blank'></td>";
								?></td>
								<td><?php
									if($version->is_current === '0' && $leader === $_SESSION['user_id'] && $version->is_deleted === '0') {
										echo "<button class='btn btn-default' onclick='rollbackVersion(".$version->versionNumber.", ".$file.")'>Rollback</button>";
									}
								?></td>
								<td><?php
									if($version->is_deleted === '0' && $version->is_current === '0') {
										echo "<button class='btn btn-default' onclick='deleteVersion(".$version->versionNumber.", ".$file.")'>Delete</button>";
									}
								?></td>
								<td><?php
									if($version->is_deleted === '1') {
										echo "<button class='btn btn-default' onclick='recoverVersion(".$version->versionNumber.", ".$file.")'>Recover</button>";
									}
						?></td>
				</tr>
				<?php endforeach; ?>
            </table>
			
			<a class='btn btn-default' href='group.php'>Back</a>
        
		</div>
		</div>
		</div>
    </body>
</html>

<script>
	$(document).ready(function(){
         if(localStorage.getItem("Rollback"))
         {
              toastr.success("File rolledback");
         } else if(localStorage.getItem("Delete")) {
			toastr.success("File deleted");
		 } else if(localStorage.getItem("Recover")) {
			toastr.success("File recovered");
		 }
		 
		 localStorage.clear();
    });

    function rollbackVersion(id, file) {
        $.ajax({
			type: "POST",
			url: 'manageversions.php',
			dataType: 'text',
			data:{action:'rollback', version:id, file:file},
			success: function(data) {
				localStorage.setItem("Rollback",data.OperationStatus);
				window.location="manageversions.php?file="+file;
            }
        });
    }
      
    function deleteVersion(id, file) {
        $.ajax({
			type: "POST",
			url: 'manageversions.php',
			dataType: 'text',
			data:{action:'delete', version: id, file: file},
			success: function(data) {
				localStorage.setItem("Delete",data.OperationStatus);
				window.location="manageversions.php?file="+file;
			}
        });
    }
      
    function recoverVersion(id, file) {
        $.ajax({
            type: "POST",
            url: 'manageversions.php',
            dataType: 'text',
            data:{action:'recover', version: id, file: file},
            success: function(data) {
				localStorage.setItem("Recover",data.OperationStatus);
                window.location="manageversions.php?file="+file;
            }
        });
    }
</script>
