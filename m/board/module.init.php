<?php
/**
 * 게시판 모듈 정의
 */
$modules['board'] = array(
	'name' => '게시판',
	'file' => null,
	'components' => array(
		'constvar' => 'board', // 클래스변수
		'class_name' => 'rankup_board_mini', // 클래스명
		'class_file' => $mobile->m_folder.'/class/rankup_board_mini.class.php', // 클래스파일명 인클루드 파일이 여러개이면 파이프(|)로 구분하여 나열
		'index_file' => $mobile->m_folder.'/board/index.html|'.$mobile->m_folder.'/board/detail.html', // 인덱스 파일 - 메뉴활성화시 모듈 확인용
		'seperator' => 'id|id', // 메뉴활성화시 콤포넌트 확인용
		'method' => 'get_components', // 수행할 메쏘드명
		'parameters' => null // 메쏘드에 넘길 파라메터 : Array()
	)
);

/**
 * 폼빌더 모듈 정의
 */
$modules['fbuilder'] = array(
	'name' => '등록폼',
	'file' => null,
	'components' => array(
		'write' => array(
			'name' => '등록폼보기',
			'file' => 'board/write.html',
			'option' => array(
				'html' => 'board/option/sel_form.html',
				'js' => 'board/option/fbuilder.js?'.time()
			),
			'url' => null
		),
		'list' => array(
			'name' => '작성글 목록보기',
			'file' => $mobile->m_folder.'/board/list.html|'.$mobile->m_folder.'/board/view.html',
			'option' => array(
				'html' => 'board/option/sel_form.html',
				'js' => 'board/option/fbuilder.js?'.time()
			),
			'url' => null,
		)
	)
);
?>