<?php
/**
 * 제품관리 클래스
 *@author: kurokisi
 *@authDate: 2012.01.26
 */
class product extends rankup_util {

	public $config_table = 'rankup_configs';
	public $product_table = 'rankup_product';
	private $config_kind = 'product_config';

	function __construct() {
		parent::rankup_util();
	}

	// 환경설정 반환
	public function get_settings() {
		$rows = $this->queryFetch("SELECT value FROM $this->config_table WHERE item='$this->config_kind'");
		return $rows['value'] ? unserialize($rows['value']) : array();
	}

	// 환경설정 저장
	public function set_settings() {
		$prows = $this->queryFetch("SELECT item FROM $this->config_table WHERE item='$this->config_kind'");
		$_vals['item'] = $this->config_kind;
		$_vals['value'] = serialize(array(
			/* 출력설정 */
			'list_limits' => $_POST['list_limits'],
			'gallery_limits' => $_POST['gallery_limits'],
			/* 견적문의설정 */
			'use_component' => $_POST['use_component'],
			/* 등록항목설정 */
			'use_feature' => $_POST['use_feature'],
			'use_purpose' => $_POST['use_purpose'],
			'use_spec' => $_POST['use_spec'],
		));
		$values = $this->change_query_string($_vals);
		if($prows['item']==$this->config_kind) $this->query("update $this->config_table set $values where item='$this->config_kind'");
		else $this->query("insert into $this->config_table set $values");
	}

	// 제품정보 반환
	function get_product($no) {
		$where = q(" where no=%d", $no);
		$rows = $this->queryFetch("select * from $this->product_table".$where);
		return $rows;
	}

	// 제품목록 반환 - JSON
	function load_products_toJSON() {
		$stacks = array();
		$stacks[] = "view='yes'";
		if($_POST['cate1']) $stacks[] = q("cate1=%d", $_POST['cate1']);
		if($_POST['cate2']) $stacks[] = q("cate2=%d", $_POST['cate2']);
		if(count($stacks)) $where = ' where '.implode(' and ', $stacks);

		$items = array();
		$datas = $this->query("select no, cate1, cate2, title from $this->product_table".$where." order by no desc"); // order by clause - 2012.03.19 added
		while($rows = $this->fetch($datas)) array_push($items, $rows);
		return $items;
	}

	// 제품 등록/수정
	function set_product() {
		global $base_dir;
		if($_POST['no']) $prows = $this->get_product($_POST['no']);
		$_vals['mpid'] = $_POST['mpid'];
		$_vals['title'] = trim(strip_tags($_POST['title']));
		$_vals['cate1'] = $_POST['cate1'];
		$_vals['cate2'] = $_POST['cate2'];
		$_vals['comment'] = strip_tags($_POST['comment']); // tag 제거
		$_vals['view'] = $_POST['view'];
		$_vals['main_view'] = $_POST['main_view'];

		$filter = new HTMLFilter;
		$_vals['feature'] = $filter->parse(parent::trans_wysiwyg($_POST['feature']));
		#$_vals['purpose'] = $filter->parse(parent::trans_wysiwyg($_POST['purpose']));
		$_vals['purpose'] = parent::trans_wysiwyg($_POST['purpose']);
		$_vals['spec'] = $filter->parse(parent::trans_wysiwyg($_POST['spec']));

		if($_POST['no']) {
			$values = $this->change_query_string($_vals);
			$this->query("update $this->product_table set $values".q(" where no=%d", $_POST['no']));
		}
		else {
			$_vals['regist_time'] = date('Y-m-d H:i:s');
			$_vals['rank'] = $this->queryR("select max(rank) from $this->product_table")+1;
			$values = $this->change_query_string($_vals);
			$this->query("insert into $this->product_table set $values");
			$_REQUEST['no'] = $this->insert_id;
		}
		if(count($_POST['on_photos'])) {
			$attach = new attachment('product', $base_dir.'product/');
			$attach->save(implode(',', $_POST['on_photos']));
		}
	}

	// 제품삭제
	function del_product($nos) {
		global $base_dir;
		foreach(explode('__', $nos) as $no) {
			$_REQUEST['no'] = $no;
			$attach = new attachment('product', $base_dir.'product/');
			$path = $base_dir.$attach->configs['save']['folder'];
			$files = glob($path.'*');
			if(is_array($files)) {
				foreach($files as $file) unlink($file);
			}
			rmdir($path);
		}
		$nos = str_replace('__', ',', $nos);
		$this->query("delete from $this->product_table where no in($nos)");
	}

