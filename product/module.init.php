<?php
/**
 * ��ǰ���� ��� ����
 *@note: �޴�Ȱ��ȭ�� $_GET[pid] ���� ��������.
 */

$modules['product'] = array(
	'name' => '��ǰ����',
	'file' => null,
	'components' => array(
		'list_normal' => array(
			'name' => '��ǰ��Ϻ���(����Ʈ��)',
			'file' => 'product/list.html',
			'option' => array(
				'html' => 'product/option/sel_category.html',
				'js' => 'product/option/product.js?'.time()
			),
			'url' => null,
		),
		'list_gallery' => array(
			'name' => '��ǰ��Ϻ���(��������)',
			'file' => 'product/list.html',
			'option' => array(
				'html' => 'product/option/sel_category.html',
				'js' => 'product/option/product.js?'.time()
			),
			'url' => null
		),
		'view' => array(
			'name' => '��ǰ�󼼺���',
			'file' => 'product/view.html|product/estimate.html',
			'option' => array(
				'html' => 'product/option/sel_product.html',
				'js' => 'product/option/product.js?'.time()
			),
			'url' => null
		)
	)
);

?>