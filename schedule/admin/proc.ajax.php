<?php
/**
 * 일정관리 처리
 */
include_once '../../Libs/_php/rankup_basic.class.php';
$rankup_control->check_admin();

switch($_POST['mode']) {

	// 달력저장
	case 'save_calendar':
		include_once '../class/calendar.class.php';
		$calendar = new calendar;
		$rankup_control->change_encoding($_POST, 'IN');
		$bundle = $calendar->set_settings();
		xmls('<xml><bundle>'.$bundle.'</bundle></xml>');
		break;

	// 달력삭제
	case 'del_calendar':
		include_once '../class/calendar.class.php';
		$calendar = new calendar;
		include_once '../../rankup_module/rankup_category/rankup_category.class.php';
		$category = new rankup_category;
		$calendar->del_calendar($_POST['nos']);
		break;

	// 일정등록
	case 'save_schedule':
		$rankup_control->change_encoding($_POST, 'IN');
		include_once '../class/schedule.class.php';
		$schedule = new schedule;
		$schedule->set_schedule();
		break;

	// 일정로드
	case 'load_schedule':
		include_once '../class/schedule.class.php';
		$schedule = new schedule;
		$rows = $schedule->get_schedule($_POST['no']);
		$rankup_control->change_encoding($rows, 'OUT');
		echo json_encode($rows);
		break;

	// 제외날짜추가
	case 'exclude_date':
		include_once '../class/schedule.class.php';
		$schedule = new schedule;
		$schedule->exclude_date($_POST['no'], $_POST['date']);
		break;

	// 일정삭제
	case 'del_schedule':
		include_once '../class/schedule.class.php';
		$schedule = new schedule;
		$schedule->del_schedule($_POST['nos']);
		break;
}

?>