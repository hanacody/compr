<?php
/*
 * 랭크업 멀티게시판 v2.1 패치
 * @author: kurokisi
 * @note: ① 게시물 분류 순위 및 게시판 내 조회수 BEST 출력갯수 패치
 *            ② 사이트환경설정 내 smlayout 패치
 *            ③ 게시판 카테고리 내 sprint 패치
 * @date: 2009.09.09
 */
include_once "../../../Libs/_php/rankup_basic.class.php";
include_once "../rankup_board.class.php";

$rankup_board = new rankup_board;
$rankup_connection = $rankup_control->conn_class;

// 미리보기 적용
exec("mysql -u$rankup_connection->db_id -p$rankup_connection->db_passwd $rankup_connection->db_name < {$rankup_board->base_dir}rankup_module/rankup_board/patch/patch_preview.sql");

// ① 게시물 분류 순위 및 게시판내 조회수 BEST 출력 갯수 패치
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
					'rank' => $rank++ // 분류 순위 설정
				);
			}
			$_val['scategory'] = serialize($scategory);
		}
		$soptions = unserialize($board_infos['soption']);
		$soptions['hit_best_num'] = 5;
		$_val['soption'] = serialize($soptions); // 조회수 BEST 출력갯수 초기값 설정

		$values = $rankup_board->change_query_string($_val);
		$rankup_board->query("update $rankup_board->bconfig_table set $values where no=$board_infos[no]");
		unset($_val);
	}
}

// ② 사이트환경설정 내 smlayout 패치 - 메인페이지
// $smlayout['sprint'] = serialize() 된 값에서 array() 값으로 변경됨
$sconfig_infos = $rankup_board->queryFetch("select smlayout from $rankup_board->sconfig_table");
$smlayouts = unserialize($sconfig_infos['smlayout']);
$sprint = @unserialize($smlayouts['sprint']);
$_xVal['smlayout'] = serialize(array(
	'mskin' => $smlayouts['mskin'],		// 메인페이지 스킨
	'mbnum' => $smlayouts['mbnum'],		// 한줄에 출력할 게시판 수
	'sprint' => array(
		'narticle' => $sprint['narticle'],		// 신규 게시물
		'narticle_num' => 5						// 신규 게시물
	)
));
$values = $rankup_board->change_query_string($_xVal);
$rankup_board->query("update $rankup_board->sconfig_table set $values");

// ③ 게시판 카테고리 내 sprint 패치 - 메뉴페이지
$category_datas = $rankup_board->queryFetchRows("select no, sprint from $rankup_board->category_table");
if($rankup_board->check_resource($category_datas)) {
	foreach($category_datas as $category_infos) {
		if(!$category_infos['sprint']) continue;
		$sprint = unserialize($category_infos['sprint']);
		$_wVal['sprint'] = serialize(array(
			"wbest" => $sprint['wbest'],		// 이번주 베스트
			"hcbest" => $sprint['hcbest'],		// 조회수/댓글수 베스트
			"narticle" => $sprint['narticle'],	// 신규 게시물
			"wbest_num" => 5,					// 이번주 베스트
			"hcbest_num" => 5,					// 조회수/댓글수 베스트
			"narticle_num" => 5					// 신규 게시물
		));
		$values = $rankup_board->change_query_string($_wVal);
		$rankup_board->query("update $rankup_board->category_table set $values where no=$category_infos[no]");
	}
}

echo "게시판 v2.1 패치완료";
?>