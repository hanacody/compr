<?php
/*
 * 랭크업 포멀라이즈 클래스 v1.0
 *@author: kurokisi
 *@authDate: 2009.09.25
 *@latestDate: 2012.02.29
 */
class rankup_formalize extends rankup_util {
	var $version = '1.1 r120229';
	function rankup_formalize() {
		parent::rankup_util();
	}
	// 스킨 치환 및 반환
	function fetch_skin($rows, $skin) {
		if(is_array($skin)) $skin = implode('', $skin);
		preg_match_all('/{:(.*?):}/', $skin, $pattern);
		if(parent::chkRes($pattern[1])) {
			$infos = array();
			foreach(array_unique($pattern[1]) as $field) $infos[] = $rows[$field];
			$skin = str_replace(array_unique($pattern[0]), $infos, $skin);
		}
		return $skin;
	}
	// 콘텐츠 치환 및 반환
	function fetch_contents($datas, $bind='', $func='') {
		if($bind) {
			if(!is_array($bind)) $bind = array('entry'=>array(1=>$bind));
			extract($bind);
		}
		if(!isset($times)) $times = 1;
		if(is_array($title)) $title = implode('', $title);
		if($title) {
			$titlebar = '';
			foreach(range(1, $times) as $time) {
				if($titlebar && is_array($entry) && $entry[3]) $titlebar .= $entry[3];
				$titlebar .= $title;
			}
			$titlebars = $title_wrap[0].$titlebar.$title_wrap[1];
		}
		$contents = '';
		$loops = is_resource($datas) ? mysql_num_rows($datas) : count($datas);
		if(!$loops && is_array($entry) && isset($entry[0])) { // empty data
			foreach(range(1, $times) as $time) {
				if($contents && is_array($entry) && $entry[3]) $contents .= $entry[3];
				$contents .= rankup_formalize::fetch_skin($rows, (is_array($entry) ? $entry[0] : $entry));
			}
			return $titlebars ? array($titlebars, $contents) : $contents;
		}
		$rank = 0;
		if(isset($entry_wrap)) $entry_wrap_count = count($entry_wrap); // 2012.02.29 added
		foreach(range(1, $loops) as $loop) {
			$content = '';
			foreach(range(1, $times) as $time) {
				$rows = is_resource($datas) ? parent::fetch($datas) : array_shift($datas); // point move
				$on_record = parent::chkRes($rows);
				$skin = is_array($entry) ? $entry[$on_record] : $entry; // skin extract
				$end_flag = !$on_record;
				if($on_record && $func) {
					$rank++; // rank counting
					$bind = array_merge($bind, compact('row', 'rank', 'skin', 'rows'));
					list($rows, $skin) = call_user_func($func, $bind); // data control
					if(isset($row)) $row--; // row counting
				}
				if($on_record || $content) {
					if($content && is_array($entry) && $entry[3]) $content .= $entry[3]; // add vertical entry
					$content .= rankup_formalize::fetch_skin($rows, $skin);
				}
			}
			if($content) {
				if($contents && is_array($entry) && $entry[2]) $contents .= $entry[2]; // add horizontal entry
				if(($end_flag||$loops==$rank) && $entry_wrap_count>2) {
					$contents .= $entry_wrap[2].$content.( isset($entry_wrap[3]) ? $entry_wrap[3] : $entry_wrap[1] ); // 2012.02.29 added
				}
				else $contents .= $entry_wrap[0].$content.$entry_wrap[1];
			}
			if($end_flag==true) break;
		}
		if(is_resource($datas)) @mysql_data_seek($datas, 0); // 2012.02.07 added
		return $titlebars ? array($titlebars, $contents) : $contents;
	}
}
?>