<?php
## 팝업를 처리하기 위한 클래스 
class rankup_popup extends rankup_util {
	var $table = "rankup_popup"; // 팝업를 저장하는 테이블	
	var $base_url; // 상대 경로
	var $base_dir; // 절대 경로
	var $version = "v1.0 r080609";
	function rankup_popup() {
		parent::rankup_util();
		$this->base_url = rankup_basic::base_url();
		$this->base_dir = rankup_basic::base_dir();
		$this->check_table(); // 테이블 체크
	}

	// 팝업 테이블 체크
	function check_table() {
		$check_table = $this->queryR("show tables like '$this->table'");
		if($check_table===$this->table) return true;
		return $this->query("
			CREATE TABLE `$this->table` (
			  `no` int(11) unsigned NOT NULL auto_increment,
			  `rank` smallint(5) unsigned NOT NULL default '0',
			  `type` enum('text','skin1','skin2','skin3') NOT NULL default 'text',
			  `title` varchar(30) NOT NULL default '',
			  `width` smallint(5) unsigned default NULL,
			  `height` smallint(5) unsigned default NULL,
			  `width_type` enum('pixel','exact') default NULL,
			  `height_type` enum('pixel','exact') default NULL,
			  `content` mediumtext NOT NULL,
			  `use_date` enum('yes','no') NOT NULL default 'no',
			  `sdate` datetime default NULL,
			  `edate` datetime default NULL,
			  `wdate` datetime NOT NULL default '0000-00-00 00:00:00',
			  `view` enum('yes','no') NOT NULL default 'yes',
			  PRIMARY KEY  (`no`),
			  KEY `rank` (`rank`),
			  KEY `view` (`view`)
			) TYPE=MyISAM AUTO_INCREMENT=1;
		");
	}

	// 팝업등록/수정
	function regist_popup($datas, $DML="insert") { // $mode : { 'insert'  or  'update' }
		//공통 입력값 설정
		$_val['type'] = $datas['popup_type']; // 팝업스킨
		$_val['title'] = addslashes($datas['title']); // 팝업제목
		$_val['use_date'] = $datas['period']; // 노출기한 설정

		// 미디어파일 등록시
		if($datas['popup_type']=="text") {
			$_val['width_type'] = $datas['width_type']; // 가로사이즈 형태
			$_val['height_type'] = $datas['height_type']; // 세로사이즈 형태
			$_val['width'] = $datas['width_type']=="exact" ? 0 : $datas['width']; // 가로사이즈
			$_val['height'] = $datas['height_type']=="exact" ? 0 : $datas['height']; // 세로사이즈
		}
		// 직접입력시
		else {
			$_val['width'] = 0;
			$_val['height'] = 0;
			$_val['width_type'] = 'exact';
			$_val['height_type'] = 'exact';
		}
		$_val['content'] = addslashes($datas['content']); // 내용

		// 노출기한 설정시
		if($datas['period']=="yes") {
			$period_mins = array("정각"=>"00", "반"=>"30");
			if($datas['period_sdate'] && $datas['period_shour'] && $datas['period_sminute']) {
				$_val['sdate'] = "$datas[period_sdate] $datas[period_shour]:".$period_mins[$datas['period_sminute']].":00"; // 노출 시작일
			}
			if($datas['period_edate'] && $datas['period_ehour'] && $datas['period_eminute']) {
				$_val['edate'] = "$datas[period_edate] $datas[period_ehour]:".$period_mins[$datas['period_eminute']].":00"; // 노출 마감일
			}
		}
		// 쿼리 생성 / 입력
		switch(strtolower($DML)) {
			case "insert":
				$_val['rank'] = 1; // 순위
				$_val['wdate'] = date("Y-m-d H:i:s"); // 등록일
				break;
			case "update":
				if(empty($datas['no'])) return false;
				$addWhere = " where no=$datas[no]";
				break;
		}
		$values = $this->change_query_string($_val);
		$result = $this->query("$DML $this->table set $values$addWhere");
		return $result ? $DML=="insert" ? mysql_insert_id() : $datas['no'] : false;
	}

	// 팝업삭제
	function delete_popup($datas) {
		// 레코드 삭제
		$this->query("delete from $this->table where no in(".str_replace("__", ",", $datas).")");
		$result = mysql_affected_rows() ? true : false;

		// 테이블 최적화
		if($result) $this->query("optimize table $this->table");
		return $result;
	}

	// 팝업정보 XML로 퍼블리쉬 - 사용자페이지에서 사용
	function get_xml_formalize($datas) {
		foreach($datas as $rows) {
			$items .= "
			<item type='$rows[type]' no='$rows[no]'>
				<title><![CDATA[".$rows['title']."]]></title>
				<top><![CDATA[0]]></top>
				<left><![CDATA[0]]></left>
				<width type='$rows[width_type]'><![CDATA[".$rows['width']."]]></width>
				<height type='$rows[height_type]'><![CDATA[".$rows['height']."]]></height>
				<content><![CDATA[".$rows['content']."]]></content>
			</item>";
		}
		return $items;
	}

	// 팝업정보 HTML로 퍼블리쉬 - 관리자페이지에서 사용
	function get_html_formalize($datas, $single_mode=false) {

		$views = array("yes"=>"use", "no"=>"unused");
		$targets = array("_self"=>"selfwin", "_blank"=>"newwin");

		// 조작버튼 설정 - 공통
		$control_buttons[0] = "<img src='./img/order_high.gif' onClick=\"classObj.set_direction(this, 'up')\" style='margin:1px'><img src='./img/order_low.gif' onClick=\"classObj.set_direction(this, 'down')\" style='margin:1px'>"; // 위로 | 아래로
		$control_buttons[1] = "<img src='./img/bt_{:view:}.gif' value='{:view:}' onClick=\"classObj.ajax_process('view')\">"; // 사용 | 미사용
		$control_buttons[2] = "<a onClick=\"classObj.preview_popup()\"><img src='./img/bt_preview.gif'></a><a onClick=\"classObj.ajax_process('modify')\"><img src='./img/bt_edit_s.gif' style='margin-left:2px'></a><a onClick=classObj.ajax_process('delete')><img src='./img/bt_del_s.gif' style='margin-left:2px'></a>"; // 미리보기 | 수정 | 삭제

		// 템플릿 popupBody - 공통
		$template['popupBody'] = "
		<tr>
			<td>
				<table name='popupItem' id='popupItem' width='100%' cellpadding='0' cellspacing='0' border='0' bgcolor='white'>
					<tbody>{:popupItem:}
					</tbody>
				</table>
			</td>
		</tr>";
		// 템플릿 popupItem - 공통
		$template['popupItem'] = "
							<table id='item' width='100%' cellpadding='4' cellspacing='0' border='0'>
							<colgroup align='center'><col span='2' width='40'><col></colgroup>
							<tr>
								<th><input type='checkbox' name='chk_no[]' value='{:no:}'><input type='hidden' name='no[]' value='{:no:}'></th>
								<th>$control_buttons[0]</th>
								<th>
									<table id='itemInfo' width='100%' height='145' cellpadding='2' cellspacing='0' border='0' style='table-layout:fixed'>
									<tr height='30'>
										<th nowrap style='padding:0px;'>
											<table style='float:left;' cellpadding='0' cellspacing='0'>
											<tr><th style='padding:0px 0px 0px 4px;' id='controlButtons'>$control_buttons[1]</th></tr>
											</table><div id='settingsInfo'>{:popup_infos:}</div></th>
										<th width='180' nowrap style='text-align:right;padding:0px'>$control_buttons[2]</th>
									</tr>
									<tr height='100%' valign='top'>
										<th colspan='2'>{:popup_contents:}</th>
									</tr>
									</table>
								</th>
							</tr>
							</table>";
		// 싱글모드가 아닌경우
		if($single_mode==false) {
			$template['popupItem'] = "
					<tr>
						<td>$template[popupItem]
						</td>
					</tr>";
		}

		// 사이즈 단위 변환용 코드
		$size_types = array("pixel"=>"px", "exact"=>"%");
		$period_mins = array("00"=>"정각", "30"=>"반");

		foreach($datas as $rows) {
			// 기한 정보 가공
			if($rows['use_date']=="no") $sdate = $edate = '';
			else {
				$sdate = $rows['sdate'] ? date("Y.m.d H시 ".$period_mins[date("i", strtotime($rows['sdate']))]."부터", strtotime($rows['sdate'])) : '';
				$edate = $rows['edate'] ? date("Y.m.d H시 ".$period_mins[date("i", strtotime($rows['sdate']))]."까지", strtotime($rows['edate'])) : '';
			}
			$_hidden = ($rows['type']=="text") ? '' : " style='display:none'";
			$_width = $_height = $__width = $__height = "100%";
			$___width = $___height = $_comment = '';
			if($rows['width_type']=="exact") {
				$_hidden = " style='display:none'";
				$_comment = ($rows['height_type']=="exact") ? "자동맞춤" : "세로지정 ".$rows['height'].$size_types[$rows['height_type']];
			}
			else {
				$_width = $rows['width'].$size_types[$rows['width_type']];
				$__width = $rows['width'];
				$___width = " width='{$rows['width']}'";
			}
			if($rows['height_type']=="exact") {
				$_hidden = " style='display:none'";
				$_comment = ($rows['width_type']=="exact") ? "자동맞춤" : "가로지정 ".$rows['width'].$size_types[$rows['width_type']];
			}
			else {
				$_height = $rows['height'].$size_types[$rows['height_type']];
				$__height = $rows['height'];
				$___height = " height='{$rows['height']}'";
			}

			// 팝업 요약 정보 - 팝업수정시 javascript 에서 span 태그 순번대로 정보를 사용하므로 수정시 주의 요망!
			$popup_infos = "<span id='width' type='$rows[width_type]'$_hidden>$_width</span><font$_hidden>×</font><span id='height' type='$rows[height_type]'$_hidden style='margin-right:10px;'>$_height</span><span id='type' type='$rows[type]' value='$media_kind' style='padding-right:15px;'>$_comment</span><span id='use_date' value='$rows[use_date]'><span id='sdate'>$sdate</span> <span id='edate'>$edate</span></span>";

			// 스크립트 자동실행을 방지하기 위한 코드 변환
			if($single_mode===true) $rows['content'] = str_replace("\\", "\\\\", $rows['content']); // stripslashes 버그픽스 - 2008.06.09
			$text_content = "<span id='text_data' width='$__width' height='$__height' style='display:none'>".str_replace(array("<", ">"), array("{:_lt:}", "{:_gt:}"), $rows['content'])."</span><iframe id='textContent$rows[no]' frameborder='0' style='width:100%;height:0px;'></iframe>";
			$popup_contents = "
			<table border='0' cellpadding='1' cellspacing='1' bgcolor='#cacaca'>
			<tr>
				<th bgcolor='#ffffff' style='padding:1px;'>
					<table border='0' cellpadding='3' cellspacing='0' bgcolor='#f1f1f1'>
					<tr>
						<th nowrap style='font-weight:bolder;color:black;padding:5px 3px 0px 4px;'><label style='font-family:dotum;font-size:9pt;'>$rows[title]</label></th>
					</tr>
					<tr>
						<th style='padding:3px;'>
							<table$___width$___height cellpadding='0' cellspacing='0'>
							<tr valign='top'>
								<th style='padding:0px'><span id='text'></span></th>
							</tr>
							</table>
						</th>
					</tr>
					<tr>
						<th background='/rankup_module/rankup_popup/img/dp_background.gif' style='padding:0px'>
							<table border='0' cellspacing='0' cellpadding='0' align='right'>
							<tr>
								<th style='padding:0px'><input type='checkbox' disabled></th>
								<th style='padding:0px'><label style='padding:2 5 0 0px;height:14px;font-size:9pt;cursor:pointer' onFocus='this.blur()' disabled>오늘하루 그만보기</label></th>
								<th style='padding:3px 3px 4px 0px;'><img src='/rankup_module/rankup_popup/img/dp_bclose.gif' border='0'></th>
							</tr>
							</table>
						</th>
					</tr>
					</table>
				</th>
			</tr>
			</table>$text_content";

			$popupItem = str_replace(array("{:no:}", "{:popup_infos:}", "{:popup_contents:}", "{:view:}"), array($rows['no'], $popup_infos, $popup_contents, $views[$rows['view']]), $template['popupItem']);
			$popupBody .= str_replace("{:popupItem:}", $popupItem, $template['popupBody']);
		}
		return $single_mode==true ? $popupItem : $popupBody;
	}

	// 팝업추출 - 2009.01.20 fixed
	function get_popup($datas, $xml_mode=true) {
		if($datas['no']) $addWhere .= "=$datas[no]";
		if($datas['view']) $addWhere .= " and view='$datas[view]'"; // 출력여부
		if($datas['check_date']=="yes") $addWhere .= " and (use_date='no' or (use_date='yes' and (sdate is NULL or sdate<=now()) and (edate is NULL or edate>=now())))";

		$popups = $this->queryFetchRows("select no,rank,type,title,width,height,width_type,height_type,content,use_date,sdate,edate,view from $this->table where no$addWhere order by rank");
		return $xml_mode ? $this->get_xml_formalize($popups) : $popups;
	}

	// 팝업 리스트 생성 - 관리자에서 사용
	function get_popup_list() {
		// 팝업정보 쿼리
		$datas = $this->get_popup(array("position"=>$position), false);
		return $this->get_html_formalize($datas, false);
	}

	// 팝업설정값 변경 :: 사용여부/순위정보
	function set_popup($kind='') {
		switch($kind) {
			case "view": // 사용/미사용 값 변경 - 참조값 : GET
				$nos = str_replace("__", ", ", $_GET['data']);
				return $this->query("update $this->table set view='$_GET[val]' where no in($nos)");
				break;

			default: // 순위 값 갱신 - 참조값 : POST
				if(!count($_POST['no'])) return false;
				foreach($_POST['no'] as $rank => $no) {
					$_val['rank'] = $rank+1;
					$values = $this->change_query_string($_val);
					$this->query("update $this->table set $values where no=$no");
				}
		}
	}
}
?>