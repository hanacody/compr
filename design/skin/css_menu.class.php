<?php
/**
 * PID 기반 CSS 메뉴 처리 클래스
 *@author: kurokisi
 *@authDate: 2011.09.23
 */
class css_menu {
	var $pids = array();
	function css_menu() {
		global $base_dir;
		include $base_dir.'design/top/pids.inc';
		$this->pids = $pids;
	}
	/**
	 * GNB 처리
	 */
	function parent() {
		$node = $this->pids[$_GET['pid']];
		if($node['parent']) {
			if($this->pids[$node['parent']]['parent']) $node = $this->pids[$node['parent']];
			return $node['parent'];
		}
		else if($_GET['pid']) {
			return $_GET['pid'];
		}
		return null;
	}
	function draw_gnb($depth, $entry) {
		$items = array();
		foreach($this->pids as $pid=>$rows) {
			if($rows['depth']==$depth && $rows['use_gnb']=='yes' && $rows['used']=='yes') {
				$rows['pid'] = $pid;
				$items[$rows['parent']][$rows['rank']] = $rows;
			}
		}
		$contents = '';
		$entry['on_parent'] = $this->parent();
		foreach($items as $parent=>$datas) {
			$display = ($entry['on_parent']==$parent) ? '' : 'none';
			$content = fetch_contents($datas, $entry, array($this, '_c14'));
			if($content) $contents .= fetch_skin(compact('parent', 'content', 'display'), $entry['wrap']);
		}
		return $contents;
	}
	function _c14($bind) {
		extract($bind);
		if($rank==1) $rows['on_first'] = $on_first;
		if(($rows['depth']==1 && $_SESSION['one']==$rows['rank'])
			|| ($rows['depth']==2 && $_SESSION['two']==$rows['rank'] && $rows['parent']==$on_parent)) $rows['on_hover'] = $on_hover;
		return array($rows, $skin);
	}
	/**
	 * LNB 처리
	 */
	function spoit($parent='', $on_pid='') {
		$items = array();
		foreach($this->pids as $pid=>$rows) {
			if($on_pid && $on_pid!=$pid) continue;
			if($rows['parent']==$parent && $rows['used']=='yes') {
				$rows['pid'] = $pid;
				$items[$rows['rank']] = $rows;
			}
		}
		return $items;
	}
	function draw_lnb($entry) {
		$datas = $this->spoit('', $this->parent());
		return fetch_contents($datas, $entry, array($this, '_c65'));
	}
	function _c65($bind) {
		global $gen;
		extract($bind);
		if($rows['depth']==2 && $_SESSION['two']==$rows['rank']) $rows['on_hover'] = $on_hover;
		else if($rows['depth']==3 && $_SESSION['two']==$prank &&
			($rows['pid']==$gen->infos['no'] ||
				($gen->infos['component'] && $gen->infos['component']==$rows['component'] && $rows['options']==$get->infos['options'])
			)
		) {
			$rows['on_hover'] = $on_hover;
		}
		if($rows['has_child']=='yes') {
			$datas = $this->spoit($rows['pid']);
			if($rows['depth']==2) $child_entry['prank'] = $rank;
			$_childes = fetch_contents($datas, $child_entry, array($this, '_c65'));
			$rows['on_child'] = isset($on_child) ? fetch_skin(array('on_child' => $_childes, 'parent'=>$rows['pid']), $on_child) : $_childes;
		}
		return array($rows, $skin);
	}
}
?>