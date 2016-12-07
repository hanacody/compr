<?php
##���������� ����� �� �ִ� utility���� Ŭ����
class rankup_util extends rankup_db{
	function rankup_util(){
		parent::rankup_db();
	}

	//���â�� �����ְ� ������ �������� �̵�.
	//�޽����� �������, ���� �������� �̵�
	function popup_msg_js($msg,$url=""){
		rankup_util::alertMessage($msg, $url);
	}

	//���â�� �����ֱ⸸ �ϴ� �Լ�.
	//�̵��� �ּҰ� ���� ���, �޽����� ���
	function popup_move_js($msg='',$url='')	{
		echo '<script language=javascript>';
		if($msg) echo "alert('$msg');";
		if($url) echo "location.replace('$url')";
		echo '</script>';
	}

	// IE ���� ��ȯ
	function ie_version() {
		preg_match('/MSIE ([0-9].[0-9])/', $_SERVER['HTTP_USER_AGENT'], $user_agent);
		return $user_agent[1];
	}

	//�ش� ���̺��� �����ϴ� �Լ�
	function make_drop_table($name)	{
		$drop_que="drop table if exists $name";
		return $this->query($drop_que);
	}

	//�ش� ���̺��� �����ϴ��� �˻��Ѵ�.
	//$tb_list�� array�̰ų�, �ϳ��� ���̺� �̸�
	function make_check_table($tb_list)	{
		$list=$tb_list;
		if(empty($list))
			return '';
		if(is_array($list))	{
			for($i=0, $j=count($list);$i < $j;$i++)	{
				$tb=$list[$i];
				$que="show tables like '$tb'";
				$nums=mysql_num_rows($this->query($que));
				if(!$nums)	{
					return '';
					break;
				}
			}
			return 1;
		} else {
			$que="show tables like '$list'";
			if(mysql_num_rows($this->query($que)))
				return 1;
			else
				return '';
		}
	}

	//���ϴ� �������� reload()��Ų��.
	//$list�� �������� �̸�
	function make_reload_frame($list){
		if($list)
			echo "<script>parent.$list.location.reload();</script>";
		else
			echo "<script>top.location.reload();</script>";
	}

	//���ڿ� �ڸ��� �Լ�
	function str_cut($msg, $cut_size, $tail="...") { // ���ڿ� ���� (�̻��� �����϶��� ... �� ǥ��)
		if($cut_size<=0) return $msg;
		if(ereg("\[re\]",$msg)) $cut_size = $cut_size+4;
		for($i=0; $i<$cut_size; $i++) {
			if(ord($msg[$i])<=127) $eng++;
			//else $han++;
		}
		$cut_size = $cut_size;//+(int)$han*0.0015;
		$point=1;
		if(strlen($msg) < strlen($cut_size))	{
			return $msg.$tail;
		}
		else {
			for($i=0;$i<strlen($msg);$i++) {
				if($point>$cut_size) return $pointtmp.$tail;
				if(ord($msg[$i])<=127) {
					$pointtmp.= $msg[$i];
					if ($point%$cut_size==0) return $pointtmp.$tail;
				}
				else {
					if ($point%$cut_size==0) return $pointtmp.$tail;
					$pointtmp.=$msg[$i].$msg[++$i];
					$point++;
				}
				$point++;
			}
			return $pointtmp;
		}
	}

	// ���ڿ� ����
	function str_len($msg) {
		for($i=0; $i<strlen($msg); $i++) {
			if(ord($msg[$i])>127) $point++;
			$point++;
		}
		return $point;
	}

	//���丮�� �����ϴ� �Լ�
	function remove_directory($dir) {
		$dir = realpath($dir)."/";
		if(empty($dir)) return false;
		$dh = @opendir($dir);
		if(!is_resource($dh)) return false;
		while($item=@readdir($dh)) {
			if(in_array($item, array(".", ".."))) continue;
			if(@is_dir($dir.$item)) $this->remove_directory($dir.$item);
			else if(is_file($dir.$item)) @unlink($dir.$item);
	   }
	   return @rmdir($dir);
   }

   //��ȿ�� ���̵� ������ üũ�ϴ� �Լ�
	//$id�� �˻��� ���̵� ��
	function make_valid_id($id){
		if(empty($id))
			return '���̵� �Է��Ͽ� �ֽʽÿ�';
		else if(ereg("[[:space:]]+",$id))
			return '������� �Է��Ͽ� �ֽʽÿ�';
		else if(strlen($id) > 16 || strlen($id) < 4)
			return '���̵��� ���ڼ��� Ȯ���Ͽ� �ֽʽÿ�';
		else if(!ereg("(^[-0-9a-zA-Z_*?#@]{4,16}$)",$id))
			return '���̵� Ȯ���Ͽ� �ֽʽÿ�';
		else
			return '';
	}

	//�ֹε�Ϲ�ȣ�� üũ�ϴ� �Լ�
	//�´� �ֹε�Ϲ�ȣ���� üũ�Ѵ�.
	function make_ssn_check($pn)	{
		if(strlen($pn) < 13)
			return 0;
		for($i =0; $i < 13; $i++)
			$p[$i] =substr($pn,$i,1);


		$check =($p[0] * 2) + ($p[1] * 3) + ($p[2] * 4) + ($p[3] * 5) + ($p[4] * 6) + ($p[5] * 7) + ($p[6] * 8) + ($p[7] * 9) + ($p[8] * 2) + ($p[9] * 3) + ($p[10] * 4) + ($p[11] * 5);
		$check =$check % 11;
		$check =11 - $check;
		$check =substr($check,-1);
		if($p[12] ==$check)
			return 1;
		else
			return 0;

	}

