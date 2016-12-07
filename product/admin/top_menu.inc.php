<?php
/**
 * 제품관리 환경설정
 */

switch(array_pop(explode('/', $_SERVER['PHP_SELF']))) {
	case 'setting.html': $_key = 0; break;
	case 'pcategory.html': $_key = 1; break;
}
$_sub_style[$_key] = ' style="font-weight:bold"';
$_title_texts = array('기본환경설정', '제품카테고리설정');

$rankup_control->print_admin_head('제품관리 환경설정 -'.$_title_texts[$_key]);
?>
<body>
	<div class="bar"><script> titleBar('제품관리 환경설정 - <?=$_title_texts[$_key]?>', 400) </script></div>
	<div id="topmenu" style="margin-bottom:30px;">
		<ul>
			<li><a href="<?=$base_url?>product/admin/setting.html"<?=$_sub_style[0]?>>기본환경설정</a></li>
			<li><a href="<?=$base_url?>product/admin/pcategory.html"<?=$_sub_style[1]?>>제품카테고리설정</a></li>
		</ul>
	</div>
