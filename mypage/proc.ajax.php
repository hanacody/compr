<?php
/**
 * �α��Ρ��α׾ƿ� ó��
 */
include_once '../Libs/_php/rankup_basic.class.php';

switch($_POST['mode']) {

	// �α���
	case 'login':
		$login_id = preg_replace('/[^a-z\d]/', '', $_POST['login_id']); // �ҹ���/���� �� ���� ����
		if(!$login_id || !$_POST['login_pw'] || $_POST['login_id']!==$login_id) {
			scripts('alert("�α��� ������ �ùٸ��� �ʽ��ϴ�.")');
			exit;
		}
		// �α��� ���� Ȯ��
		$rows = $rankup_member->queryFetch("select m.uid, m.kind, m.passwd, m.passwd_time, me.secession, me.level, m.join_time from $rankup_member->member_table as m, $rankup_member->member_table2 as me where me.uid=m.uid".q(" and m.uid='%s'", $login_id));
		$_login_pw = $rankup_member->queryR(q("select password('%s')", $_POST['login_pw']));

		if(!is_array($rows)) $rankup_member->popup_msg_js($_STRINGSET[302], 'VOID'); // 302 - ���̵� ���� ����
		else if($rows['passwd']!=$_login_pw) $rankup_member->popup_msg_js($_STRINGSET[303], 'VOID'); // 303 - ����� ��ġ���� ����
		else if($rows['secession']=='yes') $rankup_member->popup_msg_js($_STRINGSET[304], 'VOID'); // Ż���û ȸ��
		else if($rows['level']==$rankup_member->stop_level) $rankup_member->popup_msg_js('ȸ������ [����ȸ��]���� ��ϵǾ� ����Ʈ �̿��� �Ұ����մϴ�.\\n�ڼ��� ������ �����ڿ��� �����Ͻñ� �ٶ��ϴ�.', $base_url.'main/index.html');
		else {
			// ���̵� ����/�ʱ�ȭ ó��
			if($_POST['keep_id']=='Y') {
				$expiry = time() + 60 * 60 * 24 * 30; // 30��
				setcookie('keep_login_id', $_POST['login_id'], $expiry, $base_url);
			}
			else if($_COOKIE['keep_login_id']) {
				setcookie('keep_login_id', '', time()-3600, $base_url);
			}
			// �α��� ó�� �� ���� �α� ���
			$rankup_member->set_member_session($login_id, $rows['kind']);

			// ��й�ȣ ����
			if($config_info['change_pwd_use']=='yes') {
				$pwd_expiry = strtotime(sprintf('-%d month', $config_info['change_pwd_terms']));
				$pwd_cdate = ($rows['passwd_time'] && $rows['passwd_time']!='0000-00-00 00:00:00') ? strtotime($rows['passwd_time']) : strtotime($rows['join_time']);
				if($pwd_expiry>$pwd_cdate) {
					scripts('location.replace("'.$base_url.'rankup_module/rankup_member/change_password.html")');
					exit;
				}
			}
		}
		// �α��� �� ������ �̵�
		$pre_page = $_POST['pre_page'] ? $_POST['pre_page'] : $base_url.'main/index.html';
		scripts('location.replace("'.$pre_page.'")');
		break;

	// �α׾ƿ�
	case 'logout':
		$rankup_member->delete_member_session();

		// �Ǹ����� ���� ������ ����
		include_once '../rankup_module/rankup_authentic/rankup_authentic.class.php';
		$auth = new rankup_authentic;
		$auth->pin_reset();

		scripts('location.replace("'.$base_url.'main/index.html")');
		break;
}

?>