<?php
$rdate = date("Y-m-d");

$date1 = (01==$tomonth)?"1":"0";
$date2 = (02==$tomonth)?"1":"0";
$date3 = (03==$tomonth)?"1":"0";
$date4 = (04==$tomonth)?"1":"0";
$date5 = (05==$tomonth)?"1":"0";
$date6 = (06==$tomonth)?"1":"0";
$date7 = (07==$tomonth)?"1":"0";
$date8 = (08==$tomonth)?"1":"0";
$date9 = (09==$tomonth)?"1":"0";
$date10 = (10==$tomonth)?"1":"0";
$date11 = (11==$tomonth)?"1":"0";

$arr = array(no=>'', wdate=>$rdate, month01=>$date1, month02=>$date2, month03=>$date3, month04=>$date4, month05=>$date5, month06=>$date6, month07=>$date7, month08=>$date8, month09=>$date9, month10=>$date10, month11=>$date11, month12=>$date12);
$query = $db->ins("rankuplog_month",$arr);
$select = $query['result'];
?>