<?php
/**
 * ����Ʈ�⺻ȯ�漳��
 */
include_once '../../Libs/_php/rankup_basic.class.php';
$config = new rankup_siteconfig;

switch($_POST['mode']) {

	// �⺻ȯ�� ����
	case 'save_siteconfig':
		include_once '../rankup_builder/attachment.class.php';
		$rankup_control->change_encoding($_POST, 'IN');
		$config->save();
		break;

	// ����Ű ����
	case 'save_mapkey':
		$config->save_mapkey();
		break;

	// ����Ű ����
	case 'del_mapkey':
		$config->del_mapkey();
		break;

	// ���� geocode ��ȯ
	case 'get_geocode':
		include_once '../rankup_post/nhn_map.class.php';
		$nhn = new nhn_map;
		$nodes = $nhn->get_geocode(array(
			'entry' => array(
				1 => array(
					'<item>',
						'<address><![CDATA[{:address:}]]></address>',
						'<sido><![CDATA[{:sido:}]]></sido>',
						'<sigugun><![CDATA[{:sigugun:}]]></sigugun>',
						'<dongmyun><![CDATA[{:dongmyun:}]]></dongmyun>',
						'<mapx><![CDATA[{:mapx:}]]></mapx>',
						'<mapy><![CDATA[{:mapy:}]]></mapy>',
					'</item>'
				)
			)
		));
		$rankup_control->print_xml_header('<xml>'.$nodes.'</xml>');
		break;
}

?>