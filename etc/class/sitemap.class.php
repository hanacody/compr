<?php
/**
 * 사이트맵 클래스
 *@author: kurokisi
 *@authDate: 2011.09.26
 */
class sitemap {
	var $pids = array();
	function __construct() {
		global $base_dir;
		include $base_dir.'design/top/pids.inc';
		$this->pids = $pids;
	}

	public function spoit($parent='') {
		$items = array();
		foreach($this->pids as $pid=>$rows) {
			if($rows['parent']==$parent && $rows['used']=='yes') {
				$rows['pid'] = $pid;
				$items[$rows['rank']] = $rows;
			}
		}
		return $items;
	}

	public function draw($entry) {
		$datas = $this->spoit('');
		return fetch_contents($datas, $entry, array($this, '_c24'));
	}
	public function _c24($bind) {
		global $gen;
		extract($bind);
		if($rows['has_child']=='yes') {
			$datas = $this->spoit($rows['pid']);
			if($rows['depth']==2) $child_entry['prank'] = $rank;
			$_childes = fetch_contents($datas, $child_entry, array($this, '_c24'));
			$rows['on_child'] = isset($on_child) ? fetch_skin(array('on_child' => $_childes), $on_child) : $_childes;
		}
		return array($rows, $skin);
	}
}
?>