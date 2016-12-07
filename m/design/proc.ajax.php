<?php
/**
 * 메뉴 핸들링
 */
include_once '../../Libs/_php/rankup_basic.class.php';
include_once '../builder/rankup_frame.class.php';
include_once '../class/rankup_moduler.class.php';

$frame = new rankup_frame;
$moduler = new rankup_moduler;

$rows = $frame->get_frame($_POST['pid']);
if($_POST['pid']) unset($_SESSION['pid']);
$_SESSION['pid'] = $_POST['pid']; // keep - page_generator 에서 활용

/**
 * 페이지 호출
 */
include_once 'page_generator.class.php';
$page = new page_generator('', false);
$url = $page->get_url($rows);
if(!$url) $url = $mobile->m_domain.'page.html?pid='.$_POST['pid']; // 기본주소

// 관리자 미리보기인 경우 새창띄우기
if($_POST['request']=='by_admin') {
	scripts("window.open('$url');");
}
else {
	if($rows['target']=='_self') scripts("location.href = '$url';");
	else if($rows['target']=='_blank') scripts("window.open('$url');");
}

?>