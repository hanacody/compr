<?php
/**
 * ��ǰ���� ��� ����
 */

$modules['product'] = array(
	'name' => '��ǰ����',
	'file' => null,
	'components' => array(
		'list_normal' => array(
			'name' => '��ǰ��Ϻ���',
			'file' => $mobile->m_folder.'/product/list.html',
			'option' => array(
				'html' => 'product/option/sel_category.html',
				'js' => 'product/option/product.js?'.time()
			),
			'url' => null,
		),
		'view' => array(
			'name' => '��ǰ�󼼺���',
			'file' => $mobile->m_folder.'/product/view.html|'.$mobile->m_folder.'/product/estimate.html',
			'option' => array(
				'html' => 'product/option/sel_product.html',
				'js' => 'product/option/product.js?'.time()
			),
			'url' => null
		)
	)
);

?>