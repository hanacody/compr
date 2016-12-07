<?php
/**
 * 갤러리 관리 클래스
 *@author: kurokisi
 *@authDate: 2011.09.16
 */

class gallery_admin extends gallery {

	function gallery_admin() {
		parent::gallery();

	}

	// 갤러리 목록 출력
	function print_contents($entry) {
		$datas = $this->query("select * from $this->gallery_table order by no desc");
		return fetch_contents($datas, $entry, array($this, '_g17'));
	}
	function _g17($bind) {
		extract($bind);
		if(isset($type_texts)) $rows['type_text'] = $type_texts[$rows['type']];
		if(isset($skin_type_texts)) $rows['skin_type_text'] = $skin_type_texts[$rows['skin_type']];
		if(isset($time_format)) $rows['regist_time'] = date($time_format, strtotime($rows['regist_time']));
		return array($rows, $skin);
	}

	// 웹진 정보 반환
	function load_webzine($no, $entry) {
		$datas = $this->query("select * from $this->webzine_table".q(" where no=%d order by no", $no));
		return fetch_contents($datas, $entry);
	}

	// 웹진 목록 출력
	function print_webzines($pno, $entry) {
		if(!$pno) $datas = array();
		else $datas = $this->query("select * from $this->webzine_table".q(" where pno=%d order by no", $pno));
		return fetch_contents($datas, $entry, array($this, '_g19'));
	}
	function _g19($bind) {
		extract($bind);
		$rows['rank'] = $rank;
		if($rows['attach']) $rows['on_attach'] = fetch_skin($rows, $on_attach);
		$rows['content'] = nl2br($rows['content']);
		return array($rows, $skin);
	}

	// 저장
	function save() {
		$_vals['name'] = $_POST['name'];
		$_vals['type'] = $_POST['gallery_type'];
		$_vals['skin_type'] = $_POST['skin_type'];

		// 갤러리형
		if($_POST['gallery_type']=='gallery') {
			// 갤러리사진
			$rows = $_POST['no'] ? $this->get_gallery($_POST['no']) : null;
			if($rows['settings']) $value = $rows['settings'];
			if(count($_POST['on_gfiles'])) {
				$attach = new attachment('gallery');
				$on_gfiles = $attach->save(implode(',', $_POST['on_gfiles']));
				$value['attach'] = $rows['settings']['attach'] ? $rows['settings']['attach'].','.$on_gfiles : $on_gfiles;
				$_vals['qty'] = count(explode(',', $value['attach'])); // 이미지 등록 개수
			}
			$value['image_motion'] = $_POST['image_motion'];
			/* image list */
			$value['image_list_use'] = $_POST['image_list_use'];
			$value['image_list_kind'] = $_POST['image_list_kind'];
			$value['image_list_top'] = $_POST['image_list_top'];
			$value['image_list_left'] = $_POST['image_list_left'];
			$value['image_list_opacity'] = $_POST['image_list_opacity'];
			/* text */
			$value['text_motion'] = $_POST['text_motion'];
			$value['top_text'] = $_POST['top_text'];
			$value['top_text_size'] = $_POST['top_text_size'];
			$value['top_text_color'] = $_POST['top_text_color'];
			$value['middle_text'] = $_POST['middle_text'];
			$value['middle_text_size'] = $_POST['middle_text_size'];
			$value['middle_text_color'] = $_POST['middle_text_color'];
			$value['bottom_text'] = $_POST['bottom_text'];
			$value['bottom_text_size'] = $_POST['bottom_text_size'];
			$value['bottom_text_color'] = $_POST['bottom_text_color'];
			$value['text_container_top'] = $_POST['text_container_top'];
			$value['text_container_left'] = $_POST['text_container_left'];
			$value['text_container_opacity'] = $_POST['text_container_opacity'];
			/* 문구배경 */
			if($_POST['on_text_container_bg']) {
				$attach = new attachment('gtext_container_bg');
				if($rows!==null && $rows['settings']['text_container_bg']) $attach->del($rows['settings']['text_container_bg']);
				$value['text_container_bg'] = $attach->save($_POST['on_text_container_bg']);
			}
			$_vals['settings'] = serialize($value);
			if($_POST['no']) {
				$values = $this->change_query_string($_vals);
				$this->query("update $this->gallery_table set $values".q(" where no=%d", $_POST['no']));
			}
			else {
				$_vals['regist_time'] = date('Y-m-d H:i:s'); // 등록일시
				$values = $this->change_query_string($_vals);
				$this->query("insert $this->gallery_table set $values");
				$_POST['no'] = mysql_insert_id();
			}
		}
		// 웹진형
		else {
			// 기본정보 저장
			if($_POST['no']) {
				$values = $this->change_query_string($_vals);
				$this->query("update $this->gallery_table set $values".q(" where no=%d", $_POST['no']));
			}
			else {
				$_vals['regist_time'] = date('Y-m-d H:i:s');
				$values = $this->change_query_string($_vals);
				$this->query("insert $this->gallery_table set $values");
				$_POST['no'] = mysql_insert_id();
			}
			// extend 정보 저장
			$rows = $_POST['wno'] ? $this->queryFetch("select * from $this->webzine_table".q(" where no=%d", $_POST['wno'])) : null;
			$value['subject'] = strip_tags($_POST['subject']);
			$value['content'] = strip_tags($_POST['content']);
			if($_POST['on_webzine'] && $rows['attach']!=$_POST['on_webzine']) {
				$attach = new attachment('webzine');
				if($rows!==null && $rows['attach']) $attach->del($rows['attach']);
				$value['attach'] = $attach->save($_POST['on_webzine']);
			}
			if($_POST['wno']) {
				$values = $this->change_query_string($value);
				$this->query("update $this->webzine_table set $values".q(" where no=%d", $_POST['wno']));
			}
			else {
				$value['pno'] = $_POST['no'];
				$values = $this->change_query_string($value);
				$this->query("insert $this->webzine_table set $values");
			}
			// qty update!
			$qty = $this->queryR("select count(no) from $this->webzine_table".q(" where pno=%d", $_POST['no']));
			$this->query("update $this->gallery_table set qty=$qty".q(" where no=%d", $_POST['no']));
		}
	}

