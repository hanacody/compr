<?php
/**
 * ������ ��� ����
 */

$modules['gallery'] = array(
	'name' => '������',
	'file' => null,
	'components' => array(
		'constvar' => 'gallery', // Ŭ��������
		'class_name' => 'gallery', // Ŭ������
		'class_file' => 'gallery/class/gallery.class.php', // Ŭ�������ϸ� ��Ŭ��� ������ �������̸� ������(|)�� �����Ͽ� ����
		'index_file' => 'gallery/index.html', // �ε��� ���� - �޴�Ȱ��ȭ�� ��� Ȯ�ο�
		'seperator' => 'no', // �޴�Ȱ��ȭ�� ������Ʈ Ȯ�ο�
		'method' => 'get_components', // ������ �޽���
		'parameters' => null // �޽�忡 �ѱ� �Ķ���� : Array()
	)
);

?>