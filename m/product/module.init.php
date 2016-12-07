<?php
/**
 * 力前包府 葛碘 沥狼
 */

$modules['product'] = array(
	'name' => '力前包府',
	'file' => null,
	'components' => array(
		'list_normal' => array(
			'name' => '力前格废焊扁',
			'file' => $mobile->m_folder.'/product/list.html',
			'option' => array(
				'html' => 'product/option/sel_category.html',
				'js' => 'product/option/product.js?'.time()
			),
			'url' => null,
		),
		'view' => array(
			'name' => '力前惑技焊扁',
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