<?php
/**
 * �α��Ρ��α׾ƿ� ó��
 */
include_once '../../Libs/_php/rankup_basic.class.php';

switch($_POST['mode']) {
	case 'login':
		// �α��� ���� Ȯ��
		$rows = $rankup_control->queryFetch("select m.uid, m.kind, m.passwd, m.passwd_time, m.join_time, me.secession from $rankup_member->member_table as m, $rankup_member->member_table2 as me where me.uid=m.uid".q(" and m.uid='%s'", $_POST['user_id']));
		$_login_pw = $rankup_member->queryR(q("select password('%s')", $_POST['user_pwd']));

		if(!is_array($rows)) $rankup_control->popup_msg_js($_STRINGSET[302], 'VOID'); // 302 - ���̵� ���� ����
		else if($rows['passwd']!=$_login_pw) $rankup_member->popup_msg_js($_STRINGSET[303], 'VOID'); // 303 - ����� ��ġ���� ����
		else if($rows['secession']=='yes') $rankup_control->popup_msg_js($_STRINGSET[304], 'VOID'); // Ż���û ȸ��
		else {
			$rankup_member->set_member_session($_POST['user_id'], $rows['kind']); // �α��� ó��
			$mobile->set_session();
		}
		// �α��� �� ������ �̵�
		$pre_page = $_POST['pre_page'] ? $_POST['pre_page'] : $m_domain;
		scripts('location.replace(\''.$pre_page.'\')');
		break;

	case 'logout':
		$rankup_member->delete_member_session();
		$mobile->delete_session();
		scripts('location.replace(\''.$m_domain.'\')');
		break;

	// �����ȣ �˻� - 2012.04.09 added
	case 'search_post':
		include_once '../../Libs/_php/rankup_post.class.php';
		rankup_util::change_encoding($_POST, 'IN');
		$rankup_post = new rankup_post;
		$nodes = $rankup_post->get_zipcode($_POST);
		xmls('<xml>'.$nodes.'</xml>');
		break;
}
?>