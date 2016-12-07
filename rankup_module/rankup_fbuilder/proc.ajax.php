<?php
/**
 * ������ ó��
 */
include_once '../../Libs/_php/rankup_basic.class.php';
$rankup_control->check_admin();

include_once 'rankup_fbuilder.class.php';
$fbuilder = new rankup_fbuilder;

switch($_POST['mode']) {
	// ������
	case 'save_forms':
		rankup_util::change_encoding($_POST, 'IN');
		$fno = $fbuilder->save_forms();
		echo json_encode(compact('fno'));
		break;

	// �ʵ�����
	case 'save_fields':
		rankup_util::change_encoding($_POST, 'IN');
		$results = $fbuilder->save_fields();
		echo json_encode($results);
		break;

	// �ʵ尪 ��ȯ
	case 'load_values':
		if($_POST['no']) $values = $fbuilder->load_fields($_POST['no']);
		if(!$values) $values = $fbuilder->defaults;
		rankup_util::change_encoding($values);
		echo json_encode($values);
		break;

	// ������
	case 'del_forms':
		$fbuilder->del_forms($_POST['nos']);

		// �亰������ ���� ����
		if($_POST['nos']) {
			include_once '../rankup_mailing/rankup_mailing.class.php';
			$mailing = new rankup_mailing;
			$kinds = 'answer_'.str_replace('__', "', 'answer_", $_POST['nos']);
			$mailing->del_settings($kinds);
		}
		break;

	// �ʵ����
	case 'del_fields':
		$fbuilder->del_fields($_POST['fno'], $_POST['nos']);
		break;

	// �Խñ� �亯���� ����
	case 'set_status':
		$fbuilder->set_status($_POST['fno'], $_POST['no'], $_POST['status']);
		break;

	// �Խñ� ����
	case 'del_articles':
		$fbuilder->del_articles($_POST['fno'], $_POST['nos']);
		break;

	// �Խñ� �޸�ε�
	case 'load_memo':
		$memo = $fbuilder->load_memo($_POST['fno'], $_POST['no']);
		rankup_util::change_encoding($memo);
		echo json_encode(compact('memo'));
		break;

	// �Խñ� �޸�����
	case 'save_memo':
		rankup_util::change_encoding($_POST, 'IN');
		$fbuilder->save_memo($_POST['fno'], $_POST['no'], $_POST['memo']);
		break;

	// �亯���� �߼�
	case 'send_reply_mail':
		include_once '../rankup_mailing/rankup_mailing.class.php';
		$mailing = new rankup_mailing;

		rankup_util::change_encoding($_POST, 'IN');
		$merge_codes = array(
			'�̸�' => $_POST['fromname']
		);
		$mailing->get_settings($_POST['kind']);
		$mailing->settings['subject'] = $_POST['subject'];
		$mailing->settings['body'] = $_POST['body'];
		$mailing->send($_POST['fromemail'], $merge_codes);

		// �亯���� ����
		$fbuilder->save_answer($_POST['fno'], $_POST['no'], array(
			'answered_title' => $mailing->fetch_content($merge_codes, $_POST['subject']),
			'answered_body' => $mailing->fetch_content($merge_codes, $_POST['body'])
		));
		break;
}

?>