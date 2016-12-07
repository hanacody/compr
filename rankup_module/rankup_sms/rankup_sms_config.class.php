<?php
class rankup_sms_config{
	var $table = "rankup_sms_config";	// 테이블
	var $info = null;
	var $iframe_domain = "http://sms.rankup.co.kr/member_asp/reply_client.php";	//값을 전송할 도메인

	// 기본적으로 제공되는 메뉴
	var $setting = array(
		"sms_join"=>array("title"=>"회원 가입시","memo"=>"사이트내에서 일반회원가입시 발송되는 메시지", "message"=>"{이름}님 회원가입을 진심으로 축하합니다.","use"=>"yes"),
		"sms_memberUpd"=>array("title"=>"회원정보 수정시", "memo"=>"일반회원정보를 수정했을경우", "message"=>"{이름}님의 정보가 올바르게 수정되었습니다.", "use"=>"yes")
	);
	var $merge_list = array(
		"domain"=>array("{도메인}", "사이트의 도메인명이 출력됩니다.<br>예)rankup.co.kr"),
		"agency"=>array("{업체}", "업체 이름이 출력됩니다.<br>예)랭크업"),
		"date"=>array("{날짜}","오늘 날짜가 출력됩니다.<br>예)12월 25일"),
		"id"=>array("{아이디}", "회원 아이디가 출력됩니다.<br>예)rankup"),
		"name"=>array("{이름}", "회원 이름이 출력됩니다.<br>예)랭크업"),
		"account"=>array("{계좌번호}", "온라인입금시 고객이 선택한 계좌번호가 출력됩니다."),
		"bank"=>array("{은행}", "온라인입금시 고객이 선택한 은행명이 출력됩니다."),
		"bankman"=>array("{예금주}", "온라인입금시 고객이 선택한 예금주가 출력됩니다."),
		"agency"=>array("{업체}", "업체 이름이 출력됩니다."),
		"order_date"=>array("{예약일}", "예약일이 출력됩니다."),
		"order_name"=>array("{예약자}", "예약자 이름이 출력됩니다."),
		"coupon_code"=>array("{예약번호}", "예약번호가 출력됩니다."),
		"money"=>array("{금액}", "금액이 출력됩니다."),
		"goods"=>array("{상품명}", "상품명이 출력됩니다.")
	);
	var $merge_list_request = array(
		"domain"=>array("{도메인}", "사이트의 도메인명이 출력됩니다.<br>예)rankup.co.kr"),
		"date"=>array("{날짜}","오늘 날짜가 출력됩니다.<br>예)12월 25일"),
		"agency"=>array("{업체}", "업체 이름이 출력됩니다.<br>예)랭크업")
	);

