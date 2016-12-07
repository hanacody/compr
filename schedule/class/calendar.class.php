<?php
/**
 * 달력 클래스
 */

class calendar extends rankup_util {
	private $table = 'rankup_calendar';

	public function __construct() {
		parent::rankup_util();

	}

	// 달력 설정 반환
	public function get_settings($no) {
		$rows = $this->queryFetch("select * from $this->table".q(" where no=%d", $no));
		// color unserialize
		if($rows['palette']) $rows += unserialize($rows['palette']);
		return $rows;
	}

	// 달력 설정 저장
	public function set_settings() {
		$_vals['subject'] = $_POST['subject'];
		$_vals['skin_type'] = $_POST['skin_type'];
		$_vals['use_cate'] = $_POST['use_cate'];
		$_vals['read_level'] = $_POST['read_level'];
		$_vals['view_holyday'] = $_POST['view_holyday'];
		$_vals['print_type'] = $_POST['print_type'];

		// 팔레트 - 미사용
		$_vals['palette'] = serialize(array(
			'frame' => array(
				'bgcolor' => $_POST['frame_bgcolor'],
				'color' => $_POST['frame_color']
			),
			'title' => array(
				'bgcolor' => $_POST['title_bgcolor'],
				'color' => $_POST['title_color']
			),
			'basic' => array(
				'bgcolor' => $_POST['basic_bgcolor'],
				'color' => $_POST['basic_color']
			),
			'today' => array(
				'bgcolor' => $_POST['today_bgcolor'],
				'color' => $_POST['today_color']
			)
		));

		$values = $this->change_query_string($_vals);
		if($_POST['no']) {
			$this->query("update $this->table set $values".q(" where no=%d", $_POST['no']));
			$bundle = $_POST['no'];
		}
		else {
			$this->query("insert into $this->table set $values");
			$bundle = $this->insert_id;
		}
		return $bundle;
	}

	// 달력 목록 반환
	public function print_calendars($entry) {
		$datas = $this->query("select * from $this->table");
		return fetch_contents($datas, $entry, array($this, '_c57'));
	}
	public function _c57($bind) {
		extract($bind);
		$rows['print_type_text'] = $print_type_texts[$rows['print_type']];
		if($rows['view_holyday']=='yes') $rows['on_holyday'] = $on_holyday;
		$rows['skin_type_text'] = $skin_type_texts[$rows['skin_type']];
		// 일정분류
		if($rows['use_cate']=='yes' && isset($cate_entry)) {
			$rows['categories'] = $this->print_cates($rows['no'], $cate_entry);
		}
		return array($rows, $skin);
	}

	// 분류 반환
	public function print_cates($no, $entry) {
		global $category;
		$datas = $category->get_bundles('calendar', $no);
		return fetch_contents($datas, $entry, array($this, '_c74'));
	}
	public function _c74($bind) {
		extract($bind);
		if($rows['value']) $rows += unserialize($rows['value']);
		return array($rows, $skin);
	}

	// 달력삭제
	public function del_calendar($nos) {
		global $category;
		$nos = str_replace('__', ',', $nos);
		$this->query("delete from $this->table where no in($nos)");
		$category->del_bundles('calendar', $nos);
	}

	// 컴포넌트 반환
	public function get_components() {
		$datas = $this->query("select no, subject from $this->table order by no");
		while($rows = $this->fetch($datas)) {
			$components[$rows['no']] = array(
				'name' => $rows['subject'],
				'file' => null,
				'url' => 'schedule/index.html?no='.$rows['no']
			);
		}
		return $components;
	}
}
?>