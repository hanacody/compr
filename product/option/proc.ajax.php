<?php
/**
 * 力前包府 可记馆券
 */
include_once '../../Libs/_php/rankup_basic.class.php';
$rankup_control->check_admin();

include_once '../class/product.class.php';
$product = new product;

switch($_POST['mode']) {
	case 'load_products': // 力前格废 馆券
		$items = $product->load_products_toJSON();
		rankup_util::change_encoding($items);
		echo json_encode($items);
		break;
}

?>