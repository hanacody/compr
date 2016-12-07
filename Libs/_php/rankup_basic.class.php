<?php
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// GET, POST, COOKIE 값 stripslashes 처리 - MAGIC QUOTES 는 PHP 5.2.x 까지만 허용(5.3.0~:배제, 6.0.x~:제거)
if((function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) || (ini_get('magic_quotes_sybase') && (strtolower(ini_get('magic_quotes_sybase'))!="off"))) {
	$in = array(&$_GET, &$_POST, &$_COOKIE);
	while(list($k,$v) = each($in)) {
		foreach($v as $key => $val) {
			if (!is_array($val)) { $in[$k][$key] = stripslashes($val); continue; }
			$in[] =& $in[$k][$key];
		}
	}
	unset($in);
}

/**
 * 솔루션 기본클래스
 */
class rankup_basic {
	// 솔루션이 설치된 베이스 url을 리턴
	function base_url() {
		$base_url = '/'; // 솔루션이 설치된 절대 경로 :: '/solution/'  처럼 설정 :: ☜ 이부분만 수정하면 됨 ##
		if($base_url=='/') { // 임시계정인지 체크하는 부분
			preg_match("/^(\/~[a-zA-Z0-9]{1,}\/)/", $_SERVER["SCRIPT_NAME"], $location_infos);
			if(count($location_infos)) $base_url = array_pop($location_infos);
			unset($location_infos);
		}
		return $base_url;
	}
	// 솔루션이 데모버젼인지, 실제 버젼인지 구별
	function is_demo() {
		$demo = true; // 데모 버젼이 맞음
		return $demo;
	}
	// 솔루션 language 설정
	function base_language() {
		$base_language = 'kor'; // 한국어 :: 'Libs/_language/kor' 처럼 해당 폴더가 'Libs/_language' 에 존재해야 함
		return $base_language;
	}
	// 솔루션 file encoding 설정
	function base_encoding() {
		return 'ANSI'; // 'ANSI'  or 'UTF-8' 로설정
	}
	// 기본 character set 설정
	function default_charset() {
		return 'euc-kr';
	}
	// Libs/_php 파일의 경로 리턴
	function base_dir_php() {
		return dirname(realpath(__FILE__)).'/';
	}
	// Libs/_js파일의 경로 리턴
	function base_dir_js() {
		$path = dirname(realpath(__FILE__));
		return substr($path,0,strrpos($path,'/')+1).'_js/';
	}
	// Libs/_language 파일의 경로 리턴
	function base_dir_language() {
		$path = dirname(realpath(__FILE__));
		return substr($path,0,strrpos($path,'/')+1).'_language/'.rankup_basic::base_language().'/';
	}
	// Libs디렉토리의 경로 리턴
	function base_dir_libs() {
		$path = dirname(realpath(__FILE__));
		return substr($path,0,strrpos($path,'/')+1);
	}
	// document루트를 리턴
	function base_dir() {
		$path = dirname(realpath(__FILE__));
		return substr($path,0,strpos($path,'Libs'));
	}
	// 필요한 기본 클래스 파일들을 리턴 - 순서에 유의
	function include_classes() {
		$base_dir = rankup_basic::base_dir();
		$base_dir_php = rankup_basic::base_dir_php();
		include_once $base_dir_php.'rankup_connection.class.php';
		include_once $base_dir_php.'rankup_db.class.php';
		include_once $base_dir_php.'rankup_util.class.php';
		include_once $base_dir_php.'rankup_formalize.class.php';
		include_once $base_dir_php.'rankup_member.class.php';
		include_once $base_dir_php.'rankup_admin.class.php';
		include_once $base_dir_php.'rankup_siteconfig.class.php';
		include_once $base_dir_php.'rankup_control.class.php';
		include_once $base_dir_php.'rankup_https_change.class.php';
		include_once $base_dir_php.'rankup_banner.class.php';
	}
	// 자바 스크립트 클래스 파일 인클루드
	function include_js_class() {
		$base_url = rankup_basic::base_url();
		$base_language = rankup_basic::base_language();
		if($_SERVER['HTTPS']=='on') echo "\n<script type='text/javascript'>var domain = \"https://\"+document.domain+\":$_SERVER[SERVER_PORT]$base_url\"</script>\n";
		else echo "\n<script type='text/javascript'>var domain = \"http://\"+document.domain+\"$base_url\"</script>\n";
		echo "<script type='text/javascript' src='{$base_url}Libs/_js/prototype.js'></script>\n";
		echo "<script type='text/javascript' src='{$base_url}Libs/_js/form.js'></script>\n";
		echo "<script type='text/javascript' src='{$base_url}Libs/_js/common.js'></script>\n";
	}
	// 세션 스타트
	function session_start() {
		global $base_dir;
		session_save_path($base_dir.'Libs/_sessions');
		session_start();
		#header("Content-type: text/html; charset=euc-kr");
		// 기본 문자셋 .htaccess 에 추가할 것
		// php_value default_charset euc-kr
	}
}

