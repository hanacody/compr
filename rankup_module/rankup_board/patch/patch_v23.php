<?php
include_once "../../../Libs/_php/rankup_basic.class.php";

/*
 * 랭크업 멀티게시판 v2.1 패치
 * @author: yeong june, park
 * @note: ① 2차댓글 패치시 기존의 코멘트테이블에 컬럼추가
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