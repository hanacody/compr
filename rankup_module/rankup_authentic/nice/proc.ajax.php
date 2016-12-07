<?php
/**
 * �Ǹ����� ó��
 *@author: kurokisi
 *@authDate: 2012.01.12
 */
include_once '../../../Libs/_php/rankup_basic.class.php';
unset($_SESSION['vmKind'], $_SESSION['recvData']); // ������ �ʱ�ȭ
include_once '../rankup_authentic.class.php';

switch($_POST['mode']) {

	// �Ǹ����� ��û
	case 'jumin_validate':

		rankup_util::change_encoding($_POST, 'IN');

		$userNm = $_POST['userNm']; // �̸�
		$foreigner = $_POST['foreigner']; // �����ܱ���
		$resIdNo = preg_replace('/[^\d]/', '', $_POST['userNo1']).preg_replace('/[^\d]/', '', $_POST['userNo2']); // �ֹι�ȣ & �ܱ��ι�ȣ

		// ��ȸ���� : '10'-ȸ������, '20'-����ȸ�� Ȯ��, '30'-��������, '40'-��ȸ�� Ȯ��, '90'-��Ÿ ����
		if($config_info['membership_age']=='19over') $InqRsn = 30;
		else if($_POST['pin_kind']=='join') $InqRsn = 10; // ȸ������
		else if($_POST['pin_kind']=='intro') $InqRsn = 40; // ��Ʈ�� - ��ȸ�� Ȯ��
		else $InqRsn = 20;

		// �˼�
		if(!$userNm) scripts("alert(getCheckMessage('S23')); return false;");
		else if(!$_POST['userNo1'] || !$_POST['userNo2']) {
			if($foreigner=='2') scripts("alert(getCheckMessage('S27')); return false;");
			else scripts("alert(getCheckMessage('S21')); return false;");
		}
		else { // �Ǹ�������û
			scripts("verify_infos('jumin_verify', makeSendInfo('$userNm', '$resIdNo', '$InqRsn', '$foreigner'));");
		}
		break;


	// �Ǹ����� ��û/����
	case 'jumin_verify':

		require_once 'jumin/nice.nuguya.oivs.php';
		$auth = new authentic($_POST['pin_kind'], 'jumin', $_POST['value']);

		// ��������
		if($auth->res->retCd==1) {
			// ȸ�����Խ� �ߺ�Ȯ��
			if($_POST['pin_kind']=='join') {
				if($rankup_member->check_di_code($auth->infos['di_code'])) {
					scripts('alert("�˼��մϴ�. ȸ������ �̹� ���Ե� �����Դϴ�.\n\n���̵�/��й�ȣ ã�⸦ �̿��� �ֽñ� �ٶ��ϴ�.")');
					exit;
				}
			}

			// �������� Ȯ�� - 2012.04.02 added
			if(!$auth->check_ages($config_info['membership_age'], $auth->infos['birthday'])) {
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

			$_SESSION['vmKind'] = 'jumin'; // ������ ����� ������� ����
			$_SESSION['recvData'] = $_POST['value']; // �������� ����

			scripts('alert("�Ǹ�Ȯ���� �Ǿ����ϴ�."); location.href = "'.$auth->pin_kinds[$_POST['pin_kind']]['landing'].'";');

		}
		// �Ǹ�Ƚ����� ����
		else {
			list($code, $message) = array($auth->res->retCd, $auth->res->retDtlCd);
			if($auth->res->retDtlCd=='Y') {
				scripts(implode("\n", array(
					"if(confirm(getMessage('$code', '$message')+ '\\n\\n'+ getCheckMessage('S31'))) {",
					"	goSafeBlockExpt();",
					"	return false;",
					"}",
					"else {",
					"	return false;",
					"}"
				)));
			}
			// �Ǹ���ǵ������� ����
			else if($auth->res->retDtlCd=='C') {
				scripts("alert(getMessage('$code', '$message') +'\\n\\n'+ getCheckMessage('S32'));");
			}
			// ��Ÿ ����
			else {
				scripts("alert(getMessage('$code', '$message'))");
			}
		}
		break;


	// ������ ������û
	case 'ipin_validate':

		require_once 'ipin/nice.nuguya.oivs.php';
		$auth = new authentic($_POST['pin_kind'], 'ipin');

		$ReturnURL = $config_info['domain'].'rankup_module/rankup_authentic/nice/ipin/response.php';
		$OrderNo = date('Ymd').rand(100000000000, 999999999999); // �ֹ���ȣ(14~20) ����ũ�� ��
		$PingInfo = getPingInfo();

		// ��ȸ���� : '10'-ȸ������, '20'-����ȸ�� Ȯ��, '30'-��������, '40'-��ȸ�� Ȯ��, '50'-��Ÿ ����
		if($config_info['membership_age']=='19over') $InqRsn = 30; // ��������
		else if($_POST['pin_kind']=='join') $InqRsn = 10; // ȸ������
		else if($_POST['pin_kind']=='intro') $InqRsn = 40; // ��Ʈ�� - ��ȸ�� Ȯ��
		else $InqRsn = 20; // ����ȸ�� Ȯ��

		// �˼�
		if(!$auth->configs['ipin_id']) scripts("alert(getCheckMessage('S60')); return false;");
		else if(!$PingInfo) scripts("alert(getCheckMessage('S61')); return false;");
		else if(!$ReturnURL) scripts("alert(getCheckMessage('S64')); return false;");
		else {

			$_SESSION['sess_OrderNo'] = $OrderNo; // ��ŷ������ ���� ��û���� ����

			// ����â �ε�
			scripts(implode("\n", array(
				sprintf("var form = document.%s;", $_POST['dest']),
				sprintf("form.SendInfo.value = makeCertKeyInfoPA('%s', '%s', '%s', '%s', '%s', '%s');", $auth->configs['ipin_id'], $PingInfo, $OrderNo, $InqRsn, $ReturnURL, $auth->configs['ipin_sikey']),
				"form.ProcessType.value = strPersonalCertKey;",
				"var pop = window.open('', 'popupCertKey', 'top=100, left=200, status=0, width=417, height=490');",
				"form.target = 'popupCertKey';",
				"form.action = strCertKeyServiceUrl;",
				"form.submit();",
				"pop.focus();"
			)));
		}
		break;

	// ������ �������� ����
	case 'ipin_verify':

		require_once 'ipin/nice.nuguya.oivs.php';
		$auth = new authentic($_POST['pin_kind'], 'ipin', $_POST['value']);

		if($_SESSION['sess_OrderNo'] != $auth->res->ordNo) scripts('alert("��û�� �ùٸ��� �ʽ��ϴ�.")');
		else {
			// ȸ�����Խ� �ߺ�Ȯ��
			if($_POST['pin_kind']=='join') {
				if($rankup_member->check_di_code($auth->infos['di_code'])) {
					scripts('alert("�˼��մϴ�. ȸ������ �̹� ���Ե� �����Դϴ�.\n\n���̵�/��й�ȣ ã�⸦ �̿��� �ֽñ� �ٶ��ϴ�.")');
					exit;
				}
			}

			// �������� Ȯ�� - 2012.04.02 added
			if(!$auth->check_ages($config_info['membership_age'], $auth->infos['birthday'])) {
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

			$_SESSION['vmKind'] = 'ipin'; // ������ ����� ������� ����
			$_SESSION['recvData'] = $_POST['value']; // �������� ����

			scripts('location.href = "'.$auth->pin_kinds[$_POST['pin_kind']]['landing'].'";');
		}
		unset($_SESSION['sess_OrderNo']);
		break;
}

?>