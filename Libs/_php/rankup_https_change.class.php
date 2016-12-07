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
	��)
	$code	: � ������� �̵��ϳ�.
	$type		: http	 =>(http://�� ���), https	 =>(https://~~~~:��Ʈ �� ���)

	referer	: rankup_https_change::https_change("referer", "https");				:�ڷ� ����
	self		: rankup_https_change::https_change("self", "https");					:�ڽ��������� ����

	[������ �����ӿ� ���� �ַ���� ��ġ���� ��쿡�� �������� �������� ��θ� �Է��ؾ���]
	default	: rankup_https_change::https_change("main/index.html", "https");	:��Ÿ ���������� - ��ũ�� �ַ�ǿ��� ������Ʈ��κ��� �����Է�.
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