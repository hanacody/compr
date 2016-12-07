<?php
include_once '../../Libs/_php/rankup_basic.class.php';

include_once 'rankup_mailing.class.php';
$mailing = new rankup_mailing;

switch($_POST['mode']) {
	// 이메일폼 설정
	case 'set_mailing':
		$rankup_control->change_encoding($_POST, 'IN');
		$mailing->set_settings();
		break;

	// 발송내역 삭제
	case 'del_mailing':
		$mailing->del_newsletter();
		break;

	// 뉴스레터 발송
	case 'send_mailing':
		$rankup_control->change_encoding($_POST, 'IN');
		$mailing->send_newsletter();
		break;

	// 고객센터 답변메일 발송 - 미사용
	case 'send_reply_mail':
		$rankup_control->change_encoding($_POST, 'IN');
		$mailing->get_settings($_POST['kind']);
		$mailing->settings['subject'] = $_POST['subject'];
		$mailing->settings['body'] = $_POST['body'];
		$mailing->send($_POST['fromemail'], array(
			'이름' => $_POST['fromname']
		));
		// 답변내용 저장
		include_once '../../Libs/_php/rankup_cooperation.class.php';
		$rankup_control->update_mail_content($config_info['site_name'], $_POST['fromemail'], addslashes($_POST['subject']), addslashes($_POST['body']),$_POST['no']);
		break;
}
?>