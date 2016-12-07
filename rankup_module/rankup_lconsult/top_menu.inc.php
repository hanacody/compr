<?php
/**
 * 고객문자상담 관리 상단
 */

switch(basename($_SERVER['PHP_SELF'])) {
	case 'index.html': $_key = 0; break;
	case 'setting.html': $_key = 1; break;
}
$_sub_style[$_key] = ' style="font-weight:bold"';
$_title_texts = array('문자상담내역', '기본환경설정');

$rankup_control->print_admin_head('고객문자상담관리- '.$_title_text[$_key]);
?>
<body>
	<div class="bar"><script> titleBar('고객문자상담관리 - <?=$_title_texts[$_key]?>', 400) </script></div>
	<div id="topmenu" style="margin-bottom:30px">
		<ul>
			<li><a href="./index.html"<?=$_sub_style[0]?>>문자상담내역</a></li>
			<li><a href="./setting.html"<?=$_sub_style[1]?>>기본환경설정</a></li>
		</ul>
	</div>