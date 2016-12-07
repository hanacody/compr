<?php
include_once '../../Libs/_php/rankup_basic.class.php';

##관리자 인증 체크
$rankup_control->check_admin($rankup_admin->is_admin());

##작업 내용을 결정
$mode = $rankup_control->getParam('mode');


##관리자 아이디 비밀번호 변경 인경우
if($mode == 'change_id') :
	$id = $rankup_control->getParam('id');	
	$passwd = $rankup_control->getParam('passwd');
	$passwd_re = $rankup_control->getParam('passwd_re');
	
	##아이디와, 패스워드의 형식을 검사.	
	if(($id_msg = $rankup_control->make_valid_id($id)))
		$rankup_control->popup_msg_js($id_msg);	
	if(($pw_msg = $rankup_control->make_valid_passwd($passwd)))
		$rankup_control->popup_msg_js($pw_msg);
	if(($pw_msg = $rankup_control->make_valid_passwd($passwd_re)))
		$rankup_control->popup_msg_js($pw_msg);
	if(strcmp($passwd,$passwd_re))
		$rankup_control->popup_msg_js('비밀번호를 정확하게 입력하여 주십시요');

	##테이블을 변경하고 세션을 발급
	if($rankup_admin->update_admin_table($id,$passwd)) :	//관리자 아이디 패스워드 변경이 성공한 경우
		##새로이 세션을 발급
		$rankup_admin->delete_admin_session();
		$rankup_admin->set_admin_session($id,$passwd,'update');
		$rankup_control->popup_msg_js('변경 되었습니다.');
	else : 
		$rankup_control->popup_msg_js('변경이 실패 하였습니다.');
	endif;
endif;
?>