<?php
##����� ó���ϱ� ���� Ŭ����
class rankup_member extends rankup_util {
	var $sess_id = 'niceId';	//���� ���̵� ��
	var $sess_val = 'niceVal';	//���� ������
	var $member_table='rankup_member';
	var $member_id = 'uid';	//��� ���̵� �ʵ�
	var $member_passwd = 'passwd';	//��� ��й�ȣ �ʵ�
	var $member_kind = 'kind';	 //��� ���� �ʵ�
	var $member_table2='rankup_member_extend';	//Ȯ�� ��� ���̺�
	var $log_table = 'rankup_member_log'; // ȸ���α��� �α� ���̺�
	var $vsms_log_table = 'rankup_member_vsms_log'; // ������ȣ �߼� �α� ���̺�
	var $vsms_send_limits = 5; // 1�� 5ȸ ����

	var $join_level = 6; // ȸ�� ���Խ� ���
	var $lowest_level = 7; // ȸ�� ������ ��� - ��ȸ��
	var $stop_level = 8; // ����ȸ�� (�̻��)

	var $base_dir = '';
	var $base_url = '';
	// ȸ�� �з�
	var $member_kinds = array(
		'personal' => '����ȸ��',
		'company' => '���ȸ��'
	);
	// ȸ�� ����
	var $member_types = array(
		'14over' => '�Ϲ�ȸ��(��14�� �̻� ������)',
		'14under' => '��14�� �̸� ȸ��(��14�� �̸� ������)',
		'inforeign' => '���� �ܱ��� ȸ��',
		'outforeign' => '�ؿ� �ܱ��� ȸ��',
	);
	var $genders = array(
		'1' => '����',
		'2' => '����'
	);
	var $form_options = array();

	function rankup_member() {
		parent::rankup_util();
		$this->base_url = rankup_basic::base_url();
		$this->base_dir = rankup_basic::base_dir();

		// ȸ�� ������ �ɼ�
		$this->form_options = array(
			array('key' => 'nickname', 'name' => '�г���'),
			array('key' => 'phone', 'name' => '�Ϲ���ȭ'),
			array('key' => 'address', 'name' => '�ּ�'),
			array('key' => 'introduce', 'name' => '�����λ�'),
			array('key' => 'hphone', 'name' => '�޴���ȭ'),
			array('key' => 'hphone_verify', 'name' => '�޴�������'),
		);
	}

	// ��й�ȣ ��ȯ - 2011.09.01 added
	function get_password($password) {
		$half = strlen($password)/2;
		return substr($password, 0, -(floor($half))).str_repeat('*', ceil($half));
	}

	// ȸ������Ȩ ��ȯ
	function print_form_options($entry) {
		return fetch_contents($this->form_options, $entry, array($this, '_m37'));
	}
	function _m37($bind) {
		extract($bind);
		if($values[$rows['key']]['use']=='yes') $rows['on_used'] = $on_used;
		if($rows['key']=='hphone_verify') $skin[2] = $on_vsms;
		if($values[$rows['key']]['req']=='yes') $rows['on_required'] = $on_required;
		return array($rows, $skin);
	}

	// ȸ�� �ӽú�й�ȣ ��ȯ
	function get_new_passwd($uid) {
		$new_passwd = preg_replace('/[^a-cA-C\d]/', '', parent::encode($uid.time(), 2));
		$this->query("UPDATE $this->member_table SET passwd=password('$new_passwd') WHERE uid='$uid'");
		return $new_passwd;
	}

	//�α��ν� ������ �߱�
	function set_member_session($id,$val){
		$_SESSION[$this->sess_id] = $id;
		$_SESSION[$this->sess_val] = $val;

		// �α������� �� �湮�� ����
		$rows = $this->queryFetch("SELECT login_infos, prev_login_infos FROM $this->member_table2 WHERE uid='$id'");
		$_vals['prev_login_infos'] = $rows['login_infos'];
		$_vals['login_infos'] = serialize(array(
			'login_ip' => $_SERVER['REMOTE_ADDR'],
			'login_time' => date('Y-m-d H:i:s')
		));
		$values = $this->change_query_string($_vals);
		$this->query("UPDATE $this->member_table2 SET visit=visit+1, ".$values." WHERE uid='$id'"); // �湮 �� ī��Ʈ
		$this->save_log($id);
	}

