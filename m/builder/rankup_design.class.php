<?php
/**
 * rankup Design class for mobile
 *@author: kurokisi
 *@authDate: 2011.11.03
 */
class rankup_design extends rankup_util {
	var $configs_table = 'rankup_mobile_config';

	function rankup_design() {
		parent::rankup_util();

	}

	// 설정값 반환
	function get_settings($item) {
		$rows = $this->queryFetch("select item, value from $this->configs_table where item='$item'");
		if(!$rows['item']) return null;
		switch($item) {
			case 'ready_content':
			case 'main_content':
				return $rows['value'];
				break;
			default:
				return $rows['value'] ? unserialize($rows['value']) : array();
		}
	}

	// 설정값 저장
	function set_settings($kind) {
		global $mobile;
		switch($kind) {
			case 'main': // 메인페이지 디자인설정
				$rows = $this->get_settings('main_design');
				if($rows!==null) $value = $rows;

				$value['main_use'] = $_POST['main_use'];
				$value['bg_type'] = $_POST['bg_type'];
				switch($_POST['bg_type']) {
					case 'color':
						$value['main_bg_type'] = $_POST['main_bg_type'];
						$value['main_bg_scolor'] = $_POST['main_bg_scolor'];
						$value['main_bg_gtype'] = $_POST['main_bg_gtype'];
						$value['main_bg_gcolor1'] = $_POST['main_bg_gcolor1'];
						$value['main_bg_gcolor2'] = $_POST['main_bg_gcolor2'];
						break;
					case 'skin':
						$value['main_bg_skin'] = $_POST['main_bg_skin'];
						break;
					case 'upload':
						if($_POST['on_main_bg']) {
							$attach = new attachment('main_bg', $mobile->m_dir.'builder/');
							if($rows!==null && $rows['menu_bg']) $attach->del($rows['main_bg']);
							$value['main_bg'] = $attach->save($_POST['on_main_bg']);
						}
						break;
				}
				$value['design_type'] = $_POST['design_type'];
				if($_POST['design_type']=='basic') {
					$value['icon_qty'] = $_POST['icon_qty'];
					$value['vertical_align'] = $_POST['vertical_align'];

					$value['col_interval'] = $_POST['col_interval'];
					$value['row_interval'] = $_POST['row_interval'];
					$value['icon_type'] = $_POST['icon_type'];
					$value['quick_pids'] = implode(',', $_POST['pids']);
					$attach = new attachment('quick_icon', $mobile->m_dir.'builder/');
					foreach($_POST['on_icons'] as $pid=>$file_name) {
						if(!$file_name) continue;
						if($value['quick_icons'][$pid]) $attach->del($value['quick_icons'][$pid]);
						$value['quick_icons'][$pid] = $attach->save($file_name);
					}
				}
				else {
					// 사용자제작
					$_val['item'] = 'main_content';
					$_val['value'] = parent::trans_wysiwyg($_POST['main_content']);
					$values = $this->change_query_string($_val);
					if($rows!==null) $this->query("update $this->configs_table set $values where item='main_content'");
					else $this->query("insert $this->configs_table set $values");
				}

				$_vals['item'] = 'main_design';
				$_vals['value'] = serialize($value);
				$values = $this->change_query_string($_vals);
				if($rows!==null) $this->query("update $this->configs_table set $values where item='main_design'");
				else $this->query("insert $this->configs_table set $values");
				break;

			case 'site': // 사이트디자인 설정
				$rows = $this->get_settings('site_design');
				if($rows!==null) $value = $rows;

				$value['bg_type'] = $_POST['bg_type'];
				switch($_POST['bg_type']) {
					case 'color':
						$value['site_bg_type'] = $_POST['site_bg_type'];
						$value['site_bg_scolor'] = $_POST['site_bg_scolor'];
						$value['site_bg_gtype'] = $_POST['site_bg_gtype'];
						$value['site_bg_gcolor1'] = $_POST['site_bg_gcolor1'];
						$value['site_bg_gcolor2'] = $_POST['site_bg_gcolor2'];
						break;
					case 'skin':
						$value['site_bg_skin'] = $_POST['site_bg_skin'];
						break;
					case 'upload':
						if($_POST['on_site_bg']) {
							$attach = new attachment('site_bg', $mobile->m_dir.'builder/');
							if($rows!==null && $rows['menu_bg']) $attach->del($rows['site_bg']);
							$value['site_bg'] = $attach->save($_POST['on_site_bg']);
						}
						break;
				}
				$value['nav_bgcolor'] = $_POST['nav_bgcolor'];
				$value['frame_color'] = $_POST['frame_color'];
				$value['menu_height'] = $_POST['menu_height'];
				$value['menu_qty'] = $_POST['menu_qty'];

				$value['menu_type'] = $_POST['menu_type'];
				$value['menu_off_bgcolor'] = $_POST['menu_off_bgcolor'];
				$value['menu_on_bgcolor'] = $_POST['menu_on_bgcolor'];
				if($value['menu_type']=='text') {
					$value['menu_off_color'] = $_POST['menu_off_color'];
					$value['menu_on_color'] = $_POST['menu_on_color'];
				}
				else { // 이미지인경우
					$attach = new attachment('gnb_text', $mobile->m_dir.'builder/');
					foreach($_POST['on_gnbs'] as $pid=>$file_name) {
						if(!$file_name) continue;
						if($value['gnb_texts'][$pid]) $attach->del($value['gnb_texts'][$pid]);
						$value['gnb_texts'][$pid] = $attach->save($file_name);
					}
				}
				$_vals['item'] = 'site_design';
				$_vals['value'] = serialize($value);
				$values = $this->change_query_string($_vals);
				if($rows!==null) $this->query("update $this->configs_table set $values where item='site_design'");
				else $this->query("insert $this->configs_table set $values");
				break;
		}
	}

	// 지정수로 배열 자르기
	function array_split($array, $qty) {
		if(count($array)<=$qty) return $array;
		$key = 0;
		while(1) {
			$_new = array();
			foreach(range(0, $qty-1) as $i) {
				$val = array_shift($array);
				if($val) array_push($_new, $val);
			}
			$new[$key++] = $_new;
			if(!count($array)) break;
		}
		return $new;
	}

	// 메인페이지 퀵메뉴 출력
	function quick_draw($ds_rows, $entry) {
		global $m_dir;
		include $m_dir.'design/top/pids.inc';
		$quick_pids = array();
		foreach(explode(',', $ds_rows['quick_pids']) as $_pid) {
			if($pids[$_pid]['used']=='yes') array_push($quick_pids, $_pid);
		}
		$quick_pids = $this->array_split($quick_pids, $ds_rows['icon_qty']);
		if(is_array($quick_pids)) {
			foreach($quick_pids as $rank=>$_pids) {
				$datas = array();
				if(!is_array($_pids)) continue; // 2012.04.02 added
				foreach($_pids as $pid) {
					$base_name = $pids[$pid]['base_name'];
					array_push($datas, compact('pid', 'base_name'));
				}
				$entry['attaches'] = $ds_rows['quick_icons'];
				if($rank) $entry['entry_wrap'][0] = $entry['entry_wrap'][2];
				$contents .= fetch_contents($datas, $entry, array($this, '_d120'));
			}
		}
		return $contents;
	}
	function _d120($bind) {
		extract($bind);
		if($rank==1) $rows['first'] = $first;
		$rows['attach'] = $attaches[$rows['pid']];
		if($ds_rows['icon_type']=='image') $rows['off_text'] = $off_text;;
		return array($rows, $skin);
	}
}

?>