// escape string in query
function q() {
	$args = func_get_args();
	if(func_num_args()===1) return $args[0];
	return vsprintf(array_shift($args), array_map('mysql_real_escape_string',$args));
}

#############################################################################
## PHP5 버전 버전용 함수 재정의 - 웹호스팅의 PHP 버전에 따라 지원되지 않는 함수들을 아래에 정의하세요.
#############################################################################
// 문자열 인코딩 변환
if(!function_exists('iconv')) {
	function iconv($from, $to, $text) {
		$text = str_replace('"', "&quot;", $text);
		$cmd = "echo \"$text\" | iconv -f $from -t $to";
		$output = `$cmd`;
		return str_replace("&quot;", '"', $output);
	}
}

// 대소문자 구별없이 해당 문자열 치환
if(!function_exists('str_ireplace')) {
	function make_pattern(&$pat, $key) {
		$pat = '/'.preg_quote($pat, '/').'/i';
	}
	function str_ireplace($search, $replace, $subject) {
		if(is_array($search)) array_walk($search, 'make_pattern');
		else $search = '/'.preg_quote($search, '/').'/i';
		return preg_replace($search, $replace, $subject);
	}
}

// 배열값을 URL 형태(&$key=$val)로 리턴
if(!function_exists('http_build_query')) {
	function http_build_query($data, $prefix=null, $sep='', $key='') {
		$ret = array();
		foreach((array)$data as $k => $v) {
			$k = urlencode($k);
			if(is_int($k) && $prefix != null) $k = $prefix.$k;
			if(!empty($key)) $k = $key."[".$k."]";
			if(is_array($v) || is_object($v)) array_push($ret,http_build_query($v,"",$sep,$k));
			else array_push($ret,$k."=".urlencode($v));
		}
		if(empty($sep)) $sep = ini_get("arg_separator.output");
		return implode($sep, $ret);
	}
}

// 파일 mime-type 리턴
if(!function_exists('mime_content_type')) {
	function mime_content_type($file) {
		return trim(exec("file -bi ".escapeshellarg($file)));
	}
}

// htmlspecialchars_decode 정의 - PHP 5.1 이상
if(!function_exists('htmlspecialchars_decode')) {
	function htmlspecialchars_decode($str, $options='') {
		$trans = get_html_translation_table(HTML_SPECIALCHARS, $options);
		$decode = array();
		foreach($trans as $char=>$entity) $decode[$entity] = $char;
		$str = strtr($str, $decode);
		return $str;
	}
}

// array_combine - PHP 5이상
if(!function_exists('array_combine')) {
	function array_combine($arr1, $arr2) {
		$out = array();
		foreach($arr1 as $key1 => $value1) {
			$out[$value1] = $arr2[$key1];
		}
		return $out;
	}
}

// json encode - PHP 5.2이상
if(!function_exists('json_encode')) {
	function json_encode($data) {
		if(is_array($data) || is_object($data)) {
			$islist = is_array($data) && ( empty($data) || array_keys($data) === range(0,count($data)-1) );
			if($islist) {
				$json = '[' . implode(',', array_map('json_encode', $data) ) . ']';
			}
			else {
				$items = Array();
				foreach($data as $key => $value) {
					$items[] = json_encode("$key") . ':' . json_encode($value);
				}
				$json = '{' . implode(',', $items) . '}';
			}
		}
		else if(is_string($data)) {
			# Escape non-printable or Non-ASCII characters.
			# I also put the \\ character first, as suggested in comments on the 'addclashes' page.
			$string = '"' . addcslashes($data, "\\\"\n\r\t/" . chr(8) . chr(12)) . '"';
			$json = '';
			$len = strlen($string);
			# Convert UTF-8 to Hexadecimal Codepoints.
			for($i = 0; $i < $len; $i++) {
				$char = $string[$i];
				$c1 = ord($char);
				# Single byte;
				if($c1 <128) {
					$json .= ($c1 > 31) ? $char : sprintf("\\u%04x", $c1);
					continue;
				}
				# Double byte
				$c2 = ord($string[++$i]);
				if(($c1 & 32) === 0) {
					$json .= sprintf("\\u%04x", ($c1 - 192) * 64 + $c2 - 128);
					continue;
				}
				# Triple
				$c3 = ord($string[++$i]);
				if(($c1 & 16) === 0) {
					$json .= sprintf("\\u%04x", (($c1 - 224) <<12) + (($c2 - 128) << 6) + ($c3 - 128));
					continue;
				}
				# Quadruple
				$c4 = ord($string[++$i]);
				if(($c1 & 8 ) === 0) {
					$u = (($c1 & 15) << 2) + (($c2>>4) & 3) - 1;
					$w1 = (54<<10) + ($u<<6) + (($c2 & 15) << 2) + (($c3>>4) & 3);
					$w2 = (55<<10) + (($c3 & 15)<<6) + ($c4-128);
					$json .= sprintf("\\u%04x\\u%04x", $w1, $w2);
				}
			}
		}
		else {
			# int, floats, bools, null
			$json = strtolower(var_export( $data, true ));
		}
		return $json;
	}
}
// json decode - PHP 5.2이상
if(!function_exists('json_decode')) {
	function json_decode($json) {
		$comment = false;
		$out = '$x=';
		for($i=0; $i<strlen($json); $i++) {
			if(!$comment) {
				if(($json[$i] == '{') || ($json[$i] == '[')) $out .= ' array(';
				else if(($json[$i] == '}') || ($json[$i] == ']')) $out .= ')';
				else if($json[$i] == ':') $out .= '=>';
				else $out .= $json[$i];
			}
			else $out .= $json[$i];
			if($json[$i]=='"' && $json[($i-1)]!="\\") $comment = !$comment;
		}
		eval($out.';');
		return $x;
	}
}

