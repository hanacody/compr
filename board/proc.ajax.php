<?php
/**
 * ��(FORM) ���(����)ó��
 */
include_once '../Libs/_php/rankup_basic.class.php';
include_once '../rankup_module/rankup_fbuilder/rankup_fbuilder.class.php';
include_once '../rankup_module/rankup_builder/attachment.class.php';

$fbuilder = new rankup_fbuilder;

switch($_POST['mode']) {

	case 'save_article': // ���Ǳ� ����
		include_once '../Libs/_filter/HTMLFilter.php';
		rankup_util::change_encoding($_POST, 'IN');
		$fs_rows = $fbuilder->get_settings($_POST['fno']);
		if($fs_rows['use_antispam']=='yes' && !$member_info['uid']) { // ���Թ����ڵ� ����(��ȸ���� ��츸)
			if(!$rankup_control->check_confirm_code($_POST['keystring'])) {
				scripts('alert("���Թ����ڵ尡 ��ġ���� �ʽ��ϴ�. �ٽ� Ȯ���Ͽ� �ֽʽÿ�.")');
				exit;
			}
		}
		$result = $fbuilder->save_article();

		// �������뺸
		if($result && $fs_rows['use_alarm']=='yes' && $fs_rows['alarm_phone'] && $fs_rows['alarm_message']) {
			include_once '../rankup_module/rankup_sms/rankup_sms_config.class.php';
			include_once '../rankup_module/rankup_sms/rankup_sms.class.php';
			$sms = new rankup_sms('sms_send');
			$sms->is_valid_send = true;
			$sms->phone = $fs_rows['alarm_phone'];
			$sms->msg = $fs_rows['alarm_message'];
			$sms->send_msg();
		}
		break;

	case 'confirm_pwd': // ���ۼ��� ����
		$rows = $fbuilder->get_article($_POST['fno'], $_POST['no']);
		$_POST['confirm_number'] = preg_replace('/[^\d]/', '', $_POST['confirm_number']);
		if(strlen($_POST['confirm_number'])<4) {
			scripts('alert("��й�ȣ�� �ùٸ��� �ʽ��ϴ�.")');
			exit;
		}
		if(substr($rows['phone'], -4)==$_POST['confirm_number']) {
			$fbuilder->verify_author($_POST['fno'], $_POST['no']); // ���ۼ��� ����
			scripts(sprintf('location.href="view.html?pid=%d&fno=%d&no=%d"', $_POST['pid'], $_POST['fno'], $_POST['no']));
		}
		else {
			scripts('alert("��й�ȣ�� ��ġ���� �ʽ��ϴ�.")');
		}
		break;


	/**
	 * ÷������ ó��
	 */
	case 'post_attach': // ���� ÷��
		$attach = new attachment($_POST['kind'], $base_dir.'rankup_module/rankup_fbuilder/');
		//@note: ���������� '����ũ��, Ȯ����' ���� �� �ݿ�
		$field_values = $fbuilder->get_field_values($_POST['fno'], $_POST['face']);
		$attach->configs['ext']['allow'] = $field_values['allow_ext'];
		$attach->configs['limit_size'] = $field_values['limit_size'].'MB';

		$result = $attach->post($_FILES['_attach_']);
		list($constvar) = explode('.', $_POST['handler']);
		$post_reset = sprintf('parent.%s.post_reset();', $constvar);
		if(!is_array($result)) {
			$msg = $attach->error_msg($result);
			scripts('alert("'.$msg.'");'.$post_reset);
		}
		else {
			if($_POST['handler']) {
				$hash = json_encode($result);
				scripts($post_reset."parent.$_POST[handler]($hash);");
			}
			else {
				// Fatal error
				scripts($post_reset.'alert("�ڵ鷯�� ���ǵǾ� ���� �ʽ��ϴ�.")');
			}
		}
		break;

	case 'del_attach': // ���� ����
		$attach = new attachment($_POST['kind'], $base_dir.'rankup_module/rankup_fbuilder/');
		if($attach->del($_POST['name'])) {
			//@do nothing...
		}
		break;
}
?>