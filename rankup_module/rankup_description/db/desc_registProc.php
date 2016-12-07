<?PHP
#start
require_once "../../../Libs/_php/rankup_basic.class.php";
require_once "../config.inc.php";

$_VAR = $_POST ? $_POST : $_GET;

## fobject 에 description 값이 없으면 신규등록, 있으면 수정
## 넘겨받은 값중에 description 값이 없으면 해당 레코드 삭제
if(empty($_VAR[ftable])) {
	echo "<script language='JavaScript'>";
	echo "parent.document.getElementById('descFrmDiv').style.visibility = 'hidden';";
	echo "</script>";
	exit;
}
if(empty($_VAR[description])) { // 삭제
	$ROW = $rankup_control->queryFetch("select idx from $table_name where location='$_VAR[ftable]' and object='$_VAR[fobject]'");
	if($ROW[idx]) $rankup_control->query("delete $table_name where idx=$ROW[idx]"); // 삭제
} else {
	$_VAR[description] = str_replace("'", "''", $_VAR[description]);
	$ROW = $rankup_control->queryFetch("select idx from $table_name where location='$_VAR[ftable]' and object='$_VAR[fobject]'");
	if($ROW[idx]) { // 수정
		$rankup_control->query("update $table_name set description='$_VAR[description]' where idx=$ROW[idx]");
	} else if($_VAR[ftable]) { // 신규등록
		$rankup_control->query("insert into $table_name set location='$_VAR[ftable]', object='$_VAR[fobject]', kind='T', description='$_VAR[description]'");
	}
}
echo "<script language='JavaScript'>";
echo "parent.document.getElementById('$_VAR[ftable]$_VAR[fobject]').innerHTML = '".$_VAR[description]."';";
echo "parent.document.getElementById('descFrmDiv').style.visibility = 'hidden';";
echo "</script>";

#end
?>