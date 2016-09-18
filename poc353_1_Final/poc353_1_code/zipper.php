<?php
        session_start();
	if($_SESSION['user_type'] === '1') {
        require('config.php');
        
	//create zip	
	$zip = new ZipArchive();
        $zipFileName = "zip/course.zip";
	
	if ($zip->open($zipFileName, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== true)
        {
            echo "Cannot Open for writing";
        }
	//get file data, name, and relevant data to create zip structure
	$sql2 = "select v.name, v.size, v.data, f.assignment_id, f.group_id from File_versions v join Files f on f.file_id = v.file_id where v.is_current = 1 order by f.group_id, f.assignment_id;";
        $result2 = mysql_query($sql2, $link);	
        while ($row = mysql_fetch_assoc($result2)) {
	    //insert files into the zip based on group and then assignment
            $zip->addFromString("Group".$row["group_id"]."/Assignment".$row["assignment_id"]."/".$row['name'], $row['data']);
        }
        $zip->close();
        if(file_exists($zipFileName)){
            //download the zip file
	    header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename='.$zipFileName.'');
            header('Content-Length: '.filesize($zipFileName).'');
            readfile($zipFileName);
            //After download the zip file which is created deleted it
            unlink($zipFileName);
        }
        mysql_close($link);
	}
?>
