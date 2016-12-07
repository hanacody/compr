<?php
if(!class_exists("rankup_control")) include_once "../Libs/_php/rankup_basic.class.php";

class wysiwyg_Class extends rankup_util {

	function wysiwyg_Class () {
		$this->img_size = 2048;
		$this->loca = rankup_basic::base_url();
		$this->path = rankup_basic::base_dir();
		$this->name = 'wysiwyg/'; // {  'wysiwyg/'  or  'rankup_wysiwyg/'  }  - 2008.05.30 �߰�
	}

	/*###################################################################
	������ �̹������� ���ε�ÿ� ����� �Լ�
	###################################################################*/
	function wysiwyg_img_upload () {
		global $base_dir;

		$img_num	= ($_POST['img_num']) ? $_POST['img_num'] : 0;
		$iname		= $_POST['iname'];

		//������ ���ϸ�
		$tmp_imgss = $_FILES['imgss']['tmp_name'];
		$extension = $this->get_extension($tmp_imgss);
		if($extension) $extension = '.'.$extension;

		//$saveName = 'W_'.time().$extension;
		//$photo_name = $this->make_thumnail($tmp_imgss, "./PEG_temp/", $saveName, $this->img_size);

		$photo_name = 'W_'.time().$extension;
		move_uploaded_file($tmp_imgss, $base_dir.$this->name.'PEG_temp/'.$photo_name);

		//�ӽ������� �Ⱓ�� ���� ���� ����
		$path = $this->path.$this->name."PEG_temp/";
		$open_dir = opendir($path);
		while($filename = readdir($open_dir)) {
			if(in_array($filename, array('.', '..'))) continue;
			if((time()-filectime($path.$filename))>86400) @unlink($path.$filename);
		}
		$body = "
		<script type='text/javascript'>
		if(window.attachEvent) {
			parent.returnValue = '$photo_name';
			parent.close();
		}
		else if(window.addEventListener) {
			parent.opener.Wysiwyg.img = '$photo_name';
			parent.opener.Wysiwyg.ff_img_view();
			parent.close();
		}
		</script>";
		return $body;
	}


	/*###################################################################
	�̹��� �����ϰų� ���ε�� �̹��� ���뿡 ����ϱ�

	���� �� ������ ���ε�Ȱ� ���������� �����ϰ� �ʹٸ� ���� ��ɾ �����ϸ� ��.
	������ �� �۾��� ���ϴ°� ����.
	������ ���ݻ� ���ε�Ȱ� �� ������ ���� �ۼ��� ��� ����� �ۼ��� �ۿ��� �̹��� �����ÿ��� �����ۿ��� �̹����� ������ ����.
	�׷��Ƿ� ���ϴ°� ����.
	�׷��� �ϰ�ʹٸ� ���� ��ɾ ����ϸ� ��.
	if($pre_content) :
		//$minus	= $this->wysiwyg_image_minus($content, $pre_content);
	endif;
	###################################################################*/
	function wysiwyg_result_func($content='', $pre_content='') { // $content : stripslashes() �� ��
		//preg_match_all('/(img [^<]*src=["|\']?([^ "\']*)["|\']?[^>].*>)/i', $content, $imgs);
		preg_match_all('/<img\s+.*?src="([^"]+)"[^>]*>/is', $content, $imgs);
		foreach($imgs[1] as $key=>$img) {
			// �̹��� ���� PEG ������ �̵�
			if(strpos($img, $this->name."PEG_temp/")!==false) {
				$_name = basename($img);
				$tmp_file = $this->path.$this->name."PEG_temp/".$_name;
				$save_file = $this->path.$this->name."PEG/".$_name;
				if(is_file($tmp_file)) {
					@copy($tmp_file, $save_file);
					@unlink($tmp_file);
				}
			}
			// �̹����� �ɸ� PEG_temp �� PEG �� ���� �� ������ ���� - 2008.06.09
			$_info = parse_url($img); // scheme : http  or  https
			$_info['scheme'] = empty($_info['scheme']) ? '' : $_info['scheme']."://";
			$_img = str_replace($img, str_replace(array($_info['scheme'].$_SERVER['HTTP_HOST'], "/PEG_temp/"), array('', "/PEG/"), $img), $imgs[1][$key]);
			if(strpos(strtolower($_img), "border")===false) $_img = eregi_replace(" src", " border=\"0\" src", $_img); // border='0' �߰�
			$content = str_replace($imgs[1][$key], $_img, $content);
		}
		return $content;
	}


	/*###################################################################
	�̹��� ��ϵȰ͵��� ������ ���������� ����� �̹��� �������ֱ�
	�̰��� �۾��� ���� �ʴ°� ����.
	###################################################################*/
	function wysiwyg_image_minus ($content='', $pre_content='') {

		$pre_content1	= explode("/".$this->name."PEG/", stripslashes($pre_content));
		$minus_content1		= explode("/".$this->name."PEG/", stripslashes($content));

		//��� �������� �ܾ��
		//��� �������� �ܾ���� PEG�� ��ϵ� �̹����� �迭�� ���
		if($pre_content) {
			for($i=1; $i<count($pre_content1); $i++) {
				$pre_content2 = explode("\"", $pre_content1[$i]);	//���ϸ� �˾Ƴ��� ����.
				$content2_gubun = explode("\"", $pre_content1[$i-1]);			//���ϰ��. �˾Ƴ��� ����

				if($i==1) $loca_val = $content2_gubun[count($content2_gubun)-1];
				$upd_use_img_arr[$pre_content2[0]] = $pre_content2[0];
			}
		}

		//������ ��� ������ �ܾ��
		//��� ������ �ܾ��߿� ���� ������ ���� �ֳ� Ȯ���ϱ� ���ؼ� ������ ����Ǿ��� PEG���� �̹����� ���Ȱ��� �ֳ� Ȯ���ϴ� �۾�
		//�̹����� ������̸� ���� �迭���� ���� ���ش�.
		//���߿��� �������� �ʴ� ���� PEG������ �����Ѵ�.
		for($i=1; $i<count($minus_content1); $i++) {
			$minus_content2		= explode("\">", $minus_content1[$i]);	//���ϸ� �˾Ƴ��� ����.
			// wysiwyg/PEG�� ��� �˾Ƴ��� ���� �۾�
			if($upd_use_img_arr[$minus_content2[0]]) $upd_use_img_arr[$minus_content2[0]]	= '';
		}

		//������ �̹��� ������ �� ����������.
		//�̰����� PEG������ ����� ���� �����ϴ°���.
		if(is_array($upd_use_img_arr)) {
			foreach($upd_use_img_arr as $key=>$val) {
				$del_img_loca	= $loca_val."/".$this->name."PEG/".$val;
				echo $del_img_loca."<br>";
				if($val) @unlink($del_img_loca);
			}
		}
	}
}

$Wysiwyg	= new wysiwyg_Class;
if($_POST['mode'] == 'wysiwyg_img_upload')
	echo $wyg	= $Wysiwyg->$_POST['mode']();
?>