<?php
/**
 * 폼빌더 클래스
 *@author: kurokisi
 *@authDate: 2012.02.13
 *@latestUpdate: 2012.04.10
 */
class rankup_fbuilder extends rankup_util {
	public $form_table = 'rankup_builder_forms';
	public $field_table = 'rankup_builder_fields';
	private $prefix = array(
		'table' => 'rankup_fb',
		'field' => 'f'
	);
	public $defaults = array();

	function __construct() {
		parent::rankup_util();
		include 'defaults.inc.php';
	}

	// 기본설정 반환
	public function get_settings($fno) {
		$rows = $this->queryFetch("SELECT no, form_name, table_name, form_settings, form_groups FROM $this->form_table".q(" WHERE no=%d", $fno));
		if(!$rows['no']) return array();
		$rows += unserialize($rows['form_settings']);
		$rows['form_groups'] = unserialize($rows['form_groups']);
		return $rows;
	}

	// 그룹 반환
	public function get_groups($fno) {
		$rows = $this->queryFetch("SELECT form_groups FROM $this->form_table WHERE no=$fno");
		return $rows['form_groups'] ? unserialize($rows['form_groups']) : array();
	}

	// 등록폼 반환 - JSON
	public function load_forms_toJSON() {
		$items = array();
		$datas = $this->query("SELECT no as fno, form_name FROM $this->form_table");
		while($rows = $this->fetch($datas)) array_push($items, $rows);
		return $items;
	}

	// 폼설정 반환
	public function load_fields($fno) {
		$values = array();
		$groups = $this->get_groups($fno);
		$datas = $this->query("SELECT * FROM $this->field_table WHERE fno=$fno ORDER BY gno, rank");
		while($rows = $this->fetch($datas)) {
			$gno = (string)$rows['gno'];
			if(!$values[$gno]) {
				$values[$gno] = $groups[$gno];
				$values[$gno]['fields'] = array();
			}
			$settings = unserialize($rows['field_setting']);
			array_push($values[$gno]['fields'], array(
				'no' => $rows['no'],
				'sno' => $rows['sno'],
				'identity' => '',
				'field_name' => $rows['field_name'],
				'field_type' => $settings['field_type'],
				'fixed' => $settings['fixed'],
				'disabled' => ($settings['fixed']=='fixed') ? 'disabled' : '',
				'required' => $settings['required'],
				'values' => $rows['field_value'] ? unserialize($rows['field_value']) : array(),
				'hint' => $rows['hint'],
				'view' => $rows['view']
			));
		}
		return array_values($values);
	}

	// 첫번째로 등록되어 있는 폼 반환 - 메일폼 관리에서 사용
	public function get_first_form() {
		$fno = $this->queryR("SELECT no FROM $this->form_table LIMIT 1");
		return $fno;
	}

	// 등록폼 반환
	public function print_forms($entry) {
		$datas = $this->query("SELECT * FROM $this->form_table");
		return fetch_contents($datas, $entry, array($this, '_f63'));
	}
	public function _f63($bind) {
		extract($bind);
		$settings = unserialize($rows['form_settings']);
		if($mode=='list') {
			if(isset($level_texts)) $rows['write_level_text'] = $level_texts[$settings['write_level']];
			if(isset($use_texts)) {
				$rows['privacy_text'] = $use_texts[$settings['use_privacy']];
				$rows['antispam_text'] = $use_texts[$settings['use_antispam']];
			}
			$rows['articles_num'] = number_format($this->queryR("select count(no) from ".$rows['table_name']));
		}
		else if($mode=='left') {
			$nums = $this->queryR("select count(no) from ".$rows['table_name']." where status='request'");
			if($nums) $rows['on_new'] = $on_new;
		}
		else if($mode=='option') {
			if($rows['no']==$value) $rows['selected'] = $on_selected;
		}
		// 관리자 서브메뉴 활성화 용
		if(isset($on_hover) && isset($value)) {
			if($rows['no']==$value) $rows['on_hover'] = $on_hover;
		}
		return array($rows, $skin);
	}

