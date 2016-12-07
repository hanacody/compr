<?php
/**
 * 페이지 제너레이터 V1.0
 *@author: kurokisi
 *@authDate: 2011.08.29
 */

class page_generator {
	var $infos = array();
	var $pages = array(
		'ready' => 'ready.inc.php',
		'html' => 'html.inc.php'
	);
	var $pids = array(); // 로케이션 출력시 참조
	var $page_title = ''; // 페이지 제목
	var $parent = array(); // 최상위 메뉴 정보
	var $branches = array(); // link 무한루프 방지용

	function page_generator($pid='', $initialize=true) {
		global $frame, $mobile, $rankup_admin;
		@include_once $mobile->m_dir.'design/top/pids.inc';
		$this->pids = $pids; // keep

		$this->is_admin = !empty($_SESSION[$rankup_admin->admin_session_id]);

		if($initialize) {
			// 기존페이지와의 연관성 체크 - 중요!
			if($pid) {
				$this->infos = $this->get_infos($pid); // ready, html, link 는 PID 가 무조건 존재함!
				if(!$this->infos) $this->error(404); // 존재하지 않는 페이지
			}
			else {
				if($_SESSION['pid']) $this->infos = $this->get_infos($_SESSION['pid']);

				// 페이지로 PID 찾기 - 메뉴가 아닌 주소를 직접치고 들어오는 경우
				include_once $mobile->m_dir.'class/rankup_moduler.class.php';
				$moduler = new rankup_moduler;
				$module = $moduler->educe_module($_SERVER['PHP_SELF']);
				$pid = $frame->educe_pid($module);
				unset($moduler);

				// 사용하지 않는 페이지인 경우
				if($pid=='deny') $this->error(405);
				else if($pid!='none') {
					$_infos = $this->get_infos($pid);
					if(!$_SESSION['pid']) $this->infos = $_infos;
					else if($_infos['page_type']=='module' && ($_infos['module']!=$this->infos['module'] || $_infos['component']!=$this->infos['component'])) {
						$this->infos = $_infos;
						$_SESSION['pid'] = $this->infos['no']; // session update!
					}
				}
				else if($pid=='none') {
					$this->infos = array();
				}
			}
			if(is_numeric($pid)) {
				// 출력제한
				$this->display_deny($this->infos['no']);

				// 실제데이터 load
				$this->infos = $frame->get_frame($this->infos['no']);
				$this->get_parent($this->infos['no'], $this->parent); // 최상위 메뉴정보

				// 접근제한
				$this->access_deny($this->infos['access_level'], "location.replace('{$mobile->m_url}index.html');");
				$this->page = $this->pages[$this->infos['page_type']]; // 페이지 형태별 로딩 파일 - ready & html 만 해당
				$this->page_title = $this->infos['base_name']; // 페이지 타이틀

				// GNB 활성화 세션생성
				$_SESSION['one'] = $this->set_gnb($this->infos['no'], 1);
				$_SESSION['two'] = $this->set_gnb($this->infos['no'], 2);
				$_GET['pid'] = $this->infos['no'];
			}
			else if($module['name']) {
				// 모듈이지만 메뉴에 등록되지 않은 경우
				$this->infos['base_name'] = $module['name'];
				$this->page_title = $module['name'];
			}
		}
		// attachment preset load
		include $mobile->m_dir.'builder/attachment.preset.php';
	}

	// Fatal 에러
	function error($code) {
		global $mobile;
		switch($code) {
			case 404: $error_msg = '페이지가 존재하지 않습니다.'; break;
			case 405: $error_msg = '사용하지 않는 페이지 입니다.'; break;
			case 500: $error_msg = '페이지를 표시할 수 없습니다.'; break;
		}
		scripts("alert('$error_msg');location.replace('{$mobile->m_url}index.html');");
		exit;
	}

	// 1차메뉴 반환
	function get_parent($pid, &$parent) {
		$parent = $this->pids[$pid];
		if($parent['parent']) $this->get_parent($parent['parent'], $parent);
	}

	// GNB 세션생성
	function set_gnb($no, $depth) {
		$item = $this->pids[$no];
		if($item['depth']<$depth) return 0;
		return ($item['depth']==$depth) ? $item['rank'] : $this->set_gnb($item['parent'], $depth);
	}

