<?php
/**
 * 회원메일발송 상단
 */

switch(array_pop(explode('/', $_SERVER['PHP_SELF']))) {
	case 'mailing_send.html': $_key = 0; break;
	case 'mailing_list.html': $_key = 1; break;
	case 'mailing_config.html': $_key = 2; break;
}
$_sub_style[$_key] = ' style="font-weight:bold"';
$_title_texts = array('뉴스레터발송', '발송내역조회', '메일폼설정');

$rankup_control->print_admin_head('뉴스레터발송관리 - '.$_title_texts[$_key]);
?>
<body>
	<div class="bar"><script> titleBar('뉴스레터발송관리 - <?=$_title_texts[$_key]?>', 400) </script></div>
	<div id="topmenu" style="margin-bottom:30px">
		<ul>
			<li><a href="../rankup_mailing/mailing_send.html"<?=$_sub_style[0]?>>뉴스레터발송</a></li>
			<li><a href="../rankup_mailing/mailing_list.html"<?=$_sub_style[1]?>>발송내역조회</a></li>
			<li><a href="../rankup_mailing/mailing_config.html"<?=$_sub_style[2]?>>메일폼설정</a></li>
		</ul>
	</div>