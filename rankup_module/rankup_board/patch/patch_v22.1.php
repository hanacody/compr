<?php
include_once "../../../Libs/_php/rankup_basic.class.php";

// �����ʵ� text ���� mediumtext �� ��ġ
$query = "ALTER TABLE `rankup_board_{:id:}` CHANGE `content` `content` MEDIUMTEXT NULL DEFAULT NULL";
$datas = $rankup_control->query("select id from rankup_board_config");
while($rows = mysql_fetch_assoc($datas)) {
	$rankup_control->query(str_replace('{:id:}', $rows['id'], $query));
}

?>