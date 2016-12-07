<?php
include_once "../../../Libs/_php/rankup_basic.class.php";

// 내용필드 text 에서 mediumtext 로 패치
$query = "ALTER TABLE `rankup_board_{:id:}` CHANGE `content` `content` MEDIUMTEXT NULL DEFAULT NULL";
$datas = $rankup_control->query("select id from rankup_board_config");
while($rows = mysql_fetch_assoc($datas)) {
	$rankup_control->query(str_replace('{:id:}', $rows['id'], $query));
}

?>