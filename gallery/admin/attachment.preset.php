<?php
/**
 * 갤러리용
 */
$this->presets = array(
	// 갤러리 이미지
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
	// 갤러리 문구전체 배경
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
	// 갤러리 웹진형 이미지
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