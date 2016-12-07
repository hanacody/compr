<?php
/**
 * 게시판 미니버전 for Mobile V1.0
 *@author: kurokisi
 *@authDate: 2011.08.30
 */
class rankup_board_mini extends rankup_util {
	var $bconfig_table = 'rankup_board_config';

	var $board_prefix = 'rankup_board_';
	var $comment_prefix = 'rankup_board_comment_';
	var $index_name = 'board';

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
		}
		return array($rows, $skin);
	}

	// 리스트 출력
	function print_contents($entry, $limits=15) {
		$sque = $this->get_query_point($_GET['page'], $limits);

		$board = new rankup_board($_GET['id']); // 2010.05.27 fixed
		if(!$board->check_granted('list_level')) $this->popup_msg_js($board->get_granted_messages('list_level'), 'BACK');
		$entry['board'] = $board;

		$table_name = $this->board_prefix.$_GET['id'];
		$totals = $this->queryRows("select no from $table_name");
		$datas = $this->query("select * from $table_name order by sno, gno, pno limit $sque, $limits");
		$contents = fetch_contents($datas, $entry, array($this, '_r354'));
		return array($totals, $contents);
	}
	function _r354($bind) {
		global $member_info;
		extract($bind);
		$rows['id'] = $_GET['id'];
		if(isset($date_format)) $rows['wdate'] = date($date_format, strtotime($rows['wdate']));

		if($rows['sno']<=$board->notice_sno) $rows['notice'] = $on_notice;
		if($rows['pno']!=0) {
			$left = $rows['pno'] * 15;
			$padding = ($rows['pno']+1) * 15;
			$rows['on_reply'] = fetch_skin(compact('left', 'padding'), $on_reply);
		}

		$rows['link'] = fetch_skin($rows, $on_readable);
		if($rows['sval']=='yes') $rows['secret_icon'] = $secret_icon;
		if($rows['sval']=='yes' && !$board->is_seeable($rows['no'])) {
			if(!$board->member_id || ($board->member_id && $rows['uid']!==$board->member_id && $board->member_id !== $read_writer)) {
				if(!$board->check_granted('secret_level')) $rows['link'] = $on_secret;
			}
		}
		if($board->soption['use_new_icon']=='on' && date('Y-m-d H:i:s', strtotime("-{$board->soption['recent_time']} hour"))<=$rows['wdate']) $rows['new_icon'] = $new_icon;

		$rows['cnum'] = $rows['cnum'] ? fetch_skin($rows, $on_cnum) : '';
		return array($rows, $skin);
	}

	// 상세내용 반환
	function get_infos($no) {
		global $board;

		// 게시물 상세정보
		$board_infos = $board->queryFetchObject("select * from $board->board_table where no=$no");

		// 내가 쓴글에 답글이 달린경우
		if($board_infos->pno > 0 && $board_infos->sval=="yes") $read_writer = $board->queryR("select uid from $board->board_table where sno = $board_infos->sno and pno = 0 and uid='$board->member_id'");
		else $read_writer = null;

		// 접근 권한체크
		if(!$board->is_member() || ($board->is_member() && $board_infos->uid!==$board->member_id && $board->member_id !== $read_writer)) {
			if(!$board->is_seeable($no) && !$board->check_granted("read_level")) $board->popup_msg_js($board->get_granted_messages('read_level'), "BACK");
		}
		if(empty($board_infos->no)) $board->popup_msg_js("요청하신 게시물은 존재 하지 않습니다.", "BACK");
		if($board_infos->dval=="yes" && !$board->is_admin()) $board->popup_msg_js("요청하신 게시물은 삭제된 게시물입니다.", "BACK");
		if($board_infos->sval=="yes" && !$board->is_seeable($no)) {
			if(!$board->member_id || ($board->member_id && $board_infos->uid!==$board->member_id && $board->member_id !== $read_writer)) {
				if(!$board->check_granted("secret_level")) $board->popup_msg_js($board->get_granted_messages('secret_level'), "BACK");
			}
		}

		// 조회수 갱신 - 관리자가 아닌경우에만 처리
		if($board->sfunction['use_duplicate_hit']=="on") $board->increase_readcount($board_infos, true);
		else if(!$board->is_admin()) $board->increase_readcount($board_infos);

		$board_infos->wdate = date('Y.m.d', strtotime($board_infos->wdate));
		return $board_infos;
	}

	// 댓글 반환
	function print_comments($ano, $entry) {
		global $board;
		$datas = $this->query("select * from $board->board_comment_table where ano=$ano");
		return fetch_contents($datas, $entry);
	}

	// 컴포넌트 반환
	function get_components() {
		global $mobile;
		$datas = $this->query("select id, name from $this->bconfig_table where id!='_sample_' and uval='yes' order by rank");
		while($rows = $this->fetch($datas)) {
			$components[$rows['id']] = array(
				'name' => $rows['name'],
				'file' => null,
				'url' => $mobile->m_folder.'/board/index.html?id='.$rows['id']
			);
		}
		return $components;
	}

}

?>