// parameters
function params($params, $que=true) {
	return rankup_util::parameters($params, $que);
}

// formalize
function fetch_contents($datas, $bind='', $func='') {
	return rankup_formalize::fetch_contents($datas, $bind, $func);
}

// formalize
function fetch_skin($datas, $skin='') {
	return rankup_formalize::fetch_skin($datas, $skin);
}

// print scripts
function scripts($scripts) {
	rankup_util::print_script_header($scripts);
}

// print xml
function xmls($nodes) {
	rankup_util::print_xml_header($nodes);
}

###################################################################################################
$base_url = rankup_basic::base_url(); // 솔루션이 설치된 경로 : 이미지, 링크를 통해서 이동하는 경우에 사용
$base_dir = rankup_basic::base_dir(); // 절대 경로로 추출 : include할때 사용
$base_encoding = rankup_basic::base_encoding(); // 파일 인코딩 설정 : Ajax 구동시 리턴 값 처리와 관련

include_once rankup_basic::base_dir_language()."language.pack.php"; // 언어팩 로드($_STRINGSET 으로 정의)
rankup_basic::session_start(); // session start
rankup_basic::include_classes(); // 기본 클래스들을 인클루드

#############################################################################
DEFINE(SKIN, 'design/skin/'); // 스킨폴더
DEFINE(LOGINPAGE, $base_url.'rankup_module/rankup_member/login.html'); // 로그인 페이지
if(is_file($base_dir.SKIN.'skin.init.php')) include_once $base_dir.SKIN.'skin.init.php'; // 스킨설정 로드 $_skin_init
$_SESSION['one'] = $_SESSION['two'] = 0; // 활성메뉴 세션 초기화

#############################################################################
// 기본적으로 밑의 3개의 클래스는 자주 사용되므로 여기에서 생성한다.
$rankup_member = new rankup_member;			// 멤버 제어
$rankup_admin = new rankup_admin;				// 관리자 제어
$rankup_control = new rankup_control;			// 제어 클래스

$wysiwyg_dir = $base_dir.'wysiwyg/';	// 위지윅 경로
$wysiwyg_url = $base_url.'wysiwyg/';	// 위지윅 경로

#############################################################################
## 클로즈 베타 테스트 코드
#if(!in_array($_SERVER['REMOTE_ADDR'],array('127.0.0.1'))) $rankup_control->popup_msg_js('', 'about:blank');

#############################################################################

#############################################################################
$config_info = $rankup_control->get_config_info(); // 기본 사이트환경 로드
$config_info['domain'] = ($_SERVER['HTTPS']=="on" ? "https://" : "http://").$_SERVER['HTTP_HOST'].$base_url;
$config_info['site_width'] = $_skin_init['content_width']; // 콘텐츠 가로크기
#############################################################################
// 모바일페이지
if(@include($base_dir.'m/class/rankup_mobile.class.php')) {
	$mobile = new rankup_mobile;
	list($m_dir, $m_url, $m_domain, $pc_domain) = array($mobile->m_dir, $mobile->m_url, $mobile->m_domain, $mobile->pc_domain);
}

// 회원제 사용시
if($config_info['membership_use']=='yes') {
	// 회원 정보 로드
	if($rankup_member->is_member()) $member_info = $rankup_member->get_member_often();
}
else {
	// 로그인 세션 제거
	$rankup_member->delete_member_session();
}
#############################################################################

?>