	// ȸ���α��� ��� - 2012.01.02 added
	function save_log($login_id) {
		$_vals['member_id'] = $login_id;
		$_vals['login_date'] = date('Y-m-d H:i:s');
		$_vals['login_ip_addr'] = $_SERVER['REMOTE_ADDR'];
		$values = $this->change_query_string($_vals);
		$this->query("INSERT INTO $this->log_table SET $values");
	}

	// ȸ���α��� ��� ��ȯ - 2012.01.02 added
	function print_log($entry, $limits=20) {
		$stacks = array();
		if($_GET['skey']) $stacks[] = q("%s like '%%%s%%'", $_GET['smode'], $_GET['skey']);
		if($_GET['use_date']=='on') $stacks[] = q("login_date>='%s 00:00:00' AND login_date<='%s 23:59:59'", $_GET['sdate'], $_GET['edate']);
		if(count($stacks)) $where = ' WHERE '.implode(' AND ', $stacks);

		$stpos = $this->get_query_point($_GET['page'], $limits);
		$datas = $this->query("SELECT SQL_CALC_FOUND_ROWS * FROM $this->log_table".$where." ORDER BY no DESC LIMIT $stpos, $limits");
		$totals = $this->queryR("SELECT FOUND_ROWS()");
		$entry['row'] = $totals - $stpos;
		$contents = fetch_contents($datas, $entry, array($this, '_m454'));
		return array($totals, $contents);
	}
	function _m454($bind) {
		extract($bind);
		$rows['row'] = $row;
		return array($rows, $skin);
	}

	// 3������ �α� ����
	function del_log() {
		$this->query("DELETE FROM $this->log_table WHERE login_date<date_sub(now(), interval 3 month)");
	}
	// �α��ʱ�ȭ
	function reset_log() {
		$this->query("TRUNCATE TABLE $this->log_table");
	}

	// ������ȣ�� ���۰������� ��ȯ - 2012.03.08 added
	function sendable_vsms($hphone) {
		$counts = $this->queryR("SELECT count(hphone) FROM $this->vsms_log_table WHERE hphone='$hphone' AND date_format(send_time, '%Y-%m-%d')=curdate()");
		return ($counts>=$this->vsms_send_limits);
	}

	// ������ȣ �߼� ��� - 2012.03.08 added
	function save_vsms_log($hphone, $vnumber) {
		$_vals['hphone'] = $hphone;
		$_vals['vnumber'] = $vnumber;
		$_vals['send_time'] = date('Y-m-d H:i:s');
		$_vals['ip_addr'] = $_SERVER['REMOTE_ADDR'];
		$values = $this->change_query_string($_vals);
		$this->query("INSERT INTO $this->vsms_log_table SET $values");
	}

	// ������ȣ �߼� ��� ��ȯ - 2012.03.08 added
	function print_vsms_log($entry, $limits=20) {
		$stacks = array();
		if($_GET['skey']) $stacks[] = q("%s like '%%%s%%'", $_GET['smode'], $_GET['skey']);
		if($_GET['use_date']=='on') $stacks[] = q("send_time>='%s 00:00:00' AND send_time<='%s 23:59:59'", $_GET['sdate'], $_GET['edate']);
		if(count($stacks)) $where = ' WHERE '.implode(' AND ', $stacks);

		$stpos = $this->get_query_point($_GET['page'], $limits);
		$datas = $this->query("SELECT SQL_CALC_FOUND_ROWS * FROM $this->vsms_log_table".$where." ORDER BY no DESC LIMIT $stpos, $limits");
		$totals = $this->queryR("SELECT FOUND_ROWS()");
		$entry['row'] = $totals - $stpos;
		$contents = fetch_contents($datas, $entry, array($this, '_m143'));
		return array($totals, $contents);
	}
	function _m143($bind) {
		extract($bind);
		$rows['row'] = $row;
		return array($rows, $skin);
	}

