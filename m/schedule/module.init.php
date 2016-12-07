<?php
/**
 * 일정관리 모듈 정의
 */

$modules['schedule'] = array(
	'name' => '일정관리',
	'file' => null,
	'components' => array(
		'constvar' => 'calendar', // 클래스변수
		'class_name' => 'calendar', // 클래스명
		'class_file' => 'schedule/class/calendar.class.php', // 클래스파일명 인클루드 파일이 여러개이면 파이프(|)로 구분하여 나열
		'index_file' => $mobile->m_folder.'/schedule/index.html', // 인덱스 파일 - 메뉴활성화시 모듈 확인용
		'seperator' => 'no', // 메뉴활성화시 콤포넌트 확인용
		'method' => 'get_components', // 수행할 메쏘드명
		'parameters' => null // 메쏘드에 넘길 파라메터 : Array()
	)
);

?>