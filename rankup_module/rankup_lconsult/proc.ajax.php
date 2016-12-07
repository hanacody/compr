<?php
/**
 * ���ڻ�㼭�� ���� ó��
 */
include_once '../../Libs/_php/rankup_basic.class.php';
include_once 'rankup_lconsult.class.php';

$lconsult = new rankup_lconsult;

switch($_POST['mode']) {

	// ���ڻ�� ȯ�漳��
	case 'save_settings':
		$rankup_control->check_admin();
		rankup_util::change_encoding($_POST, 'IN');
		$lconsult->query("UPDATE rankup_siteconfig".q(" SET letter_consult_use='%s'", $_POST['letter_consult_use']));
		$lconsult->set_settings();
		break;

	// ���Ǳ� ����
	case 'del':
		$rankup_control->check_admin();
		$lconsult->del();
		break;

	// �亯���� ����
	case 'save_answer':
		$rankup_control->check_admin();
		rankup_util::change_encoding($_POST, 'IN');
		$lconsult->save_answer();
		break;

	// �亯���� ����
	case 'set_status':
		$rankup_control->check_admin();
		$answered_time = $lconsult->set_status();
		echo json_encode(compact('answered_time'));
		break;

	// ���ǳ��� ���� - ����ڿ�
	case 'save_lconsult':
		// XSS ���
		include_once '../../Libs/_filter/HTMLFilter.php';
		$filter = new HTMLFilter;

		rankup_util::change_encoding($_POST, 'IN');

		// �Է°� �˼�
		$_POST['l_consult'] = $filter->parse(trim(strip_tags($_POST['l_consult']))); // ���ǳ���
		if(!$_POST['l_consult']) {
			scripts("alert('���ǳ����� �ùٸ��� �ʽ��ϴ�.'); $('l_consult').focus()");
			exit;
		}
		$_POST['l_phone'] = preg_replace('/[^\+\)\-\d]/', '', $_POST['l_phone']); // ����ó @note: '+ ) - ����' �� ���� ����
		if(strlen($_POST['l_phone'])<8) {
			scripts("alert('����ó�� �ùٸ��� �ʽ��ϴ�.'); $('l_phone').focus()");
			exit;
		}
		$_POST['l_name'] = $filter->parse(preg_replace('/[\d]/', '', trim(strip_tags($_POST['l_name'])))); // ��������
		if(!$_POST['l_name']) {
			scripts("alert('�̸��� �ùٸ��� �ʽ��ϴ�.'); $('l_name').focus()");
			exit;
		}

		//
		//@note: ���Ժ� �� �ʿ��� ��� �̰��� �߰� ������ ��.
		//

		// ���ǳ��� ����
		$lconsult->save_consult();

		// �������뺸 ó��
		$ls_rows = $lconsult->get_settings();
		if($ls_rows['use_alarm']=='yes' && $ls_rows['alarm_phone'] && $ls_rows['alarm_message']) {
			include_once '../rankup_sms/rankup_sms_config.class.php';
			include_once '../rankup_sms/rankup_sms.class.php';
			$sms = new rankup_sms('sms_send');
			$sms->is_valid_send = true;
			$sms->phone = $ls_rows['alarm_phone'];
			$sms->msg = fetch_skin(array('name'=>$_POST['l_name'], 'phone'=>$_POST['l_phone']), str_replace(array('{�̸�}', '{����ó}'), array('{:name:}', '{:phone:}'), $ls_rows['alarm_message']));
			$sms->send_msg();
		}
		break;
}
?>