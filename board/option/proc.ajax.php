<?php
/**
 * ��������� �ɼǹ�ȯ
 */
include_once '../../Libs/_php/rankup_basic.class.php';
$rankup_control->check_admin();

include_once '../../rankup_module/rankup_fbuilder/rankup_fbuilder.class.php';
$fbuilder = new rankup_fbuilder;

switch($_POST['mode']) {
	case 'load_forms': // ����� ��ȯ
		$items = $fbuilder->load_forms_toJSON();
		rankup_util::change_encoding($items);
		echo json_encode($items);
		break;
}

?>