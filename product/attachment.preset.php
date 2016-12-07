<?php
/**
 * ÷������ ������
 */
$this->presets = array(
	// ��ǰ�̹���
	'product' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'PEG/product/',
			'name' => '_junk_.{mtime}.{ext}'
		),
		'save' => array(
			'folder' => 'PEG/product/{no}/',
			'name' => '{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'verify' => 'mime-type' // name | getimagesize | mime-type
		),
		'limit_size' => '3MB',
		'return_name' => 'remote' // remote | client | both = array()
	)
);

?>