<?php
/**
 * 랭크업 모바일 클래스 v1.5
 *@author: kurokisi
 *@authDate: 2011.10.17
 */

class rankup_mobile extends rankup_util {
	var $config_table = 'rankup_mobile_config';
	var $settings, $m_folder, $m_url, $m_dir, $skin_url, $skin_dir, $m_domain, $pc_domain;
	function rankup_mobile() {
		global $base_url, $base_dir, $config_info;
		parent:: rankup_util();

		$this->m_folder = substr(dirname(dirname(str_replace($_SERVER['DOCUMENT_ROOT'], '', __FILE__))), 1);
		$this->m_url = $base_url.$this->m_folder.'/';
		$this->m_dir = $base_dir.$this->m_folder.'/';

		$this->check_config();

		$this->set_configs(); // $this->configs 정의
		$this->settings = $this->configs['settings'];

		$this->settings['layout'] = 'a'; // fixed
		$this->skin_url = $this->m_url.'design/skin_'.$this->settings['layout'].'/';
		$this->skin_dir = $this->m_dir.'design/skin_'.$this->settings['layout'].'/';

		// 보유 도메인 사용시
		if($this->settings['domain_kind']=='own' && $this->settings['domain']) {
			$this->m_domain = $this->settings['domain'];
			$this->pc_domain = $this->settings['pc_domain'];
		}
		else {
			$this->m_domain = $config_info['domain'].$this->m_folder.'/';
			$this->pc_domain = $config_info['domain'];
		}

		$this->check_agent(); // UserAgent 체크
	}

	// 설정테이블 체크
	function check_config() {
		$table = $this->queryR("show tables like '$this->config_table'");
		if($table!==$this->config_table) {
			$this->query("CREATE TABLE `$this->config_table` (
			`item` VARCHAR( 20 ) NOT NULL ,
			`value` TEXT NOT NULL ,
			PRIMARY KEY ( `item` )
			) ENGINE = MYISAM");
		}
	}

	// UserAgent 체크
	function check_agent() {
		if($this->settings['mobile_use']!='yes' || strpos($_SERVER['SCRIPT_NAME'], $this->m_url)!==false) {
			$this->keep_alive_session();
			return;
		}
		$patterns = array(
			'allow' => '/cldc|midp|phone|mobile|ezweb|skt|sti|up\.browser/i',
			'disallow' => '/ipad|xoom|asktb/i' // asktb - 2012.05.23 added
		);
		if(empty($_COOKIE['from_mobile']) && !preg_match($patterns['disallow'], $_SERVER['HTTP_USER_AGENT']) && (preg_match($patterns['allow'], $_SERVER['HTTP_USER_AGENT']) || preg_match($patterns['allow'], $_SERVER['HTTP_X_UP_SUBNO']))) {
			$this->keep_alive_session();
			header('Location: '.$this->m_domain);
			exit;
		}
	}

	// PC 화면 이동
	function go_index() {
		global $base_url;
		@setcookie('from_mobile', time(), 0, $base_url);
		header('Location: '.$this->pc_domain);
	}

	// 회원로그인 처리
	function keep_alive_session() {
		global $rankup_member, $member_info;
		if($member_info['uid']) return true;
		$sess = $this->get_session();
		if($sess['keepid'] && $sess['keeplogin'] && $sess['userid'] && $sess['userkind']) {
			$rankup_member->set_member_session($sess['userid'], $sess['userkind']);
		}
	}

	// 로그인 세션키 반환
	function get_session_key() {
		return $this->encode('mousess', 2); // mobile user session
	}

	// 로그인 쿠키 반환
	function get_session() {
		$sess_key = $this->get_session_key();
		if($_COOKIE[$sess_key]) {
			list($keepid, $keeplogin, $userid, $userkind) = explode('|', $_COOKIE[$sess_key]);
		}
		return compact('keepid', 'keeplogin', 'userid', 'userkind');
	}

	// 로그인 쿠키 생성
	function set_session() {
		$sess_key = $this->get_session_key();
		$sess_val = implode('|', array($_POST['keep_id'], $_POST['keep_login'], $_POST['user_id'], 'general'));
		@setcookie($sess_key, $sess_val, time()+(86400*356), $this->m_url);
	}

	// 로그인 쿠키 제거 - 로그아웃시 자동로그인 초기화
	function delete_session() {
		$sess_key = $this->get_session_key();
		$sess = $this->get_session();
		$sess_val = implode('|', array($sess['keepid'], '', $sess['userid'], $sess['userkind']));
		@setcookie($sess_key, $sess_val, time()+(86400*365), $this->m_url);
	}

	// 헤더출력
	function print_header($title='') {
		if(!$title) $title = $this->configs['sitename'];
		$head = array();
		$head[] = '<!doctype html />';
		$head[] = '<html lang="ko">';
		$head[] = '<head>';
		$head[] = '<meta charset="euc-kr" />';
		$head[] = '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densitydpi=medium-dpi" />';
		$head[] = '<meta name="apple-mobile-web-app-capable" content="yes" />';
		$head[] = '<meta name="apple-mobile-web-app-status-bar-style" content="black" />';
		$head[] = '<title>'.$title.'</title>';
		$head[] = '<link rel="stylesheet" type="text/css" href="'.$this->m_domain.'styles/common.css" />';
		$head[] = '<script type="text/javascript"> var domain = "'.$this->m_domain.'"; </script>';
		$head[] = '<script type="text/javascript" src="'.$this->m_domain.'scripts/prototype.js"></script>';
		$head[] = '<script type="text/javascript" src="'.$this->m_domain.'scripts/common.js"></script>';
		$head[] = '</head>';
		$head[] = '';
		print implode("\n", $head);
	}

	// 환경설정 반환
	function set_configs() {
		$datas = $this->query("select * from $this->config_table");
		//if(!mysql_num_rows($datas)) die('<font color="red">Fatal : 먼저 관리자페이지에서 모바일웹설정을 해주시기 바랍니다.</font>');
		while($rows = $this->fetch($datas)) {
			$value = unserialize($rows['value']);
			$this->configs[$rows['item']] = is_array($value) ? $value : $rows['value'];
		}
	}

	// 환경설정 저장
	function save_settings() {
		global $base_url, $config_info;
		switch($_POST['kind']) {
			// 기본설정
			case 'basic':
				$value = $this->settings;
				$value['mobile_use'] = $_POST['mobile_use'];
				$value['membership_use'] = $_POST['membership_use'];
				$value['domain_kind'] = $_POST['domain_kind'];
				$domain = str_replace(array('http://', 'www.'), '', $_POST['domain']);
				$value['domain'] = $domain ? 'http://'.$domain : '';
				$value['pc_domain'] = $config_info['domain'];
				$value['phone'] = $_POST['phone'];
				if($_POST['on_logo']) {
					$attach = new attachment('mobile_logo', $this->m_dir.'builder/');
					if($this->settings['logo']) $attach->del($this->settings['logo']);
					$value['logo'] = $attach->save($_POST['on_logo']);
				}
				$this->save('sitename', $_POST['sitename']);
				$this->save('copyright', parent::trans_wysiwyg($_POST['copyright']));
				$this->save('ready_content', parent::trans_wysiwyg($_POST['ready_content']));
				$this->save('settings', serialize($value));
				break;
		}
	}
	function save($item, $value) {
		$values = $this->change_query_string(compact('value'));
		if(isset($this->configs[$item])) $this->query("update $this->config_table set $values where item='$item'");
		else $this->query("insert $this->config_table set item='$item', $values");
	}

}
?>