<?php
/**
 * 실명인증 처리
 */
include_once '../../Libs/_php/rankup_basic.class.php';
include_once './rankup_authentic.class.php';

$auth = new rankup_authentic;

switch($_POST['mode']) {
	case 'set_configs':
		$auth->set_configs();
		break;

	case 'set_pins':
		$auth->set_pins();
		break;
}

?>