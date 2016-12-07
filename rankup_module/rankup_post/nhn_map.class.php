<?php
/**
 * NHN MAP 클래스
 *@author: kurokisi
 *@authDate: 2011.09.21
 */
class nhn_map {

	function nhn_map() {
		//@Do nothing...
	}

	function file_get_contents($url) {
		$data_string = '';
		$url_infos = parse_url($url);
		if(!isset($url_infos['port'])) $url_infos['port'] = 80;
		if(!$referer) $referer = $_SERVER['SCRIPT_URI'];
		if(is_array($_POST)) {
			foreach($_POST as $key=>$value) $values[] = $key.'='.urlencode($value);
			$data_string = implode('&', $values);
		}
		$request .= sprintf("GET %s?%s HTTP/1.1\n", $url_infos['path'], $url_infos['query']);
		$request .= sprintf("Host: %s\n", $url_infos['host']);
		$request .= "User-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2.1) Gecko/20021204\n";
		$request .= "Accept-Language: ko-Kr, ko;q=0.50\n";
		$request .= "Accept-Charset: euc-kr, utf-8;q=0.66, *;q=0.66\n";
		$request .= "Referer: $referer\n";
		$request .= "Content-type: application/x-www-form-urlencoded\n";
		$request .= sprintf("Content-length: %d\n", strlen($data_string));
		$request .= "Connection: close\n";
		$request .= "\n";
		$request .= sprintf("%s\n", $data_string);

		$fp = fsockopen($url_infos['host'], $url_infos['port'], $errno, $errstr, 2);
		if(!$fp) die("$errstr ($errno)<br/>\n".$fp);

		fputs($fp, $request);
		socket_set_timeout($fp, 2);

		$active = false;
		$content = '';
		while(!feof($fp)) {
			$string = fgets($fp, 1024);
			$socket_status = socket_get_status($fp);
			if($socket_status['timed_out']) return false;
			if($active) {
				if(strpos($string, '<')===false) continue; // XML 구조가 아닌 경우 스킵 처리 - 2009.06.08 fixed
				$content .= $string;
			}
			else if(strpos($string, "\r\n", 0)==0) {
				$active = true;
			}
		}
		fclose($fp);
		return $content;
	}

	// 주소별 경·위도값 반환
	function get_geocode($entry) {
		global $base_dir;

		if(!$_POST['query']) return '';
		unset($_POST['mode']);

		// POST : query: '주소', key: '지도키', coord: { tm128 | latlng }, encording: { utf-8 | euc-kr }
		$url = 'http://map.naver.com/api/geocode.php?'.http_build_query($_POST);

		$datas = array();

		if(version_compare(PHP_VERSION, '5.0.0', '>') && function_exists("simplexml_load_file") && ini_get('allow_url_fopen')) {
			$xml = @simplexml_load_file($url);
			if(isset($xml->item)) {
				foreach($xml->item as $index=>$item) {
					if($_POST['qty'] && $_POST['qty']==$index) break;
					$mapx = $item->point->x;
					$mapy = $item->point->y;
					$address = trim($item->address);
					$sido = trim($item->addrdetail->sido);
					$sigugun = trim($item->addrdetail->sido->sigugun);
					$dongmyun = trim($item->addrdetail->sido->sigugun->dongmyun);
					array_push($datas, compact('mapx', 'mapy', 'address', 'sido', 'sigugun', 'dongmyun'));
				}
			}
		}
		if(!$xml || $xml===false) {
			include_once $base_dir.'Libs/_php/rankup_XMLParser.class.php';
			$xml = new XMLParser($this->file_get_contents($url));
			$xml->Parse();
			if(isset($xml->document->item)) {
				foreach($xml->document->item as $index=>$item) {
					if($_POST['qty'] && $_POST['qty']==$index) break;
					$mapx = $item->point[0]->x[0]->tagData;
					$mapy = $item->point[0]->y[0]->tagData;
					$address = $item->address[0]->tagData;
					$sido = $item->addrdetail[0]->sido[0]->tagData;
					$sigugun = $item->addrdetail[0]->sido[0]->sigugun[0]->tagData;
					$dongmyun = $item->addrdetail[0]->sido[0]->sigugun[0]->dongmyun[0]->tagData;
					array_push($datas, compact('mapx', 'mapy', 'address', 'sido', 'sigugun', 'dongmyun'));
				}
			}
		}
		return count($datas) ? fetch_contents($datas, $entry) : '';
	}
}
?>