	// 등록폼 저장
	public function save_forms() {
		$_vals['form_name'] = $_POST['form_name'];
		$_vals['form_settings'] = serialize(array(
			'write_level' => $_POST['write_level'],
			'use_alarm' => $_POST['use_alarm'],
			'alarm_phone' => $_POST['alarm_phone'],
			'alarm_message' => $_POST['alarm_message'],
			'use_privacy' => $_POST['use_privacy'],
			'use_antispam' => $_POST['use_antispam']
		));
		$groups = array();
		foreach($_POST['gno'] as $key=>$gno) {
			$gno = (string)$gno;
			$groups[$gno] = array(
				'gno' => $gno,
				'group_name' => $_POST['group_name'][$key]
			);
		}
		$_vals['form_groups'] = serialize($groups);
		if($_POST['fno']) {
			$values = $this->change_query_string($_vals);
			$this->query("UPDATE $this->form_table SET $values WHERE no=$_POST[fno]");
			$fno = $_POST['fno'];
		}
		else {
			$_vals['regist_date'] = date('Y-m-d');
			$values = $this->change_query_string($_vals);
			$this->query("INSERT INTO $this->form_table SET $values");
			$fno = $this->insert_id;
			$table_name = $this->prefix['table'].$fno;
			$this->query("UPDATE $this->form_table SET table_name='$table_name' WHERE no=$fno");

			// 테이블생성
			$this->query(sprintf($this->table_scheme, $table_name));
		}
		return $fno;
	}

