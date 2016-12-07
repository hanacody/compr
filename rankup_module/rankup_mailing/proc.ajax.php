<?php
include_once '../../Libs/_php/rankup_basic.class.php';

include_once 'rankup_mailing.class.php';
$mailing = new rankup_mailing;

switch($_POST['mode']) {
	// �̸����� ����
	case 'set_mailing':
		$rankup_control->change_encoding($_POST, 'IN');
		$mailing->set_settings();
		break;

	// �߼۳��� ����
	case 'del_mailing':
		$mailing->del_newsletter();
		break;

	// �������� �߼�
	case 'send_mailing':
		$rankup_control->change_encoding($_POST, 'IN');
		$mailing->send_newsletter();
		break;

	// ������ �亯���� �߼� - �̻��
	case 'send_reply_mail':
		$rankup_control->change_encoding($_POST, 'IN');
		$mailing->get_settings($_POST['kind']);
		$mailing->settings['subject'] = $_POST['subject'];
		$mailing->settings['body'] = $_POST['body'];
		$mailing->send($_POST['fromemail'], array(
			'�̸�' => $_POST['fromname']
		));
		// �亯���� ����
		include_once '../../Libs/_php/rankup_cooperation.class.php';
		$rankup_control->update_mail_content($config_info['site_name'], $_POST['fromemail'], addslashes($_POST['subject']), addslashes($_POST['body']),$_POST['no']);
		break;
}
?>