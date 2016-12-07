<?php
global $mobile;

$this->presets = array(
	// ����� �ΰ�
	'mobile_logo' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => $mobile->m_folder.'/design/top/',
			'name' => '_junk_.logo.{mtime}.{ext}'
		),
		'save' => array(
			'folder' => $mobile->m_folder.'/design/top/',
			'name' => 'logo.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '2MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ����� ���� ���
	'main_bg' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => $mobile->m_folder.'/design/main/',
			'name' => '_junk_.mbg.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => $mobile->m_folder.'/design/main/',
			'name' => 'mbg.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '5MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ����� ����Ʈ ���
	'site_bg' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => $mobile->m_folder.'/design/site/',
			'name' => '_junk_.sbg.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => $mobile->m_folder.'/design/site/',
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
	// GNB �ؽ�Ʈ
	'gnb_text' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => $mobile->m_folder.'/design/top/',
			'name' => '_junk_.gnb.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => $mobile->m_folder.'/design/top/',
			'name' => 'gnb.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '1MB',
		'return_name' => 'remote' // remote | client | both = array()
	),
	// ���޴� ������
	'quick_icon' => array(
		'nonjunk' => false,
		'junk' => array(
			'folder' => $mobile->m_folder.'/design/main/',
			'name' => '_junk_.quick.{mtime}.{ext}',
		),
		'save' => array(
			'folder' => $mobile->m_folder.'/design/main/',
			'name' => 'quick.{mtime}.{ext}',
		),
		'ext' => array(
			'allow' => 'jpg,gif,png',
			'disallow' => '',
			'verify' => 'name' // name | getimagesize | mime-type
		),
		'limit_size' => '1MB',
		'return_name' => 'remote' // remote | client | both = array()
	)
);

?>