<?php
include_once "../../../Libs/_php/rankup_basic.class.php";

/*
 * ��ũ�� ��Ƽ�Խ��� v2.1 ��ġ
 * @author: yeong june, park
 * @note: �� 2����� ��ġ�� ������ �ڸ�Ʈ���̺� �÷��߰�
 * @date: 2011. 08. 18
 */

$datas = $rankup_control->query("select id from rankup_board_config");
while($rows = mysql_fetch_array($datas)) {
	$table_id = "`rankup_board_comment_".$rows['id']."`";
	$query_ml = "ALTER TABLE {$table_id}";
	$query_ml.= "ADD `pno` VARCHAR( 10 ) NOT NULL DEFAULT '0' AFTER `ano` ,";
	$query_ml.= "ADD `cnum` VARCHAR( 10 ) NOT NULL DEFAULT '0' AFTER `pno`, ";
	$query_ml.= "ADD `remove` ENUM(  'yes',  'no' ) DEFAULT 'no' AFTER `cnum`;";
	$rankup_control->query($query_ml);
	unset($query_ml);
}

?>