	function rankup_sms_config($type='') {

		######################관리자 인증을 테스트
		#$type=='user' 인경우 회원인증
		#값이 없거나 $type=='admin' 인 경우 관리자 인증
		##########################################
		/*
		if($type=='user'){
			$uid = $_SESSION['niceId'];
			$group = $_SESSION['groupVal'];

			//회원 가입부분은 따로 처리 해야 합니다.

			//if(!$uid || !$group)
				//echo "<script>alert('회원 전용 페이지 입니다.');top.location.replace('../index.html');</script>";
		} else if($type=='admin' || !$type){
			if(!isset($_SESSION['AdminId']) || !isset($_SESSION['Adminpw']) || empty($_SESSION['AdminId']) || empty($_SESSION['Adminpw']))	{
				echo "<script>alert('관리자만 접근이 허용된 페이지 입니다.');top.location.replace('../index.html');</script>";
				exit;
			}

			if($_SESSION['Administrator'] != 'Administrator')	{
				echo "<script>alert('관리자만 접근이 허용된 페이지 입니다.');top.location.replace('../index.html');</script>";
				exit;
			}
		}
		*/
		##################################################3
		#관리자 및 사용자 인증 부분 처리
		###################################################

		$this->domain = $_SERVER['HTTP_HOST'];

		//테이블 유무
		$nums = mysql_fetch_row(mysql_query("show tables like '$this->table'"));
		if(!$nums[0]) {
			foreach($this->setting as $key=>$vals) {
				$add_que .= "
			  `$key` enum('yes','no') NOT NULL default '$vals[use]',
			  `{$key}_msg` varchar(100) default NULL,";
			}
			mysql_query("
			CREATE TABLE if not exists `rankup_sms_config` (
			  `use_sms` enum('yes','no') NOT NULL default 'no',
			  `client_id` varchar(20) default NULL,
			  `client_pw` varchar(20) default NULL,
			  `callback` varchar(20) default NULL,
			  `sleeping_mode` enum('yes','no') NOT NULL default 'yes',
			  `sleeping_mode_time` varchar(50) default '21-09',
			  $add_que
			  `send_sms` enum('yes','no') NOT NULL default 'no',
			  `send_call` varchar(20) default NULL,
			  `send_authentic` enum('no','yes') default NULL
			) TYPE=MyISAM");
		}
		//데이터가 없다면 데이터를 입력한다.
		$num = mysql_fetch_row(mysql_query("select count(*) from $this->table"));
		if(!$num[0]) $this->init('first');

		$this->info = @mysql_fetch_array(mysql_query("select * from $this->table"), MYSQL_ASSOC);
		list($this->info['sleeping_mode_start'],$this->info['sleeping_mode_end']) = explode("-",$this->info['sleeping_mode_time']); // 추가

		@mysql_free_result();
	}

	//SMS 머지값 추출 - 2008.06.10 추가
	function print_sms_merge() {
		$count = 0;
		$max_cols = 2;
		foreach($this->merge_list as $val) {
			$_merge_list .= "
			<td align='center' bgcolor='#f4f4f4' width='12%'>$val[0]</td>
			<td width='38%'>$val[1]</td>";
			if(!(++$count%$max_cols)) {
				$merge_list .= "
				<tr>$_merge_list
				</tr>";
				unset($_merge_list);
			}
		}
		if(isset($_merge_list)) {
			$_tds = str_repeat("<td bgcolor='#f4f4f4'></td><td></td>", $max_cols-($count%$max_cols));
			$merge_list .= "
			<tr>$_merge_list
			$_tds
			</tr>";
			unset($_merge_list);
		}
		return $merge_list;
	}

	//SMS 설정값 추출 - 2008.06.10 추가
	function print_sms_setting() {
		$count = 0;
		$max_cols = 2;
		foreach($this->setting as $key=>$rows) {
			$_checked = $this->info[$key]=="yes" ? " checked" : '';
			$_setting_list .= "
			<td width='50%' align='center'>
				<table width='98%' border='0' cellpadding='0' cellspacing='0'>
				<tr>
					<td width='135'>
						<table width='130' border='0' cellpadding='0' cellspacing='0'>
						<tbody align='center'>
						<tr><td><img src='./img/sms_bg_01.gif'></td></tr>
						<tr><td background='./img/sms_bg_02.gif' height='87'><textarea id='{$key}_txt' rows='5' cols='16' class='form' style='background-color:transparent;overflow:hidden;border:0;font-family:돋움체;font-size:12px;color:#111111;' onkeyup=\"rankup_util.change_word(this,'$key')\">".$this->info["{$key}_msg"]."</textarea></td></tr>
						<tr><td background='./img/sms_bg_03.gif' height='22'><span id='$key'>".strlen($this->info["{$key}_msg"])."</span>Byte / 최대 80Byte</td></tr>
						</tbody>
						</table>
					</td>
					<td>
						<table width='100%' border='0' cellpadding='5' cellspacing='1' bgcolor='#d4d4d4'>
						<tbody bgcolor='#ffffff'>
						<tr><td height='25' bgcolor='#f4f4f4'>".$this->setting[$key]['title']."</td></tr>
						<tr>
							<td>
								<table width='100%' cellpadding='0' cellspacing='0'>
								<tr>
									<td><input name='$key' id='used_id$key' type='checkbox'".$_checked."><label for='used_id$key'>사용여부</label></td>
									<td width='65' align='right'><img src='./img/btn_regist_s.gif' border='0' style='cursor:hand;' align='absmiddle' onClick=\"set_message('$key')\" alt='등록하기'></td>
								</tr>
								</table>
							</td>
						</tr>
						<tr><td height='67' valign=top>".$this->setting[$key]['memo']."</td></tr>
						</tbody>
						</table>
					</td>
				</tr>
				<tr><td height='5'></td></tr>
				</table>
			</td>";
			if(!(++$count%$max_cols)) {
				$setting_list .= "
				<tr>$_setting_list
				</tr>";
				unset($_setting_list);
			}
		}
		if(isset($_setting_list)) {
			$_tmp_table = "
				<table width='340' border='0' cellpadding='0' cellspacing='0' disabled>
				<tr>
					<td width='135' valign='top'>
						<table width='130' border='0' cellpadding='0' cellspacing='0'>
						<tbody align='center'>
						<tr><td><img src='./img/sms_bg_01.gif'></td></tr>
						<tr><td background='./img/sms_bg_02.gif' height='87'><textarea rows='5' cols='16' class='form' style='background-color:transparent;overflow:hidden;border:0;font-family:돋움체;font-size:12px;color:#111111;' readOnly></textarea></td></tr>
						<tr><td background='./img/sms_bg_03.gif' height='22'><span>0</span>Byte / 최대 80Byte</td></tr>
						</tbody>
						</table>
					</td>
					<td width='205' valign='top'>
						<table width='100%' border='0' cellpadding='5' cellspacing='1' bgcolor='#d4d4d4'>
						<tbody bgcolor='#ffffff'>
						<tr><td height='25' bgcolor='#f4f4f4'></td></tr>
						<tr>
							<td>
								<table width='100%' cellpadding='0' cellspacing='0'>
								<tr>
									<td><input disabled type='checkbox'>사용여부</td>
									<td width='65' align='right'><img src='./img/admin_regist_button.gif' border='0' align='absmiddle'></td>
								</tr>
								</table>
							</td>
						</tr>
						<tr><td height='67' valign=top></td></tr>
						</tbody>
						</table>
					</td>
				</tr>
				</table>";
			$_tds = str_repeat("<td>$_tmp_table</td>", $max_cols-($count%$max_cols));
			$setting_list .= "
			<tr>$_setting_list
			$_tds
			</tr>";
			unset($_setting_list, $_tmp_table, $_tds);
		}
		return $setting_list;
	}

	function strip_slash(&$val, $key){
		if(!is_array($val)) $val = stripslashes($val);
		else array_walk($val, array($this,"strip_slash"));
	}

	function add_slash(&$val, $key){
		$val = addslashes($val);
	}

	//정보를 변경하는 함수
	function set_sms_config($val){
		$que = "update $this->table set ";
		foreach($val as $key => $val) $que .= " $key = '$val',";
		$que = substr($que, 0, -1);
		return mysql_query($que);
	}

	//메시지및 사용여부를 초기화
	function init($mode='') {
		$que = ($mode=='first') ? "insert into $this->table set " : "update $this->table set ";
		// 기본값 세팅
		foreach($this->setting as $key=>$vals) {
			$vals['message'] = str_replace("'", "''", $vals['message']);
			$que .= "`$key` = '$vals[use]', `{$key}_msg` = '$vals[message]', ";
		}
		$que .= " send_authentic = '".$this->setting['send_authentic']['message']."' "; // 실명인증
		$result=mysql_query($que);
		if(!$result) die("Error(".mysql_errno().") : ".mysql_error());
		return true;
	}


	##################################################
	# 메일을 발송할 전체 회원 검색 부분
	#################################################

	//전체 sms 발송시, 제어 쿼리를 리턴하는 함수
	function make_list_query($info) {
		// 날짜 검색을 이용한 경우
		if($info['use_date']=='yes') {
			if($info['start']) $addWhere .= " and date_format(m.join_time, '%Y-%m-%d') >= '$info[start]'";
			if($info['end']) $addWhere .= " and date_format(m.join_time, '%Y-%m-%d') <= '$info[end]'";
		}
		if($info['slevel']) $addWhere .= " and me.level=$info[slevel]";
		// 검색어 종류
		if($info['member_type'] && $info['member_value']) {
			switch($info['member_type']) {
				case "uid": case "name":  $addWhere .= " and m.$info[member_type] like '%$info[member_value]%'"; break; // 아이디/이름
				case "email": $addWhere .= " and me.email like '%$info[member_value]%'"; break; // 이메일
				case "hphone": $addWhere .= " and me.hphone regexp '^01([-0-9*]+)$' and me.hphone like '%$info[member_value]%'"; break; // 핸드폰
			}
		}
		// 수신거부 제외
		if($info['use_sms']=="no") $addWhere .= " and me.sms='yes'";

		//리턴할 값을 가공
		$result = array();
		$rankup_member = new rankup_member;

		// 예약회원 구분
		switch($info['sale']){
			case "yes":
				include_once "../../pension/class/reserve.class.php";
				$reserve = new rankup_reserve;
				$sel = mysql_query("select m.uid, m.name, me.hphone from $rankup_member->member_table as m, $rankup_member->member_table2 as me, $reserve->reserve_table as g where me.uid=m.uid and m.uid=g.uid and (g.status != 'cancel' AND g.status != 'pre') $addWhere  group by m.uid order by m.no desc, m.join_time desc");
				break;

			default:
				$sel = mysql_query("select m.uid, m.name, me.hphone from $rankup_member->member_table as m, $rankup_member->member_table2 as me where me.uid=m.uid$addWhere order by m.no desc, m.join_time desc");
		}

		if(!$sel) die("ERROR[".mysql_errno()."] ".mysql_error());
		while($rows = mysql_fetch_array($sel, MYSQL_NUM)) {
			if(!ereg("^01[-0-9]+$", $rows[2])) continue;
			$result[] = $rows;
		}
		return $result;
	}
}
?>