<?php
/*
 * ���� �Խ��� ������ ��ũ��Ʈ
 * @tesk : �������������� ������ �����ߴ� �Խ��� ����
 * @author: kurokisi
 */
include_once "../Libs/_php/rankup_basic.class.php";
include_once "../rankup_module/rankup_board/rankup_board.class.php";
$rankup_board = new rankup_board;

// ��ũ�� ��Ƽ�Խ����� �ʼ� ���̺� ����
$required_tables = array('rankup_board_category', 'rankup_board_config', 'rankup_board_division', 'rankup_board_comment_best', 'rankup_board_comment_hit_best', 'rankup_board_new_article', 'rankup_board_weekly_best', 'rankup_board_weekly_cbest');
$datas = $rankup_board->query("select id from $rankup_board->bconfig_table");
if($rankup_board->check_resource($datas)) {
	while($rows = mysql_fetch_assoc($datas)) {
		$required_tables[] = 'rankup_board_'.$rows['id'];
		$required_tables[] = 'rankup_board_comment_'.$rows['id'];
	}
}
// ���ʿ��� �Խ��� ������ ����
$datas = $rankup_board->query("show tables like 'rankup_board_%'");
if($rankup_board->check_resource($datas)) {
	$drop_tables = array();
	while($rows = mysql_fetch_array($datas, MYSQL_NUM)) {
		if(in_array($rows[0], $required_tables)) continue;
		$drop_tables[] = $rows[0];
		if(eregi('rankup_board_comment', $rows[0])===false) {
			$board = str_replace('rankup_board_', '', $rows[0]);
			// ÷�� ���� ����
			exec("rm -rf {$rankup_board->base_dir}rankup_module/rankup_board/attach/$board");
		}
	}
	// ���̺� ����
	if($rankup_board->check_resource($drop_tables)) {
		$rankup_board->query("drop table ".@implode(',', $drop_tables));
		echo mysql_affected_rows()." �� ���� ���̺� ����/÷������ ���� �Ϸ�";
	}
}
?>