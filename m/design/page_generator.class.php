<?php
/**
 * ������ ���ʷ����� V1.0
 *@author: kurokisi
 *@authDate: 2011.08.29
 */

class page_generator {
	var $infos = array();
	var $pages = array(
		'ready' => 'ready.inc.php',
		'html' => 'html.inc.php'
	);
	var $pids = array(); // �����̼� ��½� ����
	var $page_title = ''; // ������ ����
	var $parent = array(); // �ֻ��� �޴� ����
	var $branches = array(); // link ���ѷ��� ������

	function page_generator($pid='', $initialize=true) {
		global $frame, $mobile, $rankup_admin;
		@include_once $mobile->m_dir.'design/top/pids.inc';
		$this->pids = $pids; // keep

		$this->is_admin = !empty($_SESSION[$rankup_admin->admin_session_id]);

		if($initialize) {
			// �������������� ������ üũ - �߿�!
			if($pid) {
				$this->infos = $this->get_infos($pid); // ready, html, link �� PID �� ������ ������!
				if(!$this->infos) $this->error(404); // �������� �ʴ� ������
			}
			else {
				if($_SESSION['pid']) $this->infos = $this->get_infos($_SESSION['pid']);

				// �������� PID ã�� - �޴��� �ƴ� �ּҸ� ����ġ�� ������ ���
				include_once $mobile->m_dir.'class/rankup_moduler.class.php';
				$moduler = new rankup_moduler;
				$module = $moduler->educe_module($_SERVER['PHP_SELF']);
				$pid = $frame->educe_pid($module);
				unset($moduler);

				// ������� �ʴ� �������� ���
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
				// �������
				$this->display_deny($this->infos['no']);

				// ���������� load
				$this->infos = $frame->get_frame($this->infos['no']);
				$this->get_parent($this->infos['no'], $this->parent); // �ֻ��� �޴�����

				// ��������
				$this->access_deny($this->infos['access_level'], "location.replace('{$mobile->m_url}index.html');");
				$this->page = $this->pages[$this->infos['page_type']]; // ������ ���º� �ε� ���� - ready & html �� �ش�
				$this->page_title = $this->infos['base_name']; // ������ Ÿ��Ʋ

				// GNB Ȱ��ȭ ���ǻ���
				$_SESSION['one'] = $this->set_gnb($this->infos['no'], 1);
				$_SESSION['two'] = $this->set_gnb($this->infos['no'], 2);
				$_GET['pid'] = $this->infos['no'];
			}
			else if($module['name']) {
				// ��������� �޴��� ��ϵ��� ���� ���
				$this->infos['base_name'] = $module['name'];
				$this->page_title = $module['name'];
			}
		}
		// attachment preset load
		include $mobile->m_dir.'builder/attachment.preset.php';
	}

	// Fatal ����
	function error($code) {
		global $mobile;
		switch($code) {
			case 404: $error_msg = '�������� �������� �ʽ��ϴ�.'; break;
			case 405: $error_msg = '������� �ʴ� ������ �Դϴ�.'; break;
			case 500: $error_msg = '�������� ǥ���� �� �����ϴ�.'; break;
		}
		scripts("alert('$error_msg');location.replace('{$mobile->m_url}index.html');");
		exit;
	}

	// 1���޴� ��ȯ
	function get_parent($pid, &$parent) {
		$parent = $this->pids[$pid];
		if($parent['parent']) $this->get_parent($parent['parent'], $parent);
	}

	// GNB ���ǻ���
	function set_gnb($no, $depth) {
		$item = $this->pids[$no];
		if($item['depth']<$depth) return 0;
		return ($item['depth']==$depth) ? $item['rank'] : $this->set_gnb($item['parent'], $depth);
	}

	// ������ ���� ��ȯ
	function get_infos($pid) {
		global $frame;
		$infos = $this->pids[$pid];
		if($infos['page_type']=='link' && $infos['link']) {
			if(in_array($infos['link'], $this->branches)) $this->error(500); // ���ѷ��� ����
			array_push($this->branches, $infos['link']);
			$infos = $this->get_infos($infos['link']); // ���ȣ��
		}
		return $infos;
	}

	// ȸ���� ���� - ��������
	function access_deny($level, $scripts='') {
		global $mobile, $config_info, $member_info, $rankup_member, $rankup_control;

		if($mobile->settings['membership_use']=='no') return;
		if($this->is_admin) return; // ������������ �α��� ����
		if($level && $level<$rankup_member->lowest_level) {
			if(!$member_info['uid']) {
				scripts("alert('".$rankup_control->member_only."');".$scripts);
				exit;
			}
			else if($member_info['level']>$level) {
				scripts(sprintf("alert('%s ��� �̻� �̿��Ͻ� �� �ִ� ���� �Դϴ�.');".$scripts, $config_info['smlevel'][$level]));
				exit;
			}
		}
	}

	// �̻�� ������ - �������
	function display_deny($pid) {
		global $base_url;
		$this->roots($pid, $roots);
		foreach(array_reverse($roots) as $rows) {
			if($rows['used']=='no') $this->error(405);
		}
	}

	// ������ ����
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

	// ��Ʈ ��ȯ
	function roots($pid, &$roots) {
		$node = $this->pids[$pid];
		$roots[$node['depth']] = $node;
		if($node['parent']) $this->roots($node['parent'], $roots);
	}

	// ������ġ
	function location($entry) {
		$this->roots($_GET['pid'], $roots);
		array_shift($roots); // self throw out
		return fetch_contents(array_reverse($roots), $entry);
	}

	// �� - 2���޴�
	function tab_menus($entry) {
		global $frame;
		if($this->infos['depth']<1 || ($this->infos['depth']==1 && $this->infos['has_child']=='no')) return '';
		$parent = ($this->infos['depth']>1) ? $this->pids[$this->infos['no']]['parent'] : $this->infos['no'];
		$datas = $frame->get_frames($parent);
		return fetch_contents($datas, $entry, array($this, '_p66'));
	}
	function _p66($bind) {
		extract($bind);
		// �޴� Ȱ��ȭ
		if(($this->infos['component'] && $this->infos['component']==$rows['component'] && $rows['no']==$this->infos['no']) ||
			(!$this->infos['component'] && $rows['no']==$this->infos['no'])) {
			$rows['on'] = $on;
		}
		return array($rows, $skin);
	}

	// ������ ������
	function body_content() {
		return $this->infos['page_body_content'];
	}

	// ù��° �޴� ��ȯ
	function get_open_pid() {
		foreach($this->pids as $pid=>$val) {
			if($val['used']=='yes' && $val['use_gnb']=='yes') return $pid;
		}
		return false;
	}

	// �̵��������� URL ��ȯ
	function get_url($rows) {
		global $mobile, $frame, $moduler;

		if(!$rows) $this->error(404);

		// ���ٱ��� üũ
		if($mobile->settings['membership_use']=='yes') $this->access_deny($rows['access_level']);
		else { // ȸ�����̻��� ���������� ����� ���
			if($rows['page_type']=='module' && $rows['module']=='mypage') {
				return $mobile->m_domain.'index.html';
			}
		}
		// URL Ȯ��
		switch($rows['page_type']) {
			case 'module': $url = $moduler->get_url($rows); break;
			case 'link':
				if($rows['link']) {
					if(in_array($rows['link'], $this->branches)) $this->error(500); // ���ѷ��� ����
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