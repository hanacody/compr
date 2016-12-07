<?php
/**
 * ������� ���� ó��
 */
include_once '../../Libs/_php/rankup_basic.class.php';
include_once '../../rankup_module/rankup_builder/attachment.class.php';

/**
 * ���ϻ��� ���� �ε�
 */
include_once 'make_files.inc.php';

switch($_POST['mode']) {

	case 'save_settings': // ����������� ����
		$rankup_control->change_encoding($_POST, 'IN');
		$mobile->save_settings();
		// QRCODE ����
		unset($mobile);
		$mobile = new rankup_mobile;
		include_once './qrcode/qrlib.php';
		QRcode::png($mobile->m_domain, $m_dir.'design/site/qrcode.png', 'H', 7, 2);
		break;

	case 'save_design': // �����μ��� ���� ����
		include_once 'rankup_design.class.php';
		$rankup_control->change_encoding($_POST, 'IN');
		$design = new rankup_design;
		$design->set_settings($_POST['kind']);
		switch($_POST['kind']) {
			case 'site':
			case 'main':
				if($_POST['kind']=='site') list($item, $field, $akey) = array('site_design', 'gnb_texts', 'gnb_text');
				else list($item, $field, $akey) = array('main_design', 'quick_icons', 'quick_icon');
				$attach = new attachment($akey, $m_dir.'builder/');
				$ds_rows = $design->get_settings($item);
				if($_POST['kind']=='site') {
					include_once '../class/palette.class.php';
					make_css_gnb_style($ds_rows);
					make_site_bg_style($ds_rows);
				}
				else make_main_bg_style($ds_rows);
				$datas = array();
				foreach($ds_rows[$field] as $no=>$name) {
					array_push($datas, compact('no', 'name'));
				}
				$folder = $attach->configs['save']['folder'];
				$nodes = fetch_contents($datas, array(
					'entry' => array(
						1 => implode("\n", array(
							'<item>',
							'	<no>{:no:}</no>',
							'	<folder><![CDATA['.$folder.']]></folder>',
							'	<name><![CDATA[{:name:}]]></name>',
							'</item>'
						))
					)
				));
				xmls('<xml>'.$nodes.'</xml>');
				break;
		}
		break;


	/**
	 * ����� ������ ó��
	 */
	case 'set_direction': // ���� ���� - ajax - background
		include_once 'rankup_frame.class.php';
		$frame = new rankup_frame;
		$frame->set_direction();
		make_pids_data(); // PIDS.inc ���� ����
		break;

	case 'save_frame': // ����
		$rankup_control->change_encoding($_POST, 'IN');
		include_once 'rankup_frame.class.php';
		$frame = new rankup_frame;
		$frame->save_frame();
		make_pids_data(); // PIDS.inc ���� ����

	case 'load': // �ε� - xml
		if($_POST['kind']=='edit' || $_POST['mode']=='save_frame') {
			include_once 'rankup_frame.class.php';
			$frame = new rankup_frame;
			$nodes = $frame->print_frames(array(
				'entry' => array(
					0 => '',
					1 => array(
						'<no>{:no:}</no>',
						'<base_name><![CDATA[{:base_name:}]]></base_name>',
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
						'<used><![CDATA[{:used:}]]></used>'
					)
				)
			), $_POST['no']);
			xmls('<xml>'.$nodes.'</xml>');
		}
		break;

	case 'del_frame': // ����
		include_once 'rankup_frame.class.php';
		$frame = new rankup_frame;
		$frame->del_frame();
		make_pids_data(); // PIDS.inc ���� ����
		break;

	case 'load_category':
		include_once 'rankup_frame.class.php';
		$frame = new rankup_frame;
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
		include_once '../class/rankup_moduler.class.php';
		$moduler = new rankup_moduler;
		$nodes = $moduler->print_components($_POST['module'], array(
			'entry' => array(
				1=> array(
					'<item>',
						'<key>{:key:}</key>',
						'<name><![CDATA[{:name:}]]></name>',
						'<option>',
						'	<init><![CDATA[{:init:}]]></init>',
						'	<html><![CDATA[{:html:}]]></html>',
						'	<js><![CDATA[{:js:}]]></js>',
						'</option>',
					'</item>'
				)
			)
		));
		xmls('<xml>'.$nodes.'</xml>');
		break;


	/**
	 * ÷������ ó��
	 */
	case 'post_attach': // ���� ÷��
		$attach = new attachment($_POST['kind'], $mobile->m_dir.'builder/');
		$result = $attach->post($_FILES['_attach_']);
		list($constvar) = explode('.', $_POST['handler']);
		$post_reset = sprintf('parent.%s.post_reset();', $constvar);
		if(!is_array($result)) {
			$msg = $attach->error_msg($result);
			scripts('alert("'.$msg.'");'.$post_reset);
		}
		else {
			if($_POST['handler']) {
				$hash = function_exists('json_encode') ? json_encode($result) : rankup_util::json_encode($result);
				scripts($post_reset."parent.$_POST[handler]($hash);");
			}
			else {
				// Fatal error
				scripts($post_reset.'alert("�ڵ鷯�� ���ǵǾ� ���� �ʽ��ϴ�.")');
			}
		}
		break;

	case 'del_attach': // ���� ����
		$attach = new attachment($_POST['kind'], $mobile->m_dir.'builder/');
		if($attach->del($_POST['name'])) {
			//
		}
		break;
}
?>