<?php
/**
 * 실명확인 클래스
 *@author: kurokisi
 *@authDate: 2012.01.12
 */

define('PIN_MODULE', 'nice'); // 한국신용정보 모듈

class rankup_authentic extends rankup_util {

	protected $config_table = 'rankup_configs';
	public $configs = array(); // 환경설정 값
	public $pin_kind = 'join'; //
	public $pin_kinds = array(); // 인증페이지 종류
	public $pin_settings = array(); //

	function __construct($pin_kind='') {
		global $base_url;
		parent::rankup_util();
		$this->pin_kinds = array(
			'join' => array(
				'name' => '회원가입페이지',
				'landing' => $base_url.'rankup_module/rankup_member/join_form.html'
			),
			'intro' => array(
				'name' => '인트로페이지',
				'landing' => $base_url.'main/index.html'
			)
		);
		$this->get_configs();
		if($pin_kind) {
			$this->pin_kind = $pin_kind;
			$this->pin_settings = $this->get_pins();
		}
	}

	// 인증값 리셋 - 로그아웃시 호출
	public function pin_reset() {
		unset($_SESSION['vmKind'], $_SESSION['recvData'], $_SESSION['member_type'], $_SESSION['sess_basic_pin']);
	}

	// 환경설정 로드
	public function get_configs($item='') {
		if($item) {
			$rows = $this->queryFetch("SELECT value FROM $this->config_table WHERE item='$item'");
			return $rows['value'];
		}
		else {
			$rows = $this->queryFetch("SELECT * FROM $this->config_table WHERE item='auth_configs'");
			if($rows['item'] && $rows['value']) $this->configs = unserialize($rows['value']);
		}
	}

	// 환경설정 저장
	public function set_configs() {
		$value = array(
			/* 실명인증 */
			'use_jumin' => $_POST['use_jumin'],
			'jumin_id1' => $_POST['jumin_id1'], // 국내일반인용 회원사ID
			'jumin_id2' => $_POST['jumin_id2'], // 국내외국인용 회원사ID
			/* 아이핀(I-Pin) */
			'use_ipin' => $_POST['use_ipin'],
			'ipin_id' => $_POST['ipin_id'], // 회원사ID
			'ipin_sikey' => $_POST['ipin_sikey'], // 사이트식별정보
			'ipin_keystr' => $_POST['ipin_keystr'] // 키스트링(80byte)
		);
		$_vals['item'] = 'auth_configs';
		$_vals['value'] = serialize($value);
		$values = $this->change_query_string($_vals);
		if(count($this->configs)) $this->query("UPDATE $this->config_table SET $values WHERE item='auth_configs'");
		else $this->query("INSERT INTO $this->config_table SET $values");
	}

	// 인증폼설정 반환
	public function get_pins() {
		$rows = $this->queryFetch("SELECT item, value FROM $this->config_table WHERE item='auth_pins'");
		if($rows['item']) {
			$values = unserialize($rows['value']);
			return $values;
		}
		else {
			return array();
		}
	}

	// 인증폼설정 저장
	public function set_pins() {
		$rows = $this->queryFetch("SELECT item, value FROM $this->config_table WHERE item='auth_pins'");
		$_vals['item'] = 'auth_pins';
		$_vals['value'] = serialize(array(
			'use_pin' => ($_POST['use_basic']=='yes' || $_POST['use_jumin']=='yes' || $_POST['use_ipin']=='yes') ? 'yes' : 'no',
			'use_basic' => ($_POST['use_basic']=='yes') ? 'yes' : 'no', // 기본인증(생일+성별)
			'use_jumin' => ($_POST['use_jumin']=='yes') ? 'yes' : 'no', // 실명인증
			'use_ipin' => ($_POST['use_ipin']=='yes') ? 'yes' : 'no' // 아이핀(I-Pin)인증
		));
		$values = $this->change_query_string($_vals);
		if($rows['item']) $this->query("UPDATE $this->config_table SET $values WHERE item='auth_pins'");
		else $this->query("INSERT INTO $this->config_table SET $values");
	}

	// 기본인증 저장 - $datas : Array()
	public function set_basic_pin($datas) {
		$_SESSION['sess_basic_pin'] = $this->encode(serialize($datas), 2);
		$_SESSION['vmKind'] = 'basic';
	}

	// 기본인증 반환
	public function get_basic_pin($datas) {
		return unserialize($this->decode($datas, 2));
	}

	// 연령제한 확인 - 2012.04.02 added
	public function check_ages($kind, $birthday) {
		$age = 0;
		switch($kind) {
			case '19over': $age = 19; break;
			case '14over': $age = 14; break;
		}
		return $age ? (strtotime($birthday) <= time() - (86400 * 365 * ($age-1))) : true;
	}
}

// 모듈 체이닝
include_once PIN_MODULE.'/authentic.class.php';

?>