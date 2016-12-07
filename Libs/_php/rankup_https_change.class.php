<?php
class rankup_https_change {
	function set_value() {
		$base_url = class_exists("rankup_basic") ? rankup_basic::base_url() : "/";
		$_set['ssl_use']	= false;
		$_set['port']		= 500;
		$_set['https']		= ($_set['ssl_use']==true) ? "https://".$_SERVER['HTTP_HOST'].":".$_set['port'] : "http://".$_SERVER['HTTP_HOST'];
		$_set['http']		= str_replace(":$_set[port]", "", "http://".$_SERVER['HTTP_HOST']);
		$_set['base_url']	= $base_url;
		return $_set;
	}
	/*#########################################
	예)
	$code	: 어떤 방식으로 이동하나.
	$type		: http	 =>(http://로 사용), https	 =>(https://~~~~:포트 로 사용)

	referer	: rankup_https_change::https_change("referer", "https");				:뒤로 갈때
	self		: rankup_https_change::https_change("self", "https");					:자신페이지로 갈때

	[계정의 폴더속에 각각 솔루션이 설치됬을 경우에는 폴더명을 쓰지말고 경로를 입력해야함]
	default	: rankup_https_change::https_change("main/index.html", "https");	:기타 페이지설정 - 랭크업 솔루션에서 계정루트경로부터 정보입력.
	#########################################*/
	function https_change($code, $type="http") {
		$_set = rankup_https_change::set_value();
		switch($code) {
			case "referer":
				$use_value	= $_SERVER['HTTP_REFERER'];
				break;
			case "self":
				$use_value	= ($type=='http') ? $_set['https'].$_SERVER['PHP_SELF'] : $_set['http'].$_SERVER['PHP_SELF'];
				break;
			case "host":
				$use_value	= ($type=='http') ? $_set['https'].$_set['base_url'] : $_set['http'].$_set['base_url'];
				break;
			default:
				$move_url['https'] = (stristr($code, 'http://')) ? $code : $_set['https'].$_set['base_url'].$code ;
				$move_url['http'] = (stristr($code, 'https://')) ? $code : $_set['http'].$_set['base_url'].$code ;
				$use_value	= ($type=='http') ? $move_url['https'] : $move_url['http'];
				break;
		}
		$return = ($type=="http") ? str_replace($_set['https'], $_set['http'], $use_value) : str_replace($_set['http'], $_set['https'], $use_value);
		return $return;
	}
}
?>