	// ������ ������ȣ�α� ����
	function del_vsms() {
		$nos = str_replace('__', ',', $_POST['nos']);
		$this->query("DELETE FROM $this->vsms_log_table WHERE no in($nos)");
	}
	// 3������ ������ȣ�α� ����
	function del_vsms_log() {
		$this->query("DELETE FROM $this->vsms_log_table WHERE send_time<date_sub(now(), interval 3 month)");
	}
	// ������ȣ�α� �ʱ�ȭ
	function reset_vsms_log() {
		$this->query("TRUNCATE TABLE $this->vsms_log_table");
	}

	//�α����� ȸ���� ������ ����
	function delete_member_session(){
		unset($_SESSION[$this->sess_id], $_SESSION[$this->sess_val]);
	}

	//id, passwd�� ��� ���̺�� ��
	function compare_member_table($id,$passwd){
	}

	//�߱��� ���ǰ��� ������ ���� �ϴ��� üũ
	function check_session_value(){
	}

	//�α��� �ߴ��� üũ - 2008.09.18 ����
	function is_member($member_kind='') {
		if(!isset($_SESSION[$this->sess_id]) || !isset($_SESSION[$this->sess_val])) return false;
		else if(!$this->match_table($_SESSION[$this->sess_id], '', $member_kind)) return false;
		return true;
	}

	//�α����� ȸ���� ���̵� ����
	function get_id(){
		return $_SESSION[$this->sess_id];
	}

	//�α����� ȸ���� �̸��� ����
	function get_name($uid=''){
		$uid = $uid ? $uid : $this->get_id();
		return empty($uid) ? false : $this->queryR("select name from $this->member_table where uid='$uid'");
	}

	//�α����� ȸ���� ���ȸ������ ����ȸ������ �����ϴ� ���� ����
	function get_value(){
		return $_SESSION[$this->sess_val];
	}

	//�α����� ȸ���� �н����带 ����
	function get_passwd(){
	}

	//ȸ�� Ż�� ��û���� üũ - Ż���û�� : 'uid' ����
	function is_secession($uid){
		return $this->queryR("select uid from $this->member_table2 where uid='$uid' and secession='yes'");
	}

	//ȸ�� ���� üũ
	function is_normalcy($uid) {
		$result = $this->queryFetch("select uid, secession from $this->member_table2 where uid='$uid'");
		return (empty($result[uid]) || $result[secession]=="yes") ? false : true;
	}

	//ȸ�� ������ ó��
	function insert_member_table($val, $val2){
		$result = $this->query("insert into $this->member_table set $val");
		$result2 = $this->query("insert into $this->member_table2 set $val2");
		return (!empty($result) && !empty($result2));
	}

	//ȸ�� ���� ������ ó��
	function modify_member_table($uid, $val='', $val2=''){
		if($val) $result = $this->query("update $this->member_table set $val where uid='$uid'");
		if($val2) $result2 = $this->query("update $this->member_table2 set $val2 where uid='$uid'");
		return true;
	}

	//ȸ�� Ż�� ó��
	function delete_member($uid){
		$result=$this->delete_basic_member($uid);
		if($result)
			$last_result=$this->delete_extend_member($uid);
		return $last_result;
	}
	//�⺻ȸ�����̺��� ����
	function delete_basic_member($uid){
		return $this->query("delete from $this->member_table where uid = '$uid'");
	}
	//Ȯ��ȸ�����̺��� ����
	function delete_extend_member($uid){
		return $this->query("delete from $this->member_table2 where uid = '$uid'");
	}

	//ȸ�� ���̺��� ���̵�, �н����尡 ��ġ�ϴ��� �˻� - 2008.09.18 ����
	function match_table($id, $pw='', $kind='') {
		if(!empty($pw)) $addWhere = " and $this->member_passwd=password('$pw')";
		if(!empty($kind)) $addWhere .= " and kind='$kind'";
		return $this->queryR("select count(*) from $this->member_table where $this->member_id='$id'$addWhere");
	}

	// ȸ�� �����ڵ� Ȯ�� - �ߺ����� ����
	//@return : boolean ( true : �ߺ�, false : ���ߺ� )
	function check_di_code($di_code) { // $di_code : string(64) or string(32)
		if(!$di_code) return false;
		$rows = $this->queryFetch("select di_code from $this->member_table2 where di_code='$di_code'");
		return ($rows['di_code'] && $rows['di_code']===$di_code);
	}

