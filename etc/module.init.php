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
			'file' => 'etc/map.html',
			'url' => null
		),
		'sitemap' => array(
			'name' => '����Ʈ��',
			'file' => 'etc/sitemap.html',
			'url' => null
		),
		'login' => array(
			'name' => '�α���',
			'file' => 'rankup_module/rankup_member/login.html',
			'url' => null
		),
		'find_info' => array(
			'name' => 'ID/PASSã��',
			'file' => 'rankup_module/rankup_member/find_login_info.html',
			'url' => null
		),
		'member_join' => array(
			'name' => 'ȸ������',
			'file' => 'rankup_module/rankup_member/join_intro.html|rankup_module/rankup_member/join_policy.html|rankup_module/rankup_member/join_pin.html|rankup_module/rankup_member/join_form.html',
			'url' => null
		),
		'change_password' => array(
			'name' => '��й�ȣ����',
			'file' => 'rankup_module/rankup_member/change_password.html',
			'url' => null
		)
	)
);

?>