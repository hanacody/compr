<?php
/**
 * 실명인증 클래스
 *@author: kurokisi
 *@authDate: 2012.01.12
 *@note: 한신정
 */
class authentic extends rankup_authentic {

	public $res = null; // response object
	public $infos = array(); // 인증정보

	function __construct($pin_kind='', $kind='', $datas='') {
		parent::__construct($pin_kind);

		/**
		 * 인증정보가 있으면 처리
		 */
		// 한신정으로 부터 인증값을 수신한 경우
		if($kind) {
			if($datas) $this->append($kind, $datas);
		}
		// 실명확인 or 아이핀(I-Pin)인증이 되어 있는 경우
		else if($_SESSION['vmKind'] && $_SESSION['recvData']) {
			$this->append($_SESSION['vmKind'], $_SESSION['recvData']);
		}
		// 기본인증을 한 경우
		else if($_SESSION['sess_basic_pin']) {
			$this->append('basic', $_SESSION['sess_basic_pin']);
		}
	}

	// 인증정보 검증
	public function pin_check() {
		if($this->pin_settings['use_pin']!='yes') return true;
		if($this->infos['di_code']) return true;
		return false;
	}

	// 인증정보 반영
	private function append($kind, $datas) {
		switch($kind) {
			case 'basic': // 기본인증
				$this->infos = $this->get_basic_pin($datas);
				$this->infos['di_code'] = md5($datas);
				break;

			case 'ipin': // 아이핀인증
				$this->res = new oivsObject();
				$this->res->athKeyStr = $this->configs['ipin_keystr'];
				$this->res->resolveClientData($datas);
				parent::change_encoding($this->res->userNm, 'IN');
				$this->infos = array(
					'name' => $this->res->niceNm, // 성명
					'birthday' => $this->res->birthday, // 생년월일(yyyymmdd)
					'gender' => $this->res->sex, // 성별(1:남성, 2:여성)
					'foreigner' => $this->res->foreigner, // 내·외국인(1:내국인, 2:외국인)
					'ipin_pk' => $this->res->paKey, // 아이핀번호 - 한사람이 여러개 소유 가능
					'di_code' => $this->res->dupeInfo, // DI 코드 - 중복가입확인정보(64byte)
					'ci_code' => $auth->res->coInfo // CI 코드 - 연계정보(88byte 이상)
				);
				break;

			case 'jumin': // 실명인증
				$this->res = new OivsObject();
				$this->res->clientData = $datas;
				$this->res->desClientData();
				$this->res->niceId = ($this->res->foreigner=='2') ? $this->configs['jumin_id2'] : $this->configs['jumin_id1'];
				$this->res->callService();
				parent::change_encoding($this->res->userNm, 'IN');

				preg_match('/(\d{2})(\d{2})(\d{2})(\d{1})/', $this->res->resIdNo, $ns); // $this->res->resIdNo; 주민(외국인)등록번호(13)
				$birthday = sprintf('%d%s%s', $ns[1] + (($ns[4]<3) ? 1900 : 2000), $ns[2], $ns[3]);
				$gender = ($ns[4]%2) ? '1' : '2';

				$this->infos = array(
					'name' => $this->res->userNm, // 성명
					'birthday' => $birthday, // 생년월일(yyyymmdd)
					'gender' => $gender, // 성별(1:남성, 2:여성)
					'foreigner' => $this->res->foreigner, // 내·외국인(1:내국인, 2:외국인)
					'di_code' => $this->res->dupeinfo // DI 코드 - 중복가입확인정보(64byte)
				);
				break;
		}
	}
}

// 필수 라이브러리 로드
if($_SESSION['vmKind'] && $_SESSION['recvData']) {
	require_once $_SESSION['vmKind'].'/nice.nuguya.oivs.php';
}

?>