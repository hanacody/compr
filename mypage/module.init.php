<?php
/**
 * ���������� ��� ����
 */

$modules['mypage'] = array(
	'name' => '����������',
	'file' => null,
	'components' => array(
		'myinfo' => array(
			'name' => '������ȸ',
			'file' => 'mypage/index.html',
			'url' => null
		),
		'modifyinfo' => array(
			'name' => '��������',
			'file' => 'rankup_module/rankup_member/member_modify.html',
			'url' => null
		)
	)
)
?>