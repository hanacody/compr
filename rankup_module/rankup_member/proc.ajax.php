<?php
/**
 * 멤버십 관련 통합 처리
 *@note: 회원가입 부터 관리 까지
 */

include_once '../../Libs/_php/rankup_basic.class.php';

switch($_POST['mode']) {

	// 전체회원관리 - 회원등급 변경
	case 'change_level':
		$rankup_control->check_admin();
		$uids = str_replace('__', "','", $_POST['uids']);
		$rankup_control->query("update $rankup_member->member_table2 set level=$_POST[level] where uid in ('$uids')");
		break;

	// 회원관리 환경설정 저장
	case 'save_member':
		$rankup_control->check_admin();
		$rankup_control->change_encoding($_POST, 'IN');

		$_vals['membership_use'] = $_POST['membership_use']; // 회원제 사용여부
		$_vals['membership_types'] = implode(',', $_POST['membership_types']); // 가입 유형 제한
		$_vals['membership_age'] = $_POST['membership_age']; // 가입 연령 제한

		$smlevel = array(); // 회원 등급 설정
		foreach(range(1,$rankup_member->lowest_level) as $key) { $smlevel[$key] = $_POST['level'.$key]; }
		$_vals['smlevel'] = serialize($smlevel);

		// 비밀번호 변경 설정
		$_vals['change_pwd_use'] = $_POST['change_pwd_use'];
		$_vals['change_pwd_terms'] = $_POST['change_pwd_terms'];

		$values = $rankup_control->change_query_string($_vals);
		$rankup_control->query("update rankup_siteconfig set $values");
		unset($_vals);

		// 실명인증 설정 저장
		include_once '../rankup_authentic/rankup_authentic.class.php';
		$auth = new rankup_authentic;
		$auth->set_pins();

		// 회원 가입시 필수항목 설정 - rankup_configs table
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

	case 'del_log': // 3개월전 로그삭제
		$rankup_control->check_admin();
		$rankup_member->del_log();
		break;

	case 'reset_log': // 로그초기화
		$rankup_control->check_admin();
		$rankup_member->reset_log();
		break;

	case 'find_id': // 아이디 찾기
		$rankup_control->change_encoding($_POST, 'IN');
		$where = q(" AND mt.name='%s' AND mt2.email='%s'", $_POST['f_name'], $_POST['f_email']);
		$rows = $rankup_member->queryFetch("select mt.name, mt.uid, mt2.email from $rankup_member->member_table as mt, $rankup_member->member_table2 as mt2 WHERE mt2.uid=mt.uid".$where);
		if($rows['uid']) {
			// 아이디 찾기
			include_once '../rankup_mailing/rankup_mailing.class.php';
			$mailing = new rankup_mailing('found_id');
			$mailing->send($rows['email'], array(
				'이름' => $rows['name'],
				'아이디' => $rows['uid']
			));
			scripts('alert("회원님의 이메일로 아이디가 발송되었습니다.");');
		}
		else {
			scripts('alert("일치하는 회원정보가 존재하지 않습니다.")');
		}
		break;

	case 'find_passwd': // 비밀번호 찾기
		$rankup_control->change_encoding($_POST, 'IN');
		$where = q(" AND mt.uid='%s' AND mt.name='%s' AND mt2.email='%s'", $_POST['f_userid'], $_POST['f_name'], $_POST['f_email']);
		$rows = $rankup_member->queryFetch("select mt.name, mt.uid, mt2.email from $rankup_member->member_table as mt, $rankup_member->member_table2 as mt2 WHERE mt2.uid=mt.uid".$where);
		if($rows['uid']) {
			// 아이디 찾기
			include_once '../rankup_mailing/rankup_mailing.class.php';
			$mailing = new rankup_mailing('found_pwd');
			$mailing->send($rows['email'], array(
				'이름' => $rows['name'],
				'아이디' => $rows['uid'],
				'비밀번호' => $rankup_member->get_new_passwd($rows['uid'])
			));
			scripts('alert("회원님의 이메일로 임시 비밀번호가 발송되었습니다.");');
		}
		else {
			scripts('alert("일치하는 회원정보가 존재하지 않습니다.")');
		}
		break;

	case 'change_passwd': // 비밀번호 변경
		$uid = $rankup_member->get_id();
		if(!$rankup_member->match_table($uid, $_POST['passwd'])) {
			scripts('alert("죄송합니다. 입력하신 비밀번호가 일치하지 않습니다.")');
		}
		else {
			$values = q("passwd=password('%s'), passwd_time=now()", $_POST['new_passwd']);
			$rankup_member->modify_member_table($uid, $values);
			scripts('alert("비밀번호가 변경되었습니다.");location.replace("../../main/index.html");');
		}
		break;


	/**
	 * 회원가입 처리
	 */
	case 'verify_join':
		switch($_POST['step']) {
			case '1': // 1단계 - 인트로
				$_SESSION['member_type'] = $_POST['type'];
				scripts('location.href="join_policy.html"');
				break;

			case '2': // 2단계 - 약관동의
				include_once '../rankup_authentic/rankup_authentic.class.php';
				$auth = new authentic('join');
				if($auth->pin_settings['use_pin']=='yes') {
					// 인트로 인증페이지에서 인증받은 경우 - 가입인증 단계 스킵
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

			case '3': // 3단계 - 회원인증(생일+성별) - @note: 실명인증 사용시에는 자동으로 4단계로 넘어감
				rankup_util::change_encoding($_POST, 'IN');
				include_once '../rankup_authentic/rankup_authentic.class.php';
				$auth = new rankup_authentic('join');

				// 연령제한 확인 - 2012.04.02 added
				$birthday = implode('', $_POST['birthday']);
				if(!$auth->check_ages($config_info['membership_age'], $birthday)) {
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

				$auth->set_basic_pin(array(
					'name' => $_POST['userNm'],
					'birthday' => $birthday,
					'gender' => $_POST['gender']
				));
				scripts('location.href="join_form.html"');
				break;

			case '4': // 4단계 - 가입처리 @note: form submit
				include_once '../rankup_authentic/rankup_authentic.class.php';
				$auth = new authentic('join');

				$_vals['kind'] = $_POST['kind']; // 회원 종류 { 'personal' | 'company' }
				$_vals['uid'] = $_POST['join_id']; // 아이디
				$_vals['name'] = $auth->infos['name']; // 이름
				$_vals['join_ip'] = $_SERVER['REMOTE_ADDR']; // 가입 IP
				$_vals['join_time'] = date('Y-m-d H:i:s'); // 가입일시
				$_vals['passwd_time'] = $_vals['join_time']; // 비번변경 일시 - default value

				// 검증
				if(!$_POST['join_passwd']) {
					scripts('alert("비밀번호가 올바르지 않습니다.")');
					exit;
				}

				$values = $rankup_control->change_query_string($_vals);
				$values .= q(", passwd=password('%s')", $_POST['join_passwd']); // 비밀번호

				// 필수항목
				$_vals2['uid'] = $_vals['uid'];
				$_vals2['pin_kind'] = $_SESSION['vmKind']; // 가입인증 종류( basic, jumin, ipin )
				$_vals2['di_code'] = $auth->infos['di_code']; // 실명인증값(고유값-64Byte) - 중복회원 방지용
				$_vals2['birthday'] = $auth->infos['birthday']; // 생일( yyyymmdd )
				$_vals2['gender'] = $auth->infos['gender']; // 성별( 1:남, 2:여 )
				$_vals2['level'] = $rankup_member->join_level; // 회원 등급
				$_vals2['email'] = $_POST['email']; // 이메일
				$_vals2['mailing'] = $_POST['mailing']; // 메일링수신
				$_vals2['sms'] = $_POST['sms']; // SMS 수신

				// 옵션항목
				$_vals2['nickname'] = $_POST['nickname']; // 닉네임
				$_vals2['phone'] = $_POST['phone'] ? implode('-', $_POST['phone']) : '';  // 일반전화
				$_vals2['hphone'] = $_POST['hphone'] ? implode('-', $_POST['hphone']) : ''; // 휴대전화
				$_vals2['zipcode'] = $_POST['zipcode']; // 우편번호
				$_vals2['address1'] = $_POST['addrs1']; // 주소
				$_vals2['address2'] = $_POST['addrs2']; // 나머지주소
				$_vals2['introduce'] = $_POST['introduce']; // 가입인사

				$values2 = $rankup_control->change_query_string($_vals2);

				$result = $rankup_member->insert_member_table($values, $values2); // 등록
				if(!$result) {
					scripts('alert("회원 가입이 실패하였습니다.")');
					exit;
				}
				else {
					// 로그인 처리
					$rankup_member->set_member_session($_vals['uid'], $_vals['kind']);

					// 회원가입 축하 이메일 발송
					include_once '../rankup_mailing/rankup_mailing.class.php';
					$mailing = new rankup_mailing('member_join');
					$mailing->send($_vals2['email'], array(
						'이름' => $_vals['name'],
						'아이디' => $_vals['uid'],
						'비밀번호' => $rankup_member->get_password($_POST['join_passwd'])
					));
					// 회원가입 축하 SMS 발송
					if($_vals2['hphone']) {
						include_once '../rankup_sms/rankup_sms_config.class.php';
						include_once '../rankup_sms/rankup_sms.class.php';
						$sms = new rankup_sms('sms_join');
						$sms->set_value($_vals2['hphone']);
						$sms->set_merge(array('name'=>$_vals['name']));
						$sms->send_msg();
					}
					$return_page = rankup_https_change::https_change('main/index.html', 'http');
					scripts('alert("회원이 되신것을 진심으로 축하합니다."); location.replace("'.$return_page.'");');
				}
				break;
		}
		break;

	// 회원 정보 변경
	case 'modify_info': //@note: 관리자가 수정

		$rankup_control->check_admin();

		$uid = $_POST['uid'];
		$_vals['name'] = $_POST['name']; // 이름
		$values = $rankup_control->change_query_string($_vals);

		$_vals2['birthday'] = implode('', $_POST['birthday']); // 생년월일 - 2012.05.23 added
		$_vals2['gender'] = $_POST['gender']; // 성별 - 2012.05.23 added
		$_vals2['level'] = $_POST['level']; // 회원등급
		$_vals2['memo'] = rankup_util::trans_wysiwyg($_POST['memo']); // 관리자메모

	case 'modify_myinfo': //@note: 회원이 수정

		if($_POST['mode']=='modify_myinfo') $uid = $rankup_member->get_id();
		// 비밀번호
		if($_POST['new_passwd']) { // 2012.05.23 fixed
			if($values) $values .= ',';
			$values .= q("passwd=password('%s'), passwd_time=now()", $_POST['new_passwd']); // 비밀번호
		}

		$_vals2['nickname'] = $_POST['nickname']; // 닉네임
		$_vals2['email'] = $_POST['email']; // 이메일
		$_vals2['phone'] = $_POST['phone'] ? implode('-', $_POST['phone']) : ''; // 전화번호
		$_vals2['hphone'] = $_POST['hphone'] ? implode('-', $_POST['hphone']) : ''; // 휴대전화
		$_vals2['zipcode'] = $_POST['zipcode']; // 우편번호
		$_vals2['address1'] = $_POST['addrs1']; // 주소
		$_vals2['address2'] = $_POST['addrs2']; // 나머지 주소
		$_vals2['introduce'] = $_POST['introduce']; // 가입인사
		$_vals2['mailing'] = $_POST['mailing']; // 메일링수신
		$_vals2['sms'] = $_POST['sms']; // SMS 수신
		$values2 = $rankup_control->change_query_string($_vals2);
		$rankup_member->modify_member_table($uid, $values, $values2); // 수정

		if($_POST['mode']=='modify_myinfo') {
			// SMS 발송
			if($_vals2['hphone']) {
				include_once '../rankup_sms/rankup_sms_config.class.php';
				include_once '../rankup_sms/rankup_sms.class.php';
				$sms = new rankup_sms('sms_memberUpd');
				$sms->set_value($_vals2['hphone']);
				$sms->set_merge(array('name'=>$member_info['name']));
				$sms->send_msg();
			}
			$rankup_control->popup_msg_js('저장되었습니다.', rankup_https_change::https_change('rankup_module/rankup_member/member_modify.html', 'http'));
		}
		else {
			scripts('alert("저장되었습니다.");history.go(-2);');
		}
		break;


	/**
	 * 휴대전화 인증
	 */

	// 인증번호 발송
	case 'send_vnumber':
		$domain = $config_info['domain'];
		$hphone = implode('-', $_POST['hphone_number']);
		$vnumber = mt_rand(100000, 999999);
		if($rankup_member->sendable_vsms($hphone)) {
			scripts('alert("죄송합니다. 인증번호를 전송할 수 없습니다.\n\n인증번호는 하루에 최대 '.$rankup_member->vsms_send_limits.'회까지만 전송하실 수 있습니다.\n자세한 사항은 사이트 관리자에게 문의하시기 바랍니다.")');
			exit;
		}
		$_SESSION['sms_verify_number'] = $vnumber; // 인증번호 세션 저장

		include_once '../rankup_sms/rankup_sms_config.class.php';
		include_once '../rankup_sms/rankup_sms.class.php';
		$sms = new rankup_sms('sms_send');
		$sms->is_valid_send = true;
		$sms->phone = $hphone;
		$sms->msg = fetch_skin(compact('vnumber', 'domain'), "회원가입 인증번호는 [{:vnumber:}]입니다.\n{:domain:}");
		$sms->send_msg();

		// SMS 발송로그 기록
		$rankup_member->save_vsms_log($hphone, $vnumber);
		break;

	// 인증번호 확인
	case 'check_vnumber':
		if($_SESSION['sms_verify_number']!=$_POST['vnumber']) {
			scripts('alert("인증번호가 일치하지 않습니다.")');
		}
		else {
			unset($_SESSION['sms_verify_number']); // 인증번호 세션 제거
		}
		break;

	// 선택한 로그삭제
	case 'del_vsms':
		$rankup_control->check_admin();
		$rankup_member->del_vsms();
		break;

	// 3개월전 로그삭제
	case 'del_vsms_log':
		$rankup_control->check_admin();
		$rankup_member->del_vsms_log();
		break;

	// 로그초기화
	case 'reset_vsms_log':
		$rankup_control->check_admin();
		$rankup_member->reset_vsms_log();
		break;

}

?>