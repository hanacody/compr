<?php
## 랭크업 달력 클래스의 날짜계산에 필요한 파일 ##
include_once "../_php/rankup_basic.class.php";

extract($_GET);
switch($mode) {
	case "today": $message = date("Y-m-d"); break;
	case "check_date":
		if(($min_date!='null' && $min_date>$sel_date) || ($max_date!='null' && $max_date<$sel_date)) {
			if($min_date!='null') $limit_date = ($max_date!='null') ? date("Y년m월d일부터 ", strtotime($min_date)).date("Y년m월d일까지만", strtotime($max_date)) : date("Y년m월d일부터", strtotime($min_date));
			else if($max_date!='null') $limit_date = date(strtotime("Y년m월d일까지만", $max_date));
			$message = "alert(\"날짜는[ $limit_date ] 입력하실 수 있습니다.\");";
		}
		else {
			$message = "
			rankup_calendar.selComponent.value = '$sel_date';
			rankup_calendar.hidden_calendar();
			if(rankup_calendar.handler!=null) rankup_calendar.handler();";
		}
		break;
	case "check_days": // 공휴일 리턴 - 2010.12.23 added
		include_once $base_dir."Libs/_php/rankup_past.class.php";
		$rankup_past = new rankup_past;
		$rest_day = array();

		$timestamp = strtotime(sprintf('%d-%02d-%02d', $_GET['year'], $_GET['month'], 1));
		foreach(range(1, date('t', $timestamp)) as $day) {
			$event = $rankup_past->check_days(sprintf('%4d-%02d-%02d', $_GET['year'], $_GET['month'], $day));
			if($event[0]===1) {
				array_push($rest_day, $day);
			}
		}
		$message = implode(',', $rest_day);
		break;
	default:
		$message = date("Y-m-d", strtotime("$_GET[base_date] $mode +1 day"));
}
$result = "success";

$rankup_control->change_encoding($message, "OUT");

header("Content-type: text/xml; charset=utf-8");
echo "<?xml version='1.0' encoding='utf-8'?>\n";
echo "<resultData result='$result'>$message</resultData>";
?>