<?php
/**
 * ȸ������ ȯ�漳�� ���� ���
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
$_title_texts = array('�⺻ȯ�漳��', 'ȸ���̿���', '������������ �� �ȳ�', '�Ǹ���������', '����������');

$rankup_control->print_admin_head('ȸ������ ȯ�漳�� - '.$_title_text[$_key]);
?>
<body>
	<div class="bar"><script> titleBar('ȸ������ ȯ�漳�� - <?=$_title_texts[$_key]?>', 400) </script></div>
	<div id="topmenu" style="margin-bottom:30px">
		<ul>
			<li><a href="../rankup_member/index.html"<?=$_sub_style[0]?>>�⺻ȯ�漳��</a></li>
			<li><a href="../rankup_environment/policy.html?kind=agreement"<?=$_sub_style[1]?>>ȸ���̿���</a></li>
			<li><a href="../rankup_environment/policy.html?kind=mem_privacy"<?=$_sub_style[2]?>>������������ �� �ȳ�</a></li>
			<li><a href="../rankup_authentic/index.html"<?=$_sub_style[3]?>>�Ǹ���������</a></li>
			<li><a href="../rankup_mailing/index.html"<?=$_sub_style[4]?>>����������</a></li>
		</ul>
	</div>