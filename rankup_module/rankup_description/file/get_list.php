<?PHP
#start
require_once "../../../Libs/_php/rankup_basic.class.php";
require_once "../config.inc.php";

$DOCUMENT_ROOT = array_shift(explode("rankup_module/", $_SERVER['SCRIPT_FILENAME']));

function searchdir($path, $maxdepth = -1, $mode = "FULL", $d = 0){
	$dirlist = array ();
	if(substr($path,-1)!= '/') $path .= '/';
	if($mode != "FILES") $dirlist[] = $path;
	if($handle = opendir($path)) {
		while(false !==($file = readdir($handle))) {
			if($file != '.' && $file != '..' ) {
				$file = $path.$file;
				if(!is_dir($file)) { 
					if($mode != "DIRS") $dirlist[] = $file;
				} else if($d >=0 && ($d < $maxdepth || $maxdepth < 0)) {
					$dirlist = @array_merge($dirlist, searchdir($file . '/', $maxdepth, $mode, $d + 1));
				}
			}
		}
		closedir($handle);
	}
	if($d == 0) natcasesort($dirlist);
	return $dirlist;
}
function printDate($timestamp) {
	return date("Y-m-d",$timestamp)." <font color='#3366CC'>".date("H:i:s",$timestamp)."</font>";
}

$dir = (in_array($_POST['dir'], array("","./","undefine"))) ? $DOCUMENT_ROOT : $_POST['dir'];
$location = ($dir==$DOCUMENT_ROOT) ? "./" : str_replace($DOCUMENT_ROOT,'',$dir);
if(substr($dir, -1) != '/') $dir .= '/';

$fulls = @array_merge(searchdir($dir,1,"DIRS"),searchdir($dir,0,"FILES"));
if(is_array($fulls)) {

	$DATA = $rankup_control->queryFetchRows("select * from $table_name where location='$location'");
	if(is_array($DATA)) {
		foreach($DATA as $ROW) {
			if($ROW[kind]=='D') { // 폴더가 존재하지 않으면 삭제할 인덱스값 저장
				if(!is_dir($dir.$ROW[object])) $delRecord[] = $ROW[idx];
				else $DESCRIPTION[$ROW[kind]][$ROW[object]] = $ROW[description];
			} else { // 파일이 존재하지 않으면 삭제할 인덱스값 저장
				if(!is_file($dir.$ROW[object])) $delRecord[] = $ROW[idx];
				else $DESCRIPTION[$ROW[kind]][$ROW[object]] = $ROW[description];
			}
		}
		// 존재하지 않는 객체(파일 or 폴더)에 대한 Record 삭제 처리
		if(is_array($delRecord)) $rankup_control->query("delete from $table_name where idx in(".join(',',$delRecord).")");
	}

	foreach($fulls as $ROW) {
		$base_name = array_reverse(explode("/",$ROW));
		if(is_dir($ROW)) { // 폴더인경우
			$base_name = $base_name[1];
			$url = $dir.$base_name.'/';
			$out_dir = ($ROW==$dir) ? ($dir==$DOCUMENT_ROOT) ? ".." : "<a href='javascript:void(0)' onClick=getList('".eregi_replace("/$base_name/$",'',$dir)."/')>..</a>" : "<a href='javascript:void(0)' onClick=getList('$url')>".$base_name."</a>";
			$icon = "<img src='../img/icon_folder.gif' align=absmiddle>";
			$fsize = "";
			$ctime = @printDate(filectime($dir));
			$kind = "D";
		} else { // 파일인경우
			$base_name = $base_name[0];
			$url = $dir.$base_name.'/';
			$out_dir = str_replace($dir,'',$ROW);
			$fsize = number_format(filesize($dir.$out_dir));
			$ctime = @printDate(filectime($dir.$out_dir));
			$kind = "F";

			// 파일 아이콘
			$file_ext = strtolower(array_pop(explode('.',$ROW)));
			$icon = (is_file("../img/icon_$file_ext.gif")) ? "<img src='../img/icon_$file_ext.gif' align=absbottom>" : "<img src='../img/icon_dat.gif' align=absbottom>";

			// 이미지파일의 경우 링크 생성
			if(in_array($file_ext, array("gif","jpg","png","swf","bmp"))) {
				list($_width, $_height, $_type, $_attr) = @getimagesize($ROW);
				switch($_type) {
					case in_array($_type, array(1,2,3,6)) : // GIF, JPG, PNG, BMP
						$file = str_replace(" ","%20",str_replace($DOCUMENT_ROOT,'',$ROW)); // 파일명 공백 치환
						$out_dir = "<a href='javascript:void(0)' onClick=imageView('http://".$_SERVER[SERVER_NAME].$base_url.$file."') title='클릭-이미지보기'>".$out_dir."</a><img src='http://".$_SERVER[SERVER_NAME]."/$file' width=0 height=0 style='display:none'>";
						$preloadImg[] = "http://".$_SERVER[SERVER_NAME]."/$file";
						break;
					case 4: default: // SWF or etc.
						break;
				}
			}
		}
		if($ROW==$dir) { // 상위폴더( .. )
			$modify_btn = "";
			$desc = "";
		} else {
			$aLink = ($is_Demo) ? "alert('죄송합니다. 데모버젼에서는 편집할 수 없습니다.          ')" : "viewDescFrmDiv('$base_name','$kind',arguments[0])";
			$modify_btn = "<a href='javascript:void(0)' onClick=\"$aLink\"><img src='../img/icon_modify.gif' border=0 align=absmiddle alt='편집'></a>";
			$desc = ($ROW==$dir) ? "" : "<a href='javascript:void(0)' onClick=\"$aLink\"><div id='".$base_name."' style='overflow-y:hidden'>".$DESCRIPTION[$kind][$base_name]."</div></a>";
		}
		$dirList .= "<tr height=20$bgcolor><td><table width=100% cellpadding=0 cellspacing=0><tr><td width=16 align=center>$icon</td><td width=4></td><td>$out_dir</td></tr></table></td><td style='color:#BCBCBC'>$fsize</td><td>$ctime</td><td><table width=100% cellpadding=0 cellspacing=0><tr><td width=24>$modify_btn</td><td>$desc</td></tr></table></td></tr>";
		$bgcolor = empty($bgcolor) ? " bgcolor=#FAFAFA" : "";
	}
}
$preloadImg = (is_array($preloadImg)) ? join("\",\"",$preloadImg) : "";
$dirList = "<table width=100% cellpadding=3 cellspacing=1 bgcolor='#FFFFFF'><col width=250><col width=80 align=right><col width=120 align=center><col><tbody bgcolor=white><tr><td colspan=5 style='color:#0033CC'><img src='../img/icon_folder_open.gif' align=absmiddle> ".$dir."</td></tr><tr height=1 bgcolor=#ACACAC><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td></tr><tr align=center bgcolor=#EDEDED><td class=d8>name</td><td class=d8 align=center>size</td><td class=d8>date</td><td class=d8>comment</td></tr>".$dirList."<tr height=1 bgcolor=#DEDEDE><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td><td style='padding:0px'></td></tr></tbody></table><input type='hidden' name='PRELOADIMG' value='\"".$preloadImg."\"'>";

echo ($is_UNICODE) ? iconv("CP949","UTF-8",$dirList) : $dirList;
#end
?>