	// 등록폼 저장
	public function save_fields() {
		$_vals['fno'] = $_POST['fno'];
		$_vals['gno'] = $_POST['gno'];

		if($_POST['sno']) {
			$sno = $_POST['sno'];
			$_rows = $this->queryFetch("SELECT no, field FROM $this->field_table WHERE fno=$_POST[fno] AND sno=$sno");
			$no = $_rows['no'];
			$prev_field = $_rows['field'];
		}
		else {
			$sno = $this->queryR("SELECT max(sno) FROM $this->field_table WHERE fno=$_POST[fno]")+1;
			$prev_field = '';
			$_vals['sno'] = $sno;
			$_vals['field'] = preg_match('/[a-z]/i', $_POST['identity']) ? $_POST['identity'] : $this->prefix['field'].$sno;
		}
		$_vals['field_setting'] = serialize(array(
			'field_type' => $_POST['field_type'],
			'fixed' => $_POST['fixed'],
			'disabled' => $_POST['disabled'],
			'required' => ($_POST['required']=='on') ? 'yes' : 'no'
		));
		$_vals['field_name'] = $_POST['field_name'];
		switch($_POST['field_type']) {
			case 'text': $field_value = array('width' => $_POST['width'], 'maxlength' => $_POST['maxlength']); break;
			case 'textarea': $field_value = array('width' => $_POST['width'], 'height' => $_POST['height'], 'editor' => ($_POST['editor']=='on') ? 'yes' : 'no'); break;
			case 'radio': case 'checkbox': case 'select': $field_value = array('items' => $this->htmlspecialchars($_POST['value'])); break;
			case 'phone': $field_value = array('option' => $_POST['option']); break;
			case 'addrs': $field_value = array('search' => $_POST['search']=='on' ? 'yes' : 'no'); break;
			case 'attach': $field_value = array('limit_size' => $_POST['limit_size'], 'allow_ext' => $_POST['allow_ext']); break;
			case 'calendar': $field_value = array('kind' => $_POST['kind']); break;
			default: $field_value = array();
		}
		$_vals['field_value'] = is_array($field_value) ? serialize($field_value) : null;
		$_vals['hint'] = trim(strip_tags($_POST['hint']));
		$_vals['view'] = in_array($_POST['view'], array('yes', 'no')) ? $_POST['view'] : 'yes'; //
		$_vals['rank'] = $_POST['rank'];

		$values = $this->change_query_string($_vals);
		if($no) $this->query("UPDATE $this->field_table SET $values WHERE no=$no");
		else {
			$this->query("INSERT INTO $this->field_table SET $values");
			$no = $this->insert_id;
		}

		// ALTER TABLE
		$TABLE_NAME = $this->prefix['table'].$_POST['fno']; // 테이블명
		$FIELD_NAME = $prev_field ? $prev_field : $_vals['field']; // 필드명
		$NULL_DEFAULT = ($_POST['required']=='on' && $_POST['view']=='yes') ? " NOT NULL default ''" : " DEFAULT NULL"; // NULL DEFAULT 값
		switch($_POST['field_type']) {
			case 'text': $FIELD_TYPE = " varchar($_POST[maxlength])"; break;
			case 'textarea':
			case 'checkbox':
			case 'addrs': $FIELD_TYPE = " text"; break;
			case 'radio':
			case 'select': $FIELD_TYPE = " varchar(40)"; break;
			case 'email': $FIELD_TYPE = " varchar(50)"; break;
			case 'homepage': $FIELD_TYPE = " varchar(200)"; break;
			case 'phone': $FIELD_TYPE = " varchar(15)"; break;
			case 'jumin': $FIELD_TYPE = " varchar(14)"; break;
			case 'attach': $FIELD_TYPE = " varchar(30)"; break;
			case 'calendar': $FIELD_TYPE = " varchar(21)"; break;
			case 'dimension': $FIELD_TYPE = " varchar(150)"; break;
		}
		$fields = $this->get_fields($TABLE_NAME, $FIELD_NAME);
		if($fields===false) $this->query("ALTER TABLE $TABLE_NAME ADD $FIELD_NAME $FIELD_TYPE $NULL_DEFAULT");
		else {
			// 2012.04.02 fixed
			$null = ($_POST['required']=='on' && $_POST['view']=='yes') ? 'NO' : 'YES';
			if(($fields['Type']!=ltrim($FIELD_TYPE)) || ($fields['Null']!=$null)) {
				$this->query("ALTER TABLE $TABLE_NAME MODIFY $FIELD_NAME $FIELD_TYPE $NULL_DEFAULT");
			}
		}

		return array('no'=>$no, 'sno' => $sno, 'identity'=>'');
	}

	// 필드 반환
	public function get_fields($table_name, $field_name) {
		$datas = $this->query("SHOW columns FROM $table_name");
		while($rows = $this->fetch($datas)) {
			if($rows['Field']!==$field_name) continue;
			if(!$rows['Null']) $rows['Null'] = 'NO'; // 2012.04.02 fixed @note: mysql 4.x 에서는 빈 값
			return $rows;
		}
		return false;
	}

	// 필드설정 반환
	public function get_field_values($fno, $field) {
		$rows = $this->queryFetch("SELECT field_value FROM $this->field_table WHERE fno=$fno AND field='$field'");
		return $rows['field_value'] ? unserialize($rows['field_value']) : '';
	}

	// &#039;, &quot;, comma 치환
	public function htmlspecialchars($string) {
		return str_replace(array('"', '"', ','), array('&quot;', '&#039;', '·'), $string);
	}

	// 등록폼 삭제
	public function del_forms($nos) {
		global $base_dir;
		$nos = str_replace('__', ',', $nos);
		$datas = $this->query("SELECT * FROM $this->form_table WHERE no IN($nos)");
		while($rows = $this->fetch($datas)) {
			$this->query("DROP TABLE IF EXISTS $rows[table_name]"); // 등록폼 테이블 제거
			// 등록폼 첨부파일 제거
			foreach(explode(',', $nos) as $no) {
				$files = glob($base_dir.'PEG/fbuilder/'.$no.'/*');
				if(is_array($files)) {
					foreach($files as $file) unlink($file);
				}
			}
		}
		$this->query("DELETE FROM $this->form_table WHERE no IN($nos)"); // 폼설정 제거
		$this->query("DELETE FROM $this->field_table WHERE fno IN($nos)"); // 필드설정 제거
	}

