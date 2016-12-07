<?php

$this->presets = array(
	// ������ ���
	'page_top' => array(
		'nonjunk' => true, // junk skip
		'save' => array(
			'folder' => 'design/page/',
			'name' => 'top.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '2MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ������ ����
	'page_title' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'design/page/',
			'name' => '_junk_.title.{mtime}.{ext}'
		),
		'save' => array(
			'folder' => 'design/page/',
			'name' => 'title.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '2MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ���� �÷��� ����̹���
	'flash_bg' => array(
		'nonjunk' => true,
		'save' => array(
			'folder' => 'design/main/',
			'name' => 'mfbg.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '5MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ������ü ���
	'text_container_bg' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'design/main/',
			'name' => '_junk_.mftbg.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'design/main/',
			'name' => 'mftbg.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '2MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ��������� ���� �÷���
	'main_flash' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'design/main/',
			'name' => '_junk_.mf.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'design/main/',
			'name' => 'mf.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'swf,jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '5MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ���� ����� ����̹���
	'visual_bg' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'design/main/',
			'name' => '_junk_.mvbg.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'design/main/',
			'name' => 'mvbg.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '5MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ��������� GNB �÷���
	'top_flash' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'design/top/',
			'name' => '_junk_.gnb.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'design/top/',
			'name' => 'gnb.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'swf',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '5MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// 1���޴� ��� �̹���
	'menu_bg' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'design/top/',
			'name' => '_junk_.mbg.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'design/top/',
			'name' => 'mbg.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '2MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ��������� LNB �÷���
	'lnb_flash' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'design/left/',
			'name' => '_junk_.lnb.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'design/left/',
			'name' => 'lnb.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'swf',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '5MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// LNB Ÿ��Ʋ �̹���
	'lnb_title' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'design/left/',
			'name' => '_junk_.lnbt.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'design/left/',
			'name' => 'lnbt.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '1MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ����Ʈ��� �̹���
	'site_bg' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'design/site/',
			'name' => '_junk_.sbg.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'design/site/',
			'name' => 'sbg.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '5MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ������ Ÿ��Ʋ �̹��� - ����Ʈ����
	'gallery_title' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'design/main/',
			'name' => '_junk_.gt.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'design/main/',
			'name' => 'gt.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '1MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	/**
	 * ��Ÿ
	 */
	// �൵ �̹���
	'map' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => 'design/site/',
			'name' => '_junk_.map.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => 'design/site/',
			'name' => 'map.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'swf,jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '2MB',
		'return_name' => 'remote' // remote | client | both = array()
	)
);

?>