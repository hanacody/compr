<?
include "../../Libs/_php/rankup_basic.class.php";
include $wysiwyg_dir."wysiwyg_Class.php";

$rankup_control->check_admin($rankup_admin->is_admin());

##�Է��� �ʵ� �̸�
$mode = $rankup_control->getParam('mode');
$content = $rankup_control->getParam('content');
if($mode!="meta") $content = $Wysiwyg->wysiwyg_result_func($content);

##�⺻ �Է�����, ���� ����. �۾� ����
$type = $rankup_control->getParam('type');

##�� �Է��� ���
if($type=='insert') {
	$result = $rankup_control->set_config_info($mode,$content);
	if($result) $rankup_control->popup_msg_js('��ϵǾ����ϴ�.');
	else $rankup_control->popup_msg_js('���� �Ͽ����ϴ�.');
}
##�ݼ�Ÿ ���� ������ ���
else if($type=='callcenter_edit') {
	$c_width = $rankup_control->getParam('callcenter_width');
	$c_height = $rankup_control->getParam('callcenter_height');
	$c_view = $rankup_control->getParam('callcenter_view');
	$result= $rankup_control->set_config_info('callcenter_width',$c_width) && $rankup_control->set_config_info('callcenter_height',$c_height) && $rankup_control->set_config_info('callcenter_view',$c_view);
	if($result) $rankup_control->popup_msg_js('��ϵǾ����ϴ�.');
	else $rankup_control->popup_msg_js('����� �����Ͽ����ϴ�.');
}
## ����Ʈ������ ������ ���
else if($type=='design_insert') {
	$rankup_siteconfig = new rankup_siteconfig;
	$result = $rankup_siteconfig->regist_design($_POST);
	if($result) $rankup_control->popup_msg_js("���������� ���� �Ǿ����ϴ�.", "javascript:parent.location.reload();");
	else $rankup_control->popup_msg_js("������ �����Ͽ����ϴ�.", "VOID");
}
## RSS��뿩��
if($_POST['mode']=='rss_use') {
	$upd = "update rankup_siteconfig set `rss_use`='$_POST[rss_use]'";
	$result = mysql_query($upd);

	if($result) $rankup_control->popup_msg_js('��ϵǾ����ϴ�.');
	else $rankup_control->popup_msg_js('����� �����Ͽ����ϴ�.');
}

## ����Ʈ ������������ ��뿩��
if($_POST['mode']=='site_working'){
	if(rankup_basic::is_demo()){
		echo "<script>alert('������������� ����Ҽ� �����ϴ�.');history.back();</script>";
		exit;
	}
	$upd = "update rankup_siteconfig set `site_working`='$_POST[siteworking_use]'";
	$result = mysql_query($upd);
	if($result) $rankup_control->popup_msg_js('��ϵǾ����ϴ�.');
	else $rankup_control->popup_msg_js('����� �����Ͽ����ϴ�.');
}

## ����Ʈ �����Ӽ� ��뿩��
if($_POST['mode']=='frameSet'){

	/*if(rankup_basic::is_demo()){
		echo "<script>alert('������������� ����Ҽ� �����ϴ�.');history.back();</script>";
			exit;
	}*/
	$upd	= "update rankup_siteconfig set `frame_use`='$_POST[frame_use]'";
	$result	= mysql_query($upd);

	if($result)	$rankup_control->popup_msg_js('��ϵǾ����ϴ�.');
	else	$rankup_control->popup_msg_js('����� �����Ͽ����ϴ�.');
}

## ��Ÿ�α׺м� �����ڵ� ����
if($_POST['mode']=='etclog'){

	if(rankup_basic::is_demo()){
		echo "<script>alert('������������� ����Ҽ� �����ϴ�.');history.back();</script>";
			exit;
	}
	$etc_log = addslashes($Wysiwyg->wysiwyg_result_func($_POST[etc_log]));
	$upd	= "update rankup_siteconfig set `etc_log`='$etc_log',`google_id`='$google_id',`google_pass`='$google_pass',`google_profile_id`='$google_profile_id'";
	$result	= mysql_query($upd);
		

	if($result)	$rankup_control->popup_msg_js('��ϵǾ����ϴ�.');
	else	$rankup_control->popup_msg_js('����� �����Ͽ����ϴ�.');

}

## ��й�ȣ ���� ���� ����
if($_POST['mode']=='password_change'){

	$_val['password_change_use'] = $_POST['password_change_use'];
	$_val['password_change_month'] = $_POST['password_change_month'];
	$values = $rankup_control->change_query_string($_val);

	$rankup_siteconfig = new rankup_siteconfig;
	$result = $rankup_control->query("update $rankup_siteconfig->config_table set $values");
	$rankup_siteconfig = null;

	if($result) $rankup_control->popup_msg_js("���������� ���� �Ǿ����ϴ�.");
	else $rankup_control->popup_msg_js("������ �����Ͽ����ϴ�.");

}
?>