	// 필드 삭제
	public function del_fields($fno, $nos) {
		$fields = array();
		$nos = str_replace('__', ',', $nos);
		$table_name = $this->prefix['table'].$fno;
		$_datas = $this->query("SHOW COLUMNS FROM $table_name");
		while($_rows = $this->fetch($_datas)) $fields[] = $_rows['Field'];
		if(count($fields)) {
			$datas = $this->query("SELECT field FROM $this->field_table WHERE no IN($nos)");
			while($rows = $this->fetch($datas)) {
				if(in_array($rows['field'], $fields)) {
					$this->query("ALTER TABLE $table_name DROP $rows[field]");
				}
			}
		}
		$this->query("DELETE FROM $this->field_table WHERE no IN($nos)");
	}

	// 등록(수정)폼 반환
	public function draw_form($forms, $no='', $entry) {
		if($no) $entry['values'] = $this->queryFetch("SELECT * FROM $forms[table_name]".q(" WHERE no=%d", $no));
		$entry['totals'] = count($forms['form_groups']);
		return fetch_contents($forms['form_groups'], $entry, array($this, '_f236'));
	}
	public function _f236($bind) {
		extract($bind);
		if(isset($values)) $field_entry['values'] = $values;
		$datas = $this->query("SELECT * FROM $this->field_table WHERE view='yes' AND fno=$forms[no] AND gno=$rows[gno] ORDER BY rank");
		// 모바일폼 - 2012.04.09 added
		if($mobile==true) { // 최대 width 추출
			$max_width = 0;
			while($_rows = $this->fetch($datas)) {
				$vals = unserialize($_rows['field_value']);
				if($vals['width']>$max_width) $max_width = $vals['width'];
			}
			mysql_data_seek($datas, 0);
			$field_entry['max_width'] = $max_width;
		}
		if($rows['group_name']) $rows['on_group_name'] = fetch_skin($rows, $on_group_name);
		if($before_contents && $rank===1) $rows['before_contents'] = $before_contents;
		if($after_contents && $rank===$totals) $rows['after_contents'] = $after_contents;
		$rows['fields'] = fetch_contents($datas, $field_entry, array($this, '_f240'));
		return array($rows, $skin);
	}
	public function _f240($bind) {
		extract($bind);
		$set = unserialize($rows['field_setting']);
		$_vals = $rows['field_value'] ? unserialize($rows['field_value']) : array();
		$_vals['field'] = $rows['field'];
		$_vals['field_name'] = $rows['field_name'];
		if($set['required']=='yes') {
			$_vals['on_required'] = $on_required;
			if(isset($require_icon)) $rows['require_icon'] = $require_icon;
		}
		else if(isset($normal_icon)) {
			$rows['normal_icon'] = $normal_icon;
		}
		if($rows['hint']) {
			$rows['hint'] = nl2br($rows['hint']);
			$rows['on_hint'] = fetch_skin($rows, $on_hint);
		}
		// 모바일용 가로크기 조정 - 2012.04.09 added
		if($mobile && $max_width) {
			if(isset($_vals['width'])) {
				if($_vals['width']<=200) $_vals['width'] .= 'px';
				else {
					$rate = ($max_width==$_vals['width']) ? 100 : round($_vals['width']/$max_width);
					$_vals['width'] = ($rate>70 ? $rate : 70).'%';
				}
			}
		}

		$template = $field_items[$set['field_type']];
		$_vals['value'] = $this->htmlspecialchars($values[$rows['field']]);
		if(in_array($set['field_type'], array('radio', 'checkbox', 'select'))) {
			$items = $template[0] ? fetch_skin($_vals, $template[0]) : '';
			foreach($_vals['items'] as $row=>$value) {
				list($selected, $checked) = in_array($value, $this->htmlspecialchars(explode(',', $values[$rows['field']]))) ? array(' selected', ' checked') : array('', '');
				$items .= fetch_skin(array_merge($_vals, compact('row', 'value', 'selected', 'checked')), $template[1]);
			}
			$items .= $template[2] ? fetch_skin($_vals, $template[2]) : '';
			$rows['field_item'] = $items;
		}
		else {
			switch($set['field_type']) {
				case 'text': break;
				case 'textarea':
					if($_vals['editor']=='yes') $_vals['on_editor'] = $on_editor;
					break;
				case 'jumin':
					list($_vals['jumin1'], $_vals['jumin2']) = explode('|', $_vals['value']);
					break;
				case 'addrs':
					if($_vals['search']=='yes') $_vals['on_readonly'] = $on_readonly; // 2012.04.10 added
					else unset($template[2]);
					list($_vals['zipcode'], $_vals['addrs1'], $_vals['addrs2']) = explode('|', $_vals['value']);
					$template = implode('', $template);
					break;
				case 'calendar':
					list($_vals['sdate'], $_vals['edate']) = explode('|', $_vals['value']);
					$template = $template[$_vals['kind']];
					break;
				case 'dimension': // 2012.03.14 added
					list($_vals['square'], $_vals['pyeong']) = explode('|', $_vals['value']);
					break;
				case 'attach':
					if($mobile) { // 모바일웹인경우 필수값 해제 - 2012.04.09 added
						$_vals['on_required'] = '';
						$rows['require_icon'] = isset($normal_icon) ? $normal_icon : '';
					}
					break;
			}
			$rows['field_item'] = fetch_skin($_vals, $template);
		}
		return array($rows, $skin);
	}