	//��Ȯ�� �̸����ּ��ΰ� üũ�ϴ� �Լ�
	//��ȿ�� �̸����ΰ� üũ�ϴ� �Լ�
	function make_valid_domain($email)	{
		//if(preg_match("/([ \n]+)([a-z0-9\_\-\.]+)@([a-z0-9\_\-\.]+)/", $email)) {
		if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+$", $email)) {
			list($alias, $domain) = split("@", $email);
			$str='';
		   if (checkdnsrr($domain, "MX")) {	//������ �����Ұ��
			   getmxrr($domain, $mxhosts);
			   $str='';
			} else {		//������ �������� ���� ���
				$str='�������� �ʴ� �̸��� ���� �Դϴ�.';
			}
		} else {	//������ �߸� �Ȱ��
			$str='�̸����� ������ �߸� �Ǿ����ϴ�.';
		}
		return $str;
	}

	//�Է��� ��й�ȣ�� ��Ȯ�� ������ üũ�ϴ� �Լ�
	function make_valid_passwd($pw){
		if(empty($pw))
			return '��й�ȣ�� �Է��Ͽ� �ֽʽÿ�';
		else if(ereg("[[:space:]]+",$pw))
			return '������� �Է��Ͽ� �ֽʽÿ�';
		else if(strlen($pw) > 16 || strlen($pw) < 4)
			return '��й�ȣ�� ���ڼ��� Ȯ���Ͽ� �ֽʽÿ�';
		else if(!ereg("(^[-0-9a-zA-Z_*?#$%^&*()+!@]{4,16}$)",$pw))
			return '��й�ȣ�� Ȯ���Ͽ� �ֽʽÿ�';
		else
			return '';
	}

	//�Ѿ�� ��������, post�� get�� �Ѿ� �°Ϳ� ������� ó��
	function getParam($name) {
		$value = $_POST[$name];
		if(empty($value)) $value = $_GET[$name];
		return is_array($value) ? $value : urldecode($value);
	}

	//post�� get���� �Ѿ�� ������ ��� ����ȭ �ϴ� �Լ�
	function getaddstring(){
		$getParam = $_GET;
		$postParam = $_POST;
		$lastParam = array_merge($getParam,$postParam);
		$lastAdd = '';
		foreach($lastParam as $key=>$val) $lastAdd.="$key=".$val."&";
		return $lastAdd;
	}

	//�ٽ� ���ư� ������ �ּҸ� �̸� ����.
	function getBackUrl(){
		$url = $_SERVER['PHP_SELF'];
		$addstring=$this->getAddString();
		$backurl=$url.'?'.$addstring;
		return base64_encode($backurl);
	}
	//Ư�� ���丮�� �����ϴ� �Լ�
	//$dir�� ������ ���丮
	//���� �Լ��� ȣ���ϹǷ�, ��Ȯ�� �����̱�� �ϳ�,�ð��� ���� �ɸ�.
	function rm($dir) {
		if(!$dh = @opendir($dir)) return 1;
		while (($obj = readdir($dh))) {
			if($obj=='.' || $obj=='..') continue;
			if (!@unlink($dir.'/'.$obj)) $this->rm($dir.'/'.$obj);
		}
		@rmdir($dir);
	}

	//�ش� ���丮�� ��� ������ ����� �Լ�
	//$dir�� ���� �̸�
	function make_delete_files($dir)	{
		$del_count=0;
		if(is_dir($dir)) {	//���� ����
			$fp = @opendir($dir);
			while($file=@readdir($fp)) { // ���ϵ��� �д´�
				if($file != '.' && $file !='..') {
					$files=$dir.'/'.$file;
					if(@unlink($files)) $del_count++; // ���� ������ ���� ī��Ʈ�� ����
				}
			}
		}
		else $del_count = -1; // ���丮�� �ƴ϶��
		return $del_count;
	}

	//���� �ٿ�ε� �ϴ� �Լ�
	function down_file($file, $rename){
		if(!is_file($file)) return false;

		if($this->check_unicode($rename)) $rename = iconv('UTF-8', 'CP949', $rename); // 2010.06.29 fixed
		$ctype = mime_content_type($file); // php ���� ������ ���� ���� ��� 'Libs/_php/rankup_basic.class.php' �� ����
		if(!$ctype) $ctype = "application/force-download";

		if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');

		//Begin writing headers
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Description: File Transfer");

		//Use the switch-generated Content-Type
		header("Content-Type: $ctype");

		//Force the download
		header("Content-Disposition: attachment; filename=$rename;");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($file));

		set_time_limit(0);
		@readfile($file);
	}

	//���� �ٿ�ε� �ϴ� �Լ�
	//$real_name�� �������� ������ ��ġ, $file_name�� ������ ������ �̸�
	function make_download($real_name, $file_name = '') {
		if(eregi("^(http://|ftp://)", $real_name)) echo @implode ('', @file($real_name));
		else {
			if(file_exists($real_name)) {
				$file_size =  filesize($real_name);
				if (empty($file_name)) $file_name = basename($real_name);
				header("Content-Disposition: attachment;filename=$file_name");
				header("Content-Length: $file_size");
				Header("Content-type: application/unknown");
				$fd = fopen( $real_name, "r" );
				@fpassthru( $fd);
				fclose( $fd );
			}
			else return false;
		}
	}

	// ���͸�ũ ����� �Լ� - 2009.01.29 �߰�
	function watermark_exec($resource, $watermark, $opacity=40) {
		$s_width = imageSX($resource); $s_height = imageSY($resource);
		list($overlay, $o_width, $o_height) = rankup_util::imagecreatefrom_photo($watermark);

		// ���͸�ũ ��ġ ���ϱ� - ���߾�
		$pos_x = ($s_width - $o_width) / 2;
		$pos_y = ($s_height - $o_height) / 2;

		imagecopymerge($resource, $overlay, $pos_x, $pos_y, 0, 0, $o_width, $o_height, $opacity);
		return $resource;
	}

	// Ȯ���ڰ� ���ؼ� ���͸�ũ Ȱ���ϱ� - 2009.01.29 �߰�
	function imagecreatefrom_photo($source) {
		list($width, $height, $type) = getimagesize($source);
		switch($type) {
			case 1: $resource = imagecreatefromgif($source); break;
			case 2: $resource = imagecreatefromjpeg($source); break;
			case 3: $resource = imagecreatefrompng($source); break;
		}
		return array($resource, $width, $height);
	}

	//����� ����� �Լ�
	//$filePath�� ������ ����� ������ �̸�
	//$saveName�� �������� ����� �̸�
	//$sFactor�� ����/������ �ִ� ũ��
	//$saveDir�� �������Ŀ� ����� ���丮�� �̸�
	//$watermark�� array(���͸�ũ���� �̹���, ���� ����) -- > jpg, png�����ϰ�� ���͸�ũ�� �ȵ˴ϴ�.
	//$destory�� �������� ���� ������ ���� ����
	function make_thumnail($filePath, $saveDir = "./", $saveName, $sFactor="", $watermark=false, $destroy="1"){
		ini_set("memory_limit", "80M"); // �޸� ������ ���� �ְ� ����
		$sz = getimagesize($filePath);
		if($sFactor) {
			if($sz[0] > $sFactor) {
				$per=$sFactor/$sz[0];
				$imgW = ceil($sz[0]*$per);
				$imgH = ceil($sz[1]*$per);
			}
			else {
				$imgW = ceil($sz[0]);
				$imgH = ceil($sz[1]);
			}
		}
		else {
			$imgW = ceil($sz[0]);
			$imgH = ceil($sz[1]);
		}
        switch ($sz[2]) {
			case 1: //gif �ϰ��
				// �ִϸ��̼� GIF �ϰ�� 1 Frame �� ���Ƽ�, ����� ����, ���� ������� ������� �Ʒ� if ���� �ּ� ó��
				if(move_uploaded_file($filePath, $saveDir.$saveName)) return $saveName;

				$src_img = imagecreatefromgif($filePath);
				$dst_img = imagecreate($imgW, $imgH);
				ImageCopyResized($dst_img,$src_img,0,0,0,0,$imgW,$imgH,$sz[0],$sz[1]);
				ImageInterlace($dst_img);
				if($watermark!=false) rankup_util::watermark_exec($dst_img, $watermark['photo'], $watermark['opacity']);
				ImageGIF($dst_img, $saveDir.$saveName); //������ �̹��������ϴ� �κ�
				break;
			case 2: //jpg �ϰ��
				$src_img = imagecreatefromjpeg($filePath);
				$dst_img = imagecreatetruecolor($imgW, $imgH);
				ImageCopyResized($dst_img,$src_img,0,0,0,0,$imgW,$imgH,$sz[0],$sz[1]);
				ImageInterlace($dst_img);
				if($watermark!=false) rankup_util::watermark_exec($dst_img, $watermark['photo'], $watermark['opacity']);
				ImageJPEG($dst_img, $saveDir.$saveName);
				break;
			case 3: //png �ϰ��
				$src_img = imagecreatefrompng($filePath);
				$dst_img = imagecreatetruecolor($imgW, $imgH);
				ImageCopyResized($dst_img,$src_img,0,0,0,0,$imgW,$imgH,$sz[0],$sz[1]);
				ImageInterlace($dst_img);
				if($watermark!=false) $dst_img = rankup_util::watermark_exec($dst_img, $watermark['photo'], $watermark['opacity']);
				ImagePNG($dst_img, $saveDir.$saveName);
				break;
			default:  //swf �ϰ��
				if(move_uploaded_file($filePath, $saveDir.$saveName));
				return $saveName;
				break;
		}
		if($destroy) {
			ImageDestroy($dst_img); // ����
			ImageDestroy($src_img);
		}
		return $saveName;
	}

	// ����� ����� - move_uploaded_file �� ������ ����� ó��
	function make_thumbnail($source_file, $dest_file, $width=null, $height=null) {
		ini_set("memory_limit", "80M"); // �޸� ������ ���� �ְ� ����

		list($image_width, $image_height, $image_type) = getimagesize($source_file);
		if($image_type==1) $source = imagecreatefromgif($source_file);
		else if($image_type==2) $source = imagecreatefromjpeg($source_file);
		else if($image_type==3) $source = imagecreatefrompng($source_file);
		else return;

		$thumb_width = $thumb_height = null;
		if($width!=null && $image_width>$width) {
			$thumb_width = $width;
			$thumb_height = $image_height / ($image_width/$width);
		}
		if($height!=null) {
			if($thumb_height!=null) {
				if($thumb_height>$height) {
					$thumb_width = $thumb_width / ($thumb_height/$height);
					$thumb_height = $height;
				}
			}
			else if($image_height>$height) {
				$thumb_height = $height;
				$thumb_width = $image_width / ($image_height/$height);
			}
		}
		if($thumb_width==null||$thumb_height==null) {
			if($source_file!=$dest_file) @copy($source_file, $dest_file);
		}
		else {
			$new_source = imagecreatetruecolor($thumb_width, $thumb_height);
			imagecopyresampled($new_source, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $image_width, $image_height);
			if($image_type==1) imagegif($new_source, $dest_file);
			else if($image_type==2) imagejpeg($new_source, $dest_file);
			else if($image_type==3) imagepng($new_source, $dest_file);
			imagedestroy($new_source);
		}
		return array_pop(explode("/", $dest_file));
	}

	//������ Ȯ���ڸ� ���ϴ� �Լ� - 2008-07-07 ����
	function get_extension($file) {
		$info = @getimagesize($file);
		$extensions = array(
			1 => 'gif',
			2 => 'jpg',
			3 => 'png',
			4 => 'swf',
			5 => 'psd',
			6 => 'bmp',
			13 => 'swf'
		);
		$extension = $extensions[$info[2]];
		if(empty($extension)) $extension = array_pop(explode(".", strtolower($file)));
		return $extension;
	}

	// ���ϻ����� ���� ǥ�� - 2008-07-07 �߰�
	function get_file_size($file) {
		$_div = 0;
		$units = array("Byte", "KB", "MB", "GB", "TB", "PB");
		if(!is_file($file)) return array(0, "0".$units[0]);
		$size = $_size = filesize($file);
		while($_size>=1024) {
			$_size = $_size/1024;
			$_div++;
		}
		$calc_size = number_format($_size, ($_div ? 1 : 0), '.', ',').$units[$_div];
		return array($size, $calc_size);
	}

	//������ ����
	//$no�� �ش� �Խù��� ��ȣ
	//$mode up/down
	//$rankno ���� ��ŷ
	//$add_que�� where���� ������ �κ�
	function change_list_rank($no,$table,$direction,$add_que=''){
		$rankno=$this->queryR("select rank from $table where no = '$no'");
		if($direction=='up') { //���� �̵���
			$next_rank = $this->queryR("select max(rank) from $table where rank < '$rankno' $add_que ");
			if($next_rank >= 0) {
				$next_no = $this->queryR("select max(no) from $table where rank = $next_rank $add_que");
				$result1 = $this->query("update $table set rank = '$next_rank' where no = '$no'");
				$result2 = $this->query("update $table set rank = '$rankno' where no = '$next_no'");
				return ($result1 && $result2);
			}
			else return true; // �̵��� ���� �������� �ʴ´ٸ�, �����Ѵ�.
		}
		else if($direction == 'down') {
			$next_rank = $this->queryR("select min(rank) from $table where rank > '$rankno' $add_que");
			if($next_rank) {
				$next_no = $this->queryR("select min(no) from $table where rank = $next_rank $add_que");
				$result1 = $this->query("update $table set rank = '$next_rank' where no = '$no'");
				$result2 = $this->query("update $table set rank = '$rankno' where no = '$next_no'");
				return ($result1 && $result2);
			}
			else return true; //�̵��� ���� �������� �ʴ´ٸ�, �����Ѵ�.
		}
		else return false;
	}
	//�̵��� ��ũ�� �����ϴ��� üũ�ϴ� �Լ�
	function check_valid_rank($no,$table,$direction,$add_que=''){
		$rankno=$this->queryR("select rank from $table where no = '$no'");
		if($direction=='up') $next_rank = $this->queryR("select count(*) from $table where rank < $rankno $add_que");
		else if($direction=='down') $next_rank = $this->queryR("select count(*) from $table where rank > $rankno $add_que");
		return $next_rank;
	}

	/*
	//�޷� ������ �����ϴ� �κ�
	//������ ��Ÿ���� text �ڽ��� �̸��� ���� ��Ÿ���� �ؽ�Ʈ �ڽ��� ���� �;� ��.
	function make_calendar_content($start,$end,$dir){
		$msg='<input type="text" name="'.$start.'" style="cursor:pointer;height:20px;width:70px;text-align:center;font-size:8pt;font-weight:bolder;font-family:Arial;border:#A9BECF 1px solid;" size=10 value="'.$this->getParam($start).'" readOnly OnClick="var result=calender(); if(!is_null(result))
		setCal(\''.$start.'\',result);"><img src="'.$dir.'rankup_module/images/calendar2.gif" OnClick="var result=calender(); if(!is_null(result))
		setCal(\''.$start.'\',result);" align="absmiddle" style="cursor:hand">~<input type="text" name="'.$end.'" style="cursor:pointer;height:20px;width:70px;text-align:center;font-size:8pt;font-weight:bolder;font-family:Arial;border:#A9BECF 1px solid;" size=10 value="'.$this->getParam($end).'" readOnly OnClick="var result=calender(); if(!is_null(result)) setCal(\''.$end.'\',result);"><img src="'.$dir.'rankup_module/images/calendar2.gif" OnClick="var result=calender(); if(!is_null(result)) setCal(\''.$end.'\',result);" align="absmiddle" style="cursor:hand">';
		return $msg;
	}

	//�޷� �ڽ��� ����ϴ� �κ�
	//���� �̸��� �;� ��
	function make_calendar_js($form,$dir){
		$msg='
		<script type="text/javascript">
		<!--
		function calender(){
			strleft = "dialogleft:" + eval(window.screenLeft + window.event.clientX ) ;
			strtop = ";dialogtop:" + eval(window.screenTop + window.event.clientY ) ;
			return window.showModalDialog("'.$dir.'Libs/_js/calendar.html","", strleft +  strtop +";dialogWidth:190px; dialogHeight:253px;scroll:no;status:no;titlebar:no;center:no;help:yes;");
		}
		function is_null(item_var) {
			if(item_var == "" || item_var == null || item_var == "undefined" || item_var == " ") return true;
			return false;
		}
		function setCal(field,val){
			document.forms[\''.$form.'\'].elements[field].value=val;
		}
		//-->
		</script>';
		return $msg;
	}
	//eidtor�� ������ �����ϴ� �޼ҵ�
	function make_editor_content($content,$dir){
		$msg = '
		<script language="javascript">
		<!--
		var config = new Object();
		config.width = "100%";
		config.height = "250px";
		config.bodyStyle = "background-color: ffffff; font-family:Verdana; font-size: x-small;";
		config.debug = 0;
		config.stylesheet = "'.$dir.'Libs/_style/rankup_style.css";
		editor_generate("'.$content.'",config);
		//-->
		</script>
		';
		return $msg;
	}
	//eidtor js�� ����ϴ� �Լ�
	function make_editor_js($dir){
		$msg = '
		<script type="text/javascript">
		<!--
		var _editor_url = "'.$dir.'Libs/editor/";
		var win_ie_ver = parseFloat(navigator.appVersion.split("MSIE")[1]);
		if (navigator.userAgent.indexOf(\'Mac\')        >= 0) { win_ie_ver = 0; }
		if (navigator.userAgent.indexOf(\'Windows CE\') >= 0) { win_ie_ver = 0; }
		if (navigator.userAgent.indexOf(\'Opera\')      >= 0) { win_ie_ver = 0; }
		if (win_ie_ver >= 5.5) {
  			document.write("<scr" + "ipt src=\"" +_editor_url+ "editor.js\"");
  			document.write(" language=\"Javascript1.2\"></scr" + "ipt>");
		} else {
  			document.write("<scr"+"ipt>function editor_generate() { return false; }</scr"+"ipt>");
		}
		//-->
		</script>';
		return $msg;
	}
	*/

	//���� ������ ������ ������ ���ϴ� �Լ�
	function return_add_query($que,$add){
		if($add) {
			if($que) return $que.' and '.$add;
			else return $add;
		}
		else {
			if(strpos($que,'where and ')===true) return str_replace(' where and ',' where ',$que);
			else if(strpos($que,'where')===true) return $que;
			else if(strpos(ltrim($que),'and')==0) return ereg_replace("^[[:space:]]*and"," where ",$que);
			else return ' where '.$que;
		}
	}

	//ok ��� xml�������� ����� ���� �Լ�
	function make_ok_xml($result){
		if($result) $ok='ok';
		else $ok='failed';
		$str="<?xml version='1.0' encoding='euc-kr'?>";
		$str.="<root>";
		$str.="<ok>$ok</ok>";
		$str.="</root>";
		return $str;
	}

	// SQL ���� ���ڿ� ���� :: �Է�/������ ���
	function change_query_string($arr) {
		//foreach($arr as $field => $value) $field_vals[count($field_vals)] = $value!==NULL ? is_array(@unserialize($value)) ? "$field='".addslashes($value)."'" : "$field='".addslashes($value)."'" : "$field=NULL";
		$field_vals = array();
		foreach($arr as $field => $value) $field_vals[] = ($value===NULL) ? "$field=NULL" : "$field='".addslashes($value)."'";
		return @join(', ', $field_vals);
	}

	// �ߺ����� ���� TIMESTAMP :: ���ϳ��ֿ̹� ���
	function uniqueTimeStamp() {
		list($msec, $sec) = explode(" ", microtime());
		return $sec.substr(array_pop(explode(".", $msec)), 0, 4);
	}

	// ����������� ���� ���� ����
	function set_sf_file_info($file, $base_dir='') { // $base_dir : ��� �������� ���̽� ������
		$rankup_explorer = new rankup_explorer;
		$ctype = mime_content_type($file); // �������� �ľ�
		if(!$ctype) $ctype = "application/force-download";
		$result['type'] = $ctype; // ��������
		$result['name'] = $file; // ���� ���ϸ�
		$result['tmp_name'] = $rankup_explorer->base_dir.$file; // ������ �ӽ� ���ϸ�
		return $result;
	}

	// ÷������ ����
	function get_file_info($file, $mixed='') { // $mixed ���� ������� �ش� ������ ��ȣȭ ����
		if(empty($file['tmp_name'])) return ''; // ÷���� ������ ������ ���鸮��
		$result[org] = $file['name']; // ���ó� ���� ���ϸ�
		$result[tmp] = $file['tmp_name']; // ������ �ӽ� ���ϸ�

		// QUploder (ActiveX) �� ������� ���
		if(is_array($file[tmp_name])) {
			for($i=0; $i<count($file[tmp_name]); $i++) {
				$result[type][$i] = array_pop(explode('/', $file['type'][$i])); // ���� ����
				$result[ext][$i] = array_pop(explode(".", $file['name'][$i])); // Ȯ����
				$result[sav][$i] = empty($mixed) ? $this->uniqueTimeStamp().'.'.$result[ext][$i] : $this->uniqueTimeStamp().'.'.base64_encode(strrev($this->uniqueTimeStamp()+rand(strtotime("-10 day"), time())).'.'.$mixed).'.'.$result[ext][$i];
			}
		}
		// ��ϴ������ �������� ������ ��� �� <input type='file' ~> �� ������� ���
		else {
			$result[type] = array_pop(explode('/', $file['type'])); // ���� ����
			$result[ext] = array_pop(explode(".", $file['name'])); // Ȯ����
			$result[sav] = empty($mixed) ? $this->uniqueTimeStamp().'.'.$result[ext] : $this->uniqueTimeStamp().'.'.base64_encode(strrev($this->uniqueTimeStamp()+rand(strtotime("-10 day"), time())).'.'.$mixed).'.'.$result[ext];
		}
		return $result;
	}

	// ����Ʈ�ڽ�����
	function createSelectBox($obj, $values='', $addTags='') { // $value = array('default'=>'', 'min'=>1, 'max'=>15, 'gap'=>5, format=>'%02d', 'unit'=>'��', 'value'=>3)  or  values=3
		if(is_array($values)) {
			for($i=$values[min];$i<=$values[max];$i++) {
				$val = empty($values[gap]) ? $i : $i*$values[gap];
				if($val>$values[max]) break;
				if(isset($values[format])) $val = sprintf("$values[format]", $val);
				$select = ($val == $values[value]) ? " selected" : "";
				$options[count($options)] = "<option value='$val'$select>$val $values[unit]</option>";
			}
			if($values['reverse']==true) $options = array_reverse($options);
			if(!empty($values['default'])) array_unshift($options, "<option value=''>$values[default]</option>");
		}
		else {
			switch(strtolower($obj)) {
				case eregi("_parking", $obj)==1: // �������
					foreach($GLOBALS[_PARKINGSET] as $value => $text) {
						$select = ($value == $values) ? " selected" : "";
						$options[count($options)] = "<option value='$value'$select>$text</option>";
					}
					break;
				case eregi("period_unit", $obj)==1:  // �Ⱓ����
					foreach($GLOBALS[_PERIODUNITSET] as $value => $text) {
						$select = ($value == $values) ? " selected" : "";
						$options[count($options)] = "<option value='$value'$select>$text</option>";
					}
					break;
			}
		}
		$selBox = "<select name='$obj' $addTags>".implode("",$options)."</select>";
		return $selBox;
	}

	// ��Ƽüũ�ڽ����� :: üũ�ڽ�, ������ư ����
	function createMultiSelectBox($type, $obj, $values, $addTags='', $space='') { // $values = array('yes'=>'���', 'no'=>'�̻��', value='yes') :: value : �޸�(,)�� ����
		if(is_array($values)) {
			foreach($values as $key=>$val) {
				if($key=="value") { $values = explode(",", $val); continue; }
				$arr[$key] = $val;
			}
			$rows = 0;
			foreach($arr as $value=>$text) {
				$check = in_array($value, $values) ? " checked" : "";
				$item = " <input type='$type' name='$obj' value='$value'$check $addTags id='{$obj}_$value'><label for='{$obj}_$value'>$text</label>";
				$multiSelBox .= empty($space) ? $item : str_replace("{:item:}", $item, $space);
			}
		}
		else {
			switch(strtolower($obj)) {
				case "...";
					// .....
					break;
			}
		}
		return $multiSelBox;
	}

	// ��ũ���� :: �ߺ��� ���� + REQUEST ���� : <a href=".$this->setLink("detail.html?Sno=100","click").">��ũ</a>
	function setLink($link, $exclusion_arg='', $add_arg='') {
		$exclusions = !is_array($exclusion_arg) ? !empty($exclusion_arg) ? explode(",",$exclusion_arg) : array() : $exclusion_arg;
		parse_str(array_pop(explode("?",$link)),$args);
		foreach($args as $key=>$val) if(empty($args[$key])||(is_array($exclusions) && in_array($key,$exclusions))||($key==$exclusions)) unset($args[$key]);
		$exclusions = array_merge($exclusions,array_keys($args));
		$arg = http_build_query($args); // http_build_query() :: php 5 or later
		$add_arg = !empty($add_arg) ? !empty($arg) ? $arg."&".$add_arg : $add_arg : $arg;
		return $this->getRequestInfo(array_shift(explode("?",$link)), $exclusions, !empty($add_arg)).$add_arg;
	}

	// ������ REQUEST ����	:: alertMessage("LOGIN_MESSAGE","${hom_path}member/login.html?url=".$cs->getRequestInfo());
	function getRequestInfo($path=true, $exclusion_arg='', $follow_arg=false) {
		if(eregi("\?",$_SERVER['REQUEST_URI'])) {
			$exclusions = !is_array($exclusion_arg) ? !empty($exclusion_arg) ? explode(",",$exclusion_arg) : array() : $exclusion_arg;
			parse_str(array_pop(explode("?",$_SERVER['REQUEST_URI'])),$args);
			foreach($args as $key=>$val) if($args[$key]==''||(is_array($exclusions) && in_array($key,$exclusions))||($key==$exclusions)) unset($args[$key]);
			$arg = http_build_query($args);
		}
		$q = ($path!=false&&(!empty($arg)||$follow_arg)) ? '?' : '';
		$arg = ($follow_arg&&!empty($arg)) ? $q.$arg.'&' : $q.$arg;
		return $request_info = ($path!=false) ? ($path==1) ? urlencode($GLOBALS['hom_path'].substr(array_shift(explode("?",$_SERVER['REQUEST_URI'])),1).$arg) : $path.$arg : $arg;
	}

	// �޽������
	function alertMessage($msg, $uri='', $target='', $cmd='', $mode="ALERT") { // target :: { self | opener[....] }
		switch(strtoupper($mode)) {
			case "ALERT":
				switch($msg) {
					case "LOGIN_MESSAGE":
						$msg = "\\n�˼��մϴ�. ������ �α����� �Ǿ� ���� �ʽ��ϴ�.\\n\\n�α����� �Ͻ� �� ���񽺸� �ٽ� �̿��� �ֽñ� �ٶ��ϴ�.";
						if(empty($uri)) $uri = "LOGIN_PAGE";
						break;
					case "ADMIN_LOGIN_MESSAGE":
						$msg = "�����ڸ� ������ ���� ������ �Դϴ�.";
						if(empty($uri)) $uri = "ADMIN_LOGIN_PAGE";
						break;
				}
				if(!empty($msg)) echo "<script>alert(\"$msg\");</script>";
				switch($uri) {
					case "BACK": case "": $uri="BACK"; echo "<script>history.back();</script>"; break;
					case "CLOSE": echo "<script>opener=this;self.close();</script>"; break;
					case "CONTINUE": case "VOID": break;
					default:
						if($uri == "HOME") $uri = rankup_basic::base_url()."main/index.html";
						if($uri == "LOGIN_PAGE") $uri = LOGINPAGE."?url=".$this->getRequestInfo();
						if($uri == "ADMIN_LOGIN_PAGE") $uri = "$GLOBALS[rad_path]index.html?url=".$this->getRequestInfo();
						if(empty($target)) {
							//echo "<meta http-equiv='refresh' content='0;url=$uri'>";
							if(eregi("javascript:",$uri)) echo "<script>".str_replace("javascript:", "", $uri)."</script>";
							else echo "<script>document.location.href = \"$uri\"</script>";
						}
						else {
							if($target=="opener" && empty($cmd)) $cmd = "CLOSE";
							echo "<script>${target}.location.href = \"$uri\"</script>";
						}
						if($cmd=="CLOSE") echo "<script>opener=this;self.close();</script>";
				}
				if(!empty($uri)) exit; // �̵��� �������� ������ Terminate!!
				break;

			case "LAYER":
				// .....
				break;
		}
	}

	// ���Ǽ��ø޽������
	function alertConfirmMessage($string, $ok, $cancel='', $type="OKCANCEL") {
		if(strtoupper($type) == "YESNO") {
			echo "
			<script language='JavaScript'>
			/*@cc_on @*/
			/*@if (@_win32 && @_jscript_version>=5)
			function window.confirm(str) {
				execScript('n = msgbox(\"'+str+'\",vbYesNo)', 'vbscript');
				return(n == 6);
			}
			@end @*/
			</script>";
		} else $string .= str_repeat(" ", 10);
		echo "<script>if(confirm(\"$string\")) { document.location.href='$ok' } else { document.location.href='$cancel' }</script>";
	}

	#############################################################################
	## ��ȣȭ ��ȣȭ
	#############################################################################
	//�⺻Ű�迭 ���� ���
	var $BASE64_CHARS = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/0123456789=";
	function getKey() {
		if(empty($_SESSION['BASE64_KEY'])) {
			$array = array(
				'_', '$', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-',
				'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
			);
			shuffle($array);
			$_SESSION['BASE64_KEY'] = join('',$array);
		}
		return $_SESSION['BASE64_KEY'];
	}
	// ��ȣȭ
	function encode($str, $time=1) {
		$str = strrev(strtr(base64_encode($str), $this->BASE64_CHARS, $this->getKey()));
		if($time>1) $str = $this->encode($str, --$time);
		return $str;
	}
	// ��ȣȭ
	function decode($encoded, $time=1) {
		$encoded = base64_decode(strtr(strrev($encoded), $this->getKey(), $this->BASE64_CHARS));
		if($time>1) $encoded = $this->decode($encoded, --$time);
		return $encoded;
	}

	// ����Ű ��ȯ - 2009.11.02 added
	function make_ckey() {
		return $this->encode(time(), 5);
	}

	// ����Ű �˻� - ���Ե�� ���� - 2009.11.02 added
	function check_ckey($ckey, $sec=300) { // ���ѽð��� �ʰ��� ��� �������
		if(!$ckey) return false;
		$ckey = $this->decode($ckey, 5);
		if($_SESSION['ckey']==$ckey) return -1;
		if(!is_numeric($ckey) || !ctype_digit($ckey)) return false;
		$ctime = time()-$ckey;
		if(!$ctime || $ctime>$sec) return false;
		$_SESSION['ckey'] = $ckey;
		return true;
	}

	// �迭�� ����
	function multi_sort($arr_data, $column, $sort=SORT_ASC) {  // sort = SORT_DESC  or  SORT_ASC
		$org_data = $arr_data;
		foreach($arr_data as $key => $val) if($val[$column] != false) $tmp_data[]=$val;
		for($i=0; $i<count($tmp_data); $i++) $sortarr[]=$tmp_data[$i][$column];
		@array_multisort($sortarr, $sort, $org_data);
		return $org_data;
	}

	// ���ҽ� üũ - 2009.01.23 fixed
	function check_resource($datas) {
		return (is_resource($datas) && mysql_num_rows($datas)) || (is_array($datas) && count($datas));
	}

	// ���ҽ� üũ ��Ī - 2009.01.08 �߰�
	function chkRes($datas) {
		return rankup_util::check_resource($datas);
	}

	// ������ ��½� &quot; ó�� - 2009.01.14 �߰�
	function self_quot(&$record, $fields) {
		foreach($fields as $field) $record[$field] = str_replace('"', "&quot;", $record[$field]);
	}

	// �̸���/Ȩ������ �ڵ���ũ - 2009.01.14 �߰�
	function self_link(&$record, $fields) {
		foreach($fields as $field) {
			$value = $record[$field];
			switch($field) {
				case "email": $record[$field] = "<a href='mailto:$value'>$value</a>"; break;
				case "homepage":
					$value = "http://".str_replace("http://", '', $value);
					$record[$field] = "<a href='$value'>$value</a>";
					break;
			}
		}
	}

	// �����ڵ� üũ - 2008.11.26 �߰�
	function check_unicode($string) {
		return iconv('CP949', 'UTF-8', iconv('UTF-8', 'CP949', $string))==$string;
	}

	//���ڵ��� ���� ������ ���� üũ :: �⺻ ���ڵ��� UTF-8 �̰ų� �⺻ �� ENG �̸� ���� �ʾƵ� �ȴ�.
	function check_encoding() {
		$base_encoding = rankup_basic::base_encoding();
		return (strtoupper($base_encoding)=="UTF-8" || strtoupper(rankup_basic::base_language())=="ENG") ? false : $base_encoding;
	}

	function check_email($email) {
		return !preg_match('/^[A-z0-9][\w\d.-_]*@[A-z0-9][\w\d.-_]+\.[A-z]{2,6}$/', $email);
	}

	// euckr ���͸� - 2011.12.02 added
	function euckr_filter($string) {
		$str = '';
		for($i=0; $i<strlen($string); $i++) {
			if(ord($string[$i])<=127) $str .= $string[$i]; // 1����Ʈ(����/����) üũ
			else {
				$_str = iconv('UTF-8', 'CP949', $string[$i].$string[++$i].$string[++$i]); // 3����Ʈ(�����ڵ�) üũ
				$str .= ($_str) ? $_str : '��'; // �������� �ʴ� ���� ä���
			}
		}
		return $str;
	}

	// 2012.02.09 added
	function euckr($datas) {
		if(is_array($datas)) foreach($datas as $key => $val) $datas[$key] = self::euckr($val);
		else if(is_object($datas)) foreach($datas as $key => $val) $datas->$key = self::euckr($val);
		else if(is_string($datas)) $datas = iconv('UTF-8', 'CP949', $datas);
		return $datas;
	}
	function utf8($datas) {
		if(is_array($datas)) foreach($datas as $key => $val) $datas[$key] = self::utf8($val);
		else if(is_object($datas)) foreach($datas as $key => $val) $datas->$key = self::utf8($val);
		else if(is_string($datas)) $datas = iconv('CP949', 'UTF-8', $datas);
		return $datas;
	}

	// ���ڵ� ��ȯ ó�� - 2012.02.09 renewal
	function change_encoding(&$mixed, $mode='OUT') {
		if(self::check_encoding()) $mixed = ($mode=='IN') ? self::euckr($mixed) : self::utf8($mixed);
		return $mixed;
	}

	// ����¡ ��� - $_GET �� ����
	function print_paging($total_records='', $division=array(15, 10), $key='page', $pattern='', $icons='' , $ajax='') {
		global $base_url;

		if(empty($total_records)) return '&nbsp;';

		if(is_array($division)) list($limits, $grouping) = $division;
		else $limits = $division;
		if(!$grouping) $grouping = 10;

		$first_page = 1;
		$last_page = ceil($total_records/$limits);

		// ���� ����
		if(!$pattern) {
			$pattern = array(
				'format'	=> '%d',		// ������ ���� �������
				'space'	=> '</li><li class="dot">|</li><li class="num">'		// ������ �� ������
			);
		}
		// ������ ����
		if(!$icons) {
			$icons = array(
				'first'			=> "<img src='{$base_url}Libs/_images/paging_pre_last.gif' align='abstop' alt='ó��'>",
				'previous'	=> "<img src='{$base_url}Libs/_images/paging_pre.gif' align='abstop' alt='����'>",
				'next'			=> "<img src='{$base_url}Libs/_images/paging_next.gif' align='abstop' alt='����'>",
				'last'			=> "<img src='{$base_url}Libs/_images/paging_next_last.gif' align='abstop' alt='������'>"
			);
		}

		// ������ ���� ����
		$open_page = ($_GET)?$_GET[$key]:$_POST[$key];
		if(!$open_page) $open_page = 1;

		$now_grouping = ceil($open_page/$grouping);
		$last_grouping = ceil($last_page/$grouping);
		$min_page = ($now_grouping-1)*$grouping+1;
		$max_page = ($now_grouping*$grouping >= $last_page) ? $last_page : $now_grouping*$grouping;
		$prev_page = ($min_page==$first_page) ? 1 : $min_page-1;
		$next_page = ($max_page==$last_page) ? $last_page : $max_page+1;

		// ����¡ ����
		$contents = array($icons['first'], $icons['previous'], '', $icons['next'], $icons['last']);
		if($open_page>1) {
			$contents[0] = "<li><a href='".params("$key=1")."'>$icons[first]</a></li>";
			$contents[1] = "<li><a href='".params("$key=".($open_page-1))."'>$icons[previous]</a></li>";
		}
		else {
			$contents[0] = "<li><a>$icons[first]</a></li>";
			$contents[1] = "<li><a>$icons[previous]</a></li>";
		}
		$pages = array();
		foreach(range($prev_page, $next_page) as $page) {
			$num = sprintf($pattern['format'], $page);
			$pages[] = ($page==$open_page) ? "<a class='on'>$num</a>" : "<a href='".params("$key=$page")."'>$num</a>";
		}
		$contents[2] = '<li class="num">'.@join($pattern['space'], $pages).'</li>';
		if($open_page<$max_page) {
			$contents[3] = "<li><a href='".params("$key=".($open_page+1))."'>$icons[next]</a></li>";
			$contents[4] = "<li><a href='".params("$key=$last_page")."'>$icons[last]</a></li>";
		}
		else {
			$contents[3] = "<li><a>$icons[next]</a></li>";
			$contents[4] = "<li><a>$icons[last]</a></li>";
		}

		ksort($contents);
		return '<ul>'.@join('', $contents).'</ul>';
	}

	// ���� �߼� - UTF-8 Format
	function send_mail($to, $subject, $body, $from) {
		global $config_info, $wysiwyg_dir, $base_url;

		if(!$this->check_unicode($subject)) $subject = iconv('CP949', 'UTF-8', $subject);
		if(!$this->check_unicode($body)) $body = iconv('CP949', 'UTF-8', $body);
		//if(!$this->check_unicode($from)) $from = iconv('CP949', 'UTF-8', $from);

		if(!strpos($from, 'charset')) $from .= ';charset=utf-8';
		$from = str_replace('euc-kr', 'utf-8', $from);

		$subject = '=?utf-8?B?'.base64_encode($subject).'?=';

		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "X-Mailer: PHP ".phpversion()."\r\n";
		$headers .= "X-Priority: 1\r\n";
		$headers .= $from;

		// �������� �̹��� ��� ���� - 2008.10.21 �߰�
		//$body = str_replace("src=\"/{$wysiwyg_dir}PEG/", "src=\"$config_info[domain]{$wysiwyg_dir}PEG/", $body);
		$body = preg_replace("/src=\"".str_replace("/", "\/", $base_url)."images\//", "src=\"$config_info[domain]images/", $body); //��ǰ���� ���ϸ���.. �̹������� ������ �ʴ´�.
		$body = preg_replace("/src=\"".str_replace("/", "\/", $base_url)."rankup_module\//", "src=\"$config_info[domain]rankup_module/", $body);
		$body = preg_replace("/src=\"".str_replace("/", "\/", $base_url)."PEG\//", "src=\"$config_info[domain]PEG/", $body);
		$body = preg_replace("/src=\"".str_replace("/", "\/", $base_url)."RAD\/PEG\//", "src=\"$config_info[domain]RAD/PEG/", $body);
		$body = preg_replace("/src=\"(.*)".str_replace("/", "\/", $wysiwyg_dir)."/", "src=\"$config_info[domain]$wysiwyg_dir", $body);
		preg_match("/<(.*)>\r\nReply\-to/", $from, $nobody);

		$nobody_return = '-f'.$nobody[1]; // -f sendmail �ɼ� ���
		//$nobody_return = null;

		return mail($to, $subject, $body, $from, $nobody_return);
	}

	// stripslashes �߰� - 2008.06.09 ( rankup_db Ŭ�������� ��� )
	function stripslashes(&$datas) {
		if(!is_array($datas) || !count($datas)) return false;
		foreach($datas as $key=>$val) {
			if(is_object($datas)) $datas->$key = stripslashes($val);
			else $datas[$key] = stripslashes($val);
		}
	}

	// �Ⱓ�˻� - 2008.09.16 ( �����ڸ�忡�� ��� )
	function print_period_search($fields, $values, $option=false, $space=" ~ ", $nolimit='') {
		$fields = explode("|", $fields);
		$values = explode("|", $values);
		foreach($fields as $key=>$field) {
			if($nolimit=='yes') {
				if($key==0) $limit_date = "mindate='2007-10-01' ";
				else $limit_date = "mindate='2008-01-01' ";
			}
			else {
				if($key==0) $limit_date = "mindate='2007-10-01' maxdate='".date("Y-m-d")."'";
				else $limit_date = "mindate='2008-01-01' maxdate='".date("Y-m-d")."'";
			}
			if(!empty($period_search_contents)&&!empty($space)) $period_search_contents .= "<span style='float:left;padding:6px 4px 0px 4px'>$space</span>";
			$period_search_contents .= "<span style='float:left'><input type='text' id='$field' name='$field' $limit_date readOnly value='{$values[$key]}' onClick='rankup_calendar.draw_calendar(this)' class='calendar'></span>";
		}
		// �����ư �ɼ� �߰�
		if($option===true) {
			$period_search_contents .= "<span style='float:left;margin-left:5px;'></span>";
			$period_search_contents .= $this->print_period_search_option($fields);
		}
		return $period_search_contents;
	}

	// �Ⱓ�˻� �ɼ� �κ� - 2008.09.16 ( �����ڸ�忡�� ��� )
	function print_period_search_option($fields) {
		$fields = explode("|", $fields);
		$option_items = array(
			"today" => "���ó�¥",
			"-7 day" => "�ֱ�1����",
			"-15 day" => "�ֱ�15��",
			"-1 month" => "�ֱ�1����",
			"-2 month" => "�ֱ�2����",
			"-3 month" => "�ֱ�3����"
		);
		if(count($fields)>1) $add_base = ", \$('$fields[1]')";
		foreach($option_items as $option_key=>$option_value) {
			$period_option_contents .= "
			<span style='float:left'><input type='button' onClick=\"rankup_calendar.set_date('$option_key', \$('$fields[0]')$add_base)\" value=\"$option_value\"></span>";
		}
		return $period_option_contents;
	}

	// Ȩ������/������ ���� ���� - 2008.12.23 �߰�
	function get_domain($domain) {
		//$domain = str_replace(array("http://", "www."), "", $domain);
		$domain = str_replace("http://", "", $domain);
		return $domain;
	}

	// �̹��� ������ ����ȭ - 2009.10.27 modified
	function get_optimize_size($image, $prefix_size, $tag=false, $fixed=false) {
		list($width, $height) = @getimagesize($image);
		if($fixed==false) {
			if($prefix_size[0]!=null && $width>$prefix_size[0]) {
				$height = round($height / ($width/$prefix_size[0]));
				$width = $prefix_size[0];
			}
			if($prefix_size[1]!=null && $height>$prefix_size[1]) {
				$width = round($width / ($height/$prefix_size[1]));
				$height = $prefix_size[1];
			}
		}
		else { // added
			$width = $prefix_size[0] ? $prefix_size[0] : $width;
			$height = $prefix_size[1] ? $prefix_size[1] : $height;
		}
		return $tag ? ' width='.$width.' height='.$height : array($width, $height);
	}

	// XML ���� - 2009.02.09 �߰�
	function print_xml_header($nodes='') {
		$charset = rankup_basic::default_charset();
		if(strtolower($charset)=='euc-kr' && rankup_util::check_unicode($nodes)) {
			$nodes = iconv('utf-8', 'euc-kr', $nodes);
		}
		header('Content-type: text/xml; charset='.$charset);
		echo '<?xml version="1.0" encoding="'.$charset.'"?>'."\n".$nodes;
	}

	// ��ũ��Ʈ ���� - 2009.07.14 added
	function print_script_header($scripts, $header=false) {
		if($header==true) rankup_basic::include_js_class();
		echo '<script type="text/javascript">'.$scripts.'</script>';
	}

	// �Ķ���� ��ȯ
	function parameters($arguments='', $que=true) {
		if(!$arguments) $params = $_SERVER['QUERY_STRING'];
		else {
			parse_str($_SERVER['QUERY_STRING'], $parameters);
			$arguments = explode('&', $arguments);
			foreach($arguments as $arg) {
				list($key, $val) = explode('=', $arg);
				if($val!='0' && $val=='') unset($parameters[$key]);
				else $parameters[$key] = $val;
			}
			//$params = http_build_query($parameters); - 2009.11.05 fixed
			$params = array();
			foreach($parameters as $key=>$val) array_push($params, $key.'='.$val);
			$params = implode('&', $params);
		}
		if($que==true && $params) $params = '?'.$params;
		return $params;
	}

	// ���͸�ũ �̹��� ��ȯ - 2009.09.22 added
	function get_watermark_image($path='') {
		global $base_dir;
		$watermark_image = '';
		$path = $path ? $path : $base_dir."PEG/watermark/";
		foreach(glob($path."watermark.*") as $watermark) { $watermark_image = basename($watermark); }
		return $watermark_image;
	}

	// ���͸�ũ ���� - 2009.09.22 added
	function watermark_image($canvasImage, $watermarkImage, $watermarkLocate='cm', $transparentColor='none', $opacity=10, $margin=0, $quality=100) {
		ini_set("memory_limit", "120M");
		chmod($canvasImage, 0777);
		if(is_file($watermarkImage)) {
			$image_info = getimagesize($canvasImage);
			switch($image_info[2]) {
				case 1: $canvas_src = imagecreatefromgif($canvasImage); break; // GIF
				case 2: $canvas_src = imagecreatefromjpeg($canvasImage); break; // JPG
				case 3: $canvas_src = imagecreatefrompng($canvasImage); break; // PNG
			}

			$canvas_w = ImageSX($canvas_src);
			$canvas_h = ImageSY($canvas_src);
			$canvas_img = imagecreatetruecolor($canvas_w, $canvas_h);
			imagecopy($canvas_img, $canvas_src, 0,0,0,0, $canvas_w, $canvas_h);
			imagedestroy($canvas_src);

			$watermark_info = getimagesize($watermarkImage);
			switch($watermark_info[2]) {
				case 1: $overlay_src = imagecreatefromgif($watermarkImage); break; // GIF
				case 2: $overlay_src = imagecreatefromjpeg($watermarkImage); break; // JPG
				case 3: $overlay_src = imagecreatefrompng($watermarkImage); break; // PNG
			}

			$overlay_w = ImageSX($overlay_src);
			$overlay_h = ImageSY($overlay_src);
			if($transparentColor=='none') $overlay_img = $overlay_src;
			else {
				// Ư�� ���� ����ó��
				$overlay_img = imagecreatetruecolor($overlay_w, $overlay_h);
				imagecopy($overlay_img, $overlay_src, 0,0,0,0, $overlay_w, $overlay_h);
				imagedestroy($overlay_src);
				switch($transparentColor) {
					case 'white': $transcolor = imagecolorallocate($overlay_img, 0xFF, 0xFF, 0xFF); break;
					case 'black': $transcolor = imagecolorallocate($overlay_img, 0x00, 0x00, 0x00); break;
					case 'magenta': $transcolor = imagecolorallocate($overlay_img, 0xFF, 0x00, 0xFF); break;
				}
				if(isset($transcolor)) imagecolortransparent($overlay_img, $transcolor);
			}

			// ���͸�ũ ��ġ
			$left = 0 + $margin;
			$center = ($canvas_w - $overlay_w) / 2;
			$right = $canvas_w - ($overlay_w + $margin);
			$top = 0 + $margin;
			$middle = ($canvas_h - $overlay_h) /2;
			$bottom = $canvas_h - ($overlay_h + $margin);
			switch($watermarkLocate) {
				case 'lt': list($ww, $wh) = array($left, $top); break; // �������
				case 'lm': list($ww, $wh) = array($left, $middle); break; // �����߾�
				case 'lb': list($ww, $wh) = array($left, $bottom); break; // �����ϴ�
				case 'ct': list($ww, $wh) = array($center, $top); break; // �߾ӻ��
				case 'cm': list($ww, $wh) = array($center, $middle); break; // ���߾�
				case 'cb': list($ww, $wh) = array($center, $bottom); break; // �߾��ϴ�
				case 'rt': list($ww, $wh) = array($right, $top); break; // �������
				case 'rm': list($ww, $wh) = array($right, $middle); break; // �����߾�
				case 'rb': list($ww, $wh) = array($right, $bottom); break; // �����ϴ�
			}

			imagecopymerge($canvas_img, $overlay_img, $ww, $wh, 0, 0, $overlay_w, $overlay_h, $opacity);
			imagejpeg($canvas_img, $canvasImage, $quality);
			imagedestroy($overlay_img);
			imagedestroy($canvas_img);
			chmod($canvasImage, 0644);
		}
	}

	/**
	 * ���Թ��� �̹��� confirm
	 * @additionDate: 2010.06.17
	 */
	// ��ȣ�ڵ� �̹��� ��ȯ
	function print_confirm_image($dimensions=array(100, 50), $attribute='', $mobile=false) { // $mobile - 2012.04.05 added
		global $base_url, $m_url;
		list($width, $height) = $dimensions;
		if($mobile==true) { // ���������
			$image = sprintf('<img src="%sconfirm/index.php" width="%d" height="%d"%s align="absmiddle" />', $m_url, $width, $height, $attribute);
		}
		else { // �Ϲ�����
			$image = sprintf('<img src="%sLibs/_confirm/index.php" width="%d" height="%d"%s align="absmiddle" />', $base_url, $width, $height, $attribute);
		}
		return $image;
	}
	// ��ȣ�ڵ� �Է��ʵ� ��ȯ
	function print_confirm_field($field_name, $attribute='') {
		return sprintf('<input type="text" id="%s" name="%s"%s maxlength="6" />', $field_name, $field_name, $attribute);
	}
	// ��ȣ�ڵ� üũ
	function check_confirm_code($keystring='') {
		$result = (isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring']==$keystring);
		unset($_SESSION['captcha_keystring']);
		return $result;
	}

	/**
	 * json_encode
	 *@additionDate: 2011.08.22
	 */
	function json_encode_string($in_str) {
		mb_internal_encoding("UTF-8");
		$convmap = array(0x80, 0xFFFF, 0, 0xFFFF);
		$str = "";
		for($i=mb_strlen($in_str)-1; $i>=0; $i--) {
			$mb_char = mb_substr($in_str, $i, 1);
			if(mb_ereg("&#(\\d+);", mb_encode_numericentity($mb_char, $convmap, "UTF-8"), $match)) $str = sprintf("\\u%04x", $match[1]).$str;
			else $str = $mb_char . $str;
		}
		return $str;
	}

	function json_encode($arr) {
		$json_str = "";
		if(is_array($arr)) {
			$pure_array = true;
			$array_length = count($arr);
			for($i=0; $i<$array_length; $i++) {
				if(! isset($arr[$i])) {
					$pure_array = false;
					break;
				}
			}
			if($pure_array) {
				$json_str ="[";
				$temp = array();
				for($i=0; $i<$array_length; $i++) {
					$temp[] = sprintf("%s", rankup_util::json_encode($arr[$i]));
				}
				$json_str .= implode(",",$temp);
				$json_str .="]";
			}
			else {
				$json_str ="{";
				$temp = array();
				foreach($arr as $key => $value) {
					$temp[] = sprintf("\"%s\":%s", $key, rankup_util::json_encode($value));
				}
				$json_str .= implode(",",$temp);
				$json_str .="}";
			}
		}
		else {
			if(is_string($arr)) $json_str = "\"". rankup_util::json_encode_string($arr) . "\"";
			else if(is_numeric($arr)) $json_str = $arr;
			else $json_str = "\"". rankup_util::json_encode_string($arr) . "\"";
		}
		return $json_str;
	}

	// ������ ÷�� �̹��� ó�� - 2011.08.23 added
	function trans_wysiwyg($content='', $keep_domain=false) { // $content : stripslashes() �� ��
		global $wysiwyg_dir, $wysiwyg_url;
		$content = stripslashes($content);
		preg_match_all('/<img\s+.*?src="([^"]+)"[^>]*>/is', $content, $imgs);
		foreach($imgs[1] as $key=>$img) {
			// �̹��� ���� PEG ������ �̵�
			if(strpos($img, $wysiwyg_url.'PEG_temp/')!==false) {
				$_name = basename($img);
				$tmp_file = $wysiwyg_dir.'PEG_temp/'.$_name;
				$save_file = $wysiwyg_dir.'PEG/'.$_name;
				if(is_file($tmp_file)) rename($tmp_file, $save_file);
			}
			// �̹����� �ɸ� PEG_temp �� PEG �� ���� �� ������ ����
			$_info = parse_url($img); // scheme : http  or  https
			$_info['scheme'] = empty($_info['scheme']) ? '' : $_info['scheme']."://";
			$_img = str_replace($img, str_replace('/PEG_temp/', '/PEG/', $img), $imgs[1][$key]);
			if(!$keep_domain) $_img = str_replace($img, str_replace($_info['scheme'].$_SERVER['HTTP_HOST'], '', $_img), $imgs[1][$key]);
			else $_img = str_replace($img, $_img, $imgs[1][$key]);
			if(strpos(strtolower($_img), 'border')===false) $_img = preg_replace('/ src/i', ' border="0" src', $_img); // border='0' �߰�
			$content = str_replace($imgs[1][$key], $_img, $content);
		}
		return $content;
	}

	// ���ڸ� �̹����� ��ü - 2012.03.02 added
	function str2img($str, $entry) {
		$img = '';
		for($i=0; $i<strlen($str); $i++) $img .= $entry[$str[$i]];
		return $img;
	}

	// ���� �̹��� ������ ���� - 2012.03.11 moved in
	function prefix_contents($content='', $prefix_size=array(240, 181)) {
		// �̹��� ���̺� ��ġ
		preg_match_all('/(<table\s+.*?width=[\'"]?+([0-9%]{1,})[\'"]?+[^>]*)>/is', $content, $tables);
		foreach($tables[2] as $key=>$val) {
			$ori_width = "width=".$val;
			$new_width = str_replace($ori_width,"width='100%' style='table_layout:fixed;'",$tables[0][$key]);
			$content = str_replace($tables[0][$key],$new_width,$content);
		}
		// ������ ������ ��ġ
		preg_match_all('/(<object\s+.*?width=[\'"]?+([0-9%]{1,})[\'"]?+[^>]*)>/is', $content, $objects);
		foreach($objects[0] as $key=>$val) {
			$new_width = str_replace("width","width='98%' _w",$objects[0][$key]);
			$content = str_replace($objects[0][$key],$new_width,$content);
		}
		preg_match_all('/(<embed\s+.*?width=[\'"]?+([0-9%]{1,})[\'"]?+[^>]*)>/is', $content, $embeds);
		foreach($embeds[0] as $key=>$val) {
			$new_width = str_replace("width","width='98%' _w",$embeds[0][$key]);
			$content = str_replace($embeds[0][$key],$new_width,$content);
		}
		// �̹��� ������ ��ġ
		preg_match_all('/(<img\s+.*?src="([^"]+)"[^>]*)>/is', $content, $images);
		foreach($images[0] as $key=>$image) {
			if(stristr($images[2][$key],'http')) @$imgsz = getimagesize($images[2][$key]);
			else $imgsz = getimagesize($_SERVER['DOCUMENT_ROOT'].$_info['path'].$images[2][$key]);

			list($new_image, $image_url) = array($image, $images[2][$key]);

			if($imgsz[0] > 300) $_width = '100%';
			else $_width = $imgsz[0];

			$_dimensions = '';
			$new_image = str_ireplace(array('width', 'height'), array('_width', '_height'), $new_image); // width & height reset
			if($imgsz[0] > 300 ) {
				$_dimensions = 'width:100%';
			} else if($imgsz[0] != '') {
				$_dimensions = 'width:'.$_width.'px';
			}
			if($_dimensions != '') {
				if(stristr($image, 'style')!==false) {
					preg_match_all('/style="([^"]+)"[^"]/is', $new_image, $styles);
					$new_image = str_replace($styles[1][0], $_dimensions.$styles[1][0], $new_image);
				}
				else $new_image = str_replace(array('/>', '>'), ' style="'.$_dimensions.'">', $new_image);

				if(stristr($new_image, 'align')!==false) $new_image = str_ireplace(array('align=center', 'align="center"', "align='center'"), '', $new_image);
				if(stristr($new_image, 'align')===false) $new_image = '<center>'.$new_image.'</center>';
				$content = str_replace($image, $new_image, $content);
			}
		}
		return $content;
	}
}
?>