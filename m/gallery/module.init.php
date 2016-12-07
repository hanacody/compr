<?php
/**
 * 갤러리 모듈 정의
 */

$modules['gallery'] = array(
	'name' => '갤러리',
	'file' => null,
	'components' => array(
		'constvar' => 'gallery', // 클래스변수
		'class_name' => 'gallery', // 클래스명
		'class_file' => 'gallery/class/gallery.class.php', // 클래스파일명 인클루드 파일이 여러개이면 파이프(|)로 구분하여 나열
		'index_file' => $mobile->m_folder.'/gallery/index.html|'.$mobile->m_folder.'/gallery/view.html', // 인덱스 파일 - 메뉴활성화시 모듈 확인용
		'seperator' => 'no|pno', // 메뉴활성화시 콤포넌트 확인용
		'method' => 'get_components', // 수행할 메쏘드명
		'parameters' => null // 메쏘드에 넘길 파라메터 : Array()
	)
);

?>