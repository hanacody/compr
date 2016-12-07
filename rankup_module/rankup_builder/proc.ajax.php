<?php
/**
 * 랭크업 프레임 프로시져 파일
 *@author: kurokisi
 *@authDate: 2011.08.10
 */
include_once '../../Libs/_php/rankup_basic.class.php';
include_once 'rankup_frame.class.php';
include_once 'attachment.class.php';
include_once 'rankup_design.class.php';

$frame = new rankup_frame;

/**
 * 파일생성 파일 로드
 */
include_once 'make_files.inc.php';


/**
 * 요청처리
 */
switch($_POST['mode']) {

	case 'set_direction': // 순서 변경 - ajax - background
		$frame->set_direction();
		// 메뉴 데이터 갱신
		$design = new rankup_design;
		make_top_menu_data();
		make_css_menu_style();
		break;

	case 'save_frame': // 저장
		rankup_util::change_encoding($_POST, 'IN');
		$frame->save_frame();
		// 메뉴 데이터 갱신
		$design = new rankup_design;
		make_top_menu_data();
		make_css_menu_style();

	case 'load': // 로드 - xml
		if($_POST['kind']=='edit' || $_POST['mode']=='save_frame') {
			$nodes = $frame->print_frames(array(
				'entry' => array(
					0 => '',
					1 => array(
						'<no>{:no:}</no>',
						'<base_name><![CDATA[{:base_name:}]]></base_name>',
						'<extra_name><![CDATA[{:extra_name:}]]></extra_name>',
						'<bundle>{:bundle:}</bundle>',
						'<position>{:position:}</position>',
						'<depth>{:depth:}</depth>',
						'<parents><![CDATA[{:parents:}]]></parents>',
						'<target><![CDATA[{:target:}]]></target>',
						'<access_level><![CDATA[{:access_level:}]]></access_level>',
						'<page_type><![CDATA[{:page_type:}]]></page_type>',
						'<module><![CDATA[{:module:}]]></module>',
						'<component><![CDATA[{:component:}]]></component>',
						'<options><![CDATA[{:options:}]]></options>',
						'<link><![CDATA[{:link:}]]></link>',
						'<url><![CDATA[{:url:}]]></url>',
						'<page_body_content><![CDATA[{:page_body_content:}]]></page_body_content>',
						'<used><![CDATA[{:used:}]]></used>',
						'<use_gnb><![CDATA[{:use_gnb:}]]></use_gnb>',
						'<use_lnb><![CDATA[{:use_lnb:}]]></use_lnb>'
					)
				)
			), $_POST['no']);
			xmls('<xml>'.$nodes.'</xml>');
		}
		break;

	case 'del_frame': // 삭제
		$frame->del_frame();
		// 메뉴 데이터 갱신
		$design = new rankup_design;
		make_top_menu_data();
		make_css_menu_style();
		break;

	case 'load_category':
		$nodes = $frame->print_frames(array(
			'entry' => array(
				1 => array(
					'<item>',
						'<no>{:no:}</no>',
						'<name><![CDATA[{:base_name:}]]></name>',
						'<extra_name><![CDATA[{:extra_name:}]]></extra_name>',
						'<depth>{:depth:}</depth>',
						'<parents><![CDATA[{:parents:}]]></parents>',
						'<has_child>{:has_child:}</has_child>',
						'<page_top_img><![CDATA[{:page_top_img:}]]></page_top_img>',
						'<page_title_type><![CDATA[{:page_title_type:}]]></page_title_type>',
						'<page_title_img><![CDATA[{:page_title_img:}]]></page_title_img>',
						'<page_top_content><![CDATA[{:page_top_content:}]]></page_top_content>',
						'<page_bottom_content><![CDATA[{:page_bottom_content:}]]></page_bottom_content>',
						'<used>{:used:}</used>',
					'</item>'
				)
			)
		), $_POST['no'], $_POST['step']);
		xmls('<xml>'.$nodes.'</xml>');
		break;

	case 'load_components':
		include_once $base_dir.'Libs/_php/rankup_moduler.class.php';
		$moduler = new rankup_moduler;
		$nodes = $moduler->print_components($_POST['module'], array(
			'entry' => array(
				1=> implode("\n", array(
					'<item>',
						'<key>{:key:}</key>',
						'<name><![CDATA[{:name:}]]></name>',
						'<option>',
						'	<init><![CDATA[{:init:}]]></init>',
						'	<html><![CDATA[{:html:}]]></html>',
						'	<js><![CDATA[{:js:}]]></js>',
						'</option>',
					'</item>'
				))
			)
		));
		xmls('<xml>'.$nodes.'</xml>');
		break;

	case 'save_design': // 디자인설정 사항 저장
		rankup_util::change_encoding($_POST, 'IN');
		switch($_POST['kind']) {
			case 'page': // 페이지별 디자인설정
				$frame->update_frame();
				break;

			case 'main': // 메인페이지 디자인설정
			case 'left': // 좌측메뉴설정
			case 'top': // 상단메뉴설정
				$design = new rankup_design;
				$design->set_settings($_POST['kind']);
				// make XML
				switch($_POST['kind']) {
					case 'main':
						make_main_flash_data();
						make_visual_bg_style();
						break;
					case 'left':
						make_css_lnb_style();
						$design = new rankup_design;
						$ds_rows = $design->get_settings('left_menu_design');
						$datas = array();
						foreach($ds_rows['lnb_titles'] as $no=>$name) array_push($datas, compact('no', 'name'));
						$attach = new attachment('lnb_title');
						$folder = $attach->configs['save']['folder'];
						$lnb_titles = fetch_contents($datas, array(
							'entry' => array(
								1 => implode("\n", array(
									'<title>',
									'	<no>{:no:}</no>',
									'	<folder><![CDATA['.$folder.']]></folder>',
									'	<name><![CDATA[{:name:}]]></name>',
									'</title>'
								))
							)
						));
						$datas = array();
						foreach($ds_rows['lnb_flashes'] as $no=>$rows) {
							$name = $rows['file'];
							array_push($datas, compact('no', 'name'));
						}
						$attach = new attachment('lnb_flash');
						$folder = $attach->configs['save']['folder'];
						$lnb_flashes = fetch_contents($datas, array(
							'entry' => array(
								1 => implode("\n", array(
									'<flash>',
									'	<no>{:no:}</no>',
									'	<folder><![CDATA['.$folder.']]></folder>',
									'	<name><![CDATA[{:name:}]]></name>',
									'</flash>'
								))
							)
						));
						xmls('<xml>'.$lnb_titles.$lnb_flashes.'</xml>');
						break;
					case 'top':
						make_top_menu_data();
						make_css_menu_style();
						break;
				}
				break;
			case 'site': // 전체설정
				$design = new rankup_design;
				$design->set_settings($_POST['kind']);
				make_site_bg_style(); // site/site.css 생성
				// 카피라이트
				$content = rankup_util::trans_wysiwyg($_POST['copyright']);
				$rankup_control->set_config_info('copyright', $content);
				break;
			case 'main_board': // 게시판 메인설정
				$design = new rankup_design;
				$design->set_settings($_POST['kind']);
				make_board_tab_style(); // main/tab.css 생성
				break;
		}
		break;

	case 'save_intro': // 인트로 설정
		rankup_util::change_encoding($_POST, 'IN');
		$design = new rankup_design;
		$design->set_settings('intro');
		$rankup_control->set_config_info('intro_use', $_POST['intro_use']);
		break;

	/**
	 * 첨부파일 처리
	 */
	// 파일 첨부
	case 'post_attach':
		$attach = new attachment($_POST['kind']);
		$result = $attach->post($_FILES['_attach_']);
		list($constvar) = explode('.', $_POST['handler']);
		$post_reset = sprintf('parent.%s.post_reset();', $constvar);
		if(!is_array($result)) {
			$msg = $attach->error_msg($result);
			scripts('alert("'.$msg.'");'.$post_reset);
		}
		else {
			if($_POST['handler']) {
				$hash = json_encode($result);
				scripts($post_reset."parent.$_POST[handler]($hash);");
			}
			else {
				// Fatal error
				scripts($post_reset.'alert("핸들러가 정의되어 있지 않습니다.")');
			}
		}
		break;

	// 파일 삭제
	case 'del_attach':
		$attach = new attachment($_POST['kind']);
		if($attach->del($_POST['name'])) {
			// 해당 파일을 사용하는 항목의 DB/XML 갱신
			$design = new rankup_design;
			switch($_POST['kind']) {
				case 'flash_bg':
					make_main_flash_data();
					break;
				case 'text_container_bg':
					$design->set_settings('remove_text_container_bg');
					make_main_flash_data();
					break;
				case 'menu_bg':
					make_top_menu_data();
					make_css_menu_style();
					break;

				// 객실관리@=== 삭제예정
				case 'rooms':
					if($_POST['index']) {
						include_once '../../pension/class/rooms.class.php';
						include_once '../../pension/class/rooms_admin.class.php';
						$rooms = new rooms_admin;
						$rooms->update_attach();
					}
					break;
			}
		}
		break;
}

?>