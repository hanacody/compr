<?php
include_once "../../Libs/_php/rankup_basic.class.php";
$rankup_control->check_member('', true);

// ��� ���� üũ
$result = $rankup_member->match_table($_POST['uid'], $_POST['passwd']);
if(!$result) $rankup_control->popup_msg_js($_STRINGSET[305]); //  305 - ���� ��ġ ����
else {
	$member_info = $rankup_member->get_member_often($_POST['uid']);
	// Ż��ó��
	$_val['secession'] = "yes";
	$_val['secession_reason'] = stripslashes($_POST['secession_reason']);
	$_val['secession_wdate'] = date("Y-m-d H:i:s");
	$values = $rankup_control->change_query_string($_val);
	$result = $rankup_control->query("update $rankup_member->member_table2 set $values where $rankup_member->member_id='$_POST[uid]'");
	if(!$result) $rankup_control->popup_msg_js($_STRINGSET[103]); // 103 - Ż�� ����
	else {
		// ���Ǵݱ�
		$rankup_member->delete_member_session();
	}
}
?>
<html>
<head>
<title>ȸ��Ż��</title>
<link rel="stylesheet" type="text/css" href="../../style/style.css">
<script type="text/javascript">
<!--
var popClose = function() {
	window.opener.location.href="../../main/index.html";
	window.close();
}
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" align="center" cellspacing="0" cellpadding='0'>
<tr>
	<td>
		<!-- Ÿ��Ʋ -->
		<table width='100%' border='0' cellpadding='0' cellspacing='0'>
		<tr>
			<td background="./img/pop_bg01.gif"><img src="./img/pop_secession.gif"></td>
			<td background="./img/pop_bg01.gif" align="right"><img src="./img/pop_bg02.gif"></td>
		</tr>
		<tr><td height="2" bgcolor="dddddd" colspan="2"></td></tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td align='center'><font class='blue_b'>���������� ȸ��Ż�� �Ǿ����ϴ�. <br> �������Ʈ�� �̿����ּż� �����մϴ�.</font></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td align='center'><a href="javascript:popClose()"><img src='./img/b_pop_close.gif' border='0'></a></td>
</tr>
</table>
</body>
</html>