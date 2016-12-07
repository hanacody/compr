<?php
/**
 * 제품관리 관련 처리
 */
include_once '../../Libs/_php/rankup_basic.class.php';
$rankup_control->check_admin();

include_once '../class/product.class.php';
$product = new product;

switch($_POST['mode']) {
	case 'save_settings': // 환경설정
		$product->set_settings();
		break;

	case 'save_product': // 제품등록/수정
		$rankup_control->change_encoding($_POST, 'IN');
		include_once '../../Libs/_filter/HTMLFilter.php'; // XSS 방어
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$product->set_product();
		break;

	case 'set_direction': // 제품순서 설정
		$product->set_direction();
		break;

	case 'del_product': // 제품삭제
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$product->del_product($_POST['nos']);
		break;

	case 'set_view': // 출력설정
		$product->set_view();
		break;

	/**
	 * 첨부파일 처리
	 */
	case 'post_attach': // 파일 첨부
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
				scripts($post_reset.'alert("핸들러가 정의되어 있지 않습니다.")');
			}
		}
		break;

	case 'del_attach': // 파일 삭제
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$attach = new attachment($_POST['kind'], $base_dir.'product/');
		if($attach->del($_POST['name'])) {
			//
		}
		break;

}

?>