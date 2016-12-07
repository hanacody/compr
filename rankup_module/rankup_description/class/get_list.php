<?PHP
#start
require_once "../../../Libs/_php/rankup_basic.class.php";
require_once "../config.inc.php";

$_VAR = $_POST ? $_POST : $_GET;

// 클래스 리스트
if(empty($_VAR['fclass'])) {

	$DATA = $rankup_control->queryFetchRows("select * from $table_name where kind='C' and object='' order by location");
	if(is_array($DATA)) {
		foreach($DATA as $ROW) {
			if($is_Demo) {
				$aLink = "alert('죄송합니다. 데모버젼에서는 편집할 수 없습니다.          ')";
				$dLink = "alert('죄송합니다. 데모버젼에서는 삭제할 수 없습니다.          ')";
			}
			else {
				$aLink = "description.classRegister(arguments[0], $ROW[idx])";
				$dLink = "description.classDelete($ROW[idx]);";
			}
			$modify_btn = "<a href='javascript:void(0)' onClick=\"$aLink\"><img src='../img/icon_modify.gif' border=0 align=absmiddle alt='편집'></a>";
			$desc = "<a href='javascript:void(0)' onClick=\"$aLink\"><div id='description_id$ROW[idx]'>$ROW[description]</div></a>";

			$tableList .= "
			<tr height=20$bgcolor>
				<td>
					<table width=100% cellpadding=0 cellspacing=0>
					<tr>
						<td width=20><img src='../img/icon_class.gif' align=absmiddle alt=' CLASS명 '></td>
						<td><a href='javascript:void(0)' onClick=\"description.getClassInfo(this.innerHTML,arguments[0])\" title='클릭-내용보기' id='fclass_id$ROW[idx]'>$ROW[location]</a></td>
					</tr>
					</table>
				</td>
				<td>
					<table width=100% cellpadding=0 cellspacing=0>
					<tr>
						<td width=24>$modify_btn</td>
						<td>$desc</td>
					</tr>
					</table>
				</td>
				<td><a href='javascript:void(0)' onClick='description.descriptDelete($ROW[idx])'><img src='../img/btn_del.gif' border='0'></a></td>
			</tr>";
			$bgcolor = empty($bgcolor) ? " bgcolor=#F7F7F7" : "";
		}
	}

	$tableList = "<table width=100% cellpadding=3 cellspacing=1 bgcolor='#FFFFFF'><col width=250><col><col width=30 align='center'><tbody bgcolor=white><tr height=1 bgcolor=#ACACAC><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td></tr><tr align=center bgcolor=#EDEDED><td class=d8>Class Name</td><td class=d8>Comment</td><td class=d8>Delete</td></tr>".$tableList."<tr height=1 bgcolor=#DEDEDE><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td></tr></tbody></table>";

}
// 메소드 리스트
else {

	$_VAR['fclass'] = iconv("UTF-8", "CP949", $_VAR['fclass']);
	$DATA = $rankup_control->queryFetchRows("select * from $table_name where kind='C' and location='$_VAR[fclass]' and object<>''");
	if(is_array($DATA)) {
		foreach($DATA as $ROW) {
			if($is_Demo) {
				$aLink = "alert('죄송합니다. 데모버젼에서는 편집할 수 없습니다.          ')";
				$dLink = "alert('죄송합니다. 데모버젼에서는 삭제할 수 없습니다.          ')";	
			}
			else {
				$aLink = "description.methodRegister(arguments[0], $ROW[idx])";
				$dLink = "description.methodDelete($ROW[idx]);";
			}
			$modify_btn = "<a href='javascript:void(0)' onClick=\"$aLink\"><img src='../img/icon_modify.gif' border=0 align=absmiddle alt='편집'></a>";
			$desc = "<a href='javascript:void(0)' onClick=\"$aLink\"><div id='description_id$ROW[idx]'>".nl2br($ROW['description'])."</div></a>";

			$tableList .= "
			<tr valign='top' height=22$bgcolor>
				<td style='padding-top:2px;font-weight:normal;font-family:verdana'><a href='javascript:void(0)' onClick=\"$aLink\" style='color:#0066CC;'><b style='color:#003399'>function </b><span id='fmethod_id$ROW[idx]'>$ROW[object]</span></a></td>
				<td>
					<table width=100% cellpadding=0 cellspacing=0>
					<tr valign='top'>
						<td width=24>$modify_btn</td>
						<td style='padding-top:2px;font-family:verdana'>$desc</td>
					</tr>
					</table>
				</td>
				<td><a href='javascript:void(0)' onClick='description.descriptDelete($ROW[idx])'><img src='../img/btn_del.gif' border='0'></a></td>
			</tr>";
			$bgcolor = empty($bgcolor) ? " bgcolor=#F7F7F7" : "";
		}
	}
	$tableList = "
	<table width=100% cellpadding=4 cellspacing=1 bgcolor='#FFFFFF'>
	<col width=250><col><col width=30 align=center>
	<tbody bgcolor=white>
	<tr height=26 valign='bottom'>
		<td colspan='3' style='color:#0033CC;padding:0px'>
			<table width='100%' cellpadding='3' cellspacing='0' border='0'>
			<tr>
				<td width='250'><a href='javascript:void(0)' onClick=\"$('viewInfoDiv').style.display=$('methodRegistFrmDiv').style.display='none'\" title='클릭-내용닫기' style='font-weight:bolder;color:#3399FF;font-family:verdana'><img src='../img/icon_class.gif' align='absmiddle' alt='CLASS명' border=0> <span id='fclass_name'>".$_VAR['fclass']."</span></a></td>
				<td align='left'><a href='javascript:void(0)' onClick='description.methodRegister(arguments[0])'><img src='../img/btn_submit2.gif' alt='메소드등록' border=0 align='absmiddle'></a></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr height=1 bgcolor=#ACACAC>
		<td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td>
	</tr>
	<tr align=center bgcolor=#EDEDED>
		<td class=d8>Method Name</td><td class=d8>Comment</td><td class=d8>Delete</td>
	</tr>
	$tableList
	<tr height=1 bgcolor=#DEDEDE>
		<td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td>
	</tr>
	<tr height=36 align='center'>
		<td colspan=8><a href='javascript:void(0)' onClick='description.methodRegister(arguments[0])'><img src='../img/btn_submit2.gif' alt='메소드등록' border=0 align='absmiddle'></a> <a href='javascript:void(0)' onClick=\"$('viewInfoDiv').style.display=$('methodRegistFrmDiv').style.display='none'\"><img src='../img/btn_close.gif' alt='내용닫기' border=0 align='absmiddle'></a></td>
	</tr>
	</tbody>
	</table>";
}
echo ($is_UNICODE) ? iconv("CP949","UTF-8",$tableList) : $tableList;
#end
?>
