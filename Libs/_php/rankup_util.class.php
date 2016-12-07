<?php
##여러곳에서 사용할 수 있는 utility모음 클래스
class rankup_util extends rankup_db{
	function rankup_util(){
		parent::rankup_db();
	}

	//경고창을 보여주고 지정된 페이지로 이동.
	//메시지가 없을경우, 이전 페이지로 이동
	function popup_msg_js($msg,$url=""){
		rankup_util::alertMessage($msg, $url);
	}

	//경고창을 보여주기만 하는 함수.
	//이동할 주소가 없을 경우, 메시지만 출력
	function popup_move_js($msg='',$url='')	{
		echo '<script language=javascript>';
		if($msg) echo "alert('$msg');";
		if($url) echo "location.replace('$url')";
		echo '</script>';
	}

	// IE 버전 반환
	function ie_version() {
		preg_match('/MSIE ([0-9].[0-9])/', $_SERVER['HTTP_USER_AGENT'], $user_agent);
		return $user_agent[1];
	}

	//해당 테이블을 삭제하는 함수
	function make_drop_table($name)	{
		$drop_que="drop table if exists $name";
		return $this->query($drop_que);
	}

	//해당 테이블이 존재하는지 검사한다.
	//$tb_list는 array이거나, 하나의 테이블 이름
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

	//원하는 프레임을 reload()시킨다.
	//$list는 프레임의 이름
	function make_reload_frame($list){
		if($list)
			echo "<script>parent.$list.location.reload();</script>";
		else
			echo "<script>top.location.reload();</script>";
	}

	//문자열 자르는 함수
	function str_cut($msg, $cut_size, $tail="...") { // 문자열 끊기 (이상의 길이일때는 ... 로 표시)
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

	// 문자열 길이
	function str_len($msg) {
		for($i=0; $i<strlen($msg); $i++) {
			if(ord($msg[$i])>127) $point++;
			$point++;
		}
		return $point;
	}

	//디렉토리를 제거하는 함수
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

   //유효한 아이디 인지를 체크하는 함수
	//$id는 검사할 아이디 값
	function make_valid_id($id){
		if(empty($id))
			return '아이디를 입력하여 주십시요';
		else if(ereg("[[:space:]]+",$id))
			return '공백없이 입력하여 주십시요';
		else if(strlen($id) > 16 || strlen($id) < 4)
			return '아이디의 글자수를 확인하여 주십시요';
		else if(!ereg("(^[-0-9a-zA-Z_*?#@]{4,16}$)",$id))
			return '아이디를 확인하여 주십시요';
		else
			return '';
	}

	//주민등록번호를 체크하는 함수
	//맞는 주민등록번호인지 체크한다.
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

	//정확한 이메일주소인가 체크하는 함수
	//유효한 이메일인가 체크하는 함수
	function make_valid_domain($email)	{
		//if(preg_match("/([ \n]+)([a-z0-9\_\-\.]+)@([a-z0-9\_\-\.]+)/", $email)) {
		if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+$", $email)) {
			list($alias, $domain) = split("@", $email);
			$str='';
		   if (checkdnsrr($domain, "MX")) {	//서버가 존재할경우
			   getmxrr($domain, $mxhosts);
			   $str='';
			} else {		//서버가 존재하지 않을 경우
				$str='존재하지 않는 이메일 서버 입니다.';
			}
		} else {	//형식이 잘못 된경우
			$str='이메일의 형식이 잘못 되었습니다.';
		}
		return $str;
	}

	//입력한 비밀번호가 정확한 값인지 체크하는 함수
	function make_valid_passwd($pw){
		if(empty($pw))
			return '비밀번호를 입력하여 주십시요';
		else if(ereg("[[:space:]]+",$pw))
			return '공백없이 입력하여 주십시요';
		else if(strlen($pw) > 16 || strlen($pw) < 4)
			return '비밀번호의 글자수를 확인하여 주십시요';
		else if(!ereg("(^[-0-9a-zA-Z_*?#$%^&*()+!@]{4,16}$)",$pw))
			return '비밀번호를 확인하여 주십시요';
		else
			return '';
	}

	//넘어온 변수값이, post나 get에 넘어 온것에 상관없이 처리
	function getParam($name) {
		$value = $_POST[$name];
		if(empty($value)) $value = $_GET[$name];
		return is_array($value) ? $value : urldecode($value);
	}

	//post나 get으로 넘어온 값들을 모두 변수화 하는 함수
	function getaddstring(){
		$getParam = $_GET;
		$postParam = $_POST;
		$lastParam = array_merge($getParam,$postParam);
		$lastAdd = '';
		foreach($lastParam as $key=>$val) $lastAdd.="$key=".$val."&";
		return $lastAdd;
	}