	// 상세 반환
	public function draw_view($forms, $entry) {
		$entry['totals'] = count($forms['form_groups']);
		return fetch_contents($forms['form_groups'], $entry, array($this, '_f349'));
	}
	public function _f349($bind) {
		extract($bind);
		if(isset($values)) $field_entry['values'] = $values;
		$datas = $this->query("SELECT * FROM $this->field_table WHERE view='yes' AND fno=$forms[no] AND gno=$rows[gno] ORDER BY rank");
		if($rows['group_name']) $rows['on_group_name'] = fetch_skin($rows, $on_group_name);
		if($before_contents && $rank===1) $rows['before_contents'] = $before_contents;
		if($after_contents && $rank===$totals) $rows['after_contents'] = $after_contents;
		$rows['fields'] = fetch_contents($datas, $field_entry, array($this, '_f354'));
		return array($rows, $skin);
	}
	public function _f354($bind) {
		extract($bind);
		$set = unserialize($rows['field_setting']);
		if($set['required']=='yes') {
			$_vals['on_required'] = $on_required;
			if(isset($require_icon)) $rows['require_icon'] = $require_icon;
		}
		else if(isset($normal_icon)) {
			$rows['normal_icon'] = $normal_icon;
		}
		$value = $values[$rows['field']];
		switch($set['field_type']) {
			case 'email':
				$value = fetch_skin(array('email'=>$value), $field_items[$set['field_type']]);
				break;
			case 'jumin':
				list($jumin1, $jumin2) = explode('|', $value);
				$value = fetch_skin(compact('jumin1', 'jumin2'), $field_items[$set['field_type']]);
				break;
			case 'addrs':
				list($zipcode, $addrs1, $addrs2) = explode('|', $value);
				$value = fetch_skin(compact('zipcode', 'addrs1', 'addrs2'), $field_items[$set['field_type']]);
				break;
			case 'calendar':
				$dates = explode('|', $value);
				$value = fetch_skin(array('sdate' => $dates[0], 'edate' => $dates[1]), $field_items[$set['field_type']][count($dates)]);
				break;
			case 'attach':
				$value = fetch_skin(array('name'=>$value), $field_items[$set['field_type']]);
				break;
			case 'textarea':
				if($value==strip_tags($value)) $value = nl2br($value); // 2012.04.10 modified
				break;
			case 'dimension':
				list($square, $pyeong) = explode('|', $value);
				$value = fetch_skin(compact('square', 'pyeong'), $field_items[$set['field_type']]);
				break;
		}
		$rows['field_value'] = $value;
		return array($rows, $skin);
	}