	//ȸ������Ʈ�� ����
	function get_member_list($nums,$block,$add_que='') {
		$que="select * from $this->member_table as m1, $this->member_table2 as m2 where m1.uid=m2.uid $add_que order by m1.no desc";
		$list=$this->queryFetchRows($que,$nums,$block);
		if($add_que)
			$add_que=' where 1 '.$add_que;
		$total=$this->get_member_count($add_que);
		$paging=$this->paging($total,$nums,$block);
		return array($list,$paging);
	}

	//��ü ȸ���� ���� ����
	function get_member_count($add_que='') {
		if($add_que)
			$add_que='where 1 '.$add_que;
		return $this->queryR("select count(*) from $this->member_table $add_que");
	}

	// ���ϱ��� ȸ�� ���� ��ȯ - �����̰���� �Է��� ��¥�� ������ ������� ����
	function get_age($birthday, $full_age=false) { // $birthday : '2008-10-30',  $full_age : ������ ��ȯ ����
		$now_infos = getdate();
		$age_infos = getdate(strtotime($birthday));
		$age = $now_infos['year'] - $age_infos['year'] + 1; // ��
		if($full_age===true && $now_infos['yday'] < $age_infos['yday']) $age--; // ��
		return $age;
	}

	// ȸ�� ����Ʈ ��ȯ - 2009.09.25 - remodeling
	function print_member_contents($bind_entry, $limits=15) {
		// �˻��Ⱓ
		if($_GET['use_date']=="on") {
			if($_GET['sdate']) {
				if($_GET['edate']) $addWhere .= " and m1.join_time>='$_GET[sdate] 00:00:00' and m1.join_time<='$_GET[edate] 23:59:59'";
				else $addWhere .= " and m1.join_time>='$_GET[sdate] 00:00:00'";
			}
			else if($_GET['edate']) $addWhere .= " and m1.join_time<='$_GET[edate] 23:59:59'";
		}
		// �˻���
		if($_GET['skey'] && $_GET['smode']) {
			switch($_GET['smode']) {
				case "name": case "uid": $addWhere .= " and m1.$_GET[smode] like '%$_GET[skey]%'"; break;
				case "phone": case "hphone": case "email":  $addWhere .= " and m2.$_GET[smode] like '%$_GET[skey]%'"; break;
				case "address": $addWhere .= " and (m2.address1 like '%$_GET[skey]%' or m2.address2 like '%$_GET[skey]%')"; break;
			}
		}
		if($_GET['slevel']) $addWhere .= " and m2.level=$_GET[slevel]";

		$kind = $_GET['skind'] ? $_GET['skind'] : $_GET['pkind'];
		switch($kind) {
			case "all": $addWhere .= " and m2.secession='no'"; break; // ��üȸ��
			case "personal": $addWhere .= " and m1.kind='personal' and m2.secession='no'"; break; // ����ȸ��
			case "secession": $addWhere .= " and m2.secession='yes'"; break; // Ż���ûȸ��
		}
		$que_point = $this->get_query_point($_GET['page'], $limits);
		$today = $this->queryRows("select no from $this->member_table where date_format(join_time, '%Y-%m-%d')=curdate()");
		$totals = $this->queryRows("select m1.no from $this->member_table as m1, $this->member_table2 as m2 where m1.uid=m2.uid$addWhere");
		$datas = $this->query("select * from $this->member_table as m1, $this->member_table2 as m2 where m1.uid=m2.uid$addWhere order by m1.no desc limit $que_point, $limits");
		list($titlebars, $contents) = fetch_contents($datas, $bind_entry, array($this, '_m149'));
		return array($totals, $today, $titlebars, $contents, parent::print_paging($totals, $limits), $addWhere);
	}
	function _m149($bind) {
		global $config_info;
		extract($bind);
		$rows['join_time'] = array_shift(explode(' ', $rows['join_time']));
		if($rows['memo']) $rows['on_memo'] = $on_memo;
		$rows['send_sms'] = (strlen($rows['hphone'])>10) ? "rankup_member.send_sms('$rows[uid]')" : "alerts('�޴���ȭ ������ �Էµ��� �ʾ� SMS�� �߼��� �� �����ϴ�.')\" style=\"color:#acacac";
		switch($_GET['pkind']) {
			case 'all':
			case 'personal':
			case 'secession':
				$rows['level'] = $config_info['smlevel'][$rows['level']];
				$rows['secession_join_time'] = array_shift(explode(' ', $rows['secession_join_time']));
				break;
		}
		$rows['kind'] = $this->member_kinds[$rows['kind']];
		return array($rows, $skin);
	}

