<?php
include '../../Libs/_php/rankup_basic.class.php';

$rankup_control->check_admin();
$db=$rankup_control->conn_class;
$Time = date("Y-m-d-Hi");

$db_name = $db->db_name;
$db_user = $db->db_id;
$db_pass = $db->db_passwd;
$db_host = $db->db_host;

$file='db_'.$Time.'.sql';
exec("/usr/local/mysql/bin/mysqldump -h $db_host -u $db_user -p$db_pass --opt  $db_name > ./backup_list/db_$Time.sql");

header("location:./download.php?file_Name=$file");
?>