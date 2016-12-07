<?php
/**
 * ī�װ� ó��
 */
include_once '../../Libs/_php/rankup_basic.class.php';
include_once 'rankup_category.class.php';

$category = new rankup_category;

switch($_POST['mode']) {

	case 'save':
		$rankup_control->change_encoding($_POST, 'IN');
		$identities = $category->save();
		if($_POST['type']=='single') {
			if($identities && is_array($identities)) {
				$hash = json_encode($identities);
				scripts('category.update('.$hash.');');
			}
			break;
		}
		//@note: if($_POST['type']=='multi') �ΰ�� case 'load': �� �̾���.

	// ī�װ� 1�� ��ȯ
	case 'load':
		$nodes = $category->get($_POST['no'], array(
			'entry' => array(
				1 => array(
					'<item>',
						'<no>{:no:}</no>',
						'<kind>{:kind:}</kind>',
						'<bundle><![CDATA[{:bundle:}]]></bundle>',
						'<name><![CDATA[{:item:}]]></name>',
						'<depth>{:depth:}</depth>',
						'<parents><![CDATA[{:parents:}]]></parents>',
						'<has_child>{:has_child:}</has_child>',
						'<rank>{:rank:}</rank>',
						'<used>{:used:}</used>',
					'</item>'
				)
			)
		));
		xmls('<xml>'.$nodes.'</xml>');
		break;

	// ī�װ� 1�� �̻� ��ȯ
	case 'load_category':
		$nodes = $category->load($_POST['kind'], array(
			'entry' => array(
				1 => array(
					'<item>',
						'<no>{:no:}</no>',
						'<kind><![CDATA[{:kind:}]]></kind>',
						'<bundle><![CDATA[{:bundle:}]]></bundle>',
						'<name><![CDATA[{:item:}]]></name>',
						'<depth>{:depth:}</depth>',
						'<parents><![CDATA[{:parents:}]]></parents>',
						'<has_child>{:has_child:}</has_child>',
						'<rank>{:rank:}</rank>',
						'<used>{:used:}</used>',
					'</item>'
				)
			)
		), $_POST['no'], $_POST['step']);
		xmls('<xml>'.$nodes.'</xml>');
		break;

	case 'save_rank':
		$category->set_rank();
		break;

	case 'del':
		$category->del();
		break;
}

?>