	// ��� ���� ����
	function delete_member_services($uids) {

	}

	// ��� ������ ����
	function delete_member_contents($uids) {

	}

	//ȸ������ XML��ȯ
	function member_list($datas, $entry) {
		if(!empty($datas['skey']) && !empty($datas['smode'])) {
			switch($datas['smode']) {
				case "name": case "uid": $addWhere .= " and m1.$datas[smode] like '%$datas[skey]%'"; break;
				case "hphone": case "phone": $addWhere .= " and m2.$datas[smode] like '%$datas[skey]%'"; break;
				case "address": $addWhere .= " and (m2.address1 like '%$datas[skey]%' or m2.address2 like '%$datas[skey]%')"; break;
			}
		}
		$datas = $this->query("select * from $this->member_table as m1 LEFT OUTER JOIN $this->member_table2 as m2 ON m1.uid = m2.uid where m2.secession='no'$addWhere ");
		return fetch_contents($datas, $entry, array($this, '_xml2'));
	}
	function _xml2($bind) {
		extract($bind);
		$rows['address'] = $rows['address1'];
		return array($rows, $skin);
	}
	//Ư�� ���� ����
	//�ʵ尡 �迭�� �Ѿ� �ü��� �ֽ�.
	//�ʵ尪 ��ü�� ������� ��� �ʵ带 ����
	function get_member_basic($field='',$uid){
		if(!$uid) return false;
		if(is_array($field)) :
			$add_que=implode(',',$field);
			$last_que="select $add_que from $this->member_table where uid='$uid'";
			return $this->queryFetch($last_que);
		elseif($field) :
			$add_que=$field;
			return $this->queryR("select $add_que from $this->member_table where uid='$uid'");
		else :
			$add_que=' * ';
			return $this->queryFetch("select $add_que from $this->member_table where uid='$uid'");
		endif;
	}
	// ���� ����ϴ� �ʵ尪 ����
	function get_member_often($uid='') {
		$uid = $uid ? $uid : $this->get_id();
		if(!$uid) return false;
		$member_info = $this->queryFetch("select m1.uid, m1.name, m1.kind, date_format(m1.join_time, '%Y-%m-%d') as join_time, m2.nickname, m2.level, m2.sms, m2.mailing, m2.phone, m2.hphone, m2.email, m2.zipcode, m2.address1, m2.address2, m2.introduce, m2.prev_login_infos from $this->member_table as m1, $this->member_table2 as m2 where m1.uid=m2.uid and m1.uid='$uid'");
		$member_info['original_photo'] = $member_info['photo'];
		return $member_info;
	}

	// ȸ���� ���� ������� ����
	function get_member_info($uid='') {
		$uid = $uid ? $uid : $this->get_id();
		if(!$uid) return false;
		$member_info = $this->queryFetch("select * from $this->member_table as m1, $this->member_table2 as m2 where m2.uid=m1.uid and m1.uid='$uid'");
		$member_info['original_photo'] = $member_info['photo'];
		return $member_info;
	}

	// ȸ���� ���� ������� ����
	function get_member_info_extend($email, $phone) {
		if(!$email && !$phone) return false;
		$member_id = $this->queryR("select m1.uid from $this->member_table as m1, $this->member_table2 as m2 where m2.uid=m1.uid and (m2.email='$email' || m2.hphone='$phone')");
		return ($member_id)?$member_id:false;
	}


	// Ư�� ���� ����
	function set_member_basic($field,$value,$uid){
		if(!$uid) return false;
		return $this->query("update $this->member_table set $field='$value' where uid = '$uid'");
	}

	// ���ϸ��� ���� ���� (��� extend table)
	function set_member_mileage($uid, $mileage, $ver="+"){
		if(!$uid) return false;
		return $this->query("update $this->member_table2 set mileage=mileage$ver($mileage) where uid = '$uid'");
	}

