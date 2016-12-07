<?php
/**
 * 스킨 스타일 정의
 */

// 컨텐츠 가로 크기
$_skin_init['content_width'] = 980;

// 갤러리 PRESET 설정
$_skin_init['gallery_preset'] = array(
	/* 이미지리스트 */
	'image_list_top' => 350, // 상단여백
	'image_list_left' => 160, // 좌측여백
	'image_list_opacity' => 99, // 불투명도
	/* 갤러리문구 등록 */
	'top_text_size' => 36, // 제목 크기
	'top_text_color' => '#FFFFFF', // 제목 색상
	'middle_text_size' => 14, // 설명글(1) 크기
	'middle_text_color' => '#9A9A9A', // 설명글(1) 색상
	'bottom_text_size' => 12, // 설명글(2) 크기
	'bottom_text_color' => '#D2D2D2', // 설명글(2) 색상
	'text_container_top' => 10, // 문구전체 상단여백
	'text_container_left' => 0, // 문구전체 상단여백
	'text_container_opacity' => 50 // 문구전체 불투명도
);

// 배너 설정
$_skin_init['banner_preset'] = array(
	3 => array("main1" => "메인배너1", "width" => 280, "height" => 60),
	4 => array("main2" => "메인배너2", "width" => 350, "height" => 60),
	5 => array("main3" => "메인배너3", "width" => 280, "height" => 60),
	6 => array("quick" => "우측퀵배너", "width" => 40, "height" => 0),
	7 => array("sub_left" => "서브좌측배너", "width" => 175, "height" => 0)
);
?>