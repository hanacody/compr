<?php
include '../../Libs/_php/rankup_basic.class.php';
$rankup_control->check_admin();

$file_Name = $rankup_control->getParam('file_Name');
$filename = './backup_list/'.$file_Name;

if(is_file($filename)) {
   if(unlink($filename)) echo"<script> alert('���������� ������ �Ͽ����ϴ�.'); location.href='./index.html'; </script> ";
}
else {
	echo"<script> alert('������ ���� ���߽��ϴ�. �ٽ� �õ����ּ���.'); location.href='./index.html'; </script>";
}

?>