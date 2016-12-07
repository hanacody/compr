<?PHP
#start
require_once "../../../Libs/_php/rankup_basic.class.php";
require_once "../config.inc.php";

$_VAR = $_POST ? $_POST : $_GET;

if(empty($_VAR[table])) {  // ���̺� ����Ʈ

	$rankup_connection = $rankup_control->conn_class;

	$CDATA = $rankup_control->queryFetchRows("select * from $table_name where kind='T' and object=''");
	if(is_array($CDATA)) foreach($CDATA as $CROW) $DESCRIPTION[$CROW[location]] = $CROW[description];

	$DATAS = array();
	$DATA = $rankup_control->queryFetchRows("show tables");
	foreach($DATA as $rows) {
		$value = array_values($rows);
		$ROW = $DATAS[] = $value[0];
		$aLink = ($is_Demo) ? "alert('�˼��մϴ�. ������������� ������ �� �����ϴ�.          ')" : "viewDescFrmDiv('$ROW','',arguments[0])";
		$modify_btn = "<a href='javascript:void(0)' onClick=\"$aLink\"><img src='../img/icon_modify.gif' border=0 align=absmiddle alt='����'></a>";
		$desc = ($ROW==$dir) ? "" : "<a href='javascript:void(0)' onClick=\"$aLink\"><div id='".$ROW."'>".$DESCRIPTION[$ROW]."</div></a>";

		$tableList .= "<tr height=20$bgcolor><td><table width=100% cellpadding=0 cellspacing=0><tr><td width=20><img src='../img/icon_table.gif' align=absmiddle alt=' TABLE�� '></td><td><a href='javascript:void(0)' onClick=\"getInfo('$ROW',arguments[0])\" title='Ŭ��-���뺸��'>$ROW</a></td></tr></table></td><td><table width=100% cellpadding=0 cellspacing=0><tr><td width=24>$modify_btn</td><td>$desc</td></tr></table></td></tr>";
		$bgcolor = empty($bgcolor) ? " bgcolor=#F7F7F7" : "";
	}
	// �������� �ʴ� ��ü(���̺�)�� ���� Record ���� ó��
	if(is_array($CDATA)) {
		foreach($CDATA as $CROW) if(!in_array($CROW[location],$DATAS)) $delRecord[] = $CROW[idx];
		if(is_array($delRecord)) $rankup_control->query("delete from $table_name where idx in(".join(',',$delRecord).")");
	}
	$tableList = "<table width=100% cellpadding=3 cellspacing=1 bgcolor='#FFFFFF'><col width=250><col><tbody bgcolor=white><tr><td colspan=2 style='color:#0033CC'><img src='../img/icon_host.gif' align=absmiddle alt='HOST��'> ".$rankup_connection->db_host." &nbsp; <img src='../img/icon_db.gif' align=absmiddle alt='DB��'> ".$rankup_connection->db_name."</td></tr><tr height=1 bgcolor=#ACACAC><td style='padding:0px'></td><td style='padding:0px'></td></tr><tr align=center bgcolor=#EDEDED><td class=d8>Table Name</td><td class=d8>Comment</td></tr>".$tableList."<tr height=1 bgcolor=#DEDEDE><td style='padding:0px'></td><td style='padding:0px'></td></tr></tbody></table>";

} else { 	// ���̺� �ʵ� ����

	$CDATA = $rankup_control->queryFetchRows("select * from $table_name where kind='T' and location='$_VAR[table]' and object<>''");
	if(is_array($CDATA)) foreach($CDATA as $CROW) $DESCRIPTION[$CROW[object]] = $CROW[description];

	$DATA = $rankup_control->queryFetchRows("desc $_VAR[table]");
	foreach($DATA as $ROW) { // Field, Type, Null (YES|NO), Key(PRI|UNI|MUL| ), Default(NULL|), Extra (auto_increment)
		$flags = explode(" ",$ROW[Type]);
		if(count($flags)>1) {
			$flags = strtoupper(array_pop($flags));
			$ROW[Type] = eregi_replace($flags,'',$ROW[Type]);
		} else $flags = '';
		$type = array_pop(array_reverse(explode("(",$ROW[Type])));
		$ROW[Type] = str_replace($type,strtoupper($type),str_replace(",","</font>,<font color=#3399CC>",str_replace(")","</font>)",str_replace("(","(<font color=#3399CC>",$ROW[Type]))));
		$type = (in_array($type,array("int","float","double","integer","tinyint","smallint","mediumint","bigint") ) || $type=="enum") ? ($type=="enum") ? "enum" : "num" : "char";
		$default_value = (is_numeric($ROW['Default']) || empty($ROW['Default'])) ? $ROW['Default'] : "'$ROW[Default]'";

		// ������
		$FIELD[] = $ROW[Field];

		// �⺻ ������ ����
		$key_icon = ($ROW[Key]=="PRI") ? "<img src='../img/icon_primary.gif' align=absmiddle alt='Primary Key'>" : "<img src='../img/icon_normal.gif' align=absmiddle>";
		$type_icon = "<img src='../img/icon_$type.gif' align=absmiddle>";
		$null_icon = ($ROW['Null']=="") ? "<img src='../img/icon_check.gif' alt='check'>" : "";
		$ai_icon = ($ROW[Extra]=="auto_increment") ? "<img src='../img/icon_check.gif' alt='check'>" : "";

		// ��ũ �� �ڸ�Ʈ
		$aLink = ($is_Demo) ? "alert('�˼��մϴ�. ������������� ������ �� �����ϴ�.          ')" : "viewDescFrmDiv('$_VAR[table]','$ROW[Field]',arguments[0])";
		$modify_btn = "<a href='javascript:void(0)' onClick=\"$aLink\"><img src='../img/icon_modify.gif' border=0 align=absmiddle alt='����'></a>";
		$desc = ($ROW==$dir) ? "" : "<a href='javascript:void(0)' onClick=\"$aLink\"><div id='".$_VAR[table].$ROW[Field]."'>".$DESCRIPTION[$ROW[Field]]."</div></a>";

		$tableList .= "<tr height=22$bgcolor><td><table width=100% cellpadding=0 cellspacing=0><tr><td width=14>$key_icon</td><td class=gd8>$ROW[Field]</td></tr></table></td><td><table width=100% cellpadding=0 cellspacing=0><tr><td width=20>$type_icon</td><td class=gd8>$ROW[Type]</td></tr></table></td><td>$null_icon</td><td>$ai_icon</td><td class=gd8>$flags</td><td class=gd8 style=color:#3399CC>$default_value</td><td><table width=100% cellpadding=0 cellspacing=0><tr><td width=24>$modify_btn</td><td>$desc</td></tr></table></td></tr>";
		$bgcolor = empty($bgcolor) ? " bgcolor=#F7F7F7" : "";
	}
	// �������� �ʴ� ��ü(���̺�)�� ���� Record ���� ó��
	if(is_array($CDATA)) {
		foreach($CDATA as $CROW) if(!in_array($CROW[object],$FIELD)) $delRecord[] = $CROW[idx];
		if(is_array($delRecord)) $rankup_control->query("delete from $table_name where idx in(".join(',',$delRecord).")");
	}
	$tableList = "<table width=100% cellpadding=3 cellspacing=1 bgcolor='#FFFFFF'><col width=160><col width=150><col width=20 align=center><col width=26 align=center><col width=100 align=center><col width=112><col><tbody bgcolor=white><tr height=26 valign='bottom'><td colspan=8 style='color:#0033CC'><a href='javascript:void(0)' onClick=\"document.getElementById('viewInfoDiv').style.visibility=document.getElementById('descFrmDiv').style.visibility='hidden'\" title='Ŭ��-����ݱ�'><img src='../img/icon_table.gif' align=absmiddle alt='TABLE��' border=0> ".$_VAR['table']."</a></td></tr><tr height=1 bgcolor=#ACACAC><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td></tr><tr align=center bgcolor=#EDEDED><td class=d8>Column Name</td><td class=d8>Data Type</td><td style='font-size:7pt;font-family:dotum'>NOT<br />NULL</td><td style='font-size:6pt;font-family:dotum'>AUTO<br />INC</td><td class=d8>Flags</td><td class=d8>Default Value</td><td class=d8>Comment</td></tr>".$tableList."<tr height=1 bgcolor=#DEDEDE><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td></tr><tr height=36 align='center'><td colspan=8><a href='javascript:void(0)' onClick=\"document.getElementById('viewInfoDiv').style.visibility=document.getElementById('descFrmDiv').style.visibility='hidden'\"><img src='../img/btn_close.gif' alt='����ݱ�' border=0></a></td></tr></tbody></table>";
}
echo ($is_UNICODE) ? iconv("CP949","UTF-8",$tableList) : $tableList;
#end
?>
