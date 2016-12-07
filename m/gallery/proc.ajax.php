<?php
/**
 * 예약 처리
 */
include_once '../../Libs/_php/rankup_basic.class.php';

switch($_POST['mode']) {
	case 'load_webzine':
		include_once '../class/gallery.class.php';
		$gallery = new gallery;
		$nodes = $gallery->load_webzine(array(
			'entry' => array(
				1 => implode("\n", array(
					'<item>',
					'	<subject><![CDATA[{:subject:}]]></subject>',
					'	<content><![CDATA[{:content:}]]></content>',
					'</item>'
				))
			)
		));
		xmls('<xml>'.$nodes.'</xml>');
		break;
}
?>