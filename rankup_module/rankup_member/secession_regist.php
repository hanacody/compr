<?php
include_once "../../Libs/_php/rankup_basic.class.php";
$rankup_control->check_member('', true);

// 멤버 인지 체크
$result = $rankup_member->match_table($_POST['uid'], $_POST['passwd']);
if(!$result) $rankup_control->popup_msg_js($_STRINGSET[305]); //  305 - 정보 일치 안함
else {
	$member_info = $rankup_member->get_member_often($_POST['uid']);
	// 탈퇴처리
	$_val['secession'] = "yes";
	$_val['secession_reason'] = stripslashes($_POST['secession_reason']);
	$_val['secession_wdate'] = date("Y-m-d H:i:s");
	$values = $rankup_control->change_query_string($_val);
	$result = $rankup_control->query("update $rankup_member->member_table2 set $values where $rankup_member->member_id='$_POST[uid]'");
	if(!$result) $rankup_control->popup_msg_js($_STRINGSET[103]); // 103 - 탈퇴 실패
	else {
		// 세션닫기
		$rankup_member->delete_member_session();
	}
}
?>
<html>
<head>
<title>회원탈퇴</title>
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
		<!-- 타이틀 -->
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
	<td align='center'><font class='blue_b'>정상적으로 회원탈퇴 되었습니다. <br> 저희사이트를 이용해주셔서 감사합니다.</font></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td align='center'><a href="javascript:popClose()"><img src='./img/b_pop_close.gif' border='0'></a></td>
</tr>
</table>
</body>
</html>