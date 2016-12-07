<?php
/**
 * ��������
 */
$this->presets = array(
	// ������ �̹���
	'gallery' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'PEG/gallery/',
			'name' => '_junk_.gallery.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'PEG/gallery/',
			'name' => 'gallery.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '5MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ������ ������ü ���
	'gtext_container_bg' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'PEG/gallery/',
			'name' => '_junk_.gtbg.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'PEG/gallery/',
			'name' => 'gtbg.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '2MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ������ ������ �̹���
	'webzine' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'PEG/gallery/',
			'name' => '_junk_.gw.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'PEG/gallery/',
			'name' => 'gw.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '2MB',
		'return_name' => 'remote' // remote | client | both = array()
	)
);
?>