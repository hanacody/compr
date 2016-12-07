<?php
/**
 * 디자인설정 관련 상단
 */

switch(basename($_SERVER['PHP_SELF'])) {
	case 'index.html': $_key = 0; break;
	case 'frame.html': $_key = 1; break;
	case 'main_design.html': $_key = 2; break;
	case 'site_design.html': $_key = 3; break;
}

$_sub_style[$_key] = ' style="font-weight: bold"';
$_title_texts = array('기본설정', '메뉴및페이지설정', '메인페이지설정', '디자인설정');

$rankup_control->print_admin_head('모바일웹설정 - '.$_title_text[$_key]);
?>
<body>
	<div class="bar"><script> titleBar('모바일웹설정 - <?=$_title_texts[$_key]?>', 400) </script></div>
	<div id="topmenu" style="margin-bottom:30px">
		<ul>
			<li><a href="<?=$mobile->m_url?>builder/index.html"<?=$_sub_style[0]?>>기본설정</a></li>
			<li><a href="<?=$mobile->m_url?>builder/frame.html"<?=$_sub_style[1]?>>메뉴및페이지설정</a></li>
			<li><a href="<?=$mobile->m_url?>builder/main_design.html"<?=$_sub_style[2]?>>메인페이지설정</a></li>
			<li><a href="<?=$mobile->m_url?>builder/site_design.html"<?=$_sub_style[3]?>>디자인설정</a></li>
		</ul>
	</div>
