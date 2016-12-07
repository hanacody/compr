<?php
/**
 * ����� ���� ���� ó��
 *@note: ȸ������ ���� ���� ����
 */

include_once '../../Libs/_php/rankup_basic.class.php';

switch($_POST['mode']) {

	// ��üȸ������ - ȸ����� ����
	case 'change_level':
		$rankup_control->check_admin();
		$uids = str_replace('__', "','", $_POST['uids']);
		$rankup_control->query("update $rankup_member->member_table2 set level=$_POST[level] where uid in ('$uids')");
		break;

	// ȸ������ ȯ�漳�� ����
	case 'save_member':
		$rankup_control->check_admin();
		$rankup_control->change_encoding($_POST, 'IN');

		$_vals['membership_use'] = $_POST['membership_use']; // ȸ���� ��뿩��
		$_vals['membership_types'] = implode(',', $_POST['membership_types']); // ���� ���� ����
		$_vals['membership_age'] = $_POST['membership_age']; // ���� ���� ����

		$smlevel = array(); // ȸ�� ��� ����
		foreach(range(1,$rankup_member->lowest_level) as $key) { $smlevel[$key] = $_POST['level'.$key]; }
		$_vals['smlevel'] = serialize($smlevel);

		// ��й�ȣ ���� ����
		$_vals['change_pwd_use'] = $_POST['change_pwd_use'];
		$_vals['change_pwd_terms'] = $_POST['change_pwd_terms'];

		$values = $rankup_control->change_query_string($_vals);
		$rankup_control->query("update rankup_siteconfig set $values");
		unset($_vals);

		// �Ǹ����� ���� ����
		include_once '../rankup_authentic/rankup_authentic.class.php';
		$auth = new rankup_authentic;
		$auth->set_pins();

		// ȸ�� ���Խ� �ʼ��׸� ���� - rankup_configs table
		$is_present = $rankup_control->queryR("select item from rankup_configs where item='member_form_options'");
		foreach($rankup_member->form_options as $form) {
			$key = $form['key'];
			$use = ($_POST['use_'.$key]=='on') ? 'yes' : 'no';
			$req = ($_POST['req_'.$key]=='on') ? 'yes' : 'no';
			$form_options[$key] = compact('use', 'req');
		}
		$_vals['item'] = 'member_form_options';
		$_vals['value'] = serialize($form_options);
		$values = $rankup_control->change_query_string($_vals);
		if($is_present) $rankup_control->query("update rankup_configs set $values where item='member_form_options'");
		else $rankup_control->query("insert rankup_configs set $values");
		break;

	case 'del_log': // 3������ �α׻���
		$rankup_control->check_admin();
		$rankup_member->del_log();
		break;

	case 'reset_log': // �α��ʱ�ȭ
		$rankup_control->check_admin();
		$rankup_member->reset_log();
		break;

	case 'find_id': // ���̵� ã��
		$rankup_control->change_encoding($_POST, 'IN');
		$where = q(" AND mt.name='%s' AND mt2.email='%s'", $_POST['f_name'], $_POST['f_email']);
		$rows = $rankup_member->queryFetch("select mt.name, mt.uid, mt2.email from $rankup_member->member_table as mt, $rankup_member->member_table2 as mt2 WHERE mt2.uid=mt.uid".$where);
		if($rows['uid']) {
			// ���̵� ã��
			include_once '../rankup_mailing/rankup_mailing.class.php';
			$mailing = new rankup_mailing('found_id');
			$mailing->send($rows['email'], array(
				'�̸�' => $rows['name'],
				'���̵�' => $rows['uid']
			));
			scripts('alert("ȸ������ �̸��Ϸ� ���̵� �߼۵Ǿ����ϴ�.");');
		}
		else {
			scripts('alert("��ġ�ϴ� ȸ�������� �������� �ʽ��ϴ�.")');
		}
		break;

	case 'find_passwd': // ��й�ȣ ã��
		$rankup_control->change_encoding($_POST, 'IN');
		$where = q(" AND mt.uid='%s' AND mt.name='%s' AND mt2.email='%s'", $_POST['f_userid'], $_POST['f_name'], $_POST['f_email']);
		$rows = $rankup_member->queryFetch("select mt.name, mt.uid, mt2.email from $rankup_member->member_table as mt, $rankup_member->member_table2 as mt2 WHERE mt2.uid=mt.uid".$where);
		if($rows['uid']) {
			// ���̵� ã��
			include_once '../rankup_mailing/rankup_mailing.class.php';
			$mailing = new rankup_mailing('found_pwd');
			$mailing->send($rows['email'], array(
				'�̸�' => $rows['name'],
				'���̵�' => $rows['uid'],
				'��й�ȣ' => $rankup_member->get_new_passwd($rows['uid'])
			));
			scripts('alert("ȸ������ �̸��Ϸ� �ӽ� ��й�ȣ�� �߼۵Ǿ����ϴ�.");');
		}
		else {
			scripts('alert("��ġ�ϴ� ȸ�������� �������� �ʽ��ϴ�.")');
		}
		break;

	case 'change_passwd': // ��й�ȣ ����
		$uid = $rankup_member->get_id();
		if(!$rankup_member->match_table($uid, $_POST['passwd'])) {
			scripts('alert("�˼��մϴ�. �Է��Ͻ� ��й�ȣ�� ��ġ���� �ʽ��ϴ�.")');
		}
		else {
			$values = q("passwd=password('%s'), passwd_time=now()", $_POST['new_passwd']);
			$rankup_member->modify_member_table($uid, $values);
			scripts('alert("��й�ȣ�� ����Ǿ����ϴ�.");location.replace("../../main/index.html");');
		}
		break;


	/**
	 * ȸ������ ó��
	 */
	case 'verify_join':
		switch($_POST['step']) {
			case '1': // 1�ܰ� - ��Ʈ��
				$_SESSION['member_type'] = $_POST['type'];
				scripts('location.href="join_policy.html"');
				break;

			case '2': // 2�ܰ� - �������
				include_once '../rankup_authentic/rankup_authentic.class.php';
				$auth = new authentic('join');
				if($auth->pin_settings['use_pin']=='yes') {
					// ��Ʈ�� �������������� �������� ��� - �������� �ܰ� ��ŵ
					if($config_info['intro_use']=='yes') {
						$intro = @unserialize($auth->get_configs('intro_design'));
						if($intro['intro_type']=='pin' && $auth->pin_check()) {
							scripts('location.href="join_form.html"');
							exit;
						}
					}
					scripts('location.href="join_pin.html"');
				}
				else scripts('location.href="join_form.html"');
				break;

			case '3': // 3�ܰ� - ȸ������(����+����) - @note: �Ǹ����� ���ÿ��� �ڵ����� 4�ܰ�� �Ѿ
				rankup_util::change_encoding($_POST, 'IN');
				include_once '../rankup_authentic/rankup_authentic.class.php';
				$auth = new rankup_authentic('join');

				// �������� Ȯ�� - 2012.04.02 added
				$birthday = implode('', $_POST['birthday']);
				if(!$auth->check_ages($config_info['membership_age'], $birthday)) {
					switch($config_info['membership_age']) {
						case '14over':
							scripts('alert("�˼��մϴ�. �� ����Ʈ�� 14�� �̻� �̿��� �����մϴ�.")');
							break;
						case '19over':
							scripts('alert("�˼��մϴ�. �� ����Ʈ�� 19�� �̻� �̿��� �����մϴ�.")');
							break;
					}
					exit;
				}

				$auth->set_basic_pin(array(
					'name' => $_POST['userNm'],
					'birthday' => $birthday,
					'gender' => $_POST['gender']
				));
				scripts('location.href="join_form.html"');
				break;

			case '4': // 4�ܰ� - ����ó�� @note: form submit
				include_once '../rankup_authentic/rankup_authentic.class.php';
				$auth = new authentic('join');

				$_vals['kind'] = $_POST['kind']; // ȸ�� ���� { 'personal' | 'company' }
				$_vals['uid'] = $_POST['join_id']; // ���̵�
				$_vals['name'] = $auth->infos['name']; // �̸�
				$_vals['join_ip'] = $_SERVER['REMOTE_ADDR']; // ���� IP
				$_vals['join_time'] = date('Y-m-d H:i:s'); // �����Ͻ�
				$_vals['passwd_time'] = $_vals['join_time']; // ������� �Ͻ� - default value

				// ����
				if(!$_POST['join_passwd']) {
					scripts('alert("��й�ȣ�� �ùٸ��� �ʽ��ϴ�.")');
					exit;
				}

				$values = $rankup_control->change_query_string($_vals);
				$values .= q(", passwd=password('%s')", $_POST['join_passwd']); // ��й�ȣ

				// �ʼ��׸�
				$_vals2['uid'] = $_vals['uid'];
				$_vals2['pin_kind'] = $_SESSION['vmKind']; // �������� ����( basic, jumin, ipin )
				$_vals2['di_code'] = $auth->infos['di_code']; // �Ǹ�������(������-64Byte) - �ߺ�ȸ�� ������
				$_vals2['birthday'] = $auth->infos['birthday']; // ����( yyyymmdd )
				$_vals2['gender'] = $auth->infos['gender']; // ����( 1:��, 2:�� )
				$_vals2['level'] = $rankup_member->join_level; // ȸ�� ���
				$_vals2['email'] = $_POST['email']; // �̸���
				$_vals2['mailing'] = $_POST['mailing']; // ���ϸ�����
				$_vals2['sms'] = $_POST['sms']; // SMS ����

				// �ɼ��׸�
				$_vals2['nickname'] = $_POST['nickname']; // �г���
				$_vals2['phone'] = $_POST['phone'] ? implode('-', $_POST['phone']) : '';  // �Ϲ���ȭ
				$_vals2['hphone'] = $_POST['hphone'] ? implode('-', $_POST['hphone']) : ''; // �޴���ȭ
				$_vals2['zipcode'] = $_POST['zipcode']; // �����ȣ
				$_vals2['address1'] = $_POST['addrs1']; // �ּ�
				$_vals2['address2'] = $_POST['addrs2']; // �������ּ�
				$_vals2['introduce'] = $_POST['introduce']; // �����λ�

				$values2 = $rankup_control->change_query_string($_vals2);

				$result = $rankup_member->insert_member_table($values, $values2); // ���
				if(!$result) {
					scripts('alert("ȸ�� ������ �����Ͽ����ϴ�.")');
					exit;
				}
				else {
					// �α��� ó��
					$rankup_member->set_member_session($_vals['uid'], $_vals['kind']);

					// ȸ������ ���� �̸��� �߼�
					include_once '../rankup_mailing/rankup_mailing.class.php';
					$mailing = new rankup_mailing('member_join');
					$mailing->send($_vals2['email'], array(
						'�̸�' => $_vals['name'],
						'���̵�' => $_vals['uid'],
						'��й�ȣ' => $rankup_member->get_password($_POST['join_passwd'])
					));
					// ȸ������ ���� SMS �߼�
					if($_vals2['hphone']) {
						include_once '../rankup_sms/rankup_sms_config.class.php';
						include_once '../rankup_sms/rankup_sms.class.php';
						$sms = new rankup_sms('sms_join');
						$sms->set_value($_vals2['hphone']);
						$sms->set_merge(array('name'=>$_vals['name']));
						$sms->send_msg();
					}
					$return_page = rankup_https_change::https_change('main/index.html', 'http');
					scripts('alert("ȸ���� �ǽŰ��� �������� �����մϴ�."); location.replace("'.$return_page.'");');
				}
				break;
		}
		break;

	// ȸ�� ���� ����
	case 'modify_info': //@note: �����ڰ� ����

		$rankup_control->check_admin();

		$uid = $_POST['uid'];
		$_vals['name'] = $_POST['name']; // �̸�
		$values = $rankup_control->change_query_string($_vals);

		$_vals2['birthday'] = implode('', $_POST['birthday']); // ������� - 2012.05.23 added
		$_vals2['gender'] = $_POST['gender']; // ���� - 2012.05.23 added
		$_vals2['level'] = $_POST['level']; // ȸ�����
		$_vals2['memo'] = rankup_util::trans_wysiwyg($_POST['memo']); // �����ڸ޸�

	case 'modify_myinfo': //@note: ȸ���� ����

		if($_POST['mode']=='modify_myinfo') $uid = $rankup_member->get_id();
		// ��й�ȣ
		if($_POST['new_passwd']) { // 2012.05.23 fixed
			if($values) $values .= ',';
			$values .= q("passwd=password('%s'), passwd_time=now()", $_POST['new_passwd']); // ��й�ȣ
		}

		$_vals2['nickname'] = $_POST['nickname']; // �г���
		$_vals2['email'] = $_POST['email']; // �̸���
		$_vals2['phone'] = $_POST['phone'] ? implode('-', $_POST['phone']) : ''; // ��ȭ��ȣ
		$_vals2['hphone'] = $_POST['hphone'] ? implode('-', $_POST['hphone']) : ''; // �޴���ȭ
		$_vals2['zipcode'] = $_POST['zipcode']; // �����ȣ
		$_vals2['address1'] = $_POST['addrs1']; // �ּ�
		$_vals2['address2'] = $_POST['addrs2']; // ������ �ּ�
		$_vals2['introduce'] = $_POST['introduce']; // �����λ�
		$_vals2['mailing'] = $_POST['mailing']; // ���ϸ�����
		$_vals2['sms'] = $_POST['sms']; // SMS ����
		$values2 = $rankup_control->change_query_string($_vals2);
		$rankup_member->modify_member_table($uid, $values, $values2); // ����

		if($_POST['mode']=='modify_myinfo') {
			// SMS �߼�
			if($_vals2['hphone']) {
				include_once '../rankup_sms/rankup_sms_config.class.php';
				include_once '../rankup_sms/rankup_sms.class.php';
				$sms = new rankup_sms('sms_memberUpd');
				$sms->set_value($_vals2['hphone']);
				$sms->set_merge(array('name'=>$member_info['name']));
				$sms->send_msg();
			}
			$rankup_control->popup_msg_js('����Ǿ����ϴ�.', rankup_https_change::https_change('rankup_module/rankup_member/member_modify.html', 'http'));
		}
		else {
			scripts('alert("����Ǿ����ϴ�.");history.go(-2);');
		}
		break;


	/**
	 * �޴���ȭ ����
	 */

	// ������ȣ �߼�
	case 'send_vnumber':
		$domain = $config_info['domain'];
		$hphone = implode('-', $_POST['hphone_number']);
		$vnumber = mt_rand(100000, 999999);
		if($rankup_member->sendable_vsms($hphone)) {
			scripts('alert("�˼��մϴ�. ������ȣ�� ������ �� �����ϴ�.\n\n������ȣ�� �Ϸ翡 �ִ� '.$rankup_member->vsms_send_limits.'ȸ������ �����Ͻ� �� �ֽ��ϴ�.\n�ڼ��� ������ ����Ʈ �����ڿ��� �����Ͻñ� �ٶ��ϴ�.")');
			exit;
		}
		$_SESSION['sms_verify_number'] = $vnumber; // ������ȣ ���� ����

		include_once '../rankup_sms/rankup_sms_config.class.php';
		include_once '../rankup_sms/rankup_sms.class.php';
		$sms = new rankup_sms('sms_send');
		$sms->is_valid_send = true;
		$sms->phone = $hphone;
		$sms->msg = fetch_skin(compact('vnumber', 'domain'), "ȸ������ ������ȣ�� [{:vnumber:}]�Դϴ�.\n{:domain:}");
		$sms->send_msg();

		// SMS �߼۷α� ���
		$rankup_member->save_vsms_log($hphone, $vnumber);
		break;

	// ������ȣ Ȯ��
	case 'check_vnumber':
		if($_SESSION['sms_verify_number']!=$_POST['vnumber']) {
			scripts('alert("������ȣ�� ��ġ���� �ʽ��ϴ�.")');
		}
		else {
			unset($_SESSION['sms_verify_number']); // ������ȣ ���� ����
		}
		break;

	// ������ �α׻���
	case 'del_vsms':
		$rankup_control->check_admin();
		$rankup_member->del_vsms();
		break;

	// 3������ �α׻���
	case 'del_vsms_log':
		$rankup_control->check_admin();
		$rankup_member->del_vsms_log();
		break;

	// �α��ʱ�ȭ
	case 'reset_vsms_log':
		$rankup_control->check_admin();
		$rankup_member->reset_vsms_log();
		break;

}

?>