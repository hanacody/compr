<?php
/**
 * ���۷α׺м� ����
 */
switch(basename($_SERVER['PHP_SELF'])) {
	case 'dimensions.html': $_key = 0; break;
	case 'pages.html': $_key = 1; break;
	case 'systems.html': $_key = 2; break;
	case 'index.html': $_key = 3; break;
}
$_sub_style[$_key] = ' style="font-weight:bold"';
$_title_texts = array('�������', '���������', '�ý������', 'ȯ�漳��');
$rankup_control->print_admin_head('���۷α׺м����� -'.$_title_texts[$_key]);
?>
<style>
#topmenu{position:relative;background-color:#eeeeee;border-bottom:1px solid #dddddd;padding:15px 0px 10px 0px;text-align:center !important;}
#topmenu ul{display:inline;margin:0;margin-left:0 auto;margin-right:auto;padding:0;width:780px;text-align:left;}
#topmenu li{display:inline;background:url('../../Libs/_images/ic_arrow1.gif') no-repeat 0 35%;padding:0 0 0 10px;margin:0 7px;height:20px;line-height:200%;}
#topmenu .menu2 li{display:inline;background:url('../../Libs/_images/ic_arrow1.gif') no-repeat 0 35%;padding:0 0 0 10px;margin:0 5px;height:20px;line-height:200%;}
#topmenu li a{color:#314194;text-decoration:none;}
#topmenu li a:hover{text-decoration:underline;}
#topmenu li.selected{font-weight:bold}
</style>
<body>
<script type="text/javascript">
//2012-03-22 add
var log_reset = function() {
	if(!confirm('������踦 �ʱ�ȭ �Ͻðڽ��ϱ�?')) return false;
	proc.parameters({mode:'log_reset'});
	proc.process(function(trans) {
		if(trans.responseText.blank()) {
			alert('������踦 �ʱ�ȭ �Ͽ����ϴ�.');
			document.location.reload();
		}
		else {
			alert(trans.responseText);
		}
	});
}
</script>
<div class="bar"><script> titleBar('���۷α׺м����� - <?=$_title_texts[$_key]?>', 400) </script></div>
<div id="topmenu" style="margin-bottom:30px;">
	<ul>
		<li><a href="./dimensions.html"<?=$_sub_style[0]?>>�������</a></li>
		<li><a href="./pages.html"<?=$_sub_style[1]?>>���������</a></li>
		<li><a href="./systems.html"<?=$_sub_style[2]?>>�ý������</a></li>
		<li><a href="./index.html"<?=$_sub_style[3]?>>ȯ�漳��</a></li>
		<li><a onclick="log_reset();">������� �ʱ�ȭ</a></li>
	</ul>
</div>
