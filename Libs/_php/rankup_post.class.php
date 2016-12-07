<?php
class rankup_post extends rankup_util {
	var $table = "rankup_zipcode";
	function rankup_post(){
		parent::rankup_util();
	}
	// PHP version 4.x - file_get_contents() �Լ��� ����� �� ���� ��� - 2008.12.11 �߰�
	function file_get_contents($url, $mdate='') {
		if(empty($mdate)) $mdate = date("Y-m-d", time());
		$url_infos = parse_url($url);
		$fp = @fsockopen($url_infos['host'], ($url_infos['port']?$url_infos['port']:80), $errno, $errstr, 2);
		if(!is_resource($fp)) return false;

		if($url_infos['query']) $url_infos['path'] .= '?';
		$header = "GET $url_infos[path]$url_infos[query] HTTP/1.1\r\n";
		$header .= "Host: $url_infos[host]\r\n";
		$header .= "If-Modified-Since: $mdate\r\n\r\n";
		fputs($fp, $header);
		socket_set_timeout($fp, 4);

		$active = false;
		while(!feof($fp)) {
			$string = fgets($fp,1024);
			$socket_status = socket_get_status($fp);
			if($socket_status['timed_out']) return false;
			if($active) {
				if(eregi('<', $string)===false) continue; // XML ������ �ƴ� ��� ��ŵ ó�� - 2009.06.08 fixed
				$contents .= $string;
			}
			else {
				if(strpos($string, "\r\n", 0) == 0) $active = true;
				$_mdate = strpos($string, "Last-Modified:");
				$_location = strpos($string, "Location:");
				if($_mdate!==false) $new_mdate = trim(substr($string, $_mdate+14));
				if($_location!==false) {
					$new_location = trim(substr($string, $_location+9));
					break;
				}
			}
		}
		fclose($fp);
		return empty($new_location) ? $contents : $this->file_get_contents($new_location, $new_mdate);
	}
	// �����ȣ ������ XML ��ȯ
	function post_data_xml_formalize($datas) {
		if(!rankup_util::check_resource($datas)) return '';
		foreach($datas as $post_infos) {
			$items .= "
			<item>
				<zipcode>$post_infos[ZIPCODE]</zipcode>
				<sido>$post_infos[SIDO]</sido>
				<sigugun>$post_infos[GUGUN]</sigugun>
				<dongmyun>".htmlspecialchars($post_infos['DONG'])."</dongmyun>
				<bunji>$post_infos[BUNJI]</bunji>
			</item>";
		}
		return $items;
	}
	// NAVER MAP ������ XML ��ȯ
	function map_data_xml_formalize($datas) {
		foreach($datas->item as $item) {
			$address = trim($item->address);
			$sido = trim($item->addrdetail->sido);
			$sigugun = trim($item->addrdetail->sido->sigugun);
			$dongmyun = trim($item->addrdetail->sido->sigugun->dongmyun);
			$mapx = $item->point->x;
			$mapy = $item->point->y;
			$_item = "
			<item>
				<address>$address</address>
				<sido>$sido</sido>
				<sigugun>$sigugun</sigugun>
				<dongmyun>$dongmyun</dongmyun>
				<mapx>$mapx</mapx>
				<mapy>$mapy</mapy>
			</item>";
			if($this->check_encoding() && $this->check_unicode($_item)) $this->change_encoding($_item, "IN");
			$items .= $_item;
		}
		return $items;
	}
	// XML �ļ� �̿�� - PHP version 4.x
	function map_data_xml_formalize2($datas) {
		foreach($datas->document->item as $item) {
			$mapx = $item->point[0]->x[0]->tagData;
			$mapy = $item->point[0]->y[0]->tagData;
			$address = $item->address[0]->tagData;
			$sido = $item->addrdetail[0]->sido[0]->tagData;
			$sigugun = $item->addrdetail[0]->sido[0]->sigugun[0]->tagData;
			$dongmyun = $item->addrdetail[0]->sido[0]->sigugun[0]->dongmyun[0]->tagData;
			$_item = "
			<item>
				<address>$address</address>
				<sido>$sido</sido>
				<sigugun>$sigugun</sigugun>
				<dongmyun>$dongmyun</dongmyun>
				<mapx>$mapx</mapx>
				<mapy>$mapy</mapy>
			</item>";
			if($this->check_encoding() && $this->check_unicode($_item)) $this->change_encoding($_item, "IN");
			$items .= $_item;
		}
		return $items;
	}
	// ���̹� ���� API �� ����� ��� ȸ����ġ�� ������ ǥ���ϱ� ���� �ּ� �ε� - ���̹� API �κ���
	function get_geocode($datas, $map_key) {
		if(empty($datas["dong"])) return '';
		$url = "http://map.naver.com/api/geocode.php?query=$datas[dong]&key=".$map_key;
		if(version_compare(PHP_VERSION, '5.0.0', '>') && function_exists("simplexml_load_file") && ini_get('allow_url_fopen')) { // 2008.12.31 - ini_get('allow_url_fopen') ���� �߰�
			$xml = simplexml_load_file($url);
			$post_contents = $this->map_data_xml_formalize($xml);
		}
		else { // PHP version 4.x ������
			include_once "rankup_XMLParser.class.php";
			$xml = new XMLParser($this->file_get_contents($url));
			$xml->Parse();
			$post_contents = $this->map_data_xml_formalize2($xml);
		}
		return $post_contents; // simplexml_load_file �Լ��� PHP 5 ���� ����
	}
	// �����ȣ �˻�
	function get_zipcode($datas, $api_mode=false) {
		if($api_mode===false) $post_datas = $this->queryFetchRows("select * from $this->table where DONG like '%$datas[dongmyun]%' order by SIDO, DONG");
		else {
			// �õ� ���͸�
			$datas['sido'] = str_replace(array("Ư����","������","Ư����ġ��","��","��","û","��"), "", $datas['sido']);
			// 2009.01.13 fixed - '���1��' �˻��� ���� ��񿡴� '�����1��'�� �Ǿ� �־� �˻��� ���� �ʾ���
			if(!preg_match('/[��|��]+[0-9]{1,}��+$/', $datas['dongmyun'])) {
				$prefix_dong = preg_replace('/([0-9]{1,})��+$/', "��$1��", $datas['dongmyun']);
				if(!empty($prefix_dong)) $prefix_dong = " or DONG like '$prefix_dong%'";
			}
			// �˻��� ���� - 2008.12.09 fixed - ���̹��� '�ñ���' ���� �ٸ���찡 �־ ������ ������쿡�� �ñ������� ������ ����
			$addWhere = empty($datas['dongmyun']) ? empty($datas['sigugun']) ? '' : " and GUGUN='$datas[sigugun]'" : " and (DONG like '$datas[dongmyun]%'$prefix_dong)"; // �˻��� ����
			$post_datas = $this->queryFetchRows("select * from $this->table where SIDO='$datas[sido]'$addWhere order by SIDO, DONG");
		}
		return $this->post_data_xml_formalize($post_datas);
	}
}
?>