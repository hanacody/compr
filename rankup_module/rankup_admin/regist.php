<?php
include_once '../../Libs/_php/rankup_basic.class.php';

##������ ���� üũ
$rankup_control->check_admin($rankup_admin->is_admin());

##�۾� ������ ����
$mode = $rankup_control->getParam('mode');


##������ ���̵� ��й�ȣ ���� �ΰ��
if($mode == 'change_id') :
	$id = $rankup_control->getParam('id');	
	$passwd = $rankup_control->getParam('passwd');
	$passwd_re = $rankup_control->getParam('passwd_re');
	
	##���̵��, �н������� ������ �˻�.	
	if(($id_msg = $rankup_control->make_valid_id($id)))
		$rankup_control->popup_msg_js($id_msg);	
	if(($pw_msg = $rankup_control->make_valid_passwd($passwd)))
		$rankup_control->popup_msg_js($pw_msg);
	if(($pw_msg = $rankup_control->make_valid_passwd($passwd_re)))
		$rankup_control->popup_msg_js($pw_msg);
	if(strcmp($passwd,$passwd_re))
		$rankup_control->popup_msg_js('��й�ȣ�� ��Ȯ�ϰ� �Է��Ͽ� �ֽʽÿ�');

	##���̺��� �����ϰ� ������ �߱�
	if($rankup_admin->update_admin_table($id,$passwd)) :	//������ ���̵� �н����� ������ ������ ���
		##������ ������ �߱�
		$rankup_admin->delete_admin_session();
		$rankup_admin->set_admin_session($id,$passwd,'update');
		$rankup_control->popup_msg_js('���� �Ǿ����ϴ�.');
	else : 
		$rankup_control->popup_msg_js('������ ���� �Ͽ����ϴ�.');
	endif;
endif;
?>