	// 갤러리 명 변경
	function set_name() {
		$values = $this->change_query_string(array('name' => $_POST['name']));
		$this->query("update $this->gallery_table set $values".q(" where no=%d", $_POST['no']));
	}

	// 웹진 스킨 설정
	function set_webzine_skin() {
		$this->query("update $this->gallery_table".q(" set skin_type='%s' where no=%d", $_POST['skin'], $_POST['no']));
	}

	// 갤러리 이미지 정보 갱신
	function update_attach() {
		$rows = $this->get_gallery($_POST['index']);
		$value = $rows['settings'];

		if($_POST['kind']=='gallery') {
			$attaches = array_diff(explode(',', $value['attach']), array('', null, $_POST['name']));
			$value['attach'] = implode(',', $attaches);
			$_vals['qty'] = count($attaches);
		}
		else if($_POST['kind']=='gtext_container_bg') {
			$value['text_container_bg'] = '';
		}

		$_vals['settings'] = serialize($value);
		$values = $this->change_query_string($_vals);
		$this->query("update $this->gallery_table set $values".q(" where no=%d", $_POST['index']));
	}

	// 웹진 삭제
	function del_webzine($no) {
		$where = q(" where no=%d", $no);
		$rows = $this->queryFetch("select * from $this->webzine_table".$where);
		if($rows['attach']) {
			$attach = new attachment('webzine');
			$attach->del($rows['attach']);
		}
		$this->query("delete from $this->webzine_table".$where);
		$this->query("update $this->gallery_table set qty=qty-1 where no=$rows[pno]");
	}

	// 갤러리 정보 삭제
	function del($nos) {
		global $base_dir;

		$attach = new attachment;
		$folder1 = $attach->presets['gallery']['save']['folder'];
		$folder2 = $attach->presets['gtext_container_bg']['save']['folder'];
		$folder3 = $attach->presets['webzine']['save']['folder'];

		$nos = str_replace('__', ',', $nos);
		$datas = $this->query("select * from $this->gallery_table where no in($nos)");
		while($rows = $this->fetch($datas)) {
			if($rows['type']=='gallery') {
				// 갤러리 이미지 삭제
				$settings = unserialize($rows['settings']);
				if($settings['attach']) {
					foreach(explode(',', $settings['attach']) as $file_name) {
						if(is_file($base_dir.$folder1.$file_name)) unlink($base_dir.$folder1.$file_name);
					}
				}
				// 텍스트 배경이미지 삭제
				if($settings['text_container_bg'] && is_file($base_dir.$folder2.$settings['text_container_bg'])) {
					unlink($base_dir.$folder2.$settings['text_container_bg']);
				}
				// 갤러리 xml 데이터 삭제
				$xml_file = $base_dir.'design/xml/gallery_'.$rows['no'].'.xml';
				if(is_file($xml_file)) unlink($xml_file);
			}
			else {
				// 갤러리 이미지 삭제
				$_datas = $this->query("select * from $this->webzine_table where pno=$rows[no]");
				while($_rows = $this->fetch($_datas)) {
					if($_rows['attach'] && is_file($base_dir.$folder3.$_rows['attach'])) {
						unlink($base_dir.$folder3.$_rows['attach']);
					}
				}
			}
		}
		$this->query("delete from $this->gallery_table where no in($nos)");
		$this->query("delete from $this->webzine_table where pno in($nos)");
	}
}
?>