	// 페이지 정보 반환
	function get_infos($pid) {
		global $frame;
		$infos = $this->pids[$pid];
		if($infos['page_type']=='link' && $infos['link']) {
			if(in_array($infos['link'], $this->branches)) $this->error(500); // 무한루프 방지
			array_push($this->branches, $infos['link']);
			$infos = $this->get_infos($infos['link']); // 재귀호출
		}
		return $infos;
	}

	// 회원제 사용시 - 접근제한
	function access_deny($level, $scripts='') {
		global $mobile, $config_info, $member_info, $rankup_member, $rankup_control;

		if($mobile->settings['membership_use']=='no') return;
		if($this->is_admin) return; // 관리자페이지 로그인 상태
		if($level && $level<$rankup_member->lowest_level) {
			if(!$member_info['uid']) {
				scripts("alert('".$rankup_control->member_only."');".$scripts);
				exit;
			}
			else if($member_info['level']>$level) {
				scripts(sprintf("alert('%s 등급 이상만 이용하실 수 있는 서비스 입니다.');".$scripts, $config_info['smlevel'][$level]));
				exit;
			}
		}
	}

	// 미사용 페이지 - 출력제한
	function display_deny($pid) {
		global $base_url;
		$this->roots($pid, $roots);
		foreach(array_reverse($roots) as $rows) {
			if($rows['used']=='no') $this->error(405);
		}
	}

	// 페이지 제목
	function title($entry) {
		global $base_url, $base_dir;
		$title = '';
		if($this->infos['page_title_type']=='image') {
			$file = $this->presets['page_title']['save']['folder'].$this->infos['page_title_img'];
			if(is_file($base_dir.$file)) {
				$this->infos['title_image'] = $base_url.$file;
				$title = fetch_skin($this->infos, $entry[0]);
			}
		}
		if(!$title) $title = fetch_skin($this->infos, $entry[1]);
		return $title;
	}

	// 루트 반환
	function roots($pid, &$roots) {
		$node = $this->pids[$pid];
		$roots[$node['depth']] = $node;
		if($node['parent']) $this->roots($node['parent'], $roots);
	}

	// 현재위치
	function location($entry) {
		$this->roots($_GET['pid'], $roots);
		array_shift($roots); // self throw out
		return fetch_contents(array_reverse($roots), $entry);
	}

	// 탭 - 2차메뉴
	function tab_menus($entry) {
		global $frame;
		if($this->infos['depth']<1 || ($this->infos['depth']==1 && $this->infos['has_child']=='no')) return '';
		$parent = ($this->infos['depth']>1) ? $this->pids[$this->infos['no']]['parent'] : $this->infos['no'];
		$datas = $frame->get_frames($parent);
		return fetch_contents($datas, $entry, array($this, '_p66'));
	}
	function _p66($bind) {
		extract($bind);
		// 메뉴 활성화
		if(($this->infos['component'] && $this->infos['component']==$rows['component'] && $rows['no']==$this->infos['no']) ||
			(!$this->infos['component'] && $rows['no']==$this->infos['no'])) {
			$rows['on'] = $on;
		}
		return array($rows, $skin);
	}

	// 페이지 콘텐츠
	function body_content() {
		return $this->infos['page_body_content'];
	}

	// 첫번째 메뉴 반환
	function get_open_pid() {
		foreach($this->pids as $pid=>$val) {
			if($val['used']=='yes' && $val['use_gnb']=='yes') return $pid;
		}
		return false;
	}

	// 이동할페이지 URL 반환
	function get_url($rows) {
		global $mobile, $frame, $moduler;

		if(!$rows) $this->error(404);

		// 접근권한 체크
		if($mobile->settings['membership_use']=='yes') $this->access_deny($rows['access_level']);
		else { // 회원제미사용시 마이페이지 모듈인 경우
			if($rows['page_type']=='module' && $rows['module']=='mypage') {
				return $mobile->m_domain.'index.html';
			}
		}
		// URL 확인
		switch($rows['page_type']) {
			case 'module': $url = $moduler->get_url($rows); break;
			case 'link':
				if($rows['link']) {
					if(in_array($rows['link'], $this->branches)) $this->error(500); // 무한루프 방지
					array_push($this->branches, $rows['link']);
					$url = $this->get_url($frame->get_frame($rows['link']));
				}
				else {
					$url = $rows['url'];
				}
				break;
		}
		return $url;
	}
}
?>