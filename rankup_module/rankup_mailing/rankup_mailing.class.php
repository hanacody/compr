<?php
/**
 * 랭크업 메일링 클래스
 *@author: kurokisi
 *@authDate: 2011.09.01
 */

class rankup_mailing extends rankup_util {
	var $config_table = 'rankup_mailing_config';
	var $mailing_table = 'rankup_mailing';

	var $settings = array();
	function rankup_mailing($kind='') {
		parent::rankup_util();

		$this->kinds = array(
			'member_join' => '회원가입',
			'found_id' => '아이디찾기',
			'found_pwd' => '비밀번호찾기'
		);
		if($kind) $this->get_settings($kind);
	}

	// 유니코드 체크
	function check_unicode($string) {
		return iconv('CP949', 'UTF-8', iconv('UTF-8', 'CP949', $string))==$string;
	}

	// 2Byte 코드 base64 인코딩
	function RFC_format($string, $charset='UTF-8') {
		if(!$this->check_unicode($string)) $string = iconv('CP949', $charset, $string);
		return '=?'.$charset.'?B?'.base64_encode($string).'?=';
	}

	// {이름|아이디|비밀번호|예약정보} -> real data
	function fetch_content($rows, $content) {
		preg_match_all('/{(.*?)}/', $content, $pattern);
		if(parent::chkRes($pattern[1])) {
			$infos = array();
			foreach(array_unique($pattern[1]) as $field) $infos[] = $rows[$field];
			$content = str_replace(array_unique($pattern[0]), $infos, $content);
		}
		return $content;
	}

	// {:domain:} -> real domain
	function fetch_domain($content) {
		global $config_info;
		$content = str_replace('{:domain:}', $config_info['domain'], $content);
		return $content;
	}

	// real domain -> {:domain:}
	function patch_domain($content) {
		global $config_info;
		$content = str_replace($config_info['domain'], '{:domain:}', $content);
		return $content;
	}

	// 설정값 로드
	function get_settings($kind) {
		$rows = $this->queryFetch("select * from $this->config_table where kind='$kind'");
		$rows['body'] = $this->fetch_domain($rows['body']);
		$this->settings = $rows;
	}

	// 설정값 저장
	function set_settings() {
		$_vals['kind'] = $_POST['kind'];
		$_vals['used'] = $_POST['used'];
		$_vals['priority'] = $_POST['priority'];
		$_vals['subject'] = $_POST['subject'];
		$body = parent::trans_wysiwyg($_POST['content'], true);
		$_vals['body'] = $this->patch_domain($body);
		$_vals['alarm'] = $_POST['alarm'];
		$values = $this->change_query_string($_vals);
		$present = $this->queryR("select kind from $this->config_table where kind='$_POST[kind]'");
		if($present) $this->query("update $this->config_table set $values where kind='$_POST[kind]'");
		else $this->query("insert $this->config_table set $values");
	}

	// 설정값 삭제
	function del_settings($kinds) {
		$this->query("DELETE FROM $this->config_table where kind in('$kinds')");
	}

	// 메일발송
	function send($emails, $datas=array()) {
		global $config_info;
		if(empty($emails) || $this->settings['used']=='no') return false;

		if(is_string($emails)) $emails = array($emails);

		$subject = $this->fetch_content($datas, $this->settings['subject']);
		$body = $this->fetch_content($datas, $this->settings['body']);

		if(!$this->check_unicode($subject)) $subject = iconv('CP949', 'UTF-8', $subject);
		if(!$this->check_unicode($body)) $body = iconv('CP949', 'UTF-8', $body);
		$subject = $this->RFC_format($subject);
		$body = chunk_split(base64_encode($body)); // 2012.05.29 renewal

		$sitename = $this->RFC_format(str_replace(':', '', $config_info['site_name']));
		$webmaster = $config_info['email'];

		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "X-Mailer: PHP ".phpversion()."\r\n";
		$headers .= "X-Priority: ".$this->settings['priority']."\r\n";
		$headers .= "From: $sitename<$webmaster>\r\n";
		$headers .= "Reply-to: $sitename<$webmaster>\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\r\n";
		$headers .= "Content-Transfer-Encoding: base64"; //2012.05.29 added

		if($this->settings['alarm']=='yes') array_push($emails, $webmaster); // 관리자 동보발송 처리
		foreach($emails as $email) {
			if($email) $result = mail($email, $subject, $body, $headers, '-f'.$webmaster);
		}
		return true;
	}

	/**
	 * 뉴스레터 발송관리
	 */

	// 뉴스레터 정보 반환
	function get_newsletter($no) {
		return $this->queryFetch("select * from $this->mailing_table where no=$no");
	}

	// 뉴스레터 발송
	function send_newsletter() {
		// 뉴스레터 환경 로드
		$this->get_settings('newsletter');
		$this->settings['subject'] = $_POST['subject'];
		$this->settings['body'] = $_POST['body'];

		$datas = $this->query(urldecode($_POST['query']));
		$qty = mysql_num_rows($datas);

		// log
		$_vals['subject'] = $_POST['subject'];
		$_vals['body'] = $_POST['body'];
		$_vals['qty'] = $qty;
		$_vals['send_time'] = date('Y-m-d H:i:s');
		$values = $this->change_query_string($_vals);
		$this->query("insert $this->mailing_table set $values");

		// sending
		$count = 0;
		while($rows = $this->fetch($datas)) {
			$this->send($rows['email'], array(
				'이름' => $rows['name'],
				'아이디' => $rows['uid']
			));
			// 300개단위 슬립
			if(!(++$count % 300)) {
				sleep(5);
			}
		}
	}

	// 뉴스레터 관리
	function print_newsletter($entry, $limits=15) {
		if(!$_GET['page']) $_GET['page'] = 1;
		$stpos = $this->get_query_point($_GET['page'], $limits);

		// 검색조건
		$stacks = array();
		if($_GET['use_date']) {
			$stacks[] = "send_time>='$_GET[sdate] 00:00:00'";
			$stacks[] = "send_time<='$_GET[edate] 23:59:59'";
		}
		if($_GET['skey']) $stacks[] = "$_GET[smode] like '%$_GET[skey]%'";
		if(count($stacks)) $addWhere = ' where '.implode(' and ', $stacks);

		$totals = $this->queryR("select count(no) from $this->mailing_table".$addWhere);
		$datas = $this->query("select * from $this->mailing_table".$addWhere." order by no desc limit $stpos, $limits");
		$contents = fetch_contents($datas, $entry, array($this, '_m112'));

		return array($totals, $contents);
	}
	function _m112($bind) {
		extract($bind);
		if(isset($time_format)) $rows['send_time'] = date($time_format, strtotime($rows['send_time']));
		return array($rows, $skin);
	}

	// 뉴스레터 삭제
	function del_newsletter() {
		$nos = str_replace('__', ',', $_POST['nos']);
		$this->query("delete from $this->mailing_table where no in ($nos)");
	}
}
?>