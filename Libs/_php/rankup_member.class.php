<?php
##멤버를 처리하기 위한 클래스
class rankup_member extends rankup_util {
	var $sess_id = 'niceId';	//세션 아이디 값
	var $sess_val = 'niceVal';	//세션 구별값
	var $member_table='rankup_member';
	var $member_id = 'uid';	//멤버 아이디 필드
	var $member_passwd = 'passwd';	//멤버 비밀번호 필드
	var $member_kind = 'kind';	 //멤버 구별 필드
	var $member_table2='rankup_member_extend';	//확장 멤버 테이블
	var $log_table = 'rankup_member_log'; // 회원로그인 로그 테이블
	var $vsms_log_table = 'rankup_member_vsms_log'; // 인증번호 발송 로그 테이블
	var $vsms_send_limits = 5; // 1일 5회 제한

	var $join_level = 6; // 회원 가입시 등급
	var $lowest_level = 7; // 회원 최하위 등급 - 비회원
	var $stop_level = 8; // 정지회원 (미사용)

	var $base_dir = '';
	var $base_url = '';
	// 회원 분류
	var $member_kinds = array(
		'personal' => '개인회원',
		'company' => '기업회원'
	);
	// 회원 유형
	var $member_types = array(
		'14over' => '일반회원(만14세 이상 내국인)',
		'14under' => '만14세 미만 회원(만14세 미만 내국인)',
		'inforeign' => '국내 외국인 회원',
		'outforeign' => '해외 외국인 회원',
	);
	var $genders = array(
		'1' => '남성',
		'2' => '여성'
	);
	var $form_options = array();

	function rankup_member() {
		parent::rankup_util();
		$this->base_url = rankup_basic::base_url();
		$this->base_dir = rankup_basic::base_dir();

		// 회원 가입폼 옵션
		$this->form_options = array(
			array('key' => 'nickname', 'name' => '닉네임'),
			array('key' => 'phone', 'name' => '일반전화'),
			array('key' => 'address', 'name' => '주소'),
			array('key' => 'introduce', 'name' => '가입인사'),
			array('key' => 'hphone', 'name' => '휴대전화'),
			array('key' => 'hphone_verify', 'name' => '휴대폰인증'),
		);
	}

	// 비밀번호 반환 - 2011.09.01 added
	function get_password($password) {
		$half = strlen($password)/2;
		return substr($password, 0, -(floor($half))).str_repeat('*', ceil($half));
	}

	// 회원가입홈 반환
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

	// 회원 임시비밀번호 반환
	function get_new_passwd($uid) {
		$new_passwd = preg_replace('/[^a-cA-C\d]/', '', parent::encode($uid.time(), 2));
		$this->query("UPDATE $this->member_table SET passwd=password('$new_passwd') WHERE uid='$uid'");
		return $new_passwd;
	}

	//로그인시 세션을 발급
	function set_member_session($id,$val){
		$_SESSION[$this->sess_id] = $id;
		$_SESSION[$this->sess_val] = $val;

		// 로그인정보 및 방문수 갱신
		$rows = $this->queryFetch("SELECT login_infos, prev_login_infos FROM $this->member_table2 WHERE uid='$id'");
		$_vals['prev_login_infos'] = $rows['login_infos'];
		$_vals['login_infos'] = serialize(array(
			'login_ip' => $_SERVER['REMOTE_ADDR'],
			'login_time' => date('Y-m-d H:i:s')
		));
		$values = $this->change_query_string($_vals);
		$this->query("UPDATE $this->member_table2 SET visit=visit+1, ".$values." WHERE uid='$id'"); // 방문 수 카운트
		$this->save_log($id);
	}

	// 회원로그인 기록 - 2012.01.02 added
	function save_log($login_id) {
		$_vals['member_id'] = $login_id;
		$_vals['login_date'] = date('Y-m-d H:i:s');
		$_vals['login_ip_addr'] = $_SERVER['REMOTE_ADDR'];
		$values = $this->change_query_string($_vals);
		$this->query("INSERT INTO $this->log_table SET $values");
	}

	// 회원로그인 기록 반환 - 2012.01.02 added
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

	// 3개월전 로그 삭제
	function del_log() {
		$this->query("DELETE FROM $this->log_table WHERE login_date<date_sub(now(), interval 3 month)");
	}
	// 로그초기화
	function reset_log() {
		$this->query("TRUNCATE TABLE $this->log_table");
	}

	// 인증번호를 전송가능한지 반환 - 2012.03.08 added
	function sendable_vsms($hphone) {
		$counts = $this->queryR("SELECT count(hphone) FROM $this->vsms_log_table WHERE hphone='$hphone' AND date_format(send_time, '%Y-%m-%d')=curdate()");
		return ($counts>=$this->vsms_send_limits);
	}

