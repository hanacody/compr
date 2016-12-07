<?php
/**
 * ÷������ ������ - ��������
 */
$this->presets = array(
	// ��ǰ�̹���
	'fbuilder' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'PEG/fbuilder/',
			'name' => '_junk_.{mtime}.{ext}'
		),
		'save' => array(
			'folder' => 'PEG/fbuilder/{fno}/',
			'name' => '{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png', // �������� �����
			'verify' => 'mime-type' // name | getimagesize | mime-type
		),
		'limit_size' => '3MB', // �������� �����
		'return_name' => 'remote' // remote | client | both = array()
	)
);

?>