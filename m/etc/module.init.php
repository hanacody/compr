<?php
/**
 * ��Ÿ ��� ����
 */

$modules['etc'] = array(
	'name' => '��Ÿ',
	'file' => null,
	'components' => array(
		'map' => array(
			'name' => '�൵',
			'file' => $mobile->m_folder.'/etc/map.html',
			'url' => null
		),
		'login' => array(
			'name' => '�α���',
			'file' => $mobile->m_folder.'/etc/login.html',
			'url' => null
		)
	)
);

?>