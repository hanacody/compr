<?php
/**
 * 로그인·로그아웃 처리
 */
include_once '../../Libs/_php/rankup_basic.class.php';

switch($_POST['mode']) {
	case 'login':
		// 로그인 정보 확인
		$rows = $rankup_control->queryFetch("select m.uid, m.kind, m.passwd, m.passwd_time, m.join_time, me.secession from $rankup_member->member_table as m, $rankup_member->member_table2 as me where me.uid=m.uid".q(" and m.uid='%s'", $_POST['user_id']));
		$_login_pw = $rankup_member->queryR(q("select password('%s')", $_POST['user_pwd']));

		if(!is_array($rows)) $rankup_control->popup_msg_js($_STRINGSET[302], 'VOID'); // 302 - 아이디 존재 안함
		else if($rows['passwd']!=$_login_pw) $rankup_member->popup_msg_js($_STRINGSET[303], 'VOID'); // 303 - 비번이 일치하지 않음
		else if($rows['secession']=='yes') $rankup_control->popup_msg_js($_STRINGSET[304], 'VOID'); // 탈퇴신청 회원
		else {
			$rankup_member->set_member_session($_POST['user_id'], $rows['kind']); // 로그인 처리
			$mobile->set_session();
		}
		// 로그인 후 페이지 이동
		$pre_page = $_POST['pre_page'] ? $_POST['pre_page'] : $m_domain;
		scripts('location.replace(\''.$pre_page.'\')');
		break;

	case 'logout':
		$rankup_member->delete_member_session();
		$mobile->delete_session();
		scripts('location.replace(\''.$m_domain.'\')');
		break;

	// 우편번호 검색 - 2012.04.09 added
	case 'search_post':
		include_once '../../Libs/_php/rankup_post.class.php';
		rankup_util::change_encoding($_POST, 'IN');
		$rankup_post = new rankup_post;
		$nodes = $rankup_post->get_zipcode($_POST);
		xmls('<xml>'.$nodes.'</xml>');
		break;
}
?>