	// 목록 반환 - 관리자 전용(사용자는 지원안함)
	public function draw_list($fno, $entry, $limits=15) {
		$stpos = $this->get_query_point($_GET['page'], $limits);
		$table_name = $this->prefix['table'].$fno;
		$datas = $this->query("SELECT SQL_CALC_FOUND_ROWS * FROM $table_name ORDER BY no DESC LIMIT $stpos, $limits");
		$totals = $this->queryR("SELECT FOUND_ROWS()");
		$entry['row'] = $totals - $stpos;
		$contents = fetch_contents($datas, $entry, array($this, '_f291'));
		return array($totals, $contents);
	}
	public function _f291($bind) {
		extract($bind);
		$rows['row'] = $row;
		if($rows['uid']) $rows['on_uid'] = fetch_skin($rows, $on_uid);
		$rows['memo_text'] = $rows['memo'] ? $memo_texts[1] : $memo_texts[0];
		if(isset($status_texts)) {
			if(isset($status_options)) {
				foreach($status_texts as $value=>$text) {
					$selected = ($rows['status']==$value) ? $on_selected : '';
					$rows['status_options'] .= fetch_skin(compact('value', 'text', 'selected'), $status_options);
				}
			}
			else {
				$rows['status_text'] = $status_texts[$rows['status']];
			}
		}
		if(isset($on_answered) && $rows['status']=='answered') $rows['on_answered'] = fetch_skin($rows, $on_answered);
		if(isset($time_format)) $rows['regist_time'] = date($time_format, strtotime($rows['regist_time']));
		return array($rows, $skin);
	}

	// 문의글 반환
	public function get_article($fno, $no) {
		$fno = preg_replace('/[^\d]/', '', $fno);
		if(!$fno) return array();
		else {
			$table_name = $this->prefix['table'].$fno;
			$rows = $this->queryFetch("SELECT * FROM $table_name".q(" WHERE no=%d", $no));
			return $rows;
		}
	}

	// 문의글 저장
	public function save_article() {
		global $member_info, $base_dir;

		$_POST['fno'] = preg_replace('/[^\d]/', '', $_POST['fno']); // 필터링
		$table_name = $this->prefix['table'].$_POST['fno'];

		// XSS 공격 방어
		$filter = new HTMLFilter;

		// 저장할 내용 정리
		$datas = $this->query("SELECT field, field_setting, field_value FROM $this->field_table WHERE view='yes' AND fno=$_POST[fno]");
		while($rows = $this->fetch($datas)) {
			$set = unserialize($rows['field_setting']);
			$value = stripslashes($_POST[$rows['field']]);
			switch($set['field_type']) {
				case 'text': $value = str_replace('"', '&quot;', $value); break;
				case 'textarea': $value = parent::trans_wysiwyg($value); break;
				case 'checkbox': $value = @implode(',', $_POST[$rows['field']]); break;
				case 'addrs': $value = @implode('|', $_POST[$rows['field']]); break;
				case 'calendar': $value = @implode('|', $_POST[$rows['field']]); break;
				case 'dimension': $value = @implode('|', $_POST[$rows['field']]); break; // 면적 - 2012.03.14 added
				case 'jumin': $value = @implode('|', $_POST[$rows['field']]); break; // 주민등록번호 - 2012.04.05 added
				case 'radio':
				case 'select':
					//@do nothing...
					break;
				case 'attach':
					$value = ''; // 2012.04.09 added
					if($_POST[$rows['field']]) {
						$attach = new attachment('fbuilder', $base_dir.'rankup_module/rankup_fbuilder/');
						$value = $attach->save($_POST[$rows['field']]);
					}
					break;
			}
			if($set['field_type']=='attach') $_vals[$rows['field']] = $value; // 2012.04.09 added
			else $_vals[$rows['field']] = ($value=='') ? null : $filter->parse($value);
		}
		if($_POST['item_no']) $_vals['item_no'] = $_POST['item_no']; // 제품번호(견적문의 시)
		if($member_info['uid']) $_vals['uid'] = $member_info['uid'];
		$_vals['regist_time'] = date('Y-m-d H:i:s');
		$_vals['regist_ip'] = $_SERVER['REMOTE_ADDR'];
		$values = $this->change_query_string($_vals);
		$this->query("INSERT INTO $table_name SET $values");
		return true;
	}

