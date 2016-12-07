<?php
/**
 * 제품관리 모듈 정의
 *@note: 메뉴활성화는 $_GET[pid] 값에 의존적임.
 */

$modules['product'] = array(
	'name' => '제품관리',
	'file' => null,
	'components' => array(
		'list_normal' => array(
			'name' => '제품목록보기(리스트형)',
			'file' => 'product/list.html',
			'option' => array(
				'html' => 'product/option/sel_category.html',
				'js' => 'product/option/product.js?'.time()
			),
			'url' => null,
		),
		'list_gallery' => array(
			'name' => '제품목록보기(갤러리형)',
			'file' => 'product/list.html',
			'option' => array(
				'html' => 'product/option/sel_category.html',
				'js' => 'product/option/product.js?'.time()
			),
			'url' => null
		),
		'view' => array(
			'name' => '제품상세보기',
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