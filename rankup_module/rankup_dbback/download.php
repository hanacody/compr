<?php
include "../../Libs/_php/rankup_basic.class.php";

$rankup_control->check_admin();
$file_Name = $rankup_control->getParam('file_Name');
$filename = "./backup_list/".$file_Name;

########## ������ �ٿ�ε��ϰ� �ϴ� ���� ########## 
header("Content-type: application/force-download"); 
Header("Content-Length: ".filesize("$filename")); // �̺κ��� �־� �־���� �ٿ�ε� ���� ���°� ǥ�� �˴ϴ�. 
if(preg_match("/MSIE/i", getenv("HTTP_USER_AGENT")))	header("Content-disposition: filename=$file_Name"); 
else header("Content-disposition: attachment; filename=$file_Name");
Header("Content-Transfer-Encoding: binary"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");

if(is_file("$filename")) {
	$fp=fopen("$filename", "r");
	fpassthru($fp);
	fclose($fp);
}
else $rankup_control->popup_msg_js("�ش� �����̳� ��ΰ� �������� �ʽ��ϴ�.", "./intro.html");
?> 