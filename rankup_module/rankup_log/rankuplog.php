<?php

include "Libs/db_class.php";
include "Libs/often_class.php";
$db = new db_class;
$often = new often_class;

$totaltoday = date("Y-m-d");
$tomon = date("Y-m");
$tomonth = date("m");
$totaltime = date("G");
$beforeip = getenv("REMOTE_ADDR");
$totalweek = date("w");
$totaldate = date("j");

$mon_sel = $db->sel("rankuplog_totaltoday", "where todaydate like '%$tomon%'");
$mon_num = mysql_num_rows($mon_sel['result']);
if(!$mon_num){
	include "./rankup_module/rankup_log/morankuplog.php"; //�����������̺�insert����

}else{
	include "./rankup_module/rankup_log/marankuplog.php"; //�����������̺�update����

}

$total_select = $db->sel("rankuplog_totaltoday","where todaydate=curdate()","todaydate");
$total_num = $total_select['cnt'];
if($total_num){
	include "./rankup_module/rankup_log/uprankuplog.php"; //���������������̺�update��������
}else{
	include "./rankup_module/rankup_log/inrankuplog.php"; //���������������̺�insert��������
}
?>