	// ���� �˻� ������ ��ȯ - 2008.10.21 ����
	function make_mail_que($datas) {
		$stacks = array();

		switch($datas['stable']) {
			// ����ȸ��
			case 'reserve':
				include_once '../../pension/class/reserve.class.php';
				$reserve = new rankup_reserve;

				if($datas['use_date']=='on') {
					$stacks[] = "join_time>='$datas[sdate] 00:00:00'";
					$stacks[] = "join_time<='$datas[edate] 23:59:59'";
				}
				if($datas['smode'] && $datas['skey']) { // �˻���
					switch($datas['smode']) {
						case 'uid': $stacks[] = "uid like '%$datas[skey]%'"; break;
						case 'name': $stacks[] = "uname like '%$datas[skey]%'"; break;
						case 'email': $stacks[] = "uemail like '%$datas[skey]%'"; break;
					}
				}
				if($datas['duplicate_chk']=='on') $addGroupby = " group by uemail"; // �̸����ߺ�üũ

				if(count($stacks)) $addWhere = ' where '.implode(' and ', $stacks);
				$query = "select uid, uname as name, uemail as email from $reserve->reserve_table where (status!='cancel' and status!='pre')".$addWhere.$addGroupby;
				break;

			// ����ȸ��
			case 'member': default:
				if($datas['use_date']=='on') {
					$stacks[] = "me.join_time>='$datas[sdate] 00:00:00'";
					$stacks[] = "me.join_time<='$datas[edate] 23:59:59'";
				}
				if($datas['slevel']) $stacks[] = "me.level=$datas[slevel]"; // ȸ�����
				if($datas['smode'] && $datas['skey']) { // �˻���
					switch($datas['smode']) {
						case 'uid': $stacks[] = "m.uid like '%$datas[skey]%'"; break;
						case 'name': $stacks[] = "m.name like '%$datas[skey]%'"; break;
						case 'email': $stacks[] = "me.email like '%$datas[skey]%'"; break;
					}
				}
				if($datas['mailing']=='no') $stacks[] = "me.mailing='yes'"; // ���Űź� ����
				if($datas['duplicate_chk']=='on') $addGroupby = " group by me.email"; // �̸����ߺ�üũ

				if(count($stacks)) $addWhere = ' and '.implode(' and ', $stacks);
				$query = "select m.uid, m.name, me.email from $this->member_table as m, $this->member_table2 as me where me.uid=m.uid and me.secession='no'".$addWhere.$addGroupby;
				break;
		}
		return $query;
	}

	// ���� �ٿ�ε�
	function download_member_datas($data) {
		global $config_info;
		ini_set('memory_limit', '80M');
		$query = "select * from `rankup_member` as m1, `rankup_member_extend` as m2 where m1.`uid`=m2.`uid` and (m1.uid REGEXP '='!=1) $_GET[where] order by m1.`no` desc";
		$ExcelData = array();
		$datas = $this->query($query);
		if($this->chkRes($datas)) {
			while($rows = $this->fetch($datas)) {
				$mailing = $rows['mailing'] =="no" ? "���ž���" : "������";
				$sms = $rows['sms'] == "no" ? "���ž���" : "������";
				if($rows['kind']=='personal') {
					$ExcelData[] = array(
						"���̵�" => $rows['uid'],
						"�н�����" => $rows['passwd'],
						"�̸�" => $rows['name'],
						"�г���" => $rows['nickname'],
						"���" => $config_info['smlevel'][$rows['level']],
						"�̸���" => $rows['email'],
						"�ڵ���" => $rows['hphone'],
						"��ȭ��ȣ" => $rows['phone'],
						"�����ȣ" => $rows['zipcode'],
						"�ּ�" => $rows['address1'],
						"�������ּ�" => $rows['address2'],
						"�湮Ƚ��" => $rows['visit'],
						"�̸��ϼ���" => $mailing,
						"SMS����" => $sms,
						"������" => $rows['join_time']
					);
				}
			}
		}
		else {
			$this->popup_msg_js("ȸ���� �������� �ʽ��ϴ�.", "VOID");
		}
		$excel = new Sql2Excel("ȸ������Ʈ");
		$excel->ExcelOutputData($ExcelData);
		unset($ExcelData);
	}
}
?>