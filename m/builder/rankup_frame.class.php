<?php
/**
 * 랭크업 모바일 프레임 클래스 v1.5
 *@author: kurokisi
 *@authDate: 2011.10.17
 */

class rankup_frame extends rankup_util {

	var $frame_table = 'rankup_mobile_frame';

	function rankup_frame() {
		parent::rankup_util();

	}

	// 프레임 정보 반환
	function get_frame($no) {
		return $this->queryFetch("select * from $this->frame_table where no=$no");
	}

	// 자식 프레임 반환
	function get_frames($parent) {
		return $this->query("select * from $this->frame_table where find_in_set($parent, parents) and used='yes' order by bundle, position");
	}

	// PID 추출
	function educe_pid($infos) {
		$stacks = array();
		if($infos['module']) $stacks[] = "module='$infos[module]'";
		if($infos['component']) $stacks[] = "component='$infos[component]'";
		if($infos['options']) $stacks[] = "options='$infos[options]'";
		if(count($stacks)) $addWhere = ' and '.implode(' and ', $stacks);
		$datas = $this->query("select no, used from $this->frame_table where page_type='module'".$addWhere." order by bundle, position");
		$records = mysql_num_rows($datas);
		while($rows = $this->fetch($datas)) {
			if($rows['used']=='yes') return $rows['no'];
		}
		return $records ? 'deny' : 'none';
	}

	// 프레임 구조 반환
	function print_frames($entry, $no='', $depth='', $used='') {
		$stacks = array();
		if($depth) {
			$stacks[] = $no ? "find_in_set('$no', parents)" : "parents=''";
			$stacks[] = "depth=$depth";
		}
		else if($no) $stacks[] = "no=$no";
		if($used) $stacks[] = "used='$used'";
		if(count($stacks)) $addWhere = ' where '.implode(' and ', $stacks);
		$datas = $this->query("select * from $this->frame_table".$addWhere." order by bundle, position");
		return fetch_contents($datas, $entry, array($this, '_f15'));
	}
	function _f15($bind) {
		global $config_info, $rankup_member;
		extract($bind);
		if($rows['parents']) {
			$parents = explode(',', $rows['parents']);
			$rows['parents'] = 't'.implode(' t', $parents).' ';
		}
		$rows['page_type_text'] = $page_types[$rows['page_type']];
		$rows['access_level_text'] = ($rows['access_level']==$rankup_member->lowest_level) ? $lowest_level_text : $config_info['smlevel'][$rows['access_level']];
		if(isset($on_access_level)) {
			$rows['on_access_level'] = ($rows['access_level']==$rankup_member->lowest_level) ? $lowest_level_text : fetch_skin($rows, $on_access_level);
		}
		if(isset($used_texts)) $rows['used_text'] = $used_texts[$rows['used']];
		if($rows['depth']<2) $rows['on_button'] = $on_button;

		if(isset($pids)) {
			if(in_array($rows['no'], explode(',', $pids))) $rows['on_checked'] = $on_checked;
		}

		// 첨부파일
		if($attaches && isset($on_attach)) {
			if($attaches[$rows['no']]) {
				$name = $attaches[$rows['no']];
				$rows['on_attach'] = fetch_skin(compact('name'), $on_attach);
			}
		}
		// 상단메뉴설정에서 정의
		if(isset($positions)) {
			$rows['position'] = $positions[$rows['no']];
		}
		return array($rows, $skin);
	}

	// 프레임 저장
	function save_frame() {
		if($_POST['kind']=='new') { // 추가
			if($_POST['no']) { // 2~3차 메뉴
				$rows = $this->queryFetch("select no, bundle, position, depth, parents from $this->frame_table where no=$_POST[no]");
				$bundle = $rows['bundle'];
				$depth = $rows['depth'] + 1;

				// child
				$parents = $rows['parents'] ? $rows['parents'].','.$rows['no'] : $rows['no'];
				$this->query("update $this->frame_table set has_child='yes' where no=$_POST[no]"); // has_child update

				$position = $this->queryR("select max(position) from $this->frame_table where bundle=$rows[bundle] and depth>=$rows[depth] and find_in_set($rows[no], parents)");
				if(!$position) $position = $rows['position'] + 1;
				else $position += 1;
				$this->query("update $this->frame_table set position=position+1 where bundle=$bundle and position>=$position"); // 이후 레코드의 position 값 +1 처리
			}
			else { // 1차 메뉴
				$bundle = $this->queryR("select max(bundle)+1 from $this->frame_table");
				$depth = 1;
				$position = 0;
				$parents = '';
			}
			$_vals['bundle'] = $bundle;
			$_vals['position'] = $position;
			$_vals['depth'] = $depth;
			$_vals['parents'] = $parents;
		}
		$_vals['base_name'] = $_POST['base_name'];
		$_vals['target'] = $_POST['target'];
		$_vals['access_level'] = $_POST['access_level']; // 0~10
		$_vals['used'] = $_POST['used'];
		$_vals['use_gnb'] = $_POST['use_gnb'];

		$_vals['page_type'] = $_POST['page_type'];
		$_vals['module'] = $_POST['module'];
		$_vals['component'] = $_POST['component'];
		$_vals['options'] = $_POST['on_option'] ? stripslashes($_POST['on_option']) : null; // 2012.02.21 added
		$_vals['page_body_content'] = parent::trans_wysiwyg($_POST['page_body_content']);
		$_vals['link'] = $_POST['link'];
		$_vals['url'] = $_POST['url'];
		$values = $this->change_query_string($_vals);

		if($_POST['kind']=='new') {
			$this->query("insert $this->frame_table set $values"); // 추가
			$_POST['no'] = mysql_insert_id(); // xml 리턴을 위한 꼼수!
		}
		else $this->query("update $this->frame_table set $values where no=$_POST[no]"); // 수정
	}

