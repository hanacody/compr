<?
//오늘날짜업데이트
$rdate = date("Y-m-d");

$query = $db->del("rankuplog_totaltoday");
$select = $query['result'];

$arr = array(no=>'', todaydate=>$rdate);
$query = $db->ins("rankuplog_totaltoday",$arr);
$select = $query['result'];

//시간대별초기화
$time1 = ($totaltime==1)?"1":"0";
$time2 = ($totaltime==2)?"1":"0";
$time3 = ($totaltime==3)?"1":"0";
$time4 = ($totaltime==4)?"1":"0";
$time5 = ($totaltime==5)?"1":"0";
$time6 = ($totaltime==6)?"1":"0";
$time7 = ($totaltime==7)?"1":"0";
$time8 = ($totaltime==8)?"1":"0";
$time9 = ($totaltime==9)?"1":"0";
$time10 = ($totaltime==10)?"1":"0";
$time11 = ($totaltime==11)?"1":"0";
$time12 = ($totaltime==12)?"1":"0";
$time13 = ($totaltime==13)?"1":"0";
$time14 = ($totaltime==14)?"1":"0";
$time15 = ($totaltime==15)?"1":"0";
$time16 = ($totaltime==16)?"1":"0";
$time17 = ($totaltime==17)?"1":"0";
$time18 = ($totaltime==18)?"1":"0";
$time19 = ($totaltime==19)?"1":"0";
$time20 = ($totaltime==20)?"1":"0";
$time21 = ($totaltime==21)?"1":"0";
$time22 = ($totaltime==22)?"1":"0";
$time23 = ($totaltime==23)?"1":"0";
$time0 = ($totaltime==0)?"1":"0";

$arr = array(no=>'', wdate=>$rdate, time1=>$time1, time2=>$time2, time3=>$time3, time4=>$time4, time5=>$time5, time6=>$time6, time7=>$time7, time8=>$time8, time9=>$time9, time10=>$time10, time11=>$time11, time12=>$time12, time13=>$time13, time14=>$time14, time15=>$time15, time16=>$time16, time17=>$time17, time18=>$time18, time19=>$time19, time20=>$time20, time21=>$time21, time22=>$time22, time23=>$time23, time0=>$time0);

$query = $db->ins("rankuplog_time",$arr);
$select = $query['result'];

//아이피초기화
$arr = array(no=>'', wdate=>$rdate, content=>$beforeip, num=>"1");
$query = $db->ins("rankuplog_ip", $arr);
$select = $query['result'];

//요일별
$sun = (0==$totalweek)?"1":"0";
$mon = (1==$totalweek)?"1":"0";
$tue = (2==$totalweek)?"1":"0";
$wed = (3==$totalweek)?"1":"0";
$thu = (4==$totalweek)?"1":"0";
$fri = (5==$totalweek)?"1":"0";
$sat = (6==$totalweek)?"1":"0";
$arr = array(no=>'', wdate=>$rdate, date0=>$sun, date1=>$mon, date2=>$tue, date3=>$wed, date4=>$thu, date5=>$fri, date6=>$sat);
$query = $db->ins("rankuplog_week", $arr);
$select = $query['result'];

//일별
$date1 = (1==$totaldate)?"1":"0";
$date2 = (2==$totaldate)?"1":"0";
$date3 = (3==$totaldate)?"1":"0";
$date4 = (4==$totaldate)?"1":"0";
$date5 = (5==$totaldate)?"1":"0";
$date6 = (6==$totaldate)?"1":"0";
$date7 = (7==$totaldate)?"1":"0";
$date8 = (8==$totaldate)?"1":"0";
$date9 = (9==$totaldate)?"1":"0";
$date10 = (10==$totaldate)?"1":"0";
$date11 = (11==$totaldate)?"1":"0";
$date12 = (12==$totaldate)?"1":"0";
$date13 = (13==$totaldate)?"1":"0";
$date14 = (14==$totaldate)?"1":"0";
$date15 = (15==$totaldate)?"1":"0";
$date16 = (16==$totaldate)?"1":"0";
$date17 = (17==$totaldate)?"1":"0";
$date18 = (18==$totaldate)?"1":"0";
$date19 = (19==$totaldate)?"1":"0";
$date20 = (20==$totaldate)?"1":"0";
$date21 = (21==$totaldate)?"1":"0";
$date22 = (22==$totaldate)?"1":"0";
$date23 = (23==$totaldate)?"1":"0";
$date24 = (24==$totaldate)?"1":"0";
$date25 = (25==$totaldate)?"1":"0";
$date26 = (26==$totaldate)?"1":"0";
$date27 = (27==$totaldate)?"1":"0";
$date28 = (28==$totaldate)?"1":"0";
$date29 = (29==$totaldate)?"1":"0";
$date30 = (30==$totaldate)?"1":"0";
$date31 = (31==$totaldate)?"1":"0";
$arr = array(no=>'', wdate=>$rdate, date1=>$date1, date2=>$date2, date3=>$date3, date4=>$date4, date5=>$date5, date6=>$date6, date7=>$date7, date8=>$date8, date9=>$date9, date10=>$date10, date11=>$date11, date12=>$date12, date13=>$date13, date14=>$date14, date15=>$date15, date16=>$date16, date17=>$date17, date18=>$date18, date19=>$date19, date20=>$date20, date21=>$date21, date22=>$date22, date23=>$date23, date24=>$date24, date25=>$date25, date26=>$date26, date27=>$date27, date28=>$date28, date29=>$date29, date30=>$date30, date31=>$date31);
$query = $db->ins("rankuplog_date",$arr);
$select = $query['result'];


//접속전도메인
$beforedomain = ($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:"직접입력또는즐겨찾기";
$arr = array(no=>'', wdate=>$rdate, content=>$beforedomain, num=>"1");
$query = $db->ins("rankuplog_domain", $arr);
$select = $query['result'];



if($_SERVER['HTTP_REFERER']){
$shortdomain = str_replace("www.","",$_SERVER['HTTP_REFERER']);
$shortdomain = explode("/",$shortdomain);
$shortdomain = $shortdomain[0]."//".$shortdomain[1].$shortdomain[2];
}else{
$shortdomain = "직접입력또는즐겨찾기";
}

$arr = array(no=>'', wdate=>$rdate, content=>$shortdomain, num=>"1");
$query = $db->ins("rankuplog_shortdomain", $arr);
$select = $query['result'];


//총접속자
$arr = array(no=>'', wdate=>$rdate, num=>"1");
$query = $db->ins("rankuplog_total",$arr);
$select = $query['result'];
?>