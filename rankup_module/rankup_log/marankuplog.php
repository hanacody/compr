<?php
$endnumsel = $db->sel("rankuplog_month"," order by no desc limit 1","no");
$endfetch = mysql_fetch_array($endnumsel['result']);

$query = "update rankuplog_month set month$tomonth = month$tomonth+1 where no='$endfetch[no]'";
$select = mysql_query($query);
?>