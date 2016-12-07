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
		$rows = $this->queryFetch("select * from $this->gallery_table".q(" where no=%d", $no));
		$rows['settings'] = $rows['settings'] ? unserialize($rows['settings']) : array();
		return $rows;
	}

	// 웹진형 콘텐츠 출력
	function print_contents($pno, $entry) {
		global $base_dir;
		$attach = new attachment('webzine', $base_dir.'gallery/admin/');
		$entry['folder'] = $attach->configs['save']['folder'];
		$datas = $this->query("select * from $this->webzine_table".q(" where pno=%d ORDER BY no", $pno));
		unset($attach);
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
		$rows['odd_even'] = $odd_even[($rank%2)];
		if(isset($content_length)) $rows['content'] = rankup_util::str_cut($rows['content'], $content_length);
		else $rows['content'] = nl2br($rows['content']);
		if(isset($on_end) && !($rank%$times)) $rows['on_end'] = $on_end;
		return array($rows, $skin);
	}

	// 컴포넌트 반환
	function get_components() {
		$datas = $this->query("select no, name from $this->gallery_table order by no");
		while($rows = $this->fetch($datas)) {
			$components[$rows['no']] = array(
				'name' => $rows['name'],
				'file' => null,
				'url' => 'gallery/index.html?no='.$rows['no']
			);
		}
		return $components;
	}
}
?>