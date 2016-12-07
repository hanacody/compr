<?php
include '../../Libs/_php/rankup_basic.class.php';
$rankup_control->check_admin();

$file_Name = $rankup_control->getParam('file_Name');
$filename = './backup_list/'.$file_Name;

if(is_file($filename)) {
   if(unlink($filename)) echo"<script> alert('정상적으로 삭제를 하였습니다.'); location.href='./index.html'; </script> ";
}
else {
	echo"<script> alert('삭제를 하지 못했습니다. 다시 시도해주세요.'); location.href='./index.html'; </script>";
}

?>