	//다시 돌아갈 페이지 주소를 미리 만듬.
	function getBackUrl(){
		$url = $_SERVER['PHP_SELF'];
		$addstring=$this->getAddString();
		$backurl=$url.'?'.$addstring;
		return base64_encode($backurl);
	}
	//특정 디렉토리를 삭제하는 함수
	//$dir는 삭제할 디렉토리
	//내부 함수를 호출하므로, 정확한 로직이기는 하나,시간이 많이 걸림.
	function rm($dir) {
		if(!$dh = @opendir($dir)) return 1;
		while (($obj = readdir($dh))) {
			if($obj=='.' || $obj=='..') continue;
			if (!@unlink($dir.'/'.$obj)) $this->rm($dir.'/'.$obj);
		}
		@rmdir($dir);
	}

	//해당 디렉토리의 모든 파일을 지우는 함수
	//$dir는 폴더 이름
	function make_delete_files($dir)	{
		$del_count=0;
		if(is_dir($dir)) {	//폴더 인지
			$fp = @opendir($dir);
			while($file=@readdir($fp)) { // 파일들을 읽는다
				if($file != '.' && $file !='..') {
					$files=$dir.'/'.$file;
					if(@unlink($files)) $del_count++; // 파일 삭제시 삭제 카운트를 증가
				}
			}
		}
		else $del_count = -1; // 디렉토리가 아니라면
		return $del_count;
	}

	//파일 다운로드 하는 함수
	function down_file($file, $rename){
		if(!is_file($file)) return false;

		if($this->check_unicode($rename)) $rename = iconv('UTF-8', 'CP949', $rename); // 2010.06.29 fixed
		$ctype = mime_content_type($file); // php 내부 지원이 되지 않을 경우 'Libs/_php/rankup_basic.class.php' 에 정의
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

	//파일 다운로드 하는 함수
	//$real_name는 물리적인 파일의 위치, $file_name는 논리적인 파일의 이름
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

	// 워터마크 만드는 함수 - 2009.01.29 추가
	function watermark_exec($resource, $watermark, $opacity=40) {
		$s_width = imageSX($resource); $s_height = imageSY($resource);
		list($overlay, $o_width, $o_height) = rankup_util::imagecreatefrom_photo($watermark);

		// 워터마크 위치 구하기 - 정중앙
		$pos_x = ($s_width - $o_width) / 2;
		$pos_y = ($s_height - $o_height) / 2;

		imagecopymerge($resource, $overlay, $pos_x, $pos_y, 0, 0, $o_width, $o_height, $opacity);
		return $resource;
	}

	// 확장자값 구해서 워터마크 활용하기 - 2009.01.29 추가
	function imagecreatefrom_photo($source) {
		list($width, $height, $type) = getimagesize($source);
		switch($type) {
			case 1: $resource = imagecreatefromgif($source); break;
			case 2: $resource = imagecreatefromjpeg($source); break;
			case 3: $resource = imagecreatefrompng($source); break;
		}
		return array($resource, $width, $height);
	}

	//썸네일 만드는 함수
	//$filePath는 실제로 저장된 파일의 이름
	//$saveName는 섬네일후 저장될 이름
	//$sFactor는 가로/세로의 최대 크기
	//$saveDir는 섬네일후에 저장될 디렉토리의 이름
	//$watermark는 array(워터마크심을 이미지, 투명도 조절) -- > jpg, png제외하고는 워터마크는 안됩니다.
	//$destory는 섬네일후 원본 파일의 제거 여부
	function make_thumnail($filePath, $saveDir = "./", $saveName, $sFactor="", $watermark=false, $destroy="1"){
		ini_set("memory_limit", "80M"); // 메모리 제한을 여유 있게 설정
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
			case 1: //gif 일경우
				// 애니메이션 GIF 일경우 1 Frame 만 남아서, 썸네일 기피, 굳이 썸네일을 만들려면 아래 if 절을 주석 처리
				if(move_uploaded_file($filePath, $saveDir.$saveName)) return $saveName;

				$src_img = imagecreatefromgif($filePath);
				$dst_img = imagecreate($imgW, $imgH);
				ImageCopyResized($dst_img,$src_img,0,0,0,0,$imgW,$imgH,$sz[0],$sz[1]);
				ImageInterlace($dst_img);
				if($watermark!=false) rankup_util::watermark_exec($dst_img, $watermark['photo'], $watermark['opacity']);
				ImageGIF($dst_img, $saveDir.$saveName); //실제로 이미지생성하는 부분
				break;
			case 2: //jpg 일경우
				$src_img = imagecreatefromjpeg($filePath);
				$dst_img = imagecreatetruecolor($imgW, $imgH);
				ImageCopyResized($dst_img,$src_img,0,0,0,0,$imgW,$imgH,$sz[0],$sz[1]);
				ImageInterlace($dst_img);
				if($watermark!=false) rankup_util::watermark_exec($dst_img, $watermark['photo'], $watermark['opacity']);
				ImageJPEG($dst_img, $saveDir.$saveName);
				break;
			case 3: //png 일경우
				$src_img = imagecreatefrompng($filePath);
				$dst_img = imagecreatetruecolor($imgW, $imgH);
				ImageCopyResized($dst_img,$src_img,0,0,0,0,$imgW,$imgH,$sz[0],$sz[1]);
				ImageInterlace($dst_img);
				if($watermark!=false) $dst_img = rankup_util::watermark_exec($dst_img, $watermark['photo'], $watermark['opacity']);
				ImagePNG($dst_img, $saveDir.$saveName);
				break;
			default:  //swf 일경우
				if(move_uploaded_file($filePath, $saveDir.$saveName));
				return $saveName;
				break;
		}
		if($destroy) {
			ImageDestroy($dst_img); // 제거
			ImageDestroy($src_img);
		}
		return $saveName;
	}

