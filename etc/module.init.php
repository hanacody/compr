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
			'file' => 'etc/map.html',
			'url' => null
		),
		'sitemap' => array(
			'name' => '사이트맵',
			'file' => 'etc/sitemap.html',
			'url' => null
		),
		'login' => array(
			'name' => '로그인',
			'file' => 'rankup_module/rankup_member/login.html',
			'url' => null
		),
		'find_info' => array(
			'name' => 'ID/PASS찾기',
			'file' => 'rankup_module/rankup_member/find_login_info.html',
			'url' => null
		),
		'member_join' => array(
			'name' => '회원가입',
			'file' => 'rankup_module/rankup_member/join_intro.html|rankup_module/rankup_member/join_policy.html|rankup_module/rankup_member/join_pin.html|rankup_module/rankup_member/join_form.html',
			'url' => null
		),
		'change_password' => array(
			'name' => '비밀번호변경',
			'file' => 'rankup_module/rankup_member/change_password.html',
			'url' => null
		)
	)
);

?>