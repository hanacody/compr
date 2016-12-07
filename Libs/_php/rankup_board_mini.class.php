<?php
/**
 * 게시판 미니버전 V1.0
 *@author: kurokisi
 *@authDate: 2011.08.30
 */
class rankup_board_mini extends rankup_util {
	var $bconfig_table = 'rankup_board_config';

	var $board_prefix = "rankup_board_";
	var $comment_prefix = "rankup_board_comment_";
	var $index_name = "board";

	function rankup_board_mini() {
		parent::rankup_util();
	}

	// 게시판 리스트 반환
	function print_boards($entry) {
		$datas = $this->query("select * from $this->bconfig_table where id!='_sample_' order by rank");
		return fetch_contents($datas, $entry);
	}

	// 게시판 이름 반환
	function get_board_names($bid, $entry) {
		$datas = $this->query("select id, name from $this->bconfig_table where id in('$bid')");
		return fetch_contents($datas, $entry);
	}

	// 최신글 반환
	function print_recent_articles($bid, $entry) {
		$board_table = $this->board_prefix.$bid;
		$entry['board_id'] = $bid; // keep
		$datas = $this->query("select * from $board_table order by sno, gno limit 0, ".$entry['times']);
		if(!mysql_num_rows($datas)) $datas = array();
		return fetch_contents($datas, $entry, array($this, '_bm13'));
	}
	function _bm13($bind) {
		global $base_url, $base_dir;
		extract($bind);
		if($subject_length) $rows['subject'] = parent::str_cut($rows['subject'], $subject_length);
		if($date_format) $rows['wdate'] = date($date_format, strtotime($rows['wdate']));
		if($on_photo) {
			$files = unserialize($rows['attach']);
			$photo = $non_photo;
			if(is_array($files)) {
				$path = 'rankup_module/rankup_board/attach/'.$board_id.'/';
				foreach($files as $file) {
					$_file = $base_dir.$path.$file['sname'];
					if(is_file($_file)) {
						$mime = mime_content_type($_file);
						if(!preg_match('/image\//', $mime)) continue;

						// 썸네일 확인
						$_thumb = $base_dir.$path.'thumb_'.$file['sname'];
						if(is_file($_thumb)) {
							$photo = $base_url.$path.'thumb_'.$file['sname'];
							break;
						}
						if($photo==$non_photo) $photo = $base_url.$path.$file['sname'];
						break;
					}
				}
			}
			$rows['on_photo'] = fetch_skin(compact('photo'), $on_photo);
			if($times==$rank) $rows['on_end'] = $on_end;
		}
		return array($rows, $skin);
	}

	// 컴포넌트 반환
	function get_components() {
		$datas = $this->query("select id, name from $this->bconfig_table where id!='_sample_' and uval='yes' order by rank");
		while($rows = $this->fetch($datas)) {
			$components[$rows['id']] = array(
				'name' => $rows['name'],
				'file' => null,
				'url' => 'board/index.html?id='.$rows['id']
			);
		}
		return $components;
	}

}

?>