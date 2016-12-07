<?php
/**
 * 문자상담서비스 관련 처리
 */
include_once '../../Libs/_php/rankup_basic.class.php';
include_once 'rankup_lconsult.class.php';

$lconsult = new rankup_lconsult;

switch($_POST['mode']) {

	// 문자상담 환경설정
	case 'save_settings':
		$rankup_control->check_admin();
		rankup_util::change_encoding($_POST, 'IN');
		$lconsult->query("UPDATE rankup_siteconfig".q(" SET letter_consult_use='%s'", $_POST['letter_consult_use']));
		$lconsult->set_settings();
		break;

	// 문의글 삭제
	case 'del':
		$rankup_control->check_admin();
		$lconsult->del();
		break;

	// 답변내용 저장
	case 'save_answer':
		$rankup_control->check_admin();
		rankup_util::change_encoding($_POST, 'IN');
		$lconsult->save_answer();
		break;

	// 답변상태 변경
	case 'set_status':
		$rankup_control->check_admin();
		$answered_time = $lconsult->set_status();
		echo json_encode(compact('answered_time'));
		break;

	// 문의내용 저장 - 사용자용
	case 'save_lconsult':
		// XSS 방어
		include_once '../../Libs/_filter/HTMLFilter.php';
		$filter = new HTMLFilter;

		rankup_util::change_encoding($_POST, 'IN');

		// 입력값 검수
		$_POST['l_consult'] = $filter->parse(trim(strip_tags($_POST['l_consult']))); // 문의내용
		if(!$_POST['l_consult']) {
			scripts("alert('문의내용이 올바르지 않습니다.'); $('l_consult').focus()");
			exit;
		}
		$_POST['l_phone'] = preg_replace('/[^\+\)\-\d]/', '', $_POST['l_phone']); // 연락처 @note: '+ ) - 숫자' 외 문자 제거
		if(strlen($_POST['l_phone'])<8) {
			scripts("alert('연락처가 올바르지 않습니다.'); $('l_phone').focus()");
			exit;
		}
		$_POST['l_name'] = $filter->parse(preg_replace('/[\d]/', '', trim(strip_tags($_POST['l_name'])))); // 숫자제거
		if(!$_POST['l_name']) {
			scripts("alert('이름이 올바르지 않습니다.'); $('l_name').focus()");
			exit;
		}

		//
		//@note: 스팸봇 방어가 필요한 경우 이곳에 추가 구현할 것.
		//

		// 문의내용 저장
		$lconsult->save_consult();

		// 관리자통보 처리
		$ls_rows = $lconsult->get_settings();
		if($ls_rows['use_alarm']=='yes' && $ls_rows['alarm_phone'] && $ls_rows['alarm_message']) {
			include_once '../rankup_sms/rankup_sms_config.class.php';
			include_once '../rankup_sms/rankup_sms.class.php';
			$sms = new rankup_sms('sms_send');
			$sms->is_valid_send = true;
			$sms->phone = $ls_rows['alarm_phone'];
			$sms->msg = fetch_skin(array('name'=>$_POST['l_name'], 'phone'=>$_POST['l_phone']), str_replace(array('{이름}', '{연락처}'), array('{:name:}', '{:phone:}'), $ls_rows['alarm_message']));
			$sms->send_msg();
		}
		break;
}
?>