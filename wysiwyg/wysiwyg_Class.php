<?php
if(!class_exists("rankup_control")) include_once "../Libs/_php/rankup_basic.class.php";

class wysiwyg_Class extends rankup_util {

	function wysiwyg_Class () {
		$this->img_size = 2048;
		$this->loca = rankup_basic::base_url();
		$this->path = rankup_basic::base_dir();
		$this->name = 'wysiwyg/'; // {  'wysiwyg/'  or  'rankup_wysiwyg/'  }  - 2008.05.30 추가
	}

	/*###################################################################
	위지윅 이미지파일 업로드시에 사용할 함수
	###################################################################*/
	function wysiwyg_img_upload () {
		global $base_dir;

		$img_num	= ($_POST['img_num']) ? $_POST['img_num'] : 0;
		$iname		= $_POST['iname'];

		//생성된 파일명
		$tmp_imgss = $_FILES['imgss']['tmp_name'];
		$extension = $this->get_extension($tmp_imgss);
		if($extension) $extension = '.'.$extension;

		//$saveName = 'W_'.time().$extension;
		//$photo_name = $this->make_thumnail($tmp_imgss, "./PEG_temp/", $saveName, $this->img_size);

		$photo_name = 'W_'.time().$extension;
		move_uploaded_file($tmp_imgss, $base_dir.$this->name.'PEG_temp/'.$photo_name);

		//임시폴더내 기간이 지난 파일 삭제
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
	이미지 삭제하거나 업로드된 이미지 내용에 사용하기

	만약 글 수정시 업로드된거 폴더내에서 삭제하고 싶다면 밑의 명령어를 실행하면 됨.
	하지만 이 작업은 안하는게 좋음.
	위지윅 성격상 업로드된거 블럭 씌워서 글을 작성한 경우 블럭씌어서 작성한 글에서 이미지 삭제시에는 원본글에도 이미지에 영향이 있음.
	그러므로 안하는게 좋음.
	그래도 하고싶다면 밑의 명령어를 사용하면 됨.
	if($pre_content) :
		//$minus	= $this->wysiwyg_image_minus($content, $pre_content);
	endif;
	###################################################################*/
	function wysiwyg_result_func($content='', $pre_content='') { // $content : stripslashes() 한 값
		//preg_match_all('/(img [^<]*src=["|\']?([^ "\']*)["|\']?[^>].*>)/i', $content, $imgs);
		preg_match_all('/<img\s+.*?src="([^"]+)"[^>]*>/is', $content, $imgs);
		foreach($imgs[1] as $key=>$img) {
			// 이미지 파일 PEG 폴더로 이동
			if(strpos($img, $this->name."PEG_temp/")!==false) {
				$_name = basename($img);
				$tmp_file = $this->path.$this->name."PEG_temp/".$_name;
				$save_file = $this->path.$this->name."PEG/".$_name;
				if(is_file($tmp_file)) {
					@copy($tmp_file, $save_file);
					@unlink($tmp_file);
				}
			}
			// 이미지에 걸린 PEG_temp 를 PEG 로 변경 및 절대경로 제거 - 2008.06.09
			$_info = parse_url($img); // scheme : http  or  https
			$_info['scheme'] = empty($_info['scheme']) ? '' : $_info['scheme']."://";
			$_img = str_replace($img, str_replace(array($_info['scheme'].$_SERVER['HTTP_HOST'], "/PEG_temp/"), array('', "/PEG/"), $img), $imgs[1][$key]);
			if(strpos(strtolower($_img), "border")===false) $_img = eregi_replace(" src", " border=\"0\" src", $_img); // border='0' 추가
			$content = str_replace($imgs[1][$key], $_img, $content);
		}
		return $content;
	}


	/*###################################################################
	이미지 등록된것들중 수정시 삭제했을때 저장된 이미지 삭제해주기
	이것은 작업을 하지 않는게 좋음.
	###################################################################*/
	function wysiwyg_image_minus ($content='', $pre_content='') {

		$pre_content1	= explode("/".$this->name."PEG/", stripslashes($pre_content));
		$minus_content1		= explode("/".$this->name."PEG/", stripslashes($content));

		//디비에 저장중인 단어들
		//디비에 저장중인 단어들중 PEG로 등록된 이미지명 배열로 담기
		if($pre_content) {
			for($i=1; $i<count($pre_content1); $i++) {
				$pre_content2 = explode("\"", $pre_content1[$i]);	//파일명 알아내기 위함.
				$content2_gubun = explode("\"", $pre_content1[$i-1]);			//파일경로. 알아내기 위해

				if($i==1) $loca_val = $content2_gubun[count($content2_gubun)-1];
				$upd_use_img_arr[$pre_content2[0]] = $pre_content2[0];
			}
		}

		//수정후 디비에 저장할 단어들
		//디비에 저장할 단어중에 새로 지웠던 값이 있나 확인하기 위해서 이전에 저장되었던 PEG값중 이번에도 사용된것이 있나 확인하는 작업
		//이번에도 사용중이면 위의 배열에서 값을 없앤다.
		//그중에서 없어지지 않는 값을 PEG폴더에 삭제한다.
		for($i=1; $i<count($minus_content1); $i++) {
			$minus_content2		= explode("\">", $minus_content1[$i]);	//파일명 알아내기 위함.
			// wysiwyg/PEG의 경로 알아내기 위한 작업
			if($upd_use_img_arr[$minus_content2[0]]) $upd_use_img_arr[$minus_content2[0]]	= '';
		}

		//실제로 이미지 삭제할 값 삭제해주자.
		//이곳에서 PEG폴더에 저장된 파일 삭제하는것임.
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