	// 인증번호 발송 기록 - 2012.03.08 added
	function save_vsms_log($hphone, $vnumber) {
		$_vals['hphone'] = $hphone;
		$_vals['vnumber'] = $vnumber;
		$_vals['send_time'] = date('Y-m-d H:i:s');
		$_vals['ip_addr'] = $_SERVER['REMOTE_ADDR'];
		$values = $this->change_query_string($_vals);
		$this->query("INSERT INTO $this->vsms_log_table SET $values");
	}

	// 인증번호 발송 기록 반환 - 2012.03.08 added
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

	// 선택한 인증번호로그 삭제
	function del_vsms() {
		$nos = str_replace('__', ',', $_POST['nos']);
		$this->query("DELETE FROM $this->vsms_log_table WHERE no in($nos)");
	}
	// 3개월전 인증번호로그 삭제
	function del_vsms_log() {
		$this->query("DELETE FROM $this->vsms_log_table WHERE send_time<date_sub(now(), interval 3 month)");
	}
	// 인증번호로그 초기화
	function reset_vsms_log() {
		$this->query("TRUNCATE TABLE $this->vsms_log_table");
	}

	//로그인한 회원의 세션을 제거
	function delete_member_session(){
		unset($_SESSION[$this->sess_id], $_SESSION[$this->sess_val]);
	}

	//id, passwd를 멤버 테이블과 비교
	function compare_member_table($id,$passwd){
	}

	//발급한 세션값이 실제로 존재 하는지 체크
	function check_session_value(){
	}

	//로그인 했는지 체크 - 2008.09.18 수정
	function is_member($member_kind='') {
		if(!isset($_SESSION[$this->sess_id]) || !isset($_SESSION[$this->sess_val])) return false;
		else if(!$this->match_table($_SESSION[$this->sess_id], '', $member_kind)) return false;
		return true;
	}

	//로그인한 회원의 아이디를 리턴
	function get_id(){
		return $_SESSION[$this->sess_id];
	}

	//로그인한 회원의 이름을 리턴
	function get_name($uid=''){
		$uid = $uid ? $uid : $this->get_id();
		return empty($uid) ? false : $this->queryR("select name from $this->member_table where uid='$uid'");
	}

	//로그인한 회원이 기업회원인지 개인회원인지 구별하는 값을 리턴
	function get_value(){
		return $_SESSION[$this->sess_val];
	}

	//로그인한 회원의 패스워드를 리턴
	function get_passwd(){
	}

	//회원 탈퇴 신청상태 체크 - 탈퇴신청시 : 'uid' 리턴
	function is_secession($uid){
		return $this->queryR("select uid from $this->member_table2 where uid='$uid' and secession='yes'");
	}

	//회원 상태 체크
	function is_normalcy($uid) {
		$result = $this->queryFetch("select uid, secession from $this->member_table2 where uid='$uid'");
		return (empty($result[uid]) || $result[secession]=="yes") ? false : true;
	}

	//회원 가입을 처리
	function insert_member_table($val, $val2){
		$result = $this->query("insert into $this->member_table set $val");
		$result2 = $this->query("insert into $this->member_table2 set $val2");
		return (!empty($result) && !empty($result2));
	}

	//회원 정보 수정을 처리
	function modify_member_table($uid, $val='', $val2=''){
		if($val) $result = $this->query("update $this->member_table set $val where uid='$uid'");
		if($val2) $result2 = $this->query("update $this->member_table2 set $val2 where uid='$uid'");
		return true;
	}

	//회원 탈퇴를 처리
	function delete_member($uid){
		$result=$this->delete_basic_member($uid);
		if($result)
			$last_result=$this->delete_extend_member($uid);
		return $last_result;
	}
	//기본회원테이블에서 삭제
	function delete_basic_member($uid){
		return $this->query("delete from $this->member_table where uid = '$uid'");
	}
	//확장회원테이블에서 삭제
	function delete_extend_member($uid){
		return $this->query("delete from $this->member_table2 where uid = '$uid'");
	}

	//회원 테이블에서 아이디, 패스워드가 일치하는지 검사 - 2008.09.18 수정
	function match_table($id, $pw='', $kind='') {
		if(!empty($pw)) $addWhere = " and $this->member_passwd=password('$pw')";
		if(!empty($kind)) $addWhere .= " and kind='$kind'";
		return $this->queryR("select count(*) from $this->member_table where $this->member_id='$id'$addWhere");
	}

