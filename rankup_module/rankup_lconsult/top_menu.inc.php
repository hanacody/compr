<?php
/**
 * �����ڻ�� ���� ���
 */

switch(basename($_SERVER['PHP_SELF'])) {
	case 'index.html': $_key = 0; break;
	case 'setting.html': $_key = 1; break;
}
$_sub_style[$_key] = ' style="font-weight:bold"';
$_title_texts = array('���ڻ�㳻��', '�⺻ȯ�漳��');

$rankup_control->print_admin_head('�����ڻ�����- '.$_title_text[$_key]);
?>
<body>
	<div class="bar"><script> titleBar('�����ڻ����� - <?=$_title_texts[$_key]?>', 400) </script></div>
	<div id="topmenu" style="margin-bottom:30px">
		<ul>
			<li><a href="./index.html"<?=$_sub_style[0]?>>���ڻ�㳻��</a></li>
			<li><a href="./setting.html"<?=$_sub_style[1]?>>�⺻ȯ�漳��</a></li>
		</ul>
	</div>