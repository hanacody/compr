<?php
/**
 * 팬션 환경 설정
 */
include_once '../../Libs/_php/rankup_basic.class.php';

switch($_POST['mode']) {

	// 갤러리 저장
	case 'save_gallery':
		include_once '../class/gallery.class.php';
		include_once '../class/gallery_admin.class.php';
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$rankup_control->change_encoding($_POST, 'IN');
		$gallery = new gallery_admin;
		$gallery->save();
		if($_POST['gallery_type']=='gallery') {
			include_once 'make_files.inc.php';
			make_gallery_flash_data($_POST['no']);
		}
		scripts("alert('저장되었습니다.');location.replace('./gallery_regist.html?no=$_POST[no]&dummy=".rand()."');"); // dummy : cache 방지
		break;

	// 갤러리 웹진 로드
	case 'load_webzine':
		include_once '../class/gallery.class.php';
		include_once '../class/gallery_admin.class.php';
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$gallery = new gallery_admin;
		$attach = new attachment('webzine');
		$folder = $attach->configs['save']['folder'];
		$nodes = $gallery->load_webzine($_POST['no'], array(
			'entry' => '
			<item>
				<wno>{:no:}</wno>
				<pno>{:pno:}</pno>
				<subject><![CDATA[{:subject:}]]></subject>
				<content><![CDATA[{:content:}]]></content>
				<attach folder="'.$folder.'"><![CDATA[{:attach:}]]></attach>
			</item>'
		));
		echo $rankup_control->print_xml_header('<xml>'.$nodes.'</xml>');
		break;

	// 갤러리 명 변경
	case 'set_gallery_name':
		include_once '../class/gallery.class.php';
		include_once '../class/gallery_admin.class.php';
		$rankup_control->change_encoding($_POST, 'IN');
		$gallery = new gallery_admin;
		$gallery->set_name();
		break;

	// 갤러리 스킨 변경
	case 'set_webzine_skin':
		include_once '../class/gallery.class.php';
		include_once '../class/gallery_admin.class.php';
		$gallery = new gallery_admin;
		$gallery->set_webzine_skin();
		break;

	// 갤러리 삭제
	case 'del_gallery':
	case 'del_webzine': // 갤러리 웹진 삭제
		include_once '../class/gallery.class.php';
		include_once '../class/gallery_admin.class.php';
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$gallery = new gallery_admin;

		if($_POST['mode']=='del_gallery') $gallery->del($_POST['nos']);
		else $gallery->del_webzine($_POST['no']);
		break;


	/**
	 * 첨부파일 처리
	 */
	// 파일 첨부
	case 'post_attach':
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$attach = new attachment($_POST['kind']);
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

	// 파일 삭제
	case 'del_attach':
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$attach = new attachment($_POST['kind']);
		if($attach->del($_POST['name'])) {
			// 해당 파일을 사용하는 항목의 DB/XML 갱신
			switch($_POST['kind']) {
				case 'gallery': // 갤러리 이미지
				case 'gtext_container_bg': // 문자전체 배경이미지
					if($_POST['index']) {
						include_once '../class/gallery.class.php';
						include_once '../class/gallery_admin.class.php';
						$gallery = new gallery_admin;
						$gallery->update_attach();
						include_once 'make_files.inc.php';
						make_gallery_flash_data($_POST['index']);
					}
					break;
			}
		}
		break;
}
?>