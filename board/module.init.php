<?php
/**
 * �Խ��� ��� ����
 */
$modules['board'] = array(
	'name' => '�Խ���',
	'file' => null,
	'components' => array(
		'constvar' => 'board', // Ŭ��������
		'class_name' => 'rankup_board_mini', // Ŭ������
		'class_file' => 'Libs/_php/rankup_board_mini.class.php', // Ŭ�������ϸ� ��Ŭ��� ������ �������̸� ������(|)�� �����Ͽ� ����
		'index_file' => 'board/index.html', // �ε��� ���� - �޴�Ȱ��ȭ�� ��� Ȯ�ο�
		'seperator' => 'id', // �޴�Ȱ��ȭ�� ������Ʈ Ȯ�ο�
		'method' => 'get_components', // ������ �޽���
		'parameters' => null // �޽�忡 �ѱ� �Ķ���� : Array()
	)
);

/**
 * ������ ��� ����
 */
$modules['fbuilder'] = array(
	'name' => '�����',
	'file' => null,
	'components' => array(
		'constvar' => 'fbuilder', // Ŭ��������
		'class_name' => 'rankup_fbuilder', // Ŭ������
		'class_file' => 'rankup_module/rankup_fbuilder/rankup_fbuilder.class.php', // Ŭ�������ϸ� ��Ŭ��� ������ �������̸� ������(|)�� �����Ͽ� ����
		'index_file' => 'board/write.html', // �ε��� ���� - �޴�Ȱ��ȭ�� ��� Ȯ�ο�
		'seperator' => 'fno', // �޴�Ȱ��ȭ�� ������Ʈ Ȯ�ο�
		'method' => 'get_components', // ������ �޽���
		'parameters' => null // �޽�忡 �ѱ� �Ķ���� : Array()
	)
);
?>