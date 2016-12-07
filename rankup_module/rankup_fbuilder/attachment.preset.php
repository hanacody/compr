<?php
/**
 * 첨부파일 프리셋 - 폼빌더용
 */
$this->presets = array(
	// 제품이미지
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
			'allow' => 'jpg,gif,png', // 동적으로 변경됨
			'verify' => 'mime-type' // name | getimagesize | mime-type
		),
		'limit_size' => '3MB', // 동적으로 변경됨
		'return_name' => 'remote' // remote | client | both = array()
	)
);

?>