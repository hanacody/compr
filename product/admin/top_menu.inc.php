<?php
/**
 * ��ǰ���� ȯ�漳��
 */

switch(array_pop(explode('/', $_SERVER['PHP_SELF']))) {
	case 'setting.html': $_key = 0; break;
	case 'pcategory.html': $_key = 1; break;
}
$_sub_style[$_key] = ' style="font-weight:bold"';
$_title_texts = array('�⺻ȯ�漳��', '��ǰī�װ�����');

$rankup_control->print_admin_head('��ǰ���� ȯ�漳�� -'.$_title_texts[$_key]);
?>
<body>
	<div class="bar"><script> titleBar('��ǰ���� ȯ�漳�� - <?=$_title_texts[$_key]?>', 400) </script></div>
	<div id="topmenu" style="margin-bottom:30px;">
		<ul>
			<li><a href="<?=$base_url?>product/admin/setting.html"<?=$_sub_style[0]?>>�⺻ȯ�漳��</a></li>
			<li><a href="<?=$base_url?>product/admin/pcategory.html"<?=$_sub_style[1]?>>��ǰī�װ�����</a></li>
		</ul>
	</div>
