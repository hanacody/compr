<?php
/**
 * 문자상담 서비스 클래스
 *@author: kurokisi
 *@authDate: 2012.03.20
 */
class rankup_lconsult extends rankup_util {
	private $table = 'rankup_letter_consult';
	private $config_table = 'rankup_configs';
	private $config_kind = 'lconsult_config';

	function __construct() {
		parent::rankup_util();
	}

	// 환경설정 반환
	public function get_settings() {
		$rows = $this->queryFetch("SELECT value from $this->config_table WHERE item='$this->config_kind'");
		return $rows['value'] ? unserialize($rows['value']) : array();
	}

	// 환경설정 저장
	public function set_settings() {
		$prows = $this->queryFetch("SELECT item FROM $this->config_table WHERE item='$this->config_kind'");;
		$_vals['item'] = $this->config_kind;
		$_vals['value'] = serialize(array(
			'use_alarm' => $_POST['use_alarm'],
			'alarm_phone' => $_POST['alarm_phone'],
			'alarm_message' => $_POST['alarm_message']
		));
		$values = $this->change_query_string($_vals);
		if($prows['item']==$this->config_kind) $this->query("UPDATE $this->config_table SET $values WHERE item='$this->config_kind'");
		else $this->query("INSERT INTO $this->config_table SET $values");
	}

	// 문의내용 반환
	public function get_conslut($no) {
		return $this->queryFetch("SELECT * FROM $this->table".q(" WHERE no=%d", $no));
	}

	// 문의내용 저장
	public function save_consult() {
		global $member_info;

		if($member_info['uid']) $_vals['uid'] = $member_info['uid'];
		$_vals['name'] = $_POST['l_name'];
		$_vals['phone'] = $_POST['l_phone'];
		$_vals['consult'] = $_POST['l_consult'];
		$_vals['regist_time'] = date('Y-m-d H:i:s');
		$_vals['regist_ip'] = $_SERVER['REMOTE_ADDR'];
		$values = $this->change_query_string($_vals);

		$this->query("INSERT INTO $this->table SET $values");
	}

	// 문의목록 반환
	public function print_contents($entry, $limits=15) {
		$stacks = array();
		if($_GET['use_date']=='on') $stacks[] = q("regist_time>='%s 00:00:00' AND regist_time<='%s 23:59:59'", $_GET['sdate'], $_GET['edate']);
		if($_GET['status']) $stacks[] = q("answer_status='%s'", $_GET['status']);
		if($_GET['skey']) $stacks[] = q("%s='%%%s%%'", $_GET['smode'], $_GET['skey']);
		if(count($stacks)) $where = ' WHERE '.implode(' AND ', $stacks);

		$stpos = $this->get_query_point($_GET['page'], $limits);
		$datas = $this->query("SELECT SQL_CALC_FOUND_ROWS * FROM $this->table".$where." ORDER BY no desc LIMIT $stpos, $limits");
		$totals = $this->queryR("SELECT FOUND_ROWS()");
		$contents = fetch_contents($datas, $entry, array($this, '_l29'));
		return array($totals, $contents);
	}
	public function _l29($bind) {
		extract($bind);
		if(isset($on_uid) && $rows['uid']) $rows['on_uid'] = fetch_skin($rows, $on_uid);
		if(isset($time_format)) {
			foreach(array('regist_time', 'answered_time') as $field) {
				$rows[$field] = date($time_format, strtotime($rows[$field]));
			}
		}
		if($rows['answer_status']=='yes') {
			$rows['on_selected'] = $on_selected;
			$rows['on_answered_time'] = fetch_skin($rows, $on_answered_time);
		}
		return array($rows, $skin);
	}

	// 답변내용 저장
	public function save_answer() {
		$_vals['answer'] = $_POST['answer'];
		$_vals['answered_time'] = date('Y-m-d H:i:s');
		$_vals['status'] = 'answered';
		$values = $this->change_query_string($_vals);
		$this->query("UPDATE $this->table SET $values WHERE no=$_POST[no]");
	}

	// 답변상태 변경
	public function set_status() {
		$time = ($_POST['status']=='yes') ? 'NOW()' : 'null';
		$this->query(q("UPDATE $this->table SET answer_status='%s', answered_time=%s WHERE no=%d", $_POST['status'], $time, $_POST['no']));
		if($_POST['status']=='yes') {
			$rows = $this->get_conslut($_POST['no']);
			return $rows['answered_time'];
		}
	}

	// 문의글 삭제
	public function del() {
		$nos = str_replace('__', ',', $_POST['nos']);
		$this->query("DELETE FROM $this->table".q(" WHERE no IN(%s)", $nos));
	}
}
?>