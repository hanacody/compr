<?php
/**
 * ȸ�����Ϲ߼� ���
 */

switch(array_pop(explode('/', $_SERVER['PHP_SELF']))) {
	case 'mailing_send.html': $_key = 0; break;
	case 'mailing_list.html': $_key = 1; break;
	case 'mailing_config.html': $_key = 2; break;
}
$_sub_style[$_key] = ' style="font-weight:bold"';
$_title_texts = array('�������͹߼�', '�߼۳�����ȸ', '����������');

$rankup_control->print_admin_head('�������͹߼۰��� - '.$_title_texts[$_key]);
?>
<body>
	<div class="bar"><script> titleBar('�������͹߼۰��� - <?=$_title_texts[$_key]?>', 400) </script></div>
	<div id="topmenu" style="margin-bottom:30px">
		<ul>
			<li><a href="../rankup_mailing/mailing_send.html"<?=$_sub_style[0]?>>�������͹߼�</a></li>
			<li><a href="../rankup_mailing/mailing_list.html"<?=$_sub_style[1]?>>�߼۳�����ȸ</a></li>
			<li><a href="../rankup_mailing/mailing_config.html"<?=$_sub_style[2]?>>����������</a></li>
		</ul>
	</div>