	// 썸네일 만들기 - move_uploaded_file 한 파일을 썸네일 처리
	function make_thumbnail($source_file, $dest_file, $width=null, $height=null) {
		ini_set("memory_limit", "80M"); // 메모리 제한을 여유 있게 설정

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

	//파일의 확장자를 구하는 함수 - 2008-07-07 수정
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

	// 파일사이즈 단위 표기 - 2008-07-07 추가
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

	//순위를 조절
	//$no는 해당 게시물의 번호
	//$mode up/down
	//$rankno 현재 랭킹
	//$add_que는 where절을 제어할 부분
	function change_list_rank($no,$table,$direction,$add_que=''){
		$rankno=$this->queryR("select rank from $table where no = '$no'");
		if($direction=='up') { //상위 이동시
			$next_rank = $this->queryR("select max(rank) from $table where rank < '$rankno' $add_que ");
			if($next_rank >= 0) {
				$next_no = $this->queryR("select max(no) from $table where rank = $next_rank $add_que");
				$result1 = $this->query("update $table set rank = '$next_rank' where no = '$no'");
				$result2 = $this->query("update $table set rank = '$rankno' where no = '$next_no'");
				return ($result1 && $result2);
			}
			else return true; // 이동할 값이 존재하지 않는다면, 종료한다.
		}
		else if($direction == 'down') {
			$next_rank = $this->queryR("select min(rank) from $table where rank > '$rankno' $add_que");
			if($next_rank) {
				$next_no = $this->queryR("select min(no) from $table where rank = $next_rank $add_que");
				$result1 = $this->query("update $table set rank = '$next_rank' where no = '$no'");
				$result2 = $this->query("update $table set rank = '$rankno' where no = '$next_no'");
				return ($result1 && $result2);
			}
			else return true; //이동할 값이 존재하지 않는다면, 종료한다.
		}
		else return false;
	}
	//이동할 랭크가 존재하는지 체크하는 함수
	function check_valid_rank($no,$table,$direction,$add_que=''){
		$rankno=$this->queryR("select rank from $table where no = '$no'");
		if($direction=='up') $next_rank = $this->queryR("select count(*) from $table where rank < $rankno $add_que");
		else if($direction=='down') $next_rank = $this->queryR("select count(*) from $table where rank > $rankno $add_que");
		return $next_rank;
	}

	/*
	//달력 내용을 추출하는 부분
	//시작을 나타내는 text 박스의 이름과 끝을 나타내는 텍스트 박스의 끝이 와야 함.
	function make_calendar_content($start,$end,$dir){
		$msg='<input type="text" name="'.$start.'" style="cursor:pointer;height:20px;width:70px;text-align:center;font-size:8pt;font-weight:bolder;font-family:Arial;border:#A9BECF 1px solid;" size=10 value="'.$this->getParam($start).'" readOnly OnClick="var result=calender(); if(!is_null(result))
		setCal(\''.$start.'\',result);"><img src="'.$dir.'rankup_module/images/calendar2.gif" OnClick="var result=calender(); if(!is_null(result))
		setCal(\''.$start.'\',result);" align="absmiddle" style="cursor:hand">~<input type="text" name="'.$end.'" style="cursor:pointer;height:20px;width:70px;text-align:center;font-size:8pt;font-weight:bolder;font-family:Arial;border:#A9BECF 1px solid;" size=10 value="'.$this->getParam($end).'" readOnly OnClick="var result=calender(); if(!is_null(result)) setCal(\''.$end.'\',result);"><img src="'.$dir.'rankup_module/images/calendar2.gif" OnClick="var result=calender(); if(!is_null(result)) setCal(\''.$end.'\',result);" align="absmiddle" style="cursor:hand">';
		return $msg;
	}

	//달력 자스를 출력하는 부분
	//폼의 이름이 와야 함
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
	//eidtor를 실제로 구현하는 메소드
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
	//eidtor js를 출력하는 함수
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

	//이전 쿼리에 나중의 쿼리를 더하는 함수
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

	//ok 라는 xml페이지를 만들어 내는 함수
	function make_ok_xml($result){
		if($result) $ok='ok';
		else $ok='failed';
		$str="<?xml version='1.0' encoding='euc-kr'?>";
		$str.="<root>";
		$str.="<ok>$ok</ok>";
		$str.="</root>";
		return $str;
	}

	// SQL 쿼리 문자열 생성 :: 입력/수정시 사용
	function change_query_string($arr) {
		//foreach($arr as $field => $value) $field_vals[count($field_vals)] = $value!==NULL ? is_array(@unserialize($value)) ? "$field='".addslashes($value)."'" : "$field='".addslashes($value)."'" : "$field=NULL";
		$field_vals = array();
		foreach($arr as $field => $value) $field_vals[] = ($value===NULL) ? "$field=NULL" : "$field='".addslashes($value)."'";
		return @join(', ', $field_vals);
	}

	// 중복값이 없는 TIMESTAMP :: 파일네이밍에 사용
	function uniqueTimeStamp() {
		list($msec, $sec) = explode(" ", microtime());
		return $sec.substr(array_pop(explode(".", $msec)), 0, 4);
	}

	// 대기컨텐츠의 파일 정보 생성
	function set_sf_file_info($file, $base_dir='') { // $base_dir : 대기 컨텐츠의 베이스 절대경로
		$rankup_explorer = new rankup_explorer;
		$ctype = mime_content_type($file); // 파일종류 파악
		if(!$ctype) $ctype = "application/force-download";
		$result['type'] = $ctype; // 파일형식
		$result['name'] = $file; // 실제 파일명
		$result['tmp_name'] = $rankup_explorer->base_dir.$file; // 서버내 임시 파일명
		return $result;
	}

	// 첨부파일 정보
	function get_file_info($file, $mixed='') { // $mixed 값이 있을경우 해당 값으로 암호화 진행
		if(empty($file['tmp_name'])) return ''; // 첨부한 파일이 없으면 공백리턴
		$result[org] = $file['name']; // 로컬내 실제 파일명
		$result[tmp] = $file['tmp_name']; // 서버내 임시 파일명

		// QUploder (ActiveX) 를 사용했을 경우
		if(is_array($file[tmp_name])) {
			for($i=0; $i<count($file[tmp_name]); $i++) {
				$result[type][$i] = array_pop(explode('/', $file['type'][$i])); // 파일 종류
				$result[ext][$i] = array_pop(explode(".", $file['name'][$i])); // 확장자
				$result[sav][$i] = empty($mixed) ? $this->uniqueTimeStamp().'.'.$result[ext][$i] : $this->uniqueTimeStamp().'.'.base64_encode(strrev($this->uniqueTimeStamp()+rand(strtotime("-10 day"), time())).'.'.$mixed).'.'.$result[ext][$i];
			}
		}
		// 등록대기중인 컨텐츠를 선택한 경우 와 <input type='file' ~> 을 사용했을 경우
		else {
			$result[type] = array_pop(explode('/', $file['type'])); // 파일 종류
			$result[ext] = array_pop(explode(".", $file['name'])); // 확장자
			$result[sav] = empty($mixed) ? $this->uniqueTimeStamp().'.'.$result[ext] : $this->uniqueTimeStamp().'.'.base64_encode(strrev($this->uniqueTimeStamp()+rand(strtotime("-10 day"), time())).'.'.$mixed).'.'.$result[ext];
		}
		return $result;
	}

	// 셀렉트박스생성
	function createSelectBox($obj, $values='', $addTags='') { // $value = array('default'=>'', 'min'=>1, 'max'=>15, 'gap'=>5, format=>'%02d', 'unit'=>'층', 'value'=>3)  or  values=3
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
				case eregi("_parking", $obj)==1: // 주차대수
					foreach($GLOBALS[_PARKINGSET] as $value => $text) {
						$select = ($value == $values) ? " selected" : "";
						$options[count($options)] = "<option value='$value'$select>$text</option>";
					}
					break;
				case eregi("period_unit", $obj)==1:  // 기간단위
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

	// 멀티체크박스생성 :: 체크박스, 라디오버튼 생성
	function createMultiSelectBox($type, $obj, $values, $addTags='', $space='') { // $values = array('yes'=>'사용', 'no'=>'미사용', value='yes') :: value : 콤마(,)로 구분
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

	// 링크생성 :: 중복값 제거 + REQUEST 정보 : <a href=".$this->setLink("detail.html?Sno=100","click").">링크</a>
	function setLink($link, $exclusion_arg='', $add_arg='') {
		$exclusions = !is_array($exclusion_arg) ? !empty($exclusion_arg) ? explode(",",$exclusion_arg) : array() : $exclusion_arg;
		parse_str(array_pop(explode("?",$link)),$args);
		foreach($args as $key=>$val) if(empty($args[$key])||(is_array($exclusions) && in_array($key,$exclusions))||($key==$exclusions)) unset($args[$key]);
		$exclusions = array_merge($exclusions,array_keys($args));
		$arg = http_build_query($args); // http_build_query() :: php 5 or later
		$add_arg = !empty($add_arg) ? !empty($arg) ? $arg."&".$add_arg : $add_arg : $arg;
		return $this->getRequestInfo(array_shift(explode("?",$link)), $exclusions, !empty($add_arg)).$add_arg;
	}

	// 페이지 REQUEST 정보	:: alertMessage("LOGIN_MESSAGE","${hom_path}member/login.html?url=".$cs->getRequestInfo());
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

	// 메시지출력
	function alertMessage($msg, $uri='', $target='', $cmd='', $mode="ALERT") { // target :: { self | opener[....] }
		switch(strtoupper($mode)) {
			case "ALERT":
				switch($msg) {
					case "LOGIN_MESSAGE":
						$msg = "\\n죄송합니다. 고객님은 로그인이 되어 있지 않습니다.\\n\\n로그인을 하신 후 서비스를 다시 이용해 주시기 바랍니다.";
						if(empty($uri)) $uri = "LOGIN_PAGE";
						break;
					case "ADMIN_LOGIN_MESSAGE":
						$msg = "관리자만 접근이 허용된 페이지 입니다.";
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
				if(!empty($uri)) exit; // 이동할 페이지가 있으면 Terminate!!
				break;

			case "LAYER":
				// .....
				break;
		}
	}

	// 조건선택메시지출력
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
	## 암호화 복호화
	#############################################################################
	//기본키배열 세션 등록
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
	// 암호화
	function encode($str, $time=1) {
		$str = strrev(strtr(base64_encode($str), $this->BASE64_CHARS, $this->getKey()));
		if($time>1) $str = $this->encode($str, --$time);
		return $str;
	}
	// 복호화
	function decode($encoded, $time=1) {
		$encoded = base64_decode(strtr(strrev($encoded), $this->getKey(), $this->BASE64_CHARS));
		if($time>1) $encoded = $this->decode($encoded, --$time);
		return $encoded;
	}

	// 인증키 반환 - 2009.11.02 added
	function make_ckey() {
		return $this->encode(time(), 5);
	}

	// 인증키 검사 - 스팸등록 방지 - 2009.11.02 added
	function check_ckey($ckey, $sec=300) { // 제한시간을 초과한 경우 등록제한
		if(!$ckey) return false;
		$ckey = $this->decode($ckey, 5);
		if($_SESSION['ckey']==$ckey) return -1;
		if(!is_numeric($ckey) || !ctype_digit($ckey)) return false;
		$ctime = time()-$ckey;
		if(!$ctime || $ctime>$sec) return false;
		$_SESSION['ckey'] = $ckey;
		return true;
	}

	// 배열값 정렬
	function multi_sort($arr_data, $column, $sort=SORT_ASC) {  // sort = SORT_DESC  or  SORT_ASC
		$org_data = $arr_data;
		foreach($arr_data as $key => $val) if($val[$column] != false) $tmp_data[]=$val;
		for($i=0; $i<count($tmp_data); $i++) $sortarr[]=$tmp_data[$i][$column];
		@array_multisort($sortarr, $sort, $org_data);
		return $org_data;
	}

	// 리소스 체크 - 2009.01.23 fixed
	function check_resource($datas) {
		return (is_resource($datas) && mysql_num_rows($datas)) || (is_array($datas) && count($datas));
	}

	// 리소스 체크 별칭 - 2009.01.08 추가
	function chkRes($datas) {
		return rankup_util::check_resource($datas);
	}

	// 수정폼 출력시 &quot; 처리 - 2009.01.14 추가
	function self_quot(&$record, $fields) {
		foreach($fields as $field) $record[$field] = str_replace('"', "&quot;", $record[$field]);
	}

	// 이메일/홈페이지 자동링크 - 2009.01.14 추가
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

	// 유니코드 체크 - 2008.11.26 추가
	function check_unicode($string) {
		return iconv('CP949', 'UTF-8', iconv('UTF-8', 'CP949', $string))==$string;
	}

	//인코딩을 할지 안할지 여부 체크 :: 기본 인코딩이 UTF-8 이거나 기본 언어가 ENG 이면 하지 않아도 된다.
	function check_encoding() {
		$base_encoding = rankup_basic::base_encoding();
		return (strtoupper($base_encoding)=="UTF-8" || strtoupper(rankup_basic::base_language())=="ENG") ? false : $base_encoding;
	}

	function check_email($email) {
		return !preg_match('/^[A-z0-9][\w\d.-_]*@[A-z0-9][\w\d.-_]+\.[A-z]{2,6}$/', $email);
	}

	// euckr 필터링 - 2011.12.02 added
	function euckr_filter($string) {
		$str = '';
		for($i=0; $i<strlen($string); $i++) {
			if(ord($string[$i])<=127) $str .= $string[$i]; // 1바이트(숫자/영문) 체크
			else {
				$_str = iconv('UTF-8', 'CP949', $string[$i].$string[++$i].$string[++$i]); // 3바이트(유니코드) 체크
				$str .= ($_str) ? $_str : '□'; // 지원하지 않는 문자 채우기
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

	// 인코딩 변환 처리 - 2012.02.09 renewal
	function change_encoding(&$mixed, $mode='OUT') {
		if(self::check_encoding()) $mixed = ($mode=='IN') ? self::euckr($mixed) : self::utf8($mixed);
		return $mixed;
	}

	// 페이징 출력 - $_GET 값 참조
	function print_paging($total_records='', $division=array(15, 10), $key='page', $pattern='', $icons='' , $ajax='') {
		global $base_url;

		if(empty($total_records)) return '&nbsp;';

		if(is_array($division)) list($limits, $grouping) = $division;
		else $limits = $division;
		if(!$grouping) $grouping = 10;

		$first_page = 1;
		$last_page = ceil($total_records/$limits);

		// 패턴 정의
		if(!$pattern) {
			$pattern = array(
				'format'	=> '%d',		// 페이지 문자 출력형태
				'space'	=> '</li><li class="dot">|</li><li class="num">'		// 페이지 간 구분자
			);
		}
		// 아이콘 정의
		if(!$icons) {
			$icons = array(
				'first'			=> "<img src='{$base_url}Libs/_images/paging_pre_last.gif' align='abstop' alt='처음'>",
				'previous'	=> "<img src='{$base_url}Libs/_images/paging_pre.gif' align='abstop' alt='이전'>",
				'next'			=> "<img src='{$base_url}Libs/_images/paging_next.gif' align='abstop' alt='다음'>",
				'last'			=> "<img src='{$base_url}Libs/_images/paging_next_last.gif' align='abstop' alt='마지막'>"
			);
		}

		// 페이지 변수 정의
		$open_page = ($_GET)?$_GET[$key]:$_POST[$key];
		if(!$open_page) $open_page = 1;

		$now_grouping = ceil($open_page/$grouping);
		$last_grouping = ceil($last_page/$grouping);
		$min_page = ($now_grouping-1)*$grouping+1;
		$max_page = ($now_grouping*$grouping >= $last_page) ? $last_page : $now_grouping*$grouping;
		$prev_page = ($min_page==$first_page) ? 1 : $min_page-1;
		$next_page = ($max_page==$last_page) ? $last_page : $max_page+1;

		// 페이징 구성
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

	// 메일 발송 - UTF-8 Format
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

		// 위지윅상 이미지 경로 교정 - 2008.10.21 추가
		//$body = str_replace("src=\"/{$wysiwyg_dir}PEG/", "src=\"$config_info[domain]{$wysiwyg_dir}PEG/", $body);
		$body = preg_replace("/src=\"".str_replace("/", "\/", $base_url)."images\//", "src=\"$config_info[domain]images/", $body); //상품정보 메일링씨.. 이미지들이 보이지 않는다.
		$body = preg_replace("/src=\"".str_replace("/", "\/", $base_url)."rankup_module\//", "src=\"$config_info[domain]rankup_module/", $body);
		$body = preg_replace("/src=\"".str_replace("/", "\/", $base_url)."PEG\//", "src=\"$config_info[domain]PEG/", $body);
		$body = preg_replace("/src=\"".str_replace("/", "\/", $base_url)."RAD\/PEG\//", "src=\"$config_info[domain]RAD/PEG/", $body);
		$body = preg_replace("/src=\"(.*)".str_replace("/", "\/", $wysiwyg_dir)."/", "src=\"$config_info[domain]$wysiwyg_dir", $body);
		preg_match("/<(.*)>\r\nReply\-to/", $from, $nobody);

		$nobody_return = '-f'.$nobody[1]; // -f sendmail 옵션 사용
		//$nobody_return = null;

		return mail($to, $subject, $body, $from, $nobody_return);
	}

	// stripslashes 추가 - 2008.06.09 ( rankup_db 클래스에서 사용 )
	function stripslashes(&$datas) {
		if(!is_array($datas) || !count($datas)) return false;
		foreach($datas as $key=>$val) {
			if(is_object($datas)) $datas->$key = stripslashes($val);
			else $datas[$key] = stripslashes($val);
		}
	}

	// 기간검색 - 2008.09.16 ( 관리자모드에서 사용 )
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
		// 단축버튼 옵션 추가
		if($option===true) {
			$period_search_contents .= "<span style='float:left;margin-left:5px;'></span>";
			$period_search_contents .= $this->print_period_search_option($fields);
		}
		return $period_search_contents;
	}

	// 기간검색 옵션 부분 - 2008.09.16 ( 관리자모드에서 사용 )
	function print_period_search_option($fields) {
		$fields = explode("|", $fields);
		$option_items = array(
			"today" => "오늘날짜",
			"-7 day" => "최근1주일",
			"-15 day" => "최근15일",
			"-1 month" => "최근1개월",
			"-2 month" => "최근2개월",
			"-3 month" => "최근3개월"
		);
		if(count($fields)>1) $add_base = ", \$('$fields[1]')";
		foreach($option_items as $option_key=>$option_value) {
			$period_option_contents .= "
			<span style='float:left'><input type='button' onClick=\"rankup_calendar.set_date('$option_key', \$('$fields[0]')$add_base)\" value=\"$option_value\"></span>";
		}
		return $period_option_contents;
	}

	// 홈페이지/도메인 포멧 리턴 - 2008.12.23 추가
	function get_domain($domain) {
		//$domain = str_replace(array("http://", "www."), "", $domain);
		$domain = str_replace("http://", "", $domain);
		return $domain;
	}

	// 이미지 사이즈 최적화 - 2009.10.27 modified
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

	// XML 리턴 - 2009.02.09 추가
	function print_xml_header($nodes='') {
		$charset = rankup_basic::default_charset();
		if(strtolower($charset)=='euc-kr' && rankup_util::check_unicode($nodes)) {
			$nodes = iconv('utf-8', 'euc-kr', $nodes);
		}
		header('Content-type: text/xml; charset='.$charset);
		echo '<?xml version="1.0" encoding="'.$charset.'"?>'."\n".$nodes;
	}

	// 스크립트 리턴 - 2009.07.14 added
	function print_script_header($scripts, $header=false) {
		if($header==true) rankup_basic::include_js_class();
		echo '<script type="text/javascript">'.$scripts.'</script>';
	}

	// 파라메터 반환
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

	// 워터마크 이미지 반환 - 2009.09.22 added
	function get_watermark_image($path='') {
		global $base_dir;
		$watermark_image = '';
		$path = $path ? $path : $base_dir."PEG/watermark/";
		foreach(glob($path."watermark.*") as $watermark) { $watermark_image = basename($watermark); }
		return $watermark_image;
	}

	// 워터마크 삽입 - 2009.09.22 added
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
				// 특정 배경색 투명처리
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

			// 워터마크 위치
			$left = 0 + $margin;
			$center = ($canvas_w - $overlay_w) / 2;
			$right = $canvas_w - ($overlay_w + $margin);
			$top = 0 + $margin;
			$middle = ($canvas_h - $overlay_h) /2;
			$bottom = $canvas_h - ($overlay_h + $margin);
			switch($watermarkLocate) {
				case 'lt': list($ww, $wh) = array($left, $top); break; // 좌측상단
				case 'lm': list($ww, $wh) = array($left, $middle); break; // 좌측중앙
				case 'lb': list($ww, $wh) = array($left, $bottom); break; // 좌측하단
				case 'ct': list($ww, $wh) = array($center, $top); break; // 중앙상단
				case 'cm': list($ww, $wh) = array($center, $middle); break; // 정중앙
				case 'cb': list($ww, $wh) = array($center, $bottom); break; // 중앙하단
				case 'rt': list($ww, $wh) = array($right, $top); break; // 우측상단
				case 'rm': list($ww, $wh) = array($right, $middle); break; // 우측중앙
				case 'rb': list($ww, $wh) = array($right, $bottom); break; // 우측하단
			}

			imagecopymerge($canvas_img, $overlay_img, $ww, $wh, 0, 0, $overlay_w, $overlay_h, $opacity);
			imagejpeg($canvas_img, $canvasImage, $quality);
			imagedestroy($overlay_img);
			imagedestroy($canvas_img);
			chmod($canvasImage, 0644);
		}
	}

	/**
	 * 스팸방지 이미지 confirm
	 * @additionDate: 2010.06.17
	 */
	// 보호코드 이미지 반환
	function print_confirm_image($dimensions=array(100, 50), $attribute='', $mobile=false) { // $mobile - 2012.04.05 added
		global $base_url, $m_url;
		list($width, $height) = $dimensions;
		if($mobile==true) { // 모바일웹용
			$image = sprintf('<img src="%sconfirm/index.php" width="%d" height="%d"%s align="absmiddle" />', $m_url, $width, $height, $attribute);
		}
		else { // 일반웹용
			$image = sprintf('<img src="%sLibs/_confirm/index.php" width="%d" height="%d"%s align="absmiddle" />', $base_url, $width, $height, $attribute);
		}
		return $image;
	}
	// 보호코드 입력필드 반환
	function print_confirm_field($field_name, $attribute='') {
		return sprintf('<input type="text" id="%s" name="%s"%s maxlength="6" />', $field_name, $field_name, $attribute);
	}
	// 보호코드 체크
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

	// 위지윅 첨부 이미지 처리 - 2011.08.23 added
	function trans_wysiwyg($content='', $keep_domain=false) { // $content : stripslashes() 한 값
		global $wysiwyg_dir, $wysiwyg_url;
		$content = stripslashes($content);
		preg_match_all('/<img\s+.*?src="([^"]+)"[^>]*>/is', $content, $imgs);
		foreach($imgs[1] as $key=>$img) {
			// 이미지 파일 PEG 폴더로 이동
			if(strpos($img, $wysiwyg_url.'PEG_temp/')!==false) {
				$_name = basename($img);
				$tmp_file = $wysiwyg_dir.'PEG_temp/'.$_name;
				$save_file = $wysiwyg_dir.'PEG/'.$_name;
				if(is_file($tmp_file)) rename($tmp_file, $save_file);
			}
			// 이미지에 걸린 PEG_temp 를 PEG 로 변경 및 절대경로 제거
			$_info = parse_url($img); // scheme : http  or  https
			$_info['scheme'] = empty($_info['scheme']) ? '' : $_info['scheme']."://";
			$_img = str_replace($img, str_replace('/PEG_temp/', '/PEG/', $img), $imgs[1][$key]);
			if(!$keep_domain) $_img = str_replace($img, str_replace($_info['scheme'].$_SERVER['HTTP_HOST'], '', $_img), $imgs[1][$key]);
			else $_img = str_replace($img, $_img, $imgs[1][$key]);
			if(strpos(strtolower($_img), 'border')===false) $_img = preg_replace('/ src/i', ' border="0" src', $_img); // border='0' 추가
			$content = str_replace($imgs[1][$key], $_img, $content);
		}
		return $content;
	}

	// 문자를 이미지로 대체 - 2012.03.02 added
	function str2img($str, $entry) {
		$img = '';
		for($i=0; $i<strlen($str); $i++) $img .= $entry[$str[$i]];
		return $img;
	}

	// 본문 이미지 사이즈 조절 - 2012.03.11 moved in
	function prefix_contents($content='', $prefix_size=array(240, 181)) {
		// 이미지 테이블 패치
		preg_match_all('/(<table\s+.*?width=[\'"]?+([0-9%]{1,})[\'"]?+[^>]*)>/is', $content, $tables);
		foreach($tables[2] as $key=>$val) {
			$ori_width = "width=".$val;
			$new_width = str_replace($ori_width,"width='100%' style='table_layout:fixed;'",$tables[0][$key]);
			$content = str_replace($tables[0][$key],$new_width,$content);
		}
		// 동영상 사이즈 패치
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
		// 이미지 사이즈 패치
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