	// 제품 목록 반환
	function print_contents($entry, $limits=10) {
		// 검색조건
		$stacks = array();
		if($_GET['cate1']) $stacks[] = q("cate1=%d", $_GET['cate1']);
		if($_GET['cate2']) $stacks[] = q("cate2=%d", $_GET['cate2']);
		if($_GET['view']) $stacks[] = q("view='%s'", $_GET['view']);
		if($_GET['skey']) $stacks[] = q("(title like '%%%s%%' or comment like '%%%s%%' or feature like '%%%s%%' or purpose like '%%%s%%' or spec like '%%%s%%')", $_GET['skey'], $_GET['skey'], $_GET['skey'], $_GET['skey'], $_GET['skey']); // 검색어
		if($entry['mode']=='main_view') $stacks[] = "main_view='yes'";
		$where = count($stacks) ? ' WHERE '.implode(' AND ', $stacks) : '';

		$stpos = $this->get_query_point($_GET['page'], $limits);
		list($orderby, $limit) = ($entry['mode']=='main_view') ? array(" ORDER BY rank", '') : array(" ORDER BY no desc", " LIMIT $stpos, $limits");
		$datas = $this->query("SELECT SQL_CALC_FOUND_ROWS * FROM $this->product_table".$where.$orderby.$limit);
		$totals = $this->queryR("SELECT FOUND_ROWS()");
		$entry['row'] = $totals - $stpos;
		$contents = fetch_contents($datas, $entry, array($this, '_p79'));
		return array($totals, $contents);
	}
	function _p79($bind) {
		global $attach, $base_dir, $base_url, $cate;
		extract($bind);
		$rows['row'] = $row;
		if(isset($time_format)) $rows['regist_time'] = date($time_format, strtotime($rows['regist_time']));
		if(isset($view_texts)) {
			$rows['view_text'] = $view_texts[$rows['view']];
			$rows['main_view_text'] = $view_texts[$rows['main_view']];
		}
		$rows['comment'] = nl2br($rows['comment']);
		if($category) {
			if($rows['cate1']) $rows['cate1_text'] = $cate->get_item($rows['cate1']);
			if($rows['cate2']) $rows['cate2_text'] = $cate->get_item($rows['cate2']);
		}

		// 제품이미지
		$_REQUEST['no'] = $rows['no'];
		$rows['save_folder'] = $attach->fetch_folder($save_folder);
		$rows['filename'] = basename(array_shift(glob($base_dir.$rows['save_folder'].'*')));
		if(isset($on_end) && !($rank%$times)) $rows['on_end'] = $on_end;
		return array($rows, $skin);
	}

	// 메인페이지 제품 목록 반환
	function print_main_contents($entry) {
		$datas = $this->query("SELECT * FROM $this->product_table WHERE main_view='yes' and view='yes' ORDER BY rank");
		return fetch_contents($datas, $entry, array($this, '_p79'));
	}

	// 출력설정
	function set_view() {
		$nos = str_replace('__', ',', $_POST['nos']);
		$this->query("UPDATE $this->product_table".q(" SET %s='%s' WHERE no IN(%s)", $_POST['kind'], $_POST['view'], $nos));
	}

	// 순위 갱신
	function set_direction() {
		$rows = $this->queryFetch("select * from $this->product_table".q(" where no=%d", $_POST['no']));
		switch($_POST['kind']) {
			case 'up':
				$prev_rows = $this->queryFetch("select no, rank from $this->product_table where main_view='yes' and rank<$rows[rank] order by rank desc limit 1");
				$this->query("update $this->product_table set rank=$rows[rank] where no=$prev_rows[no]");
				$this->query("update $this->product_table set rank=$prev_rows[rank] where no=$rows[no]");
				break;
			case 'down':
				$next_rows = $this->queryFetch("select no, rank from $this->product_table where main_view='yes' and rank>$rows[rank] order by rank limit 1");
				$this->query("update $this->product_table set rank=$rows[rank] where no=$next_rows[no]");
				$this->query("update $this->product_table set rank=$next_rows[rank] where no=$rows[no]");
				break;
		}
	}

	// 제품상세 - 탭반환
	function draw_tabs($tabs, $tab, $entry) {
		$entry['value'] = $tab;
		return fetch_contents($tabs, $entry, array($this, '_p163'));
	}
	function _p163($bind) {
		extract($bind);
		if($rank===1) $rows['on_first'] = $on_first;
		if($value==$rows['tab']) {
			$rows['on_choice'] = $on_choice;
			$rows['on_name'] = fetch_skin($rows, $on_name);
		}
		return array($rows, $skin);
	}
}

?>