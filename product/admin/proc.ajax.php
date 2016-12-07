<?php
/**
 * ��ǰ���� ���� ó��
 */
include_once '../../Libs/_php/rankup_basic.class.php';
$rankup_control->check_admin();

include_once '../class/product.class.php';
$product = new product;

switch($_POST['mode']) {
	case 'save_settings': // ȯ�漳��
		$product->set_settings();
		break;

	case 'save_product': // ��ǰ���/����
		$rankup_control->change_encoding($_POST, 'IN');
		include_once '../../Libs/_filter/HTMLFilter.php'; // XSS ���
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$product->set_product();
		break;

	case 'set_direction': // ��ǰ���� ����
		$product->set_direction();
		break;

	case 'del_product': // ��ǰ����
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$product->del_product($_POST['nos']);
		break;

	case 'set_view': // ��¼���
		$product->set_view();
		break;

	/**
	 * ÷������ ó��
	 */
	case 'post_attach': // ���� ÷��
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$attach = new attachment($_POST['kind'], $base_dir.'product/');
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
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$attach = new attachment($_POST['kind'], $base_dir.'product/');
		if($attach->del($_POST['name'])) {
			//
		}
		break;

}

?>