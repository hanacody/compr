<?php
/**
 * �������� ��� ����
 */

$modules['schedule'] = array(
	'name' => '��������',
	'file' => null,
	'components' => array(
		'constvar' => 'calendar', // Ŭ��������
		'class_name' => 'calendar', // Ŭ������
		'class_file' => 'schedule/class/calendar.class.php', // Ŭ�������ϸ� ��Ŭ��� ������ �������̸� ������(|)�� �����Ͽ� ����
		'index_file' => $mobile->m_folder.'/schedule/index.html', // �ε��� ���� - �޴�Ȱ��ȭ�� ��� Ȯ�ο�
		'seperator' => 'no', // �޴�Ȱ��ȭ�� ������Ʈ Ȯ�ο�
		'method' => 'get_components', // ������ �޽���
		'parameters' => null // �޽�忡 �ѱ� �Ķ���� : Array()
	)
);

?>