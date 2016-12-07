<?php
##################################################################################
#  회사명   : 뉴스엔에드(http://www.rankup.co.kr)                                #
#  작성자   : 랭크업 개발팀                                                      #
#  작성일   : 2006-06-10                                                         #
#  설명     : 자주 사용하는 클래스 묶음                                          #
##################################################################################

class often_class extends db_class {

var $home_url;  //모듈화된 사용자페이지 파일들의 경로 맞추기위해 사용됨
var $home_src;  //배너 출력시 배너 경로 맞추기위해 사용됨

function often_class() {

	$this->db_class();
    $home_dir = $_SERVER['SCRIPT_FILENAME'];	//파일위치의 서버절대경로
	$home_ex = stristr($home_dir,"/RAD");
    if(empty($home_ex)) {
       $this->home_url = "../RAD/rankup_member/";
	   $this->home_src = "../RAD/PEG";
    } else {
       $this->home_url = "../rankup_member/";
	   $this->home_src = "../PEG";
    }

}

##################################################################################
# 페이징분할 스크립트
##################################################################################
//$scale  페이지당 행수
//$page_scale  페이지번호 묶음단위
function fPagset($scale,$page_scale) {

		 define("START1",(int)$_GET['start1']);  //출력시작 행
	     define("START",(int)$_GET['start']);    //출력시작 행
         define("SCALE",(int)$scale);            //페이지당 행수
         define("PAGE_SCALE",(int)$page_scale);  //페이지번호 묶음단위
         define("SELF",$_SEVER['PHP_SELF']);     //현재 페이지명

}

//$cnt 총 출력갯수
//$qs get방식의 뒤에 붙을 변수들
function inner_cur($pos) {
	return "<font color='ff6600'> <b>".$pos."</b> </font>";
}
function inner_other($start_name2,$str,$qs="",$start_name3) {
	return "<a href='".SELF."?".$qs."&$start_name3=$start_name2'>$str</a>";
}

//$num 한페이지에서 2개 페이징 할 경우 $num="two" 값을 넘기면 된다.
function fPaging($cnt,$qs="",$num="") {

	if($num=='two') {
		$start_name1 = START1;
		$start_name2 = $start1;
        $start_name3 = "start1";
    } else {
        $start_name1 = START;
		$start_name2 = $start;
		$start_name3 = "start";
    }
	$contexture=SCALE*PAGE_SCALE;
	//$a_division = array(all=> , offset=> , offsetMax=> , from=> , curSize=> );
	$a_division=array("all"=>ceil($cnt/SCALE), "offset"=>$start_name1==0? 0 : floor(($start_name1/SCALE)/PAGE_SCALE));
	$a_division[offsetMax]=floor($a_division[all]/PAGE_SCALE);
	$a_division[from]=$a_division[offset]*$contexture;
	$a_division[curSize]=($a_division[offset]==$a_division[offsetMax])? ceil(($cnt%$contexture)/SCALE) : PAGE_SCALE ;

	$pos=$a_division[offset]*PAGE_SCALE;

	if ($a_division[offset]>0) {
		$startStr=$this->inner_other(1,"<img src=$this->home_url/img/icon_first.gif>",$qs,$start_name3);
	}
	if ($a_division[offset]>0) {
		$prevStr=$this->inner_other($a_division[from]-SCALE,"<img src=$this->home_url/img/icon_back.gif>",$qs,$start_name3);
	}
	if ($a_division[offset]<$a_division[offsetMax] && SCALE*SCALE<=$cnt) {
		$nextStr=$this->inner_other($a_division[from]+$contexture,	"<img src=$this->home_url/img/icon_next.gif>",$qs,$start_name3);
	}
	if ($a_division[offset]<$a_division[offsetMax] && SCALE*SCALE<=$cnt) {
		$lastStr=$this->inner_other($a_division[all]*SCALE - SCALE,	"<img src=$this->home_url/img/icon_last.gif>",$qs,$start_name3);
	}
	for ($i=0;$i<$a_division[curSize];$i++) {
		$start_name2=$i*SCALE+$a_division[from];
		++$pos;
		$str.=($start_name2==$start_name1)? $this->inner_cur($pos) : $this->inner_other($start_name2,"&nbsp;".$pos."&nbsp;",$qs,$start_name3);
	}
	$pageTotal=ceil($cnt/SCALE);
	return  $startStr.$prevStr.$str.$nextStr.$lastStr;
}



##################################################################################
# 페이징 시킬 get방식 값들
##################################################################################

function paging($arrays) {

	foreach($arrays as $key=>$value)
		$values	.= ($value)?urlencode("$key=$value&"):"";


	return urldecode($values);
}


##################################################################################
# get방식으로 이용되다 post로 넘어올때 배열로 변환하여 변수이용하기
##################################################################################

function paging_change($val) {

	$val_arr	= explode("&",$val);

	foreach($val_arr as $key=>$value) {

		$val_arr2	= explode("=",$value);
		$vals[$val_arr2[0]]	= $val_arr2[1];
	}

	return $vals;
}


##################################################################################
# 파일 읽고 쓰기 스크립트
##################################################################################
#파일쓰기
//$content 파일에 쓸내용
//$filename 파일명
function setFile($content,$filename,$mode="w+") {
		$fp=fopen($filename,$mode);
		fwrite($fp,stripslashes($content));
		fclose($fp);
}
#파일읽기
//$filename 파일명
function getFile($filename,$mode="r") {
		if (!file_exists($filename)) setFile("",$filename);
		$fp=fopen($filename,$mode);
		$buf=fread($fp,filesize($filename));
		fclose($fp);
		return $buf;
}


##################################################################################
# 한글 글자자르기 스크립트
##################################################################################
//$arg1 대상문자열
//$arg2 자르고픈 size
function Strcut($arg1, $arg2){
	if(strlen($arg1) > $arg2){
		if($arg2){
			$arg1=substr($arg1,0,$arg2);
			for($i=strlen($arg1)-1;$i>=0;$i--){
				if(ord($arg1[$i]) > 127) $s++;
				else break;
			}
			if($s%2) $arg1 = substr($arg1,0,strlen($arg1)-1);
			$arg1.='...';
		}
	}
	return $arg1;
}


##################################################################################
# Session 발급 및 체크
##################################################################################
#세션발급
//$table 테이블명
//$where 조건절
//$field 필드명(id, groupVal명 순으로 넣을것)
//$group 유저(User)인지 관리자(Admin)인지 구분값
function setSession($table,$where="",$field="",$group="User") {

		$q="Select $field from $table $where";
        $re=$this->setResult($q);
		$qrow = mysql_fetch_array($re['result']);
		if($re['cnt'] <= 0)	{
			$this->f_page("아이디와 비밀번호를 정확히 입력하여 주십시요");
			exit;
		}
		$va = strstr($field,",");
		if(is_string($va) && !$va) {
           $val = array($field);
        } else {
           $val = explode(",",$field);
		}
        if($group=='User') {
		$_SESSION['niceId'] = $qrow["$val[0]"];   //유저 세션값
	    $_SESSION['groupVal'] = $qrow["$val[1]"]; //유저 그룹값
		} else if($group=='Admin') {
 		$_SESSION['RSAI'] = $qrow["$val[0]"];     //관리자 세션값
	  //$_SESSION['RSAG'] = $qrow["$val[1]"];     //관리자 그룹값
		}
}
#관리자 세션체크
function getSession() {

		if(!$_SESSION['admin_session_id'] || $_SESSION['admin_session_val'] != 'rankup_administrator')	{
			$this->t_page("관리자만 접근이 허용된 페이지 입니다.","../../RAD/index.html");
			exit;
		}

}
#사용자 세션체크
function getuser_Session($msg="회원전용페이지입니다.", $f_url="../member/login.html") {

		if(!isset($_SESSION['niceId']) || empty($_SESSION['niceId'])) {
			$this->t_page($msg,$f_url);
			exit;
		}

}
#그룹세션값으로 페이지 접근 제어하기
#$val 그룹세션값   $msg 메세지   $field 결제값 필드명  $table 결제 테이블명
function groupSession($val,$msg,$field='',$table='') {
   if(empty($field)) {
        if(strcmp($_SESSION['groupVal'],$val)) {
            $this->f_page($msg);
	        exit;
        }
   } else {
	    $to_day = date("Y-m-d");
        $q = $this->sel($table,"where id='$_SESSION[niceId]' order by $field desc limit 1",$field);
		$expiry = @mysql_result($q['result'],0,0);
		if($expiry < $to_day) {
            $this->f_page($msg);
	        exit;
		}
   }
}
#세션삭제
#$opener 값에 opener 값을 넘기면 부모창 세션 끊는다.
function delSession($opener="") {
		$_SESSION=array();
        session_destroy();
  if($opener=='opener') {
        echo"
		     <script>
				 opener.location.href='../index.html';
			 </script>
			";
  } else {
        echo"
		     <script>
				 location.href='../index.html';
			 </script>
			";
  }
}


##################################################################################
# 이미지 업로드 관련 스크립트
##################################################################################
//$file  업로드하는 파일(이미지)명
//$upload_dir  업로드 지정폴더
//$first_name 여러장일때 앞네임값 넣기
//$sumfile 썸네일구현여부(썸네일 : yeszip, 일반업로드 : nozip)
//$sFactor 썸네일 구현할 가로,세로사이즈
//$file_types_array 업로드 가능 확장자명
//$max_file_size  업로드 최대사이즈
function uploadFiles(
	                     $file,
	                     $upload_dir="",
	                     $first_name="R_",
	                     $sumfile="nozip",
	                     $sFactor="",
						 $file_types_array=array("JPG","JPEG","GIF","PNG"),
						 $max_file_size=2097152
					 )
{

  if(!is_numeric($max_file_size)) { $max_file_size = 2097152; } //숫자로 넘어왔는지 체크(최대2Mbyte로 설정)

     if($_FILES[$file]["name"]!="")
     {

		 $origfilename = $_FILES[$file]["name"];   //오리지널이름(print:랭크업.gif)
         $filename = explode(".", $_FILES[$file]["name"]);
         $filenameext = $filename[count($filename)-1];    //확장자뽑기(print:gif)
         unset($filename[count($filename)-1]);            //확장자파괴(print:랭크업)
         $filename = implode(".", $filename);             //제목출력(print:랭크업)
         $filename = $first_name.date("Ymdhis").".".$filenameext;     //파일명생성(print:R_20060501111523.gif)
         $file_ext_allow = FALSE;

         for($x=0;$x<count($file_types_array);$x++){      //확장자 검색
           if(strtolower($filenameext)==strtolower($file_types_array[$x]))
           {
             $file_ext_allow = TRUE;
           }
         }

         if($file_ext_allow) { //지정한 확장자가 TRUE일때

           if($_FILES[$file]["size"] < $max_file_size) { //max 사이즈를 넘지 않았을때

			 //정상적으로 업로드실행
             if(move_uploaded_file($_FILES[$file]["tmp_name"], $upload_dir."/".$filename)) {

				//실행파일이 업로드되는지 체크하기
			    $sz = getimagesize($upload_dir."/".$filename);
//print_r($sz);
//Array ([0]=> 343 [1]=> 270 [2]=> 1 [3]=> width="343" height="270" [bits]=> 8 [channels]=> 3 [mime]=>image/gif)
//              $szexplode = explode("/",$sz['mime']);
//				$ftypechk = array_search($szexplode[1], $file_types_array);
//				if(isset($ftypechk) && !empty($ftypechk)) { //jpg,gif 파일만 업로드가능
                if($sz[2]==1 || $sz[2]==2 || $sz[2]==3) {  //1:gif, 2:jpg, 3.png  버전이 낮으므로 이것을 사용

                  if($sumfile=='yeszip') {  //썸네일 사용할경우

                       $filePath = $upload_dir."/".$filename;
				       $img = $this->imgThumbo($filePath, $filename, $sFactor, $upload_dir);
					   $filename = $img[1]; //리턴받은 파일명($saveAll = array($saveDir, $saveName, $imgW, $imgH))
					   return $filename;

				  } else {  //썸네일 사용안할경우
                    return $filename;
				  }

				} else {
                exec("rm -f $upload_dir/$filename"); //조작된 이미지파일 삭제
                $this->f_page('정상적인 이미지파일만 업로드 가능합니다.');
				exit;
			    }


             } else {
                $this->f_page('업로드를 하지 못했습니다. 다시 시도해주세요.');
				exit;
			 }

           } else {
                $this->f_page('지정한 최대사이즈를 넘었습니다. 용량을 줄여서 다시 올려주세요.');
				exit;
		   }

		 } else {
                $this->f_page('jpg, gif 파일만 업로드 가능합니다.');
				exit;
		 }

     }
}


##################################################################################
# 이미지 썸네일 관련 스크립트
##################################################################################
//$filePath = $upload_dir."/".$filename; 파일저장디렉토리
//$saveName  $saveName = $hename.$imname.".".$howak; // 이미지명
//$sFactor   이미지사이즈크기(width,height)
//$saveDir   $saveDir = "$dirctory"; // 저장할 경로
function imgThumbo($filePath, $saveName, $sFactor, $saveDir = "./", $destroy="1"){

        $sz = @getimagesize($filePath);
        //$sz[0] 이미지 가로사이즈
		//$sz[1] 이미지 세로사이즈
		//$sz[2] 이미지 형태(1:gif, 2:jpg, 3:png, 4:swf)
		//$sz[3] 이미지 태그앞에 쓸수있는 문자열

		if($sFactor)	{	//이미지 사이즈를 지정한 경우
			if($sz[0]  > $sFactor || $sz[1] > $sFactor){  //지정한 450사이즈보다 클때

				if($sz[0]>$sz[1]) {  //가로사이즈가 세로사이즈보다 클때
					$per=$sFactor/$sz[0];
				} else {
					$per=$sFactor/$sz[1];
				}
				$imgW=ceil($sz[0]*$per);
				$imgH=ceil($sz[1]*$per);

			} else {  //지정한 450사이즈보다 적을때

				$imgW=ceil($sz[0]); //이미지 가로사이즈
				$imgH=ceil($sz[1]); //이미지 세로사이즈

			}
		} else {	//지정하지 않은 경우는 원본 사이즈로 한다.
                $imgW=ceil($sz[0]); //이미지 가로사이즈
                $imgH=ceil($sz[1]); //이미지 세로사이즈
		}

        switch ($sz[2]) {
        case 1:  //gif 일경우
                $src_img = imagecreatefromgif($filePath);  //gif형식의 이미지를 생성한다
                $dst_img = imagecreate($imgW, $imgH);      //새로운 이미지를 생성한다
                ImageCopyResized($dst_img,$src_img,0,0,0,0,$imgW,$imgH,$sz[0],$sz[1]); //타겟이미지에 원하는 사이즈의 이미지를 저장합니다
                ImageInterlace($dst_img);
                //ImageGIF($dst_img, $saveDir."/".$saveName); //실제로 이미지생성하는 부분
                break;
        case 2:  //jpg 일경우
                $src_img = imagecreatefromjpeg($filePath);
                $dst_img = imagecreatetruecolor($imgW, $imgH);
                ImageCopyResized($dst_img,$src_img,0,0,0,0,$imgW,$imgH,$sz[0],$sz[1]);
                ImageInterlace($dst_img);
                ImageJPEG($dst_img, $saveDir."/".$saveName);
                break;
        case 3:  //png 일경우
                $src_img = imagecreatefrompng($filePath);
                $dst_img = imagecreatetruecolor($imgW, $imgH);
                ImageCopyResized($dst_img,$src_img,0,0,0,0,$imgW,$imgH,$sz[0],$sz[1]);
                ImageInterlace($dst_img);
                ImagePNG($dst_img, $saveDir."/".$saveName);
                break;
        default:  //swf 일경우
                return false;
                break;
        }

        return $saveAll = array($saveDir, $saveName, $imgW, $imgH);
        if($destroy){
                ImageDestroy($dst_img); // 제거
                ImageDestroy($src_img); //
        }
}


##################################################################################
# 이미지 삭제 관련 스크립트
##################################################################################
//$filename  삭제할 파일(이미지)명 [1차배열로 넘김]
//$dir  삭제할 파일(이미지)폴더
function delFiles($filename,$dir) {
	for($i=0; $i < count($filename); $i++) {
		$del_dir = $dir."/".$filename[$i];
		exec("rm -f $del_dir");
	}
}


##################################################################################
# 배너 출력 스크립트
##################################################################################
//$view_code 배너 출력위치
function banner_view($view_code) {

	if($view_code == 'main_buttom_text') {  //카테고리형 배너일경우

		$DATA = $this->q("select * from R_source_banner_category where view='yes' order by rank asc");
		if(is_array($DATA)) {
			foreach($DATA as $ROW) {
				$cDATA = $this->q("select * from R_source_banner where category='$ROW[no]' and position='$view_code' and view='yes' order by rank asc");
				foreach($cDATA as $cROW) {
					$content = stripslashes($cROW[content]);
					$_banners .= "<td> <a href=\"$cROW[address]\" target=\"$cROW[target]\"> $content </a> </td>";
				}
				$banner_view .= "<tr><td> $ROW[content] </td>$_banners</tr>";
				unset($_banners);
			}
			$banner_view = "<table border=0 align=center width=100%>$banner_view</table>";
		}

	} else if($view_code == 'main_top_banner' || $view_code == 'main_top_right_text' || $view_code == 'main_top_left_text' || $view_code == 'sub_top_banner' ||  $view_code == 'sub_top_right_text' || $view_code == 'sub_top_left_text' || $view_code == 'login_banner' || $view_code == 'main_top_right_banner' || $view_code == 'main_top_left_banner') {

		//한개만 출력이 되며 랜덤으로 출력됨
		$banners = $this->ql("select count(no) as rows from R_source_banner where position='$view_code' and view='yes'");
		$rand = $banners[rows];
		$randvalue = ($rand == 0) ? 0 : $rand-1;

		$value = rand(0,$randvalue);
		$ROW = $this->ql("select * R_source_banner where position='$view_code' and view='yes' limit $value, 1");

		$content = stripslashes($ROW['content']);
		$banner_view	= "<table border=0 cellpadding='0' cellspacing='0' >";
		$banner_view .= "<tr><td>";
		$banner_view .= ($ROW['type']=='image') ? "<a href=\"$ROW[address]\" target=\"$ROW[target]\"><img src=\"$this->home_src/$content\" border=0></a>" : $content;
		$banner_view .= "</td></tr>";
		$banner_view .= "</table>";

  } else if($view_code == 'logo') {

		//한개만 출력
		$ROW = $this->ql("select * from R_source_banner where position='$view_code' and view='yes' limit 1");

		$content = stripslashes($ROW['content']);
		$banner_view	= "<table border=0 cellpadding='0' cellspacing='0' >";
		$banner_view .= "<tr><td>";
		$banner_view .= ($ROW['type']=='image') ? "<a href=\"$ROW[address]\" target=\"$ROW[target]\"><img src=\"$this->home_src/$content\" border=0></a>" : $content;
		$banner_view .= "</td></tr>";
		$banner_view .= "</table>";

	} else {

		//출력선택된것은 밑으로 계속 출력됨
		$DATA = $this->q("select * from R_source_banner where position='$view_code' and view='yes' order by rank asc");
		$banner_view	= "<table border=0 cellpadding='0' cellspacing='0' >";
		if(is_array($DATA)) {
			foreach($DATA as $ROW) {
				$content = stripslashes($ROW['content']);
				$banner_view	.= "<tr><td height='5'></td></tr><tr><td>";
				$banner_view .= ($ROW['type']=='image') ? "<a href=\"$ROW[address]\" target=\"$ROW[target]\"><img src=\"$this->home_src/$content\" border=0></a>" : $content;
				$banner_view	.= "</td></tr>";
			}
			$banner_view .= "</table>";
		}
	}
	return $banner_view;
}


##################################################################################
# 순위조절 관련 스크립트
##################################################################################
//$table 테이블명
//$field 필드명(주키필드,랭킹필드) 형식으로 넘겨줄것
//$mode  업/다운 모드(up, down 으로 넘길것)
//$Prino 주키값
//$rank  랭킹값
//$where 조건절
function outputranking($table,$field,$mode,$Prino,$rank,$where="")	{

$fname = explode(",", $field);
$fNo   = $fname[0];
$fRank = $fname[1];

	if($mode == 'up') {
        $min_q="select min($fRank) as rank from $table where $fNo $where";
		$min_result=$this->setResult($min_q);
		$min_row = mysql_fetch_array($min_result['result']);
		$min_rank = $min_row[$fRank];   //가장 작은 랭킹값

		if($rank > $min_rank) {

			//다음 랭킹을 갖고 있는 데이터 번호
			$q = "select $field from $table where $fRank < '$rank' $where order by $fRank desc limit 1 ";
			$result=$this->setResult($q);
			$qrow = mysql_fetch_array($result['result']);
			$beforno = $qrow[$fNo];   //이전랭킹 주키값
			$beforrank = $qrow[$fRank]; //이전랭킹값

			$upque = "update $table set $fRank = $beforrank where $fNo=$Prino $where"; //현재랭킹이 이전랭킹값으로 변경
			$upsel = $this->setResult($upque);
			$seque = "update $table set $fRank = $rank where $fNo=$beforno $where"; //이전랭킹이 현재랭킹값으로 변경
			$sesel = $this->setResult($seque);
			return $sesel;

		} else {
			$this->f_page("최상위 랭킹입니다.");
		}


	} else if($mode=='down') {

		$max_q="select max($fRank) as rank from $table where $fNo $where";
		$max_result=$this->setResult($max_q);
		$max_row = mysql_fetch_array($max_result['result']);
		$max_rank = $max_row[$fRank];   //가장 큰 랭킹값

		if($max_rank && $rank==$max_rank) {

            $this->f_page("최하위 랭킹입니다.");

		} else {

			$q = "select $field from $table where $fRank > $rank $where order by $fRank asc limit 1";
            $result=$this->setResult($q);
			$qrow = mysql_fetch_array($result['result']);
			$beforno = $qrow[$fNo];   //이후랭킹 주키값
			$beforrank = $qrow[$fRank]; //이후랭킹값

			$upque = "update $table set $fRank = $beforrank where $fNo=$Prino $where"; //현재랭킹이 이후랭킹값으로 변경
			$upsel = $this->setResult($upque);
			$seque = "update $table set $fRank = $rank where $fNo=$beforno $where"; //이후랭킹이 현재랭킹값으로 변경
			$sesel = $this->setResult($seque);
			return $sesel;

		}

	}
}

##################################################################################
# 날짜 출력 스크립트(윤년계산)
##################################################################################
//$fname  폼 네임값
//$sfield  날짜필드명 및 펑션명 으로 사용됨
function calendar($fname,$sfield="") {

$syear = date("Y");         //현재 년값
$smonth = date("m");        //현재 달값
$sday = date("d");          //현재 일값
$yearfield = $sfield."yy";  //년 필드명
$monthfield = $sfield."mm"; //달 필드명
$dayfield = $sfield."dd";   //일 필드명
$funct = $sfield."sc";      //펑션명

$Day_Arr = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
if ($syear % 400 == 0 || ( $syear % 4 == 0 && $syear % 100 != 0 )) $Day_Arr[1] += 1; //윤년 계산공식
$forday = $Day_Arr[$smonth-1];
?>
	 <script>
		function <?php echo $funct; ?>(sel,choice) {

		  var formname = document.forms["<?php echo $fname; ?>"];
		  var dfield = formname.<?php echo $dayfield; ?>;
		  var chyear   = (choice=="year")?sel.value:formname.<?php echo $yearfield; ?>.value;
		  var chmonth  = (choice=="month")?sel.value:formname.<?php echo $monthfield; ?>.value;
		  var Day_Arr = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
		  if (chyear % 400 == 0 || ( chyear % 4 == 0 && chyear % 100 != 0 )) Day_Arr[1] += 1;

		  var dday = parseInt(Day_Arr[chmonth-1]);

			  dfield.length = dday;
			  for(i=0; i < dday; i++){
		      dfield.options[i].text = i+1;
		      dfield.options[i].value = i+1;
			  }
	    }
	 </script>

     <select name=<?php echo $yearfield; ?> onChange="<?php echo $funct; ?>(this, 'year')">
     <?php for($y=1970; $y <= date("Y"); $y++) { ?>
     <option value=<?php echo $y; ?> <?php echo ($y==$_GET[$yearfield])?"selected":(($y==$syear && empty($_GET[$yearfield]))?"selected":"")?>> <?php echo $y; ?> </option>
     <?php } ?>
     </select>
     &nbsp;
     <select name=<?php echo $monthfield; ?> onChange="<?php echo $funct; ?>(this, 'month')">
     <?php for($m=1; $m < 13; $m++) { ?>
     <option value=<?php echo $m; ?> <?php echo ($m==$_GET[$monthfield])?"selected":(($m==$smonth && empty($_GET[$monthfield]))?"selected":"")?>> <?php echo $m; ?> </option>
     <?php } ?>
     </select>
     &nbsp;
     <select name=<?php echo $dayfield; ?>>
     <?php for($d=1; $d <= $forday; $d++) { ?>
      <option value=<?php echo $d; ?> <?php echo ($d==$_GET[$dayfield])?"selected":(($d==$sday && empty($_GET[$dayfield]))?"selected":"")?>> <?php echo $d; ?> </option>
     <?php } ?>
     </select>

<?php
}


##################################################################################
# 팝업창 24시간 쿠키적용
##################################################################################
//$cookie 쿠키명
function popCookie($cookie) {

   if(empty($_COOKIE[$cookie])) {
	  echo"
	    <script>
	       window.open('../RAD/rankup_popup/main_popup.html?cookie=$cookie','newpopup','width=100, height=100,left=0,top=0');
	    </script>
	  ";
   }

}



##################################################################################
# 우편번호 검색 클래스
##################################################################################
//$form 폼 네임명
//$post1  우편번호 앞자리 네임명
//$post2  우편번호 뒤자리 네임명
//$address 부모창의 주소 네임명
function post($form,$post1,$post2,$address) {

echo"window.open('".$this->home_url."find_post.html?c_form=$form&c_post1=$post1&c_post2=$post2&c_add=$address','viewInfo','width=300,height=250,scrollbars=yes');";

}



##################################################################################
# 아이디 중복체크 클래스
##################################################################################
//$form 폼 네임명
//$id_name 아이디 네임명
//$table_name 회원테이블명
function id_check($form,$id_name,$table_name) {

echo"window.open('".$this->home_url."id_check.html?c_form=$form&c_id=$id_name&c_table=$table_name','viewInfo','width=300,height=250,scrollbars=yes');";

}



##################################################################################
# 주민번호 중복체크 클래스
##################################################################################
//$form 폼 네임명
//$ssn_name 주미번호 네임명
//$table_name 회원테이블명
function ssn_check($form,$ssn_name,$table_name) {

echo"window.open('".$this->home_url."ssn_check.html?c_form=$form&c_ssn=$ssn_name&c_table=$table_name','viewInfo','width=300,height=250,scrollbars=yes');";

}


##################################################################################
# 버전체크 클래스(formal, demo 구분)
##################################################################################
function r_version() {

$version_que = $this->sel("R_source_environment","","version");
$version = mysql_result($version_que['result'],0,0);

return $version;
}


##################################################################################
# 회원에게 메일보내기
##################################################################################
//$to_email 받을 이메일주소
function email_send($to_email) {

$parent_url = $_SERVER['PHP_SELF'];
$string= getenv('QUERY_STRING');
$url = $parent_url.'?'.$string;  //리턴되어서 돌아올 페이지
echo"window.open('".$this->home_url."email_form.html?to_email=$to_email&next_url=$url','viewInfo','width=550,height=400,scrollbars=yes');";

}
}
?>