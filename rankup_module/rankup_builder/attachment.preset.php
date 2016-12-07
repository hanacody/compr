<?php

$this->presets = array(
	// 페이지 상단
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
	// 페이지 제목
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
	// 메인 플래시 배경이미지
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
	// 문구전체 배경
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
	// 사용자제작 메인 플래시
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
	// 메인 비쥬얼 배경이미지
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
	// 사용자제작 GNB 플래시
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
	// 1차메뉴 배경 이미지
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
	// 사용자제작 LNB 플래시
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
	// LNB 타이틀 이미지
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
	// 사이트배경 이미지
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
	// 갤러리 타이틀 이미지 - 사이트메인
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
	 * 기타
	 */
	// 약도 이미지
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