	// 회원 고유코드 확인 - 중복가입 방지
	//@return : boolean ( true : 중복, false : 비중복 )
	function check_di_code($di_code) { // $di_code : string(64) or string(32)
		if(!$di_code) return false;
		$rows = $this->queryFetch("select di_code from $this->member_table2 where di_code='$di_code'");
		return ($rows['di_code'] && $rows['di_code']===$di_code);
	}

	//회원리스트를 추출
	function get_member_list($nums,$block,$add_que='') {
		$que="select * from $this->member_table as m1, $this->member_table2 as m2 where m1.uid=m2.uid $add_que order by m1.no desc";
		$list=$this->queryFetchRows($que,$nums,$block);
		if($add_que)
			$add_que=' where 1 '.$add_que;
		$total=$this->get_member_count($add_que);
		$paging=$this->paging($total,$nums,$block);
		return array($list,$paging);
	}

	//전체 회원의 수를 리턴
	function get_member_count($add_que='') {
		if($add_que)
			$add_que='where 1 '.$add_que;
		return $this->queryR("select count(*) from $this->member_table $add_que");
	}

	// 생일기준 회원 나이 반환 - 만나이계산은 입력한 날짜를 무조건 양력으로 간주
	function get_age($birthday, $full_age=false) { // $birthday : '2008-10-30',  $full_age : 만나이 반환 여부
		$now_infos = getdate();
		$age_infos = getdate(strtotime($birthday));
		$age = $now_infos['year'] - $age_infos['year'] + 1; // 살
		if($full_age===true && $now_infos['yday'] < $age_infos['yday']) $age--; // 세
		return $age;
	}

