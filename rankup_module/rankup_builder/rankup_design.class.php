<?php
/**
 * rankup Design class
 *@author: kurokisi
 *@authDate: 2011.08.23
 */
class rankup_design extends rankup_util {
	var $configs_table = 'rankup_configs';

	function rankup_design() {
		parent::rankup_util();

	}

	// 설정값 반환
	function get_settings($item) {
		$rows = $this->queryFetch("select item, value from $this->configs_table where item='$item'");
		if(!$rows['item']) return null;
		switch($item) {
			case 'main_bottom_content':
			case 'ready_content':
			case 'intro_content':
				return $rows['value'];
				break;
			default:
				return $rows['value'] ? unserialize($rows['value']) : array();
		}
	}

	// 설정값 저장
	function set_settings($kind) {
		switch($kind) {
			case 'main': // 메인페이지 디자인설정
				$rows = $this->get_settings('main_visual_design');
				if($rows!==null) $value = $rows;
				if($_POST['flash_type']=='basic') {
					$value['flash_type'] = $_POST['flash_type'];
					$value['container_width'] = $_POST['container_width'];
					$value['container_height'] = $_POST['container_height'];
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

					if($_POST['on_text_container_bg']) {
						$attach = new attachment('text_container_bg');
						if($rows!==null && $rows['text_container_bg']) $attach->del($rows['text_container_bg']);
						$value['text_container_bg'] = $attach->save($_POST['on_text_container_bg']);
					}
				}
				else {
					$value['flash_type'] = $_POST['flash_type'];
					if($_POST['on_main_flash']) {
						$attach = new attachment('main_flash');
						if($rows!==null && $rows['main_flash']) $attach->del($rows['main_flash']);
						$value['main_flash'] = $attach->save($_POST['on_main_flash']);
					}
				}
				$_vals['item'] = 'main_visual_design';
				$_vals['value'] = serialize($value);
				$values = $this->change_query_string($_vals);
				if($rows!==null) $this->query("update $this->configs_table set $values where item='main_visual_design'");
				else $this->query("insert $this->configs_table set $values");
				unset($value, $_vals);

				// 메인비쥬얼 배경
				$rows = $this->get_settings('main_visual_bg');
				if($rows!==null) $value = $rows;
				$value['bg_type'] = $_POST['bg_type'];
				switch($_POST['bg_type']) {
					case 'color':
						$value['visual_bg_type'] = $_POST['visual_bg_type'];
						$value['visual_bg_scolor'] = $_POST['visual_bg_scolor'];
						$value['visual_bg_gtype'] = $_POST['visual_bg_gtype'];
						$value['visual_bg_gcolor1'] = $_POST['visual_bg_gcolor1'];
						$value['visual_bg_gcolor2'] = $_POST['visual_bg_gcolor2'];
						break;
					case 'skin':
						$value['visual_bg_skin'] = $_POST['visual_bg_skin'];
						break;
					case 'image':
						if($_POST['on_visual_bg']) {
							$attach = new attachment('visual_bg');
							if($rows!==null && $rows['visual_bg']) $attach->del($rows['visual_bg']);
							$value['visual_bg'] = $attach->save($_POST['on_visual_bg']);
						}
						break;
				}
				$_vals['item'] = 'main_visual_bg';
				$_vals['value'] = serialize($value);
				$values = $this->change_query_string($_vals);
				if($rows!==null) $this->query("update $this->configs_table set $values where item='main_visual_bg'");
				else $this->query("insert $this->configs_table set $values");
				unset($_vals);

				// 메인 아웃로그인 설정
				$this->apply_config('main_outlogin', array('value' => serialize(array(
					'use_outlogin' => $_POST['use_outlogin'],
					'outlogin_top' => $_POST['outlogin_top'],
					'outlogin_left' => $_POST['outlogin_left']
				))));

				// 메인컨텐츠 하단 디자인
				$this->apply_config('main_bottom_content', array('value' => parent::trans_wysiwyg($_POST['main_bottom_content'])));
				break;

			case 'remove_text_container_bg': // 문구전체 배경 제거
				$value = $this->get_settings('main_visual_design');
				$value['text_container_bg'] = '';
				$_vals['value'] = serialize($value);
				$values = $this->change_query_string($_vals);
				$this->query("update $this->configs_table set $values where item='main_visual_design'");
				break;

			case 'top': // 상단메뉴설정
				$rows = $this->get_settings('top_menu_design');
				if($rows!==null) $value = $rows;

				$value['gnb_type'] = $_POST['gnb_type'];

				if($_POST['gnb_type']=='upload') {
					$value['container_width'] = $_POST['container_width'];
					$value['container_height'] = $_POST['container_height'];
					if($_POST['on_top_flash']) {
						$attach = new attachment('top_flash');
						if($rows!==null && $rows['top_flash']) $attach->del($rows['top_flash']);
						$value['top_flash'] = $attach->save($_POST['on_top_flash']);
					}
				}
				else {
					$value['container_width'] = $_POST['container_width'];
					$value['container_height'] = $_POST['container_height'];
					$value['menu_item_space'] = $_POST['menu_item_space'];
					$value['submenu_item_space'] = $_POST['submenu_item_space'];
					$value['submenu_pos'] = $_POST['submenu_pos'];
					$value['menu_bg_type'] = $_POST['menu_bg_type'];
					$value['menu_bg_scolor'] = $_POST['menu_bg_scolor'];
					$value['menu_bg_gtype'] = $_POST['menu_bg_gtype'];
					$value['menu_bg_gcolor1'] = $_POST['menu_bg_gcolor1'];
					$value['menu_bg_gcolor2'] = $_POST['menu_bg_gcolor2'];
					$value['submenu_bg_type'] = $_POST['submenu_bg_type'];
					$value['submenu_bg_scolor'] = $_POST['submenu_bg_scolor'];
					$value['submenu_bg_gtype'] = $_POST['submenu_bg_gtype'];
					$value['submenu_bg_gcolor1'] = $_POST['submenu_bg_gcolor1'];
					$value['submenu_bg_gcolor2'] = $_POST['submenu_bg_gcolor2'];
					$value['menu_text_offcolor'] = $_POST['menu_text_offcolor'];
					$value['menu_text_oncolor'] = $_POST['menu_text_oncolor'];
					$value['submenu_text_offcolor'] = $_POST['submenu_text_offcolor'];
					$value['submenu_text_oncolor'] = $_POST['submenu_text_oncolor'];
					if($_POST['on_menu_bg']) {
						$attach = new attachment('menu_bg');
						if($rows!==null && $rows['menu_bg']) $attach->del($rows['menu_bg']);
						$value['menu_bg'] = $attach->save($_POST['on_menu_bg']);
					}
				}
				$_vals['item'] = 'top_menu_design';
				$_vals['value'] = serialize($value);
				$values = $this->change_query_string($_vals);
				if($rows!==null) $this->query("update $this->configs_table set $values where item='top_menu_design'");
				else $this->query("insert $this->configs_table set $values");
				break;

			case 'left': // 좌측메뉴설정
				$rows = $this->get_settings('left_menu_design');
				if($rows!==null) $value = $rows;

				$value['lnb_type'] = $_POST['lnb_type'];

				if($_POST['lnb_type']=='upload') {
					$attach = new attachment('lnb_flash');
					foreach($_POST['on_lnbs'] as $pid=>$file_name) {
						if(!$file_name) {
							$value['lnb_flashes'][$pid]['width'] = $_POST['widths'][$pid];
							$value['lnb_flashes'][$pid]['height'] = $_POST['heights'][$pid];
							continue;
						}
						if($value['lnb_flashes'][$pid]) $attach->del($value['lnb_flashes'][$pid]);
						$value['lnb_flashes'][$pid] = array(
							'file' => $attach->save($file_name),
							'width' => $_POST['widths'][$pid],
							'height' => $_POST['heights'][$pid]
						);
					}
				}
				else {
					$attach = new attachment('lnb_title');
					foreach($_POST['on_titles'] as $pid=>$file_name) {
						if(!$file_name) continue;
						if($value['lnb_titles'][$pid]) $attach->del($value['lnb_titles'][$pid]);
						$value['lnb_titles'][$pid] = $attach->save($file_name);
					}
					$value['lnb_title_type'] = $_POST['lnb_title_type'];
					// 배경색
					$value['sm_off_bgcolor'] = $_POST['sm_off_bgcolor'];
					$value['sm_on_bgcolor'] = $_POST['sm_on_bgcolor'];
					$value['tm_off_bgcolor'] = $_POST['tm_off_bgcolor'];
					$value['tm_on_bgcolor'] = $_POST['tm_on_bgcolor'];
					// 글자색
					$value['sm_off_color'] = $_POST['sm_off_color'];
					$value['sm_on_color'] = $_POST['sm_on_color'];
					$value['tm_off_color'] = $_POST['tm_off_color'];
					$value['tm_on_color'] = $_POST['tm_on_color'];
					// 테두리색
					$value['sm_off_bordercolor'] = $_POST['sm_off_bordercolor'];
					$value['sm_on_bordercolor'] = $_POST['sm_on_bordercolor'];
					$value['tm_off_bordercolor'] = $_POST['tm_off_bordercolor'];
					$value['tm_on_bordercolor'] = $_POST['tm_on_bordercolor'];
				}
				$_vals['item'] = 'left_menu_design';
				$_vals['value'] = serialize($value);
				$values = $this->change_query_string($_vals);
				if($rows!==null) $this->query("update $this->configs_table set $values where item='left_menu_design'");
				else $this->query("insert $this->configs_table set $values");
				break;

			case 'site': // 전체설정
				$rows = $this->get_settings('site_design');
				if($rows!==null) $value = $rows;
				#$value['site_align'] = $_POST['site_align'];
				$value['site_theme_color'] = $_POST['site_theme_color']; // 테마색상
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
							$attach = new attachment('site_bg');
							if($rows!==null && $rows['site_bg']) $attach->del($rows['site_bg']);
							$value['site_bg'] = $attach->save($_POST['on_site_bg']);
						}
						break;
				}
				$_vals['item'] = 'site_design';
				$_vals['value'] = serialize($value);
				$values = $this->change_query_string($_vals);
				if($rows!==null) $this->query("update $this->configs_table set $values where item='site_design'");
				else $this->query("insert $this->configs_table set $values");
				unset($_vals);

