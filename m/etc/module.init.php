<?php
/**
 * 기타 모듈 정의
 */

$modules['etc'] = array(
	'name' => '기타',
	'file' => null,
	'components' => array(
		'map' => array(
			'name' => '약도',
			'file' => $mobile->m_folder.'/etc/map.html',
			'url' => null
		),
		'login' => array(
			'name' => '로그인',
			'file' => $mobile->m_folder.'/etc/login.html',
			'url' => null
		)
	)
);

?>