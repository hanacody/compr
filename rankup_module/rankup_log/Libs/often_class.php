<?php
##################################################################################
#  ȸ���   : ����������(http://www.rankup.co.kr)                                #
#  �ۼ���   : ��ũ�� ������                                                      #
#  �ۼ���   : 2006-06-10                                                         #
#  ����     : ���� ����ϴ� Ŭ���� ����                                          #
##################################################################################

class often_class extends db_class {

var $home_url;  //���ȭ�� ����������� ���ϵ��� ��� ���߱����� ����
var $home_src;  //��� ��½� ��� ��� ���߱����� ����

function often_class() {

	$this->db_class();
    $home_dir = $_SERVER['SCRIPT_FILENAME'];	//������ġ�� ����������
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
# ����¡���� ��ũ��Ʈ
##################################################################################
//$scale  �������� ���
//$page_scale  ��������ȣ ��������
function fPagset($scale,$page_scale) {

		 define("START1",(int)$_GET['start1']);  //��½��� ��
	     define("START",(int)$_GET['start']);    //��½��� ��
         define("SCALE",(int)$scale);            //�������� ���
         define("PAGE_SCALE",(int)$page_scale);  //��������ȣ ��������
         define("SELF",$_SEVER['PHP_SELF']);     //���� ��������

}

//$cnt �� ��°���
//$qs get����� �ڿ� ���� ������
function inner_cur($pos) {
	return "<font color='ff6600'> <b>".$pos."</b> </font>";
}
function inner_other($start_name2,$str,$qs="",$start_name3) {
	return "<a href='".SELF."?".$qs."&$start_name3=$start_name2'>$str</a>";
}

//$num ������������ 2�� ����¡ �� ��� $num="two" ���� �ѱ�� �ȴ�.
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
# ����¡ ��ų get��� ����
##################################################################################

function paging($arrays) {

	foreach($arrays as $key=>$value)
		$values	.= ($value)?urlencode("$key=$value&"):"";


	return urldecode($values);
}


##################################################################################
# get������� �̿�Ǵ� post�� �Ѿ�ö� �迭�� ��ȯ�Ͽ� �����̿��ϱ�
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
# ���� �а� ���� ��ũ��Ʈ
##################################################################################
#���Ͼ���
//$content ���Ͽ� ������
//$filename ���ϸ�
function setFile($content,$filename,$mode="w+") {
		$fp=fopen($filename,$mode);
		fwrite($fp,stripslashes($content));
		fclose($fp);
}
#�����б�
//$filename ���ϸ�
function getFile($filename,$mode="r") {
		if (!file_exists($filename)) setFile("",$filename);
		$fp=fopen($filename,$mode);
		$buf=fread($fp,filesize($filename));
		fclose($fp);
		return $buf;
}


##################################################################################
# �ѱ� �����ڸ��� ��ũ��Ʈ
##################################################################################
//$arg1 ����ڿ�
//$arg2 �ڸ����� size
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
# Session �߱� �� üũ
##################################################################################
#���ǹ߱�
//$table ���̺��
//$where ������
//$field �ʵ��(id, groupVal�� ������ ������)
//$group ����(User)���� ������(Admin)���� ���а�
function setSession($table,$where="",$field="",$group="User") {

		$q="Select $field from $table $where";
        $re=$this->setResult($q);
		$qrow = mysql_fetch_array($re['result']);
		if($re['cnt'] <= 0)	{
			$this->f_page("���̵�� ��й�ȣ�� ��Ȯ�� �Է��Ͽ� �ֽʽÿ�");
			exit;
		}
		$va = strstr($field,",");
		if(is_string($va) && !$va) {
           $val = array($field);
        } else {
           $val = explode(",",$field);
		}
        if($group=='User') {
		$_SESSION['niceId'] = $qrow["$val[0]"];   //���� ���ǰ�
	    $_SESSION['groupVal'] = $qrow["$val[1]"]; //���� �׷찪
		} else if($group=='Admin') {
 		$_SESSION['RSAI'] = $qrow["$val[0]"];     //������ ���ǰ�
	  //$_SESSION['RSAG'] = $qrow["$val[1]"];     //������ �׷찪
		}
}
#������ ����üũ
function getSession() {

		if(!$_SESSION['admin_session_id'] || $_SESSION['admin_session_val'] != 'rankup_administrator')	{
			$this->t_page("�����ڸ� ������ ���� ������ �Դϴ�.","../../RAD/index.html");
			exit;
		}

}
#����� ����üũ
function getuser_Session($msg="ȸ�������������Դϴ�.", $f_url="../member/login.html") {

		if(!isset($_SESSION['niceId']) || empty($_SESSION['niceId'])) {
			$this->t_page($msg,$f_url);
			exit;
		}

}
#�׷켼�ǰ����� ������ ���� �����ϱ�
#$val �׷켼�ǰ�   $msg �޼���   $field ������ �ʵ��  $table ���� ���̺��
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
#���ǻ���
#$opener ���� opener ���� �ѱ�� �θ�â ���� ���´�.
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
# �̹��� ���ε� ���� ��ũ��Ʈ
##################################################################################
//$file  ���ε��ϴ� ����(�̹���)��
//$upload_dir  ���ε� ��������
//$first_name �������϶� �ճ��Ӱ� �ֱ�
//$sumfile ����ϱ�������(����� : yeszip, �Ϲݾ��ε� : nozip)
//$sFactor ����� ������ ����,���λ�����
//$file_types_array ���ε� ���� Ȯ���ڸ�
//$max_file_size  ���ε� �ִ������
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

  if(!is_numeric($max_file_size)) { $max_file_size = 2097152; } //���ڷ� �Ѿ�Դ��� üũ(�ִ�2Mbyte�� ����)

     if($_FILES[$file]["name"]!="")
     {

		 $origfilename = $_FILES[$file]["name"];   //���������̸�(print:��ũ��.gif)
         $filename = explode(".", $_FILES[$file]["name"]);
         $filenameext = $filename[count($filename)-1];    //Ȯ���ڻ̱�(print:gif)
         unset($filename[count($filename)-1]);            //Ȯ�����ı�(print:��ũ��)
         $filename = implode(".", $filename);             //�������(print:��ũ��)
         $filename = $first_name.date("Ymdhis").".".$filenameext;     //���ϸ����(print:R_20060501111523.gif)
         $file_ext_allow = FALSE;

         for($x=0;$x<count($file_types_array);$x++){      //Ȯ���� �˻�
           if(strtolower($filenameext)==strtolower($file_types_array[$x]))
           {
             $file_ext_allow = TRUE;
           }
         }

         if($file_ext_allow) { //������ Ȯ���ڰ� TRUE�϶�

           if($_FILES[$file]["size"] < $max_file_size) { //max ����� ���� �ʾ�����

			 //���������� ���ε����
             if(move_uploaded_file($_FILES[$file]["tmp_name"], $upload_dir."/".$filename)) {

				//���������� ���ε�Ǵ��� üũ�ϱ�
			    $sz = getimagesize($upload_dir."/".$filename);
//print_r($sz);
//Array ([0]=> 343 [1]=> 270 [2]=> 1 [3]=> width="343" height="270" [bits]=> 8 [channels]=> 3 [mime]=>image/gif)
//              $szexplode = explode("/",$sz['mime']);
//				$ftypechk = array_search($szexplode[1], $file_types_array);
//				if(isset($ftypechk) && !empty($ftypechk)) { //jpg,gif ���ϸ� ���ε尡��
                if($sz[2]==1 || $sz[2]==2 || $sz[2]==3) {  //1:gif, 2:jpg, 3.png  ������ �����Ƿ� �̰��� ���

                  if($sumfile=='yeszip') {  //����� ����Ұ��

                       $filePath = $upload_dir."/".$filename;
				       $img = $this->imgThumbo($filePath, $filename, $sFactor, $upload_dir);
					   $filename = $img[1]; //���Ϲ��� ���ϸ�($saveAll = array($saveDir, $saveName, $imgW, $imgH))
					   return $filename;

				  } else {  //����� �����Ұ��
                    return $filename;
				  }

				} else {
                exec("rm -f $upload_dir/$filename"); //���۵� �̹������� ����
                $this->f_page('�������� �̹������ϸ� ���ε� �����մϴ�.');
				exit;
			    }


             } else {
                $this->f_page('���ε带 ���� ���߽��ϴ�. �ٽ� �õ����ּ���.');
				exit;
			 }

           } else {
                $this->f_page('������ �ִ����� �Ѿ����ϴ�. �뷮�� �ٿ��� �ٽ� �÷��ּ���.');
				exit;
		   }

		 } else {
                $this->f_page('jpg, gif ���ϸ� ���ε� �����մϴ�.');
				exit;
		 }

     }
}


##################################################################################
# �̹��� ����� ���� ��ũ��Ʈ
##################################################################################
//$filePath = $upload_dir."/".$filename; ����������丮
//$saveName  $saveName = $hename.$imname.".".$howak; // �̹�����
//$sFactor   �̹���������ũ��(width,height)
//$saveDir   $saveDir = "$dirctory"; // ������ ���
function imgThumbo($filePath, $saveName, $sFactor, $saveDir = "./", $destroy="1"){

        $sz = @getimagesize($filePath);
        //$sz[0] �̹��� ���λ�����
		//$sz[1] �̹��� ���λ�����
		//$sz[2] �̹��� ����(1:gif, 2:jpg, 3:png, 4:swf)
		//$sz[3] �̹��� �±׾տ� �����ִ� ���ڿ�

		if($sFactor)	{	//�̹��� ����� ������ ���
			if($sz[0]  > $sFactor || $sz[1] > $sFactor){  //������ 450������� Ŭ��

				if($sz[0]>$sz[1]) {  //���λ���� ���λ������ Ŭ��
					$per=$sFactor/$sz[0];
				} else {
					$per=$sFactor/$sz[1];
				}
				$imgW=ceil($sz[0]*$per);
				$imgH=ceil($sz[1]*$per);

			} else {  //������ 450������� ������

				$imgW=ceil($sz[0]); //�̹��� ���λ�����
				$imgH=ceil($sz[1]); //�̹��� ���λ�����

			}
		} else {	//�������� ���� ���� ���� ������� �Ѵ�.
                $imgW=ceil($sz[0]); //�̹��� ���λ�����
                $imgH=ceil($sz[1]); //�̹��� ���λ�����
		}

        switch ($sz[2]) {
        case 1:  //gif �ϰ��
                $src_img = imagecreatefromgif($filePath);  //gif������ �̹����� �����Ѵ�
                $dst_img = imagecreate($imgW, $imgH);      //���ο� �̹����� �����Ѵ�
                ImageCopyResized($dst_img,$src_img,0,0,0,0,$imgW,$imgH,$sz[0],$sz[1]); //Ÿ���̹����� ���ϴ� �������� �̹����� �����մϴ�
                ImageInterlace($dst_img);
                //ImageGIF($dst_img, $saveDir."/".$saveName); //������ �̹��������ϴ� �κ�
                break;
        case 2:  //jpg �ϰ��
                $src_img = imagecreatefromjpeg($filePath);
                $dst_img = imagecreatetruecolor($imgW, $imgH);
                ImageCopyResized($dst_img,$src_img,0,0,0,0,$imgW,$imgH,$sz[0],$sz[1]);
                ImageInterlace($dst_img);
                ImageJPEG($dst_img, $saveDir."/".$saveName);
                break;
        case 3:  //png �ϰ��
                $src_img = imagecreatefrompng($filePath);
                $dst_img = imagecreatetruecolor($imgW, $imgH);
                ImageCopyResized($dst_img,$src_img,0,0,0,0,$imgW,$imgH,$sz[0],$sz[1]);
                ImageInterlace($dst_img);
                ImagePNG($dst_img, $saveDir."/".$saveName);
                break;
        default:  //swf �ϰ��
                return false;
                break;
        }

        return $saveAll = array($saveDir, $saveName, $imgW, $imgH);
        if($destroy){
                ImageDestroy($dst_img); // ����
                ImageDestroy($src_img); //
        }
}


##################################################################################
# �̹��� ���� ���� ��ũ��Ʈ
##################################################################################
//$filename  ������ ����(�̹���)�� [1���迭�� �ѱ�]
//$dir  ������ ����(�̹���)����
function delFiles($filename,$dir) {
	for($i=0; $i < count($filename); $i++) {
		$del_dir = $dir."/".$filename[$i];
		exec("rm -f $del_dir");
	}
}


##################################################################################
# ��� ��� ��ũ��Ʈ
##################################################################################
//$view_code ��� �����ġ
function banner_view($view_code) {

	if($view_code == 'main_buttom_text') {  //ī�װ��� ����ϰ��

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

		//�Ѱ��� ����� �Ǹ� �������� ��µ�
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

		//�Ѱ��� ���
		$ROW = $this->ql("select * from R_source_banner where position='$view_code' and view='yes' limit 1");

		$content = stripslashes($ROW['content']);
		$banner_view	= "<table border=0 cellpadding='0' cellspacing='0' >";
		$banner_view .= "<tr><td>";
		$banner_view .= ($ROW['type']=='image') ? "<a href=\"$ROW[address]\" target=\"$ROW[target]\"><img src=\"$this->home_src/$content\" border=0></a>" : $content;
		$banner_view .= "</td></tr>";
		$banner_view .= "</table>";

	} else {

		//��¼��õȰ��� ������ ��� ��µ�
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
# �������� ���� ��ũ��Ʈ
##################################################################################
//$table ���̺��
//$field �ʵ��(��Ű�ʵ�,��ŷ�ʵ�) �������� �Ѱ��ٰ�
//$mode  ��/�ٿ� ���(up, down ���� �ѱ��)
//$Prino ��Ű��
//$rank  ��ŷ��
//$where ������
function outputranking($table,$field,$mode,$Prino,$rank,$where="")	{

$fname = explode(",", $field);
$fNo   = $fname[0];
$fRank = $fname[1];

	if($mode == 'up') {
        $min_q="select min($fRank) as rank from $table where $fNo $where";
		$min_result=$this->setResult($min_q);
		$min_row = mysql_fetch_array($min_result['result']);
		$min_rank = $min_row[$fRank];   //���� ���� ��ŷ��

		if($rank > $min_rank) {

			//���� ��ŷ�� ���� �ִ� ������ ��ȣ
			$q = "select $field from $table where $fRank < '$rank' $where order by $fRank desc limit 1 ";
			$result=$this->setResult($q);
			$qrow = mysql_fetch_array($result['result']);
			$beforno = $qrow[$fNo];   //������ŷ ��Ű��
			$beforrank = $qrow[$fRank]; //������ŷ��

			$upque = "update $table set $fRank = $beforrank where $fNo=$Prino $where"; //���緩ŷ�� ������ŷ������ ����
			$upsel = $this->setResult($upque);
			$seque = "update $table set $fRank = $rank where $fNo=$beforno $where"; //������ŷ�� ���緩ŷ������ ����
			$sesel = $this->setResult($seque);
			return $sesel;

		} else {
			$this->f_page("�ֻ��� ��ŷ�Դϴ�.");
		}


	} else if($mode=='down') {

		$max_q="select max($fRank) as rank from $table where $fNo $where";
		$max_result=$this->setResult($max_q);
		$max_row = mysql_fetch_array($max_result['result']);
		$max_rank = $max_row[$fRank];   //���� ū ��ŷ��

		if($max_rank && $rank==$max_rank) {

            $this->f_page("������ ��ŷ�Դϴ�.");

		} else {

			$q = "select $field from $table where $fRank > $rank $where order by $fRank asc limit 1";
            $result=$this->setResult($q);
			$qrow = mysql_fetch_array($result['result']);
			$beforno = $qrow[$fNo];   //���ķ�ŷ ��Ű��
			$beforrank = $qrow[$fRank]; //���ķ�ŷ��

			$upque = "update $table set $fRank = $beforrank where $fNo=$Prino $where"; //���緩ŷ�� ���ķ�ŷ������ ����
			$upsel = $this->setResult($upque);
			$seque = "update $table set $fRank = $rank where $fNo=$beforno $where"; //���ķ�ŷ�� ���緩ŷ������ ����
			$sesel = $this->setResult($seque);
			return $sesel;

		}

	}
}

##################################################################################
# ��¥ ��� ��ũ��Ʈ(������)
##################################################################################
//$fname  �� ���Ӱ�
//$sfield  ��¥�ʵ�� �� ��Ǹ� ���� ����
function calendar($fname,$sfield="") {

$syear = date("Y");         //���� �Ⱚ
$smonth = date("m");        //���� �ް�
$sday = date("d");          //���� �ϰ�
$yearfield = $sfield."yy";  //�� �ʵ��
$monthfield = $sfield."mm"; //�� �ʵ��
$dayfield = $sfield."dd";   //�� �ʵ��
$funct = $sfield."sc";      //��Ǹ�

$Day_Arr = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
if ($syear % 400 == 0 || ( $syear % 4 == 0 && $syear % 100 != 0 )) $Day_Arr[1] += 1; //���� ������
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
# �˾�â 24�ð� ��Ű����
##################################################################################
//$cookie ��Ű��
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
# �����ȣ �˻� Ŭ����
##################################################################################
//$form �� ���Ӹ�
//$post1  �����ȣ ���ڸ� ���Ӹ�
//$post2  �����ȣ ���ڸ� ���Ӹ�
//$address �θ�â�� �ּ� ���Ӹ�
function post($form,$post1,$post2,$address) {

echo"window.open('".$this->home_url."find_post.html?c_form=$form&c_post1=$post1&c_post2=$post2&c_add=$address','viewInfo','width=300,height=250,scrollbars=yes');";

}



##################################################################################
# ���̵� �ߺ�üũ Ŭ����
##################################################################################
//$form �� ���Ӹ�
//$id_name ���̵� ���Ӹ�
//$table_name ȸ�����̺��
function id_check($form,$id_name,$table_name) {

echo"window.open('".$this->home_url."id_check.html?c_form=$form&c_id=$id_name&c_table=$table_name','viewInfo','width=300,height=250,scrollbars=yes');";

}



##################################################################################
# �ֹι�ȣ �ߺ�üũ Ŭ����
##################################################################################
//$form �� ���Ӹ�
//$ssn_name �ֹ̹�ȣ ���Ӹ�
//$table_name ȸ�����̺��
function ssn_check($form,$ssn_name,$table_name) {

echo"window.open('".$this->home_url."ssn_check.html?c_form=$form&c_ssn=$ssn_name&c_table=$table_name','viewInfo','width=300,height=250,scrollbars=yes');";

}


##################################################################################
# ����üũ Ŭ����(formal, demo ����)
##################################################################################
function r_version() {

$version_que = $this->sel("R_source_environment","","version");
$version = mysql_result($version_que['result'],0,0);

return $version;
}


##################################################################################
# ȸ������ ���Ϻ�����
##################################################################################
//$to_email ���� �̸����ּ�
function email_send($to_email) {

$parent_url = $_SERVER['PHP_SELF'];
$string= getenv('QUERY_STRING');
$url = $parent_url.'?'.$string;  //���ϵǾ ���ƿ� ������
echo"window.open('".$this->home_url."email_form.html?to_email=$to_email&next_url=$url','viewInfo','width=550,height=400,scrollbars=yes');";

}
}
?>