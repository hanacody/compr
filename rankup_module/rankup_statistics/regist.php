<?php
include '../../Libs/_php/rankup_basic.class.php';
$mode= $rankup_control->getParam('mode');
$no=$rankup_control->getParam('no');


//직접입력 내용을 삭제 하는 부분
if($mode=='hand_del' || $mode=='direct_del') :
	if($mode=='hand_del')
		$type='hand';
	elseif($mode=='direct_del')
		$type='direct';
	$result=$rankup_control->delete_search_table($no,$type);
	if($result)
		$rankup_control->popup_msg_js($rankup_control->delete_true);
	else
		$rankup_control->popup_msg_js($rankup_control->delete_false);

//직접입력검색어를 수정하는 부분
elseif($mode=='hand_modify') :	
	$content=$rankup_control->getParam('key');
	$result=$rankup_control->update_search_table($no,$content,'hand');
	if($result)
		$rankup_control->popup_msg_js($rankup_control->change_true);
	else
		$rankup_control->popup_msg_js($rankup_control->change_false);

//통합 검색어 전체를 삭제
elseif($mode=='direct_all_del') :
	$result=$rankup_control->delete_search_all('direct');
	if($result)
		$rankup_control->popup_msg_js($rankup_control->delete_true);
	else
		$rankup_control->popup_msg_js($rankup_control->delete_false);

//직접입력, 통하검색어 보기인지를 설정 변경
elseif($mode=='view_choice') :
	$search_view_num=$rankup_control->getParam('search_view_num');
	$search_mode=$rankup_control->getParam('search_mode');
	$result1=$rankup_control->set_config_info('search_view_num',$search_view_num);
	$result2=$rankup_control->set_config_info('search_mode',$search_mode);
	if($result1 && $result2)
		$rankup_control->popup_msg_js($rankup_control->insert_true);
	else
		$rankup_control->popup_msg_js($rankup_control->insert_false);

//직접입력 검색어의 랭킹을 변경하는 경우
elseif($mode=='rank') :
	$direction=$rankup_control->getParam('direction');
	$table=$rankup_control->get_search_table('hand');	
	
	//이동할 랭킹이 있는지 체크
	$next_step=$rankup_control->check_valid_rank($no,$table,$direction);
	if($direction=='up' && !$next_step)
		$rankup_control->popup_msg_js($rankup_control->first_rank);
	elseif($direction=='down' && !$next_step)
		$rankup_control->popup_msg_js($rankup_control->last_rank);

	$result=$rankup_control->change_list_rank($no,$table,$direction);

	if($result)
		$rankup_control->popup_msg_js($rankup_control->change_true);
	else
		$rankup_control->popup_msg_js($rankup_control->change_false);

endif;
?>