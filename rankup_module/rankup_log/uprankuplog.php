<?php

$rdate = date("Y-m-d");
//시간대별처리
$query = "update rankuplog_time set time$totaltime = time$totaltime+1 where wdate=curdate()";
$select = mysql_query($query);

//아이피
$ip_select = $db->sel("rankuplog_ip","where content='$beforeip' and wdate=curdate()");
$ip_num = $ip_select['cnt'];
if($ip_num){
	$query = "update rankuplog_ip set num=num+1 where content='$beforeip' and wdate=curdate()";
	$select = mysql_query($query);
}else{
	$arr = array(no=>'', wdate=>$rdate, content=>$beforeip, num=>"1");
	$query = $db->ins("rankuplog_ip",$arr);
	$select = $query['result'];
}

//요일별
$query = "update rankuplog_week set date$totalweek=date$totalweek+1 where wdate=curdate()";
$select = mysql_query($query);

//일별
$query = "update rankuplog_date set date$totaldate = date$totaldate+1 where wdate=curdate()";
$select = mysql_query($query);

//접속전도메인
$beforedomain = ($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:"직접입력또는즐겨찾기";

$domain_select = $db->sel("rankuplog_domain","where content='$beforedomain' and wdate=curdate()");
$domain_num = $domain_select['cnt'];
if($domain_num){
	$query = "update rankuplog_domain set num=num+1 where content='$beforedomain' and wdate=curdate()";
	$select = mysql_query($query);
}else{
	$arr = array(no=>'', wdate=>$rdate, content=>$beforedomain, num=>"1");
	$query = $db->ins("rankuplog_domain", $arr);
	$select = $query['result'];
}

//짧은도메인
if($_SERVER['HTTP_REFERER']){
$shortdomain = str_replace("www.","",$_SERVER['HTTP_REFERER']);
$shortdomain = explode("/",$shortdomain);
$shortdomain = $shortdomain[0]."//".$shortdomain[1].$shortdomain[2];
}else{
$shortdomain = "직접입력또는즐겨찾기";
}

$shdomain_select = $db->sel("rankuplog_shortdomain","where content='$shortdomain' and wdate=curdate()");
$shdomain_num = $shdomain_select['cnt'];
if($shdomain_num){
	$query = "update rankuplog_shortdomain set num=num+1 where content='$shortdomain' and wdate=curdate()";
	$select = mysql_query($query);
}else{
	$arr = array(no=>'', wdate=>$rdate, content=>$shortdomain, num=>"1");
	$query = $db->ins("rankuplog_shortdomain",$arr);
	$select = $query['cnt'];
}


//총접속자
$query = "update rankuplog_total set num=num+1 where wdate=curdate()";
$select = mysql_query($query);


?>
