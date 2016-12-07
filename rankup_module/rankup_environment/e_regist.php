<?
include "../../Libs/_php/rankup_basic.class.php";
include $wysiwyg_dir."wysiwyg_Class.php";

$rankup_control->check_admin($rankup_admin->is_admin());

##입력할 필드 이름
$mode = $rankup_control->getParam('mode');
$content = $rankup_control->getParam('content');
if($mode!="meta") $content = $Wysiwyg->wysiwyg_result_func($content);

##기본 입력인지, 삭제 인지. 작업 내역
$type = $rankup_control->getParam('type');

##값 입력인 경우
if($type=='insert') {
	$result = $rankup_control->set_config_info($mode,$content);
	if($result) $rankup_control->popup_msg_js('등록되었습니다.');
	else $rankup_control->popup_msg_js('실패 하였습니다.');
}
##콜센타 정보 수정인 경우
else if($type=='callcenter_edit') {
	$c_width = $rankup_control->getParam('callcenter_width');
	$c_height = $rankup_control->getParam('callcenter_height');
	$c_view = $rankup_control->getParam('callcenter_view');
	$result= $rankup_control->set_config_info('callcenter_width',$c_width) && $rankup_control->set_config_info('callcenter_height',$c_height) && $rankup_control->set_config_info('callcenter_view',$c_view);
	if($result) $rankup_control->popup_msg_js('등록되었습니다.');
	else $rankup_control->popup_msg_js('등록이 실패하였습니다.');
}
## 사이트디자인 수정인 경우
else if($type=='design_insert') {
	$rankup_siteconfig = new rankup_siteconfig;
	$result = $rankup_siteconfig->regist_design($_POST);
	if($result) $rankup_control->popup_msg_js("성공적으로 저장 되었습니다.", "javascript:parent.location.reload();");
	else $rankup_control->popup_msg_js("저장이 실패하였습니다.", "VOID");
}
## RSS사용여부
if($_POST['mode']=='rss_use') {
	$upd = "update rankup_siteconfig set `rss_use`='$_POST[rss_use]'";
	$result = mysql_query($upd);

	if($result) $rankup_control->popup_msg_js('등록되었습니다.');
	else $rankup_control->popup_msg_js('등록이 실패하였습니다.');
}

## 사이트 공사중페이지 사용여부
if($_POST['mode']=='site_working'){
	if(rankup_basic::is_demo()){
		echo "<script>alert('데모버전에서는 사용할수 없습니다.');history.back();</script>";
		exit;
	}
	$upd = "update rankup_siteconfig set `site_working`='$_POST[siteworking_use]'";
	$result = mysql_query($upd);
	if($result) $rankup_control->popup_msg_js('등록되었습니다.');
	else $rankup_control->popup_msg_js('등록이 실패하였습니다.');
}

## 사이트 프레임셋 사용여부
if($_POST['mode']=='frameSet'){

	/*if(rankup_basic::is_demo()){
		echo "<script>alert('데모버전에서는 사용할수 없습니다.');history.back();</script>";
			exit;
	}*/
	$upd	= "update rankup_siteconfig set `frame_use`='$_POST[frame_use]'";
	$result	= mysql_query($upd);

	if($result)	$rankup_control->popup_msg_js('등록되었습니다.');
	else	$rankup_control->popup_msg_js('등록이 실패하였습니다.');
}

## 기타로그분석 추적코드 저장
if($_POST['mode']=='etclog'){

	if(rankup_basic::is_demo()){
		echo "<script>alert('데모버전에서는 사용할수 없습니다.');history.back();</script>";
			exit;
	}
	$etc_log = addslashes($Wysiwyg->wysiwyg_result_func($_POST[etc_log]));
	$upd	= "update rankup_siteconfig set `etc_log`='$etc_log',`google_id`='$google_id',`google_pass`='$google_pass',`google_profile_id`='$google_profile_id'";
	$result	= mysql_query($upd);
		

	if($result)	$rankup_control->popup_msg_js('등록되었습니다.');
	else	$rankup_control->popup_msg_js('등록이 실패하였습니다.');

}

## 비밀번호 변경 관련 저장
if($_POST['mode']=='password_change'){

	$_val['password_change_use'] = $_POST['password_change_use'];
	$_val['password_change_month'] = $_POST['password_change_month'];
	$values = $rankup_control->change_query_string($_val);

	$rankup_siteconfig = new rankup_siteconfig;
	$result = $rankup_control->query("update $rankup_siteconfig->config_table set $values");
	$rankup_siteconfig = null;

	if($result) $rankup_control->popup_msg_js("성공적으로 저장 되었습니다.");
	else $rankup_control->popup_msg_js("저장이 실패하였습니다.");

}
?>