	// 게시글 메모 반환
	public function load_memo($fno, $no) {
		$table_name = $this->prefix['table'].$fno;
		$rows = $this->queryFetch("SELECT memo FROM $table_name WHERE no=$no");
		return $rows['memo'];
	}

	// 게시글 메모 저장
	public function save_memo($fno, $no, $memo='') {
		$table_name = $this->prefix['table'].$fno;
		$values = $this->change_query_string(compact('memo'));
		$this->query("UPDATE $table_name SET $values WHERE no=$no");
	}

	// 게시글 삭제
	public function del_articles($fno, $nos) {
		global $base_dir;
		$nos = str_replace('__', ',', $nos);
		// 첨부파일 삭제
		foreach(explode(',', $nos) as $no) {
			$files = glob($base_dir.'PEG/fbuilder/'.$fno.'/'.$no.'.*');
			if(is_array($files)) {
				foreach($files as $file) unlink($file);
			}
		}
		$table_name = $this->prefix['table'].$fno;
		$this->query("DELETE FROM $table_name WHERE no IN($nos)");
	}

	// 게시글 답변상태 변경
	public function set_status($fno, $no, $status) {
		$table_name = $this->prefix['table'].$fno;
		$_vals['status'] = $status;
		switch($status) {
			case 'answered': $_vals['answered_time'] = date('Y-m-d H:i:s'); break;
			default: $_vals['answered_time'] = null;
		}
		$values = $this->change_query_string($_vals);
		$this->query("UPDATE $table_name SET $values WHERE no=$no");
	}

	// 게시글 답변메일 저장
	public function save_answer($fno, $no, $_vals) {
		$table_name = $this->prefix['table'].$fno;
		$_vals['answered_time'] = date('Y-m-d H:i:s');
		$_vals['status'] = 'answered';
		$values = $this->change_query_string($_vals);
		$this->query("UPDATE $table_name SET $values WHERE no=$no");
	}

	// 글작성자 인증
	public function verify_author($fno, $no) {
		$_SESSION[sprintf('readsses_%d_%d', $fno, $no)] = time() + 600; // 10분짜리 세션 생성
	}

	// 글작성자 세션 확인
	public function check_author($fno, $no) {
		$v_sess = $_SESSION[sprintf('readsses_%d_%d', $fno, $no)];
		return ($v_sess && time()<=$v_sess);
	}

	// 컴포넌트 반환
	public function get_components() {
		$datas = $this->query("SELECT no, form_name FROM $this->form_table ORDER BY no");
		while($rows = $this->fetch($datas)) {
			$components[$rows['no']] = array(
				'name' => $rows['form_name'],
				'file' => null,
				'url' => 'board/write.html?fno='.$rows['no']
			);
		}
		return $components;
	}
}
?>