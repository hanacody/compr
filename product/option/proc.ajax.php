<?php
/**
 * ��ǰ���� �ɼǹ�ȯ
 */
include_once '../../Libs/_php/rankup_basic.class.php';
$rankup_control->check_admin();

include_once '../class/product.class.php';
$product = new product;

switch($_POST['mode']) {
	case 'load_products': // ��ǰ��� ��ȯ
		$items = $product->load_products_toJSON();
		rankup_util::change_encoding($items);
		echo json_encode($items);
		break;
}

?>