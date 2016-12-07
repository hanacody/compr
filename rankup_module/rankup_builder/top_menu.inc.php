<?php
/**
 * 디자인설정 관련 상단
 */

switch(array_pop(explode('/', $_SERVER['PHP_SELF']))) {
	case 'site_design.html': $_key = 0; break;
	case 'index.html': $_key = 1; break;
	case 'intro.html': $_key = 2; break;
	case 'top_frame_design.html': $_key = 3; break;
	case 'left_frame_design.html': $_key = 4; break;
	case 'main_design.html': $_key = 5; break;
	case 'page_design.html': $_key = 6; break;
}

$_sub_style[$_key] = ' style="font-weight: bold"';
$_title_texts = array('전체설정', '로고설정', '인트로설정', '상단메뉴설정', '좌측메뉴설정', '메인페이지 디자인설정', '페이지별 디자인설정');

$rankup_control->print_admin_head('디자인설정 - '.$_title_text[$_key]);
?>
<body>
	<div class="bar"><script> titleBar('디자인설정 - <?=$_title_texts[$_key]?>', 400) </script></div>
	<div id="topmenu" style="margin-bottom:30px">
		<ul>
			<li><a href="../rankup_builder/site_design.html"<?=$_sub_style[0]?>>전체설정</a></li>
			<li><a href="../rankup_logo/index.html"<?=$_sub_style[1]?>>로고설정</a></li>
			<li><a href="../rankup_builder/intro.html"<?=$_sub_style[2]?>>인트로설정</a></li>
			<li><a href="../rankup_builder/top_frame_design.html"<?=$_sub_style[3]?>>상단메뉴설정</a></li>
			<li><a href="../rankup_builder/left_frame_design.html"<?=$_sub_style[4]?>>좌측메뉴설정</a></li>
			<li><a href="../rankup_builder/main_design.html"<?=$_sub_style[5]?>>메인페이지 디자인설정</a></li>
			<li><a href="../rankup_builder/page_design.html"<?=$_sub_style[6]?>>페이지별 디자인설정</a></li>
		</ul>
	</div>