				// 준비중 페이지
				$this->apply_config('ready_content', array('value' => parent::trans_wysiwyg($_POST['ready_content'])));
				break;

			case 'intro':
				$value['intro_type'] = $_POST['intro_type'];
				$value['intro_url'] = $_POST['intro_url'];
				$value['out_url'] = $_POST['out_url']; // 만19세미만나가기 URL
				$_vals['value'] = serialize($value);
				$this->apply_config('intro_design', $_vals);

				if($_POST['intro_type']=='html') { // 인트로 페이지
					$this->apply_config('intro_content', array('value' => parent::trans_wysiwyg($_POST['intro_content'])));
				}
				break;

			case 'main_board': // 게시판 메인설정
				$rows = $this->get_settings('main_board_design');
				if($rows!==null) $value = $rows;
				foreach(range(1, 3) as $num) {
					$prefix = 'tab'.$num;
					$value[$prefix] = array(
						'board' => $_POST[$prefix.'_board'],
						'limits' => $_POST[$prefix.'_limits'],
						'length' => $_POST[$prefix.'_length'],
						'text_offcolor' => $_POST[$prefix.'_text_offcolor'],
						'text_oncolor' => $_POST[$prefix.'_text_oncolor'],
						'bg_offcolor' => $_POST[$prefix.'_bg_offcolor'],
						'bg_oncolor' => $_POST[$prefix.'_bg_oncolor']
					);
				}
				$value['gallery_board'] = $_POST['gallery_board'];
				if($_POST['on_gallery_title']) {
					$attach = new attachment('gallery_title');
					if($rows!==null && $rows['gallery_title']) $attach->del($rows['gallery_title']);
					$value['gallery_title'] = $attach->save($_POST['on_gallery_title']);
				}
				$_vals['item'] = 'main_board_design';
				$_vals['value'] = serialize($value);
				$values = $this->change_query_string($_vals);
				if($rows!==null) $this->query("update $this->configs_table set $values where item='main_board_design'");
				else $this->query("insert $this->configs_table set $values");
				break;
		}
	}
	function apply_config($item, $_vals) {
		$rows = $this->get_settings($item);
		if($rows!==null) {
			$values = $this->change_query_string($_vals);
			$this->query("update $this->configs_table set $values where item='$item'");
		}
		else {
			$_vals['item'] = $item;
			$values = $this->change_query_string($_vals);
			$this->query("insert $this->configs_table set $values");
		}
	}
}

?>