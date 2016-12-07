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
		'class_file' => $mobile->m_folder.'/class/rankup_board_mini.class.php', // Ŭ�������ϸ� ��Ŭ��� ������ �������̸� ������(|)�� �����Ͽ� ����
		'index_file' => $mobile->m_folder.'/board/index.html|'.$mobile->m_folder.'/board/detail.html', // �ε��� ���� - �޴�Ȱ��ȭ�� ��� Ȯ�ο�
		'seperator' => 'id|id', // �޴�Ȱ��ȭ�� ������Ʈ Ȯ�ο�
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
		'write' => array(
			'name' => '���������',
			'file' => 'board/write.html',
			'option' => array(
				'html' => 'board/option/sel_form.html',
				'js' => 'board/option/fbuilder.js?'.time()
			),
			'url' => null
		),
		'list' => array(
			'name' => '�ۼ��� ��Ϻ���',
			'file' => $mobile->m_folder.'/board/list.html|'.$mobile->m_folder.'/board/view.html',
			'option' => array(
				'html' => 'board/option/sel_form.html',
				'js' => 'board/option/fbuilder.js?'.time()
			),
			'url' => null,
		)
	)
);
?>