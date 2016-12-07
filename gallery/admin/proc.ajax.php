<?php
/**
 * �Ҽ� ȯ�� ����
 */
include_once '../../Libs/_php/rankup_basic.class.php';

switch($_POST['mode']) {

	// ������ ����
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
		scripts("alert('����Ǿ����ϴ�.');location.replace('./gallery_regist.html?no=$_POST[no]&dummy=".rand()."');"); // dummy : cache ����
		break;

	// ������ ���� �ε�
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

	// ������ �� ����
	case 'set_gallery_name':
		include_once '../class/gallery.class.php';
		include_once '../class/gallery_admin.class.php';
		$rankup_control->change_encoding($_POST, 'IN');
		$gallery = new gallery_admin;
		$gallery->set_name();
		break;

	// ������ ��Ų ����
	case 'set_webzine_skin':
		include_once '../class/gallery.class.php';
		include_once '../class/gallery_admin.class.php';
		$gallery = new gallery_admin;
		$gallery->set_webzine_skin();
		break;

	// ������ ����
	case 'del_gallery':
	case 'del_webzine': // ������ ���� ����
		include_once '../class/gallery.class.php';
		include_once '../class/gallery_admin.class.php';
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$gallery = new gallery_admin;

		if($_POST['mode']=='del_gallery') $gallery->del($_POST['nos']);
		else $gallery->del_webzine($_POST['no']);
		break;


	/**
	 * ÷������ ó��
	 */
	// ���� ÷��
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
				scripts($post_reset.'alert("�ڵ鷯�� ���ǵǾ� ���� �ʽ��ϴ�.")');
			}
		}
		break;

	// ���� ����
	case 'del_attach':
		include_once '../../rankup_module/rankup_builder/attachment.class.php';
		$attach = new attachment($_POST['kind']);
		if($attach->del($_POST['name'])) {
			// �ش� ������ ����ϴ� �׸��� DB/XML ����
			switch($_POST['kind']) {
				case 'gallery': // ������ �̹���
				case 'gtext_container_bg': // ������ü ����̹���
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