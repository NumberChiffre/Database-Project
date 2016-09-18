<?php

$server_name = 'poc353_1.encs.concordia.ca';
$user = 'poc353_1';
$pass = '353CoMp1';
$database = 'poc353_1';
$link = mysql_connect($server_name, $user, $pass);
mysql_select_db($database);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
?>