	// 회원 리스트 반환 - 2009.09.25 - remodeling
	function print_member_contents($bind_entry, $limits=15) {
		// 검색기간
		if($_GET['use_date']=="on") {
			if($_GET['sdate']) {
				if($_GET['edate']) $addWhere .= " and m1.join_time>='$_GET[sdate] 00:00:00' and m1.join_time<='$_GET[edate] 23:59:59'";
				else $addWhere .= " and m1.join_time>='$_GET[sdate] 00:00:00'";
			}
			else if($_GET['edate']) $addWhere .= " and m1.join_time<='$_GET[edate] 23:59:59'";
		}
		// 검색어
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
			case "all": $addWhere .= " and m2.secession='no'"; break; // 전체회원
			case "personal": $addWhere .= " and m1.kind='personal' and m2.secession='no'"; break; // 개인회원
			case "secession": $addWhere .= " and m2.secession='yes'"; break; // 탈퇴신청회원
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
		$rows['send_sms'] = (strlen($rows['hphone'])>10) ? "rankup_member.send_sms('$rows[uid]')" : "alerts('휴대전화 정보가 입력되지 않아 SMS를 발송할 수 없습니다.')\" style=\"color:#acacac";
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

	// 등록 서비스 삭제
	function delete_member_services($uids) {

	}

	// 등록 컨텐츠 삭제
	function delete_member_contents($uids) {

	}

	//회원정보 XML반환
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
	//특정 값을 리턴
	//필드가 배열로 넘어 올수도 있슴.
	//필드값 자체가 없을경우 모든 필드를 리턴
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
	// 자주 사용하는 필드값 리턴
	function get_member_often($uid='') {
		$uid = $uid ? $uid : $this->get_id();
		if(!$uid) return false;
		$member_info = $this->queryFetch("select m1.uid, m1.name, m1.kind, date_format(m1.join_time, '%Y-%m-%d') as join_time, m2.nickname, m2.level, m2.sms, m2.mailing, m2.phone, m2.hphone, m2.email, m2.zipcode, m2.address1, m2.address2, m2.introduce, m2.prev_login_infos from $this->member_table as m1, $this->member_table2 as m2 where m1.uid=m2.uid and m1.uid='$uid'");
		$member_info['original_photo'] = $member_info['photo'];
		return $member_info;
	}

	// 회원이 갖는 모든정보 리턴
	function get_member_info($uid='') {
		$uid = $uid ? $uid : $this->get_id();
		if(!$uid) return false;
		$member_info = $this->queryFetch("select * from $this->member_table as m1, $this->member_table2 as m2 where m2.uid=m1.uid and m1.uid='$uid'");
		$member_info['original_photo'] = $member_info['photo'];
		return $member_info;
	}

	// 회원이 갖는 모든정보 리턴
	function get_member_info_extend($email, $phone) {
		if(!$email && !$phone) return false;
		$member_id = $this->queryR("select m1.uid from $this->member_table as m1, $this->member_table2 as m2 where m2.uid=m1.uid and (m2.email='$email' || m2.hphone='$phone')");
		return ($member_id)?$member_id:false;
	}


	// 특정 값을 변경
	function set_member_basic($field,$value,$uid){
		if(!$uid) return false;
		return $this->query("update $this->member_table set $field='$value' where uid = '$uid'");
	}

	// 마일리지 값을 변경 (멤버 extend table)
	function set_member_mileage($uid, $mileage, $ver="+"){
		if(!$uid) return false;
		return $this->query("update $this->member_table2 set mileage=mileage$ver($mileage) where uid = '$uid'");
	}

	// 메일 검색 쿼리문 반환 - 2008.10.21 수정
	function make_mail_que($datas) {
		$stacks = array();

		switch($datas['stable']) {
			// 예약회원
			case 'reserve':
				include_once '../../pension/class/reserve.class.php';
				$reserve = new rankup_reserve;

				if($datas['use_date']=='on') {
					$stacks[] = "join_time>='$datas[sdate] 00:00:00'";
					$stacks[] = "join_time<='$datas[edate] 23:59:59'";
				}
				if($datas['smode'] && $datas['skey']) { // 검색어
					switch($datas['smode']) {
						case 'uid': $stacks[] = "uid like '%$datas[skey]%'"; break;
						case 'name': $stacks[] = "uname like '%$datas[skey]%'"; break;
						case 'email': $stacks[] = "uemail like '%$datas[skey]%'"; break;
					}
				}
				if($datas['duplicate_chk']=='on') $addGroupby = " group by uemail"; // 이메일중복체크

				if(count($stacks)) $addWhere = ' where '.implode(' and ', $stacks);
				$query = "select uid, uname as name, uemail as email from $reserve->reserve_table where (status!='cancel' and status!='pre')".$addWhere.$addGroupby;
				break;

			// 가입회원
			case 'member': default:
				if($datas['use_date']=='on') {
					$stacks[] = "me.join_time>='$datas[sdate] 00:00:00'";
					$stacks[] = "me.join_time<='$datas[edate] 23:59:59'";
				}
				if($datas['slevel']) $stacks[] = "me.level=$datas[slevel]"; // 회원등급
				if($datas['smode'] && $datas['skey']) { // 검색어
					switch($datas['smode']) {
						case 'uid': $stacks[] = "m.uid like '%$datas[skey]%'"; break;
						case 'name': $stacks[] = "m.name like '%$datas[skey]%'"; break;
						case 'email': $stacks[] = "me.email like '%$datas[skey]%'"; break;
					}
				}
				if($datas['mailing']=='no') $stacks[] = "me.mailing='yes'"; // 수신거부 제외
				if($datas['duplicate_chk']=='on') $addGroupby = " group by me.email"; // 이메일중복체크

				if(count($stacks)) $addWhere = ' and '.implode(' and ', $stacks);
				$query = "select m.uid, m.name, me.email from $this->member_table as m, $this->member_table2 as me where me.uid=m.uid and me.secession='no'".$addWhere.$addGroupby;
				break;
		}
		return $query;
	}

	// 엑셀 다운로드
	function download_member_datas($data) {
		global $config_info;
		ini_set('memory_limit', '80M');
		$query = "select * from `rankup_member` as m1, `rankup_member_extend` as m2 where m1.`uid`=m2.`uid` and (m1.uid REGEXP '='!=1) $_GET[where] order by m1.`no` desc";
		$ExcelData = array();
		$datas = $this->query($query);
		if($this->chkRes($datas)) {
			while($rows = $this->fetch($datas)) {
				$mailing = $rows['mailing'] =="no" ? "수신암함" : "수신함";
				$sms = $rows['sms'] == "no" ? "수신안함" : "수신함";
				if($rows['kind']=='personal') {
					$ExcelData[] = array(
						"아이디" => $rows['uid'],
						"패스워드" => $rows['passwd'],
						"이름" => $rows['name'],
						"닉네임" => $rows['nickname'],
						"등급" => $config_info['smlevel'][$rows['level']],
						"이메일" => $rows['email'],
						"핸드폰" => $rows['hphone'],
						"전화번호" => $rows['phone'],
						"우편번호" => $rows['zipcode'],
						"주소" => $rows['address1'],
						"나머지주소" => $rows['address2'],
						"방문횟수" => $rows['visit'],
						"이메일수신" => $mailing,
						"SMS수신" => $sms,
						"가입일" => $rows['join_time']
					);
				}
			}
		}
		else {
			$this->popup_msg_js("회원이 존재하지 않습니다.", "VOID");
		}
		$excel = new Sql2Excel("회원리스트");
		$excel->ExcelOutputData($ExcelData);
		unset($ExcelData);
	}
}
?>