	// 프레임 순위(bundle or position 치환) 변경
	function set_direction() {
		$rows = $this->queryFetch("select * from $this->frame_table where no=$_POST[no]");

		// 1차 순위가 바뀌는 경우 - bundle 값만 변경
		if($rows['depth']==1) {
			if($_POST['kind']=='up') $dest_bundle = $this->queryR("select max(bundle) from $this->frame_table where bundle<$rows[bundle]");
			else if($_POST['kind']=='down') $dest_bundle = $this->queryR("select min(bundle) from $this->frame_table where bundle>$rows[bundle]");
			$this->query("update $this->frame_table set bundle=0 where bundle=$dest_bundle");
			$this->query("update $this->frame_table set bundle=$dest_bundle where bundle=$rows[bundle]");
			$this->query("update $this->frame_table set bundle=$rows[bundle] where bundle=0");
		}
		// 2~3차 순위가 바뀌는 경우 - position 값만 변경
		else {
			if($_POST['kind']=='up') {
				$prev_rows = $this->queryFetch("select no, position from $this->frame_table where bundle=$rows[bundle] and depth=$rows[depth] and position<$rows[position] order by position desc limit 1");
				$position = $prev_rows['position'];

				// 대상 레코드 처리
				$this->query("update $this->frame_table set position=$position where no=$rows[no]");
				$n_datas = $this->query("select no from $this->frame_table where find_in_set($rows[no], parents) order by position");
				while($n_rows = $this->fetch($n_datas)) {
					$position++;
					$this->query("update $this->frame_table set position=$position where no=$n_rows[no]");
				}
				// 이웃하는 레코드 처리
				$position++;
				$this->query("update $this->frame_table set position=$position where no=$prev_rows[no]");
				$p_datas = $this->query("select no from $this->frame_table where find_in_set($prev_rows[no], parents) order by position");
				while($p_rows = $this->fetch($p_datas)) {
					$position++;
					$this->query("update $this->frame_table set position=$position where no=$p_rows[no]");
				}
			}
			else if($_POST['kind']=='down') {
				$next_rows = $this->queryFetch("select no, position from $this->frame_table where bundle=$rows[bundle] and depth=$rows[depth] and position>$rows[position] order by position limit 1");
				$position = $rows['position'];

				// 이웃하는 레코드 처리
				$this->query("update $this->frame_table set position=$position where no=$next_rows[no]");
				$p_datas = $this->query("select no from $this->frame_table where find_in_set($next_rows[no], parents) order by position");
				while($p_rows = $this->fetch($p_datas)) {
					$position++;
					$this->query("update $this->frame_table set position=$position where no=$p_rows[no]");
				}
				// 대상 레코드 처리
				$position++;
				$this->query("update $this->frame_table set position=$position where no=$rows[no]");
				$n_datas = $this->query("select no from $this->frame_table where find_in_set($rows[no], parents) order by position");
				while($n_rows = $this->fetch($n_datas)) {
					$position++;
					$this->query("update $this->frame_table set position=$position where no=$n_rows[no]");
				}
			}
		}
	}

	// 프레임 갱신 - POST - ajax
	function update_frame() {
		$prev_rows = $this->queryFetch("select * from $this->frame_table where no=$_POST[no]");
		switch($_POST['kind']) {
			case 'page': // 페이지별 디자인 설정
				$_vals['page_top_img'] = $_POST['page_top_img'];
				$_vals['page_title_type'] = $_POST['page_title_type'];
				if($_POST['on_page_title_img']) {
					$attach = new attachment('page_title');
					$_vals['page_title_img'] = $attach->save($_POST['on_page_title_img']);
					if($prev_rows['page_title_img']) {
						$attach->del($prev_rows['page_title_img']);
					}
				}
				$_vals['page_top_content'] = parent::trans_wysiwyg($_POST['page_top_content']);
				$_vals['page_bottom_content'] = parent::trans_wysiwyg($_POST['page_bottom_content']);
				$values = $this->change_query_string($_vals);
				$this->query("update $this->frame_table set $values where no=$_POST[no]");
				break;
		}
	}

	// 프레임 삭제
	function del_frame() {
		$rows = $this->queryFetch("select parents from $this->frame_table where no=$_POST[no]");
		$this->query("delete from $this->frame_table where no=$_POST[no] or find_in_set($_POST[no], parents)");

		// parent node 'has_child' update
		$childs = $this->queryR("select count(no) from $this->frame_table where parents='$rows[parents]'");
		if(!$childs) {
			$parent = array_pop(explode(',', $rows['parents']));
			$this->query("update $this->frame_table set has_child='no' where no=$parent");
		}
		// 삭제후 bundle & position 리셋팅@=== 필요시 구현
	}

}
?>