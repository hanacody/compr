<?PHP
#start
require_once "../../../Libs/_php/rankup_basic.class.php";
require_once "../config.inc.php";

$_VAR = $_POST ? $_POST : $_GET;

## fobject �� description ���� ������ �űԵ��, ������ ����
## �Ѱܹ��� ���߿� description ���� ������ �ش� ���ڵ� ����
$_VAR[flocation] = str_replace($_SERVER[DOCUMENT_ROOT].'/','',$_VAR[flocation]);
if(empty($_VAR[flocation])) $_VAR[flocation] = "./";
if(empty($_VAR[description])) { // ����
	$ROW = $rankup_control->queryFetch("select idx from $table_name where location='$_VAR[flocation]' and object='$_VAR[fobject]'");
	if($ROW[idx]) $rankup_control->query("delete $table_name where idx=$ROW[idx]");
} else {
	$_VAR[description] = str_replace("'", "''", $_VAR[description]);	// ����
	$ROW = $rankup_control->queryFetch("select idx from $table_name where location='$_VAR[flocation]' and object='$_VAR[fobject]'");
	if($ROW[idx]) { // ����
		$rankup_control->query("update $table_name set description='$_VAR[description]' where idx=$ROW[idx]");
	} else if($_VAR[flocation] && $_VAR[fobject]) { // �űԵ��
		$rankup_control->query("insert into $table_name set location='$_VAR[flocation]', kind='$_VAR[fkind]', object='$_VAR[fobject]', description='$_VAR[description]'");
	}
}
echo "<script language='JavaScript'>";
echo "parent.document.getElementById('$_VAR[fobject]').innerHTML = '".$_VAR[description]."';";
echo "parent.descFrmDiv.style.visibility = 'hidden';";
echo "</script>";

#end
?>