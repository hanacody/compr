<?php
/**
 * �����μ��� ���� ���
 */

switch(basename($_SERVER['PHP_SELF'])) {
	case 'index.html': $_key = 0; break;
	case 'frame.html': $_key = 1; break;
	case 'main_design.html': $_key = 2; break;
	case 'site_design.html': $_key = 3; break;
}

$_sub_style[$_key] = ' style="font-weight: bold"';
$_title_texts = array('�⺻����', '�޴�������������', '��������������', '�����μ���');

$rankup_control->print_admin_head('����������� - '.$_title_text[$_key]);
?>
<body>
	<div class="bar"><script> titleBar('����������� - <?=$_title_texts[$_key]?>', 400) </script></div>
	<div id="topmenu" style="margin-bottom:30px">
		<ul>
			<li><a href="<?=$mobile->m_url?>builder/index.html"<?=$_sub_style[0]?>>�⺻����</a></li>
			<li><a href="<?=$mobile->m_url?>builder/frame.html"<?=$_sub_style[1]?>>�޴�������������</a></li>
			<li><a href="<?=$mobile->m_url?>builder/main_design.html"<?=$_sub_style[2]?>>��������������</a></li>
			<li><a href="<?=$mobile->m_url?>builder/site_design.html"<?=$_sub_style[3]?>>�����μ���</a></li>
		</ul>
	</div>
