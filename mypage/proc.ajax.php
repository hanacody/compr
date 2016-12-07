<?php
/**
 * 로그인·로그아웃 처리
 */
include_once '../Libs/_php/rankup_basic.class.php';

switch($_POST['mode']) {

	// 로그인
	case 'login':
		$login_id = preg_replace('/[^a-z\d]/', '', $_POST['login_id']); // 소문자/숫자 외 문자 제거
		if(!$login_id || !$_POST['login_pw'] || $_POST['login_id']!==$login_id) {
			scripts('alert("로그인 정보가 올바르지 않습니다.")');
			exit;
		}
		// 로그인 정보 확인
		$rows = $rankup_member->queryFetch("select m.uid, m.kind, m.passwd, m.passwd_time, me.secession, me.level, m.join_time from $rankup_member->member_table as m, $rankup_member->member_table2 as me where me.uid=m.uid".q(" and m.uid='%s'", $login_id));
		$_login_pw = $rankup_member->queryR(q("select password('%s')", $_POST['login_pw']));

		if(!is_array($rows)) $rankup_member->popup_msg_js($_STRINGSET[302], 'VOID'); // 302 - 아이디 존재 안함
		else if($rows['passwd']!=$_login_pw) $rankup_member->popup_msg_js($_STRINGSET[303], 'VOID'); // 303 - 비번이 일치하지 않음
		else if($rows['secession']=='yes') $rankup_member->popup_msg_js($_STRINGSET[304], 'VOID'); // 탈퇴신청 회원
		else if($rows['level']==$rankup_member->stop_level) $rankup_member->popup_msg_js('회원님은 [정지회원]으로 등록되어 사이트 이용이 불가능합니다.\\n자세한 사항은 관리자에게 문의하시기 바랍니다.', $base_url.'main/index.html');
		else {
			// 아이디 저장/초기화 처리
			if($_POST['keep_id']=='Y') {
				$expiry = time() + 60 * 60 * 24 * 30; // 30일
				setcookie('keep_login_id', $_POST['login_id'], $expiry, $base_url);
			}
			else if($_COOKIE['keep_login_id']) {
				setcookie('keep_login_id', '', time()-3600, $base_url);
			}
			// 로그인 처리 및 접속 로그 기록
			$rankup_member->set_member_session($login_id, $rows['kind']);

			// 비밀번호 변경
			if($config_info['change_pwd_use']=='yes') {
				$pwd_expiry = strtotime(sprintf('-%d month', $config_info['change_pwd_terms']));
				$pwd_cdate = ($rows['passwd_time'] && $rows['passwd_time']!='0000-00-00 00:00:00') ? strtotime($rows['passwd_time']) : strtotime($rows['join_time']);
				if($pwd_expiry>$pwd_cdate) {
					scripts('location.replace("'.$base_url.'rankup_module/rankup_member/change_password.html")');
					exit;
				}
			}
		}
		// 로그인 후 페이지 이동
		$pre_page = $_POST['pre_page'] ? $_POST['pre_page'] : $base_url.'main/index.html';
		scripts('location.replace("'.$pre_page.'")');
		break;

	// 로그아웃
	case 'logout':
		$rankup_member->delete_member_session();

		// 실명인증 값이 있으면 삭제
		include_once '../rankup_module/rankup_authentic/rankup_authentic.class.php';
		$auth = new rankup_authentic;
		$auth->pin_reset();

		scripts('location.replace("'.$base_url.'main/index.html")');
		break;
}

?>