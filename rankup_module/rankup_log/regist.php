<?
include_once "../../Libs/_php/rankup_basic.class.php";
include "./Libs/db_class.php";
include "./Libs/often_class.php";
$db = new db_class;
$often = new often_class;

$mode = ($_GET['mode'])?$_GET['mode']:$_POST['mode'];


if($mode == 'domaindel'){

	$que = $db->del("rankuplog_domain");
	$select = $que['result'];

	$que = $db->del("rankuplog_shortdomain");
	$select = $que['result']
		;
	echo "<script>alert('정상적으로삭제되었습니다');location.replace('./rankuplog_domain.html');</script>";
}


if($mode == 'ipdel'){
	$que = $db->del("rankuplog_ip");
	$select = $que['result'];
	echo "<script>alert('정상적으로삭제되었습니다');location.replace('./rankuplog_ip.html');</script>";
}

if($mode == 'change'){
	$arr = array(uid=>$uid, upasswd=>$upasswd);
	$que = $db->upd("rankuplog_admin",$arr);
	$select = $que['result'];
	echo "<script>alert('정상적으로변경되었습니다');location.replace('rankuplog_logout.php');</script>";
}



if($mode == 'tabledelete'){
$que = "drop table rankuplog_admin;";
$select = mysql_query($que);
$que = "drop table rankuplog_date;";
$select = mysql_query($que);
$que = "drop table rankuplog_domain;";
$select = mysql_query($que);
$que = "drop table rankuplog_ip;";
$select = mysql_query($que);
$que = "drop table rankuplog_month;";
$select = mysql_query($que);
$que = "drop table rankuplog_shortdomain;";
$select = mysql_query($que);
$que = "drop table rankuplog_time;";
$select = mysql_query($que);
$que = "drop table rankuplog_total;";
$select = mysql_query($que);
$que = "drop table rankuplog_totaltoday;";
$select = mysql_query($que);
$que = "drop table rankuplog_week;";
$select = mysql_query($que);
}



if($mode == 'org'){
$que = $db->del("rankuplog_date");
$select = $que['result'];

$que = $db->del("rankuplog_ip");
$select = $que['result'];

$que = $db->del("rankuplog_month");
$select = $que['result'];

$que = $db->del("rankuplog_shortdomain");
$select = $que['result'];

$que = $db->del("rankuplog_time");
$select = $que['result'];

$que = $db->del("rankuplog_total");
$select = $que['result'];

$que = $db->del("rankuplog_totaltoday");
$select = $que['result'];

$que = $db->del("rankuplog_week");
$select = $que['result'];

echo "<script>alert('정상적으로초기화되었습니다');location.replace('./rankuplog_main.html');</script>";

}
?>