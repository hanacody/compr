<?php
class rankup_sns extends rankup_util {

	var $setting_table = "rankup_etcconfig_setting";//SNS 설정 테이블
	var $bitly_login = null; //bitly 사용자 이름
	var $bitly_api_key = null; //구글 bit.ly API key
	var $bitly_url = "http://api.bit.ly/v3/shorten"; //구글 짧은 주소 리턴 url;
	var $sns_list_arry = array("twitter"=>"트위터", "yozm"=>"요즘", "me2day"=>"미투데이", "linknow"=>"링크나우", "facebook"=>"페이스북");
	var $site_name = null;
	var $sns_settings = array(); //SNS 환경설성값
	var $base_url = null;
	var $base_dir = null;

	//값 초기화
	function rankup_sns() {
		parent::rankup_util();
		$this->check_etc_tables();
		$this->base_url = rankup_basic::base_url();
		$this->base_dir = rankup_basic::base_dir();
		$this->sns_settings = $this->get_sns_settings();
		$this->bitly_api_key = $this->sns_settings['bitly_apikey'];
		$this->bitly_login = $this->sns_settings['bitly_name'];
	}
		//테이블이 없을경우 설정
	function check_etc_tables() {
		$check_table = $this->queryR("show tables like '$this->setting_table'");
		if($check_table === $this->setting_table) return false;
		$this->query("CREATE TABLE `rankup_etcconfig_setting` (
		`item_name` varchar(30) NOT NULL default '',
		`item_value` text NOT NULL,
		UNIQUE KEY `item_name` (`item_name`)) TYPE=MyISAM");
	}
	//SNS 환경 설정반환
	function get_sns_settings() {
		$sns_rows = $this->queryFetch("select * from $this->setting_table where item_name='sns_configs'");
		return unserialize($sns_rows['item_value']);
	}
	// SNS 환경 설정 셋팅
	function set_sns_settings($datas) {
		unset($datas['x'], $datas['y'], $datas['mode']);
		if($this->chkRes($datas)) {
			$_val['item_value'] = serialize($datas);
			$values = $this->change_query_string($_val);
			if(!isset($this->sns_settings['use_sns'])) $this->query("insert $this->setting_table set item_name='sns_configs', $values");
			else $this->query("update $this->setting_table set $values where item_name='sns_configs'");
		}
		return true;
	}

