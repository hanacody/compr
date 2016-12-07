<?php
/**
 * 마이페이지 모듈 정의
 */

$modules['mypage'] = array(
	'name' => '마이페이지',
	'file' => null,
	'components' => array(
		'myinfo' => array(
			'name' => '정보조회',
			'file' => 'mypage/index.html',
			'url' => null
		),
		'modifyinfo' => array(
			'name' => '정보수정',
			'file' => 'rankup_module/rankup_member/member_modify.html',
			'url' => null
		)
	)
)
?>