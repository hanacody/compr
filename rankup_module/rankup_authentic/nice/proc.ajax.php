<?php
/**
 * 실명인증 처리
 *@author: kurokisi
 *@authDate: 2012.01.12
 */
include_once '../../../Libs/_php/rankup_basic.class.php';
unset($_SESSION['vmKind'], $_SESSION['recvData']); // 인증값 초기화
include_once '../rankup_authentic.class.php';

switch($_POST['mode']) {

	// 실명인증 요청
	case 'jumin_validate':

		rankup_util::change_encoding($_POST, 'IN');

		$userNm = $_POST['userNm']; // 이름
		$foreigner = $_POST['foreigner']; // 내·외국인
		$resIdNo = preg_replace('/[^\d]/', '', $_POST['userNo1']).preg_replace('/[^\d]/', '', $_POST['userNo2']); // 주민번호 & 외국인번호

		// 조회사유 : '10'-회원가입, '20'-기존회원 확인, '30'-성인인증, '40'-비회원 확인, '90'-기타 사유
		if($config_info['membership_age']=='19over') $InqRsn = 30;
		else if($_POST['pin_kind']=='join') $InqRsn = 10; // 회원가입
		else if($_POST['pin_kind']=='intro') $InqRsn = 40; // 인트로 - 비회원 확인
		else $InqRsn = 20;

		// 검수
		if(!$userNm) scripts("alert(getCheckMessage('S23')); return false;");
		else if(!$_POST['userNo1'] || !$_POST['userNo2']) {
			if($foreigner=='2') scripts("alert(getCheckMessage('S27')); return false;");
			else scripts("alert(getCheckMessage('S21')); return false;");
		}
		else { // 실명인증요청
			scripts("verify_infos('jumin_verify', makeSendInfo('$userNm', '$resIdNo', '$InqRsn', '$foreigner'));");
		}
		break;


	// 실명인증 요청/검증
	case 'jumin_verify':

		require_once 'jumin/nice.nuguya.oivs.php';
		$auth = new authentic($_POST['pin_kind'], 'jumin', $_POST['value']);

		// 인증성공
		if($auth->res->retCd==1) {
			// 회원가입시 중복확인
			if($_POST['pin_kind']=='join') {
				if($rankup_member->check_di_code($auth->infos['di_code'])) {
					scripts('alert("죄송합니다. 회원님은 이미 가입된 상태입니다.\n\n아이디/비밀번호 찾기를 이용해 주시기 바랍니다.")');
					exit;
				}
			}

			// 연령제한 확인 - 2012.04.02 added
			if(!$auth->check_ages($config_info['membership_age'], $auth->infos['birthday'])) {
				switch($config_info['membership_age']) {
					case '14over':
						scripts('alert("죄송합니다. 본 사이트는 14세 이상만 이용이 가능합니다.")');
						break;
					case '19over':
						scripts('alert("죄송합니다. 본 사이트는 19세 이상만 이용이 가능합니다.")');
						break;
				}
				exit;
			}

			$_SESSION['vmKind'] = 'jumin'; // 인증시 사용한 모듈종류 저장
			$_SESSION['recvData'] = $_POST['value']; // 인증정보 저장

			scripts('alert("실명확인이 되었습니다."); location.href = "'.$auth->pin_kinds[$_POST['pin_kind']]['landing'].'";');

		}
		// 실명안심차단 상태
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
			// 실명명의도용차단 상태
			else if($auth->res->retDtlCd=='C') {
				scripts("alert(getMessage('$code', '$message') +'\\n\\n'+ getCheckMessage('S32'));");
			}
			// 기타 오류
			else {
				scripts("alert(getMessage('$code', '$message'))");
			}
		}
		break;


	// 아이핀 인증요청
	case 'ipin_validate':

		require_once 'ipin/nice.nuguya.oivs.php';
		$auth = new authentic($_POST['pin_kind'], 'ipin');

		$ReturnURL = $config_info['domain'].'rankup_module/rankup_authentic/nice/ipin/response.php';
		$OrderNo = date('Ymd').rand(100000000000, 999999999999); // 주문번호(14~20) 유니크한 값
		$PingInfo = getPingInfo();

		// 조회사유 : '10'-회원가입, '20'-기존회원 확인, '30'-성인인증, '40'-비회원 확인, '50'-기타 사유
		if($config_info['membership_age']=='19over') $InqRsn = 30; // 성인인증
		else if($_POST['pin_kind']=='join') $InqRsn = 10; // 회원가입
		else if($_POST['pin_kind']=='intro') $InqRsn = 40; // 인트로 - 비회원 확인
		else $InqRsn = 20; // 기존회원 확인

		// 검수
		if(!$auth->configs['ipin_id']) scripts("alert(getCheckMessage('S60')); return false;");
		else if(!$PingInfo) scripts("alert(getCheckMessage('S61')); return false;");
		else if(!$ReturnURL) scripts("alert(getCheckMessage('S64')); return false;");
		else {

			$_SESSION['sess_OrderNo'] = $OrderNo; // 해킹방지를 위한 요청정보 저장

			// 인증창 로드
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

	// 아이핀 인증정보 검증
	case 'ipin_verify':

		require_once 'ipin/nice.nuguya.oivs.php';
		$auth = new authentic($_POST['pin_kind'], 'ipin', $_POST['value']);

		if($_SESSION['sess_OrderNo'] != $auth->res->ordNo) scripts('alert("요청이 올바르지 않습니다.")');
		else {
			// 회원가입시 중복확인
			if($_POST['pin_kind']=='join') {
				if($rankup_member->check_di_code($auth->infos['di_code'])) {
					scripts('alert("죄송합니다. 회원님은 이미 가입된 상태입니다.\n\n아이디/비밀번호 찾기를 이용해 주시기 바랍니다.")');
					exit;
				}
			}

			// 연령제한 확인 - 2012.04.02 added
			if(!$auth->check_ages($config_info['membership_age'], $auth->infos['birthday'])) {
				switch($config_info['membership_age']) {
					case '14over':
						scripts('alert("죄송합니다. 본 사이트는 14세 이상만 이용이 가능합니다.")');
						break;
					case '19over':
						scripts('alert("죄송합니다. 본 사이트는 19세 이상만 이용이 가능합니다.")');
						break;
				}
				exit;
			}

			$_SESSION['vmKind'] = 'ipin'; // 인증시 사용한 모듈종류 저장
			$_SESSION['recvData'] = $_POST['value']; // 인증정보 저장

			scripts('location.href = "'.$auth->pin_kinds[$_POST['pin_kind']]['landing'].'";');
		}
		unset($_SESSION['sess_OrderNo']);
		break;
}

?>