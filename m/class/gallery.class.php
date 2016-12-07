<?php
/**
 * 갤러리 관리 클래스
 *@author: kurokisi
 *@authDate: 2011.09.16
 */

class gallery extends rankup_util {

	var $gallery_table = 'rankup_gallery';
	var $webzine_table = 'rankup_gallery_webzine';

	function gallery() {
		parent::rankup_util();

	}

	// 갤러리 정보 반환
	function get_gallery($no) {
		$rows = $this->queryFetch("select * from $this->gallery_table where no=$no");
		$rows['settings'] = $rows['settings'] ? unserialize($rows['settings']) : array();
		return $rows;
	}

	// 웹진형 콘텐츠 출력
	function print_contents($pno, $entry) {
		$datas = $this->query("select * from $this->webzine_table where pno=$pno order by no");
		return fetch_contents($datas, $entry, array($this, '_g26'));
	}
	function _g26($bind) {
		global $base_url, $base_dir;
		extract($bind);
		if($rows['attach'] && is_file($base_dir.$folder.$rows['attach'])) {
			$rows['on_attach'] = fetch_skin(array('attach' => $base_url.$folder.$rows['attach']), $on_attach);
		}
		else {
			$rows['on_attach'] = $non_attach;
		}
		if(isset($on_active) && $value==$rank) $rows['on_active'] = $on_active;
		$rows['odd_even'] = $odd_even[($rank%2)];
		if(isset($content_length)) $rows['content'] = rankup_util::str_cut($rows['content'], $content_length);
		else $rows['content'] = nl2br($rows['content']);
		return array($rows, $skin);
	}

	// 웹진형 콘텐츠 반환
	function get_webzine($pno, $no) {
		$datas = $this->query("select * from $this->webzine_table where pno=$pno order by no");
		$totals = mysql_num_rows($datas);
		$rank = 0;
		$next_no = $prev_no = '';
		while($rows = $this->fetch($datas)) {
			$rank++;
			if($rows['no']==$no) {
				$infos = $rows;
				$next_infos = $this->fetch($datas);
				$next_no = $next_infos ? $next_infos['no'] : '';
				break;
			}
			else {
				$prev_no = $rows['no'];
			}
		}
		return array($totals, $rank, $infos, $prev_no, $next_no);
	}
	function load_webzine($entry) {
		$datas = $this->query("select * from $this->webzine_table where no=$_POST[no] order by no");
		return fetch_contents($datas, $entry);
	}

	// 컴포넌트 반환
	function get_components() {
		global $mobile;
		$datas = $this->query("select no, name from $this->gallery_table order by no");
		while($rows = $this->fetch($datas)) {
			$components[$rows['no']] = array(
				'name' => $rows['name'],
				'file' => null,
				'url' => $mobile->m_folder.'/gallery/index.html?no='.$rows['no']
			);
		}
		return $components;
	}

}
?>