<?php
/**
 * �Ǹ����� Ŭ����
 *@author: kurokisi
 *@authDate: 2012.01.12
 *@note: �ѽ���
 */
class authentic extends rankup_authentic {

	public $res = null; // response object
	public $infos = array(); // ��������

	function __construct($pin_kind='', $kind='', $datas='') {
		parent::__construct($pin_kind);

		/**
		 * ���������� ������ ó��
		 */
		// �ѽ������� ���� �������� ������ ���
		if($kind) {
			if($datas) $this->append($kind, $datas);
		}
		// �Ǹ�Ȯ�� or ������(I-Pin)������ �Ǿ� �ִ� ���
		else if($_SESSION['vmKind'] && $_SESSION['recvData']) {
			$this->append($_SESSION['vmKind'], $_SESSION['recvData']);
		}
		// �⺻������ �� ���
		else if($_SESSION['sess_basic_pin']) {
			$this->append('basic', $_SESSION['sess_basic_pin']);
		}
	}

	// �������� ����
	public function pin_check() {
		if($this->pin_settings['use_pin']!='yes') return true;
		if($this->infos['di_code']) return true;
		return false;
	}

	// �������� �ݿ�
	private function append($kind, $datas) {
		switch($kind) {
			case 'basic': // �⺻����
				$this->infos = $this->get_basic_pin($datas);
				$this->infos['di_code'] = md5($datas);
				break;

			case 'ipin': // ����������
				$this->res = new oivsObject();
				$this->res->athKeyStr = $this->configs['ipin_keystr'];
				$this->res->resolveClientData($datas);
				parent::change_encoding($this->res->userNm, 'IN');
				$this->infos = array(
					'name' => $this->res->niceNm, // ����
					'birthday' => $this->res->birthday, // �������(yyyymmdd)
					'gender' => $this->res->sex, // ����(1:����, 2:����)
					'foreigner' => $this->res->foreigner, // �����ܱ���(1:������, 2:�ܱ���)
					'ipin_pk' => $this->res->paKey, // �����ɹ�ȣ - �ѻ���� ������ ���� ����
					'di_code' => $this->res->dupeInfo, // DI �ڵ� - �ߺ�����Ȯ������(64byte)
					'ci_code' => $auth->res->coInfo // CI �ڵ� - ��������(88byte �̻�)
				);
				break;

			case 'jumin': // �Ǹ�����
				$this->res = new OivsObject();
				$this->res->clientData = $datas;
				$this->res->desClientData();
				$this->res->niceId = ($this->res->foreigner=='2') ? $this->configs['jumin_id2'] : $this->configs['jumin_id1'];
				$this->res->callService();
				parent::change_encoding($this->res->userNm, 'IN');

				preg_match('/(\d{2})(\d{2})(\d{2})(\d{1})/', $this->res->resIdNo, $ns); // $this->res->resIdNo; �ֹ�(�ܱ���)��Ϲ�ȣ(13)
				$birthday = sprintf('%d%s%s', $ns[1] + (($ns[4]<3) ? 1900 : 2000), $ns[2], $ns[3]);
				$gender = ($ns[4]%2) ? '1' : '2';

				$this->infos = array(
					'name' => $this->res->userNm, // ����
					'birthday' => $birthday, // �������(yyyymmdd)
					'gender' => $gender, // ����(1:����, 2:����)
					'foreigner' => $this->res->foreigner, // �����ܱ���(1:������, 2:�ܱ���)
					'di_code' => $this->res->dupeinfo // DI �ڵ� - �ߺ�����Ȯ������(64byte)
				);
				break;
		}
	}
}

// �ʼ� ���̺귯�� �ε�
if($_SESSION['vmKind'] && $_SESSION['recvData']) {
	require_once $_SESSION['vmKind'].'/nice.nuguya.oivs.php';
}

?>