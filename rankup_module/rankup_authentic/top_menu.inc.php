<?php
/**
 * 구글로그분석 관리
 */
switch(basename($_SERVER['PHP_SELF'])) {
	case 'index.html': $_key = 0; break;
	case 'form_setting.html': $_key = 1; break;
	case 'page_design.html': $_key = 2; break;
}
$_sub_style[$_key] = ' style="font-weight:bold"';
$_title_texts = array('기본설정', '인증폼설정', '성인인증페이지설정');

$rankup_control->print_admin_head('실명인증설정 -'.$_title_texts[$_key]);
?>
<body>
	<div class="bar"><script> titleBar('실명인증설정 - <?=$_title_texts[$_key]?>', 400) </script></div>
	<div id="topmenu" style="margin-bottom:30px;">
		<ul>
			<li><a href="./index.html"<?=$_sub_style[0]?>>기본설정</a></li>
			<li><a href="./form_setting.html"<?=$_sub_style[1]?>>인증폼설정</a></li>
			<!--<li><a href="./page_design.html"<?=$_sub_style[2]?>>성인인증페이지설정</a></li>-->
		</ul>
	</div>
