<?php
/*
 * ��ũ�� ��Ƽ�Խ��� v2.1 ��ġ
 * @author: kurokisi
 * @note: �� �Խù� �з� ���� �� �Խ��� �� ��ȸ�� BEST ��°��� ��ġ
 *            �� ����Ʈȯ�漳�� �� smlayout ��ġ
 *            �� �Խ��� ī�װ� �� sprint ��ġ
 * @date: 2009.09.09
 */
include_once "../../../Libs/_php/rankup_basic.class.php";
include_once "../rankup_board.class.php";

$rankup_board = new rankup_board;
$rankup_connection = $rankup_control->conn_class;

// �̸����� ����
exec("mysql -u$rankup_connection->db_id -p$rankup_connection->db_passwd $rankup_connection->db_name < {$rankup_board->base_dir}rankup_module/rankup_board/patch/patch_preview.sql");

// �� �Խù� �з� ���� �� �Խ��ǳ� ��ȸ�� BEST ��� ���� ��ġ
$board_datas = $rankup_board->queryFetchRows("select no, scategory, soption from $rankup_board->bconfig_table");
if($rankup_board->check_resource($board_datas)) {
	foreach($board_datas as $board_infos) {
		$categories = @unserialize($board_infos['scategory']);
		if($rankup_board->check_resource($categories)) {
			$rank = 1;
			$scategory = array();
			foreach($categories as $cno=>$rows) {
				$scategory[$cno] = array(
					'name' => $rows['name'],
					'anum' => $rows['anum'],
					'rank' => $rank++ // �з� ���� ����
				);
			}
			$_val['scategory'] = serialize($scategory);
		}
		$soptions = unserialize($board_infos['soption']);
		$soptions['hit_best_num'] = 5;
		$_val['soption'] = serialize($soptions); // ��ȸ�� BEST ��°��� �ʱⰪ ����

		$values = $rankup_board->change_query_string($_val);
		$rankup_board->query("update $rankup_board->bconfig_table set $values where no=$board_infos[no]");
		unset($_val);
	}
}

// �� ����Ʈȯ�漳�� �� smlayout ��ġ - ����������
// $smlayout['sprint'] = serialize() �� ������ array() ������ �����
$sconfig_infos = $rankup_board->queryFetch("select smlayout from $rankup_board->sconfig_table");
$smlayouts = unserialize($sconfig_infos['smlayout']);
$sprint = @unserialize($smlayouts['sprint']);
$_xVal['smlayout'] = serialize(array(
	'mskin' => $smlayouts['mskin'],		// ���������� ��Ų
	'mbnum' => $smlayouts['mbnum'],		// ���ٿ� ����� �Խ��� ��
	'sprint' => array(
		'narticle' => $sprint['narticle'],		// �ű� �Խù�
		'narticle_num' => 5						// �ű� �Խù�
	)
));
$values = $rankup_board->change_query_string($_xVal);
$rankup_board->query("update $rankup_board->sconfig_table set $values");

// �� �Խ��� ī�װ� �� sprint ��ġ - �޴�������
$category_datas = $rankup_board->queryFetchRows("select no, sprint from $rankup_board->category_table");
if($rankup_board->check_resource($category_datas)) {
	foreach($category_datas as $category_infos) {
		if(!$category_infos['sprint']) continue;
		$sprint = unserialize($category_infos['sprint']);
		$_wVal['sprint'] = serialize(array(
			"wbest" => $sprint['wbest'],		// �̹��� ����Ʈ
			"hcbest" => $sprint['hcbest'],		// ��ȸ��/��ۼ� ����Ʈ
			"narticle" => $sprint['narticle'],	// �ű� �Խù�
			"wbest_num" => 5,					// �̹��� ����Ʈ
			"hcbest_num" => 5,					// ��ȸ��/��ۼ� ����Ʈ
			"narticle_num" => 5					// �ű� �Խù�
		));
		$values = $rankup_board->change_query_string($_wVal);
		$rankup_board->query("update $rankup_board->category_table set $values where no=$category_infos[no]");
	}
}

echo "�Խ��� v2.1 ��ġ�Ϸ�";
?>