<?php
/**
 * 회원관리 환경설정 관련 상단
 */

if($_GET['kind']=='agreement') $_key = 1;
if($_GET['kind']=='mem_privacy') $_key = 2;
switch(basename($_SERVER['PHP_SELF'])) {
	case 'index.html':
		if(strpos($_SERVER['PHP_SELF'], 'rankup_member')) $_key = 0;
		else if(strpos($_SERVER['PHP_SELF'], 'rankup_authentic')) $_key = 3;
		else $_key = 4; break;
	case 'authentic_manager.html': $_key = 3; break;
}
$_sub_style[$_key] = ' style="font-weight:bold"';
$_title_texts = array('기본환경설정', '회원이용약관', '개인정보수집 및 안내', '실명인증설정', '메일폼설정');

$rankup_control->print_admin_head('회원관리 환경설정 - '.$_title_text[$_key]);
?>
<body>
	<div class="bar"><script> titleBar('회원관리 환경설정 - <?=$_title_texts[$_key]?>', 400) </script></div>
	<div id="topmenu" style="margin-bottom:30px">
		<ul>
			<li><a href="../rankup_member/index.html"<?=$_sub_style[0]?>>기본환경설정</a></li>
			<li><a href="../rankup_environment/policy.html?kind=agreement"<?=$_sub_style[1]?>>회원이용약관</a></li>
			<li><a href="../rankup_environment/policy.html?kind=mem_privacy"<?=$_sub_style[2]?>>개인정보수집 및 안내</a></li>
			<li><a href="../rankup_authentic/index.html"<?=$_sub_style[3]?>>실명인증설정</a></li>
			<li><a href="../rankup_mailing/index.html"<?=$_sub_style[4]?>>메일폼설정</a></li>
		</ul>
	</div>