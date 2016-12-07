<?php
/**
 * 老沥包府 贸府
 */
include_once '../Libs/_php/rankup_basic.class.php';

switch($_POST['mode']) {
	// 老沥肺靛
	case 'load_schedule':
		include_once './class/schedule.class.php';
		$schedule = new schedule;
		$rows = $schedule->get_schedule($_POST['no']);
		$rankup_control->change_encoding($rows, 'OUT');
		echo json_encode($rows);
		break;
}

?>