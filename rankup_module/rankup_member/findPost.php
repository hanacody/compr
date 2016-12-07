<?PHP
include_once "../../Libs/_php/rankup_basic.class.php";

$rankup_control->change_encoding($_GET, "IN");
$DATA = $rankup_control->queryFetchRows("select * from rankup_zipcode where DONG like '%$_GET[dong]%' order by DONG");
if(is_array($DATA)) {
	foreach($DATA as $ROW) $list .= "<tr><td><a href='javascript:void(0)' onClick=\"applyPost('$ROW[ZIPCODE]', '$ROW[SIDO] $ROW[GUGUN] $ROW[DONG]')\">$ROW[SIDO] $ROW[GUGUN] $ROW[DONG] $ROW[BUNJI]</a></td></tr>";
	if(empty($list)) $list = "<tr><td>검색결과가 존재하지 않습니다.</td></tr>";
	$postList = "<table width=100% cellpadding=1 cellspacing=0>$list</table>";
}

$rankup_control->change_encoding($postList, "OUT");
header("Content-type: text/xml; charset=utf-8");
echo "<?xml version='1.0' encoding='utf-8'?>";
echo "<postData>";
echo "<postList>";
echo "<![CDATA[$postList]]>";
echo "</postList>";
echo "</postData>";
?>