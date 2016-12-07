<?php
include "../../Libs/_php/rankup_basic.class.php";

$rankup_control->check_admin();
$file_Name = $rankup_control->getParam('file_Name');
$filename = "./backup_list/".$file_Name;

########## 파일을 다운로드하게 하는 파일 ########## 
header("Content-type: application/force-download"); 
Header("Content-Length: ".filesize("$filename")); // 이부부을 넣어 주어야지 다운로드 진행 상태가 표시 됩니다. 
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
else $rankup_control->popup_msg_js("해당 파일이나 경로가 존재하지 않습니다.", "./intro.html");
?> 