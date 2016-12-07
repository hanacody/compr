<?php
/**
 * 탭검 클래스
 */
class tab {
	var $pids = array();
	var $datas = array();

	function tab() {
		global $m_dir;
		include $m_dir.'design/top/pids.inc';
		$this->pids = $pids;
		foreach($this->pids as $pid=>$rows) {
			if($rows['use_gnb']=='yes' && $rows['used']=='yes') {
				$rows['pid'] = $pid;
				$this->datas[$rows['rank']] = $rows;
			}
		}
	}
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
	function draw($step, $entry) {
		$datas = array();
		if($step==1) {
			$times = $entry['times'];
			$on_drag = false;
			if($_SESSION['one'] && $_SESSION['one']>$times) {
				$on_drag = true;
				$entry['times'] += 1;
			}
			$count = 0;
			foreach($this->datas as $data) {
				if(++$count==$times && $on_drag) {
					array_push($datas, $this->datas[$_SESSION['one']]);
					$data['non_display'] = $entry['non_display'];
				}
				array_push($datas, $data);
				if($count==$times) break;
			}
		}
		else {
			$datas = $this->datas;
			foreach(range(1, $entry['times']) as $time) array_shift($datas);
		}
		$entry['on_parent'] = $this->parent();
		return fetch_contents($datas, $entry, array($this, '_c14'));
	}
	function _c14($bind) {
		extract($bind);
		if(($rows['depth']==1 && $_SESSION['one']==$rows['rank'])
			|| ($rows['depth']==2 && $_SESSION['two']==$rows['rank'] && $rows['parent']==$on_parent)) $rows['on'] = $on;
		return array($rows, $skin);
	}
}
?>