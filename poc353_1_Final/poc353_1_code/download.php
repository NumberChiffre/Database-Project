<?php
    session_start();
    require('config.php');
    $fileid = mysql_real_escape_string(htmlspecialchars($_GET['fileid']));
    $versionNum = mysql_real_escape_string(htmlspecialchars($_GET['version']));
    //if no version number is provided, get the current version, otherwise get the version requested
    if($versionNum === '') {
        $query = "SELECT name, size, file_type, data, version_number FROM File_versions WHERE file_id = ".$fileid." AND is_current = 1;";
    } else {
        $query = "SELECT name, size, file_type, data, version_number FROM File_versions WHERE file_id = ".$fileid." AND version_number = ".$versionNum.";";
    }
    //store relevant data
    $result = mysql_query($query, $link);
    $value = mysql_fetch_assoc($result);
    $file = $value["name"];
    $size = $value["size"];
    $type = $value["file_type"];
    $content = $value["data"];
    $version = $value["version_number"];
    header("Content-length: ".$size."");
    header("Content-type: ".$type."");
    header("Content-Disposition: attachment; filename=".$file."");
    ob_clean();
    flush();
    //download the file
    echo $content;
    //if the user is a student log their activity
    if($_SESSION['user_type'] === '4') {
        $sql2 = "INSERT INTO Downloads (download_id, version_number, file_id, download_date)
                            VALUES (".$_SESSION['user_id'].", ".$version.", ".$fileid.", now());";
        mysql_query($sql2, $link);
    }
    mysql_close($link);
    die();
?>