	// 정보 가져오기
	function file_post_contents($url,$headers=false) {
		$url = parse_url($url);
		if (!isset($url['port'])) {
			if ($url['scheme'] == 'http') { $url['port']=80; }
			elseif ($url['scheme'] == 'https') { $url['port']=443; }
		}
		$url['query']=isset($url['query'])?$url['query']:'';
		$url['protocol']=$url['scheme'].'://';
		$eol="\r\n";
		$headers =  "POST ".$url['protocol'].$url['host'].$url['path']." HTTP/1.0".$eol."Host: ".$url['host'].$eol. "Referer: ".$url['protocol'].$url['host'].$url['path'].$eol. "Content-Type: application/x-www-form-urlencoded".$eol. "Content-Length: ".strlen($url['query']).$eol. $eol.$url['query'];
		$fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 5);
		if($fp) {
			fputs($fp, $headers);
			$result = '';
			while(!feof($fp)) { $result .= fgets($fp, 128); }
			fclose($fp);
			//$pattern="/^.*\r\n\r\n/s";
			//$result=preg_replace($pattern,'',$result);
			$xml_start_point = strpos($result, "<?xml");
			$result = substr($result, $xml_start_point, strlen($result));
			return $result;
		}
	}
	//SNS 페이지 이동
	function sns_link_replace($datas) {
		global $rankup_control;
		global $rankup_board,$config_info;
		$request_url = $this->bitly_url."?login=" . $this->bitly_login . "&apiKey=" . $this->bitly_api_key . "&longUrl=" . urlencode($datas['link'])."&format=xml";
		if(!in_array($datas['stype'], array("yozm", "linknow"))) { //다음, 링크나우의 자체적으로 주소줄임
			if(version_compare(PHP_VERSION, '5.0.0', '>') && function_exists("simplexml_load_file") && ini_get('allow_url_fopen')) {
				$xml = simplexml_load_file($request_url);
				$short_url = $xml->data->url;
			}
			else { // PHP version 4.x 버전용
				include_once "rankup_XMLParser.class.php";
				$xml = new XMLParser($this->file_post_contents($request_url));
				$xml->Parse();
				$short_url = $xml->document->data[0]->url[0]->tagData;
			}
		}

		//게시판과 상세페이지에 들어가는 db를 다르게함 ftype 1:게시판 2:news
		if($datas['ftype']==1) $_infos = $this->queryFetch("select * from $rankup_board->board_table where no = $datas[no]");
		//if($datas['ftype']==2) $_infos = $this->queryFetch("select subject from {$rankup_news->news_content_table} where no = $datas[no]");
		$_infos['subject'] = $config_info['site_name']." - ".$_infos['subject'];
		$_infos['subject'] = str_replace("&quot;", "'", $_infos['subject']);

		switch($datas['stype']) {
			case "me2day":
				$tm_content = "\"".$_infos['subject']."\":".$short_url;
				$this->change_encoding($tm_content, "OUT");
				$link_data = "http://me2day.net/posts/new?new_post[body]=".urlencode($tm_content);
				break;
			case "twitter":
				$tm_content = $_infos['subject']." ".$short_url;
				$this->change_encoding($tm_content, "OUT");
				//$link_data = "http://twitter.com/home?status=".urlencode($tm_content); // old version - 2011.06.09 remarked
				$link_data = "http://twitter.com/intent/tweet?status=".urlencode($tm_content); // new version - 2011.06.09 added
				break;
			case "yozm":
				$tm_content = $_infos['subject'];
				$this->change_encoding($tm_content, "OUT");
				$link_data = "http://yozm.daum.net/api/popup/prePost?prefix=".urlencode($tm_content)."&link=".urlencode($short_url)."&meta=&key=&imgurl=&crossdomain=0&callback=";
				break;
			case "linknow":
				$link_data['subject']= $_infos['subject'];
				$link_data['url'] = $short_url;
				break;
			case "facebook":
				$tm_content = $_infos['subject'];
				$this->change_encoding($tm_content, "OUT");
				$link_data = "http://www.facebook.com/share.php?v=4&src=bm&u=".urlencode($short_url)."&t=".urlencode($tm_content);
				break;
		}
		return $link_data;
	}
	//SNS 링크 구성
	function sns_link_list($datas) {
		global $rankup_board;
		foreach($this->sns_list_arry as $type=>$sns_name) {
			if($datas['type']!="none"){
				if($this->sns_settings['use_sns_type'][$type] == $type) {
					$link .= "<a href='{$this->base_url}Libs/_php/sns_replace.php?ftype=1&stype=$type&id=$datas[id]&no=$datas[no]&link=".urlencode($datas['link'])."' target='_blank' style='padding-left:3px;padding-right:3px'><img src='{$rankup_board->board_url}img/sns_$type.gif' alt='$sns_name' border='0' align='absmiddle'></a>";
				}
			}
			else{
				$link .= "<a href='{$this->base_url}Libs/_php/sns_replace.php?ftype=2&work_type=$datas[work_type]&stype=$type&id=$datas[id]&no=$datas[no]&link=".urlencode($datas['link'])."' target='_blank' style='padding-left:3px;padding-right:3px'><img src='{$this->base_url}images/sns2_$type.gif' alt='$sns_name' border='0' align='absmiddle'></a>";
			}
		}
		return $link;
	}
	//SNS 관리자 구성
	function sns_use_setting($datas=null) {
		$i = 0;
		foreach($this->sns_list_arry as $type=>$sns_name) {
			if((++$i/7) == 0) $option .= "<br/>";
			if($this->chkRes($datas)) $checked = (in_array($type, $datas)) ? "checked" : "";
			$option .= "<input type='checkbox' name='use_sns_type[$type]' value='$type' id='use_sns_$type' $checked><label for='use_sns_$type'>$sns_name</label>";
		}
		return $option;
	}
}
?>