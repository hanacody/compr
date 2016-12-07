<?php
##����Ʈ�� �ʿ��� �⺻���� ���� �����ϴ� Ŭ����
class rankup_siteconfig extends rankup_util{
	var $config_table = 'rankup_siteconfig'; // ���� ���� ���̺�

	function rankup_siteconfig() {
		parent::rankup_util();
	}

	//����Ʈ �⺻������ ���� - 2008.07.22 ����
	function get_config_info($selection=true) {
		// $selection = main  : print_user_head() ���� ȣ��
		if($selection!==true && $selection==='main') $selection = "subject, email, site_name, bookmark, meta, check_date, smlevel, membership_use";
		else if($selection===true) $selection = "subject, email, site_name, bookmark, etc_settings, smlayout, smlevel, check_date, frame_use, intro_use, letter_consult_use, change_pwd_use, change_pwd_terms, membership_use, membership_types, membership_age";
		$result = $this->queryFetch("select $selection from $this->config_table");

		if($result['smlevel']) { // ȸ�����
			$result['smlevel'] = unserialize($result['smlevel']);
		}
		if($result['etc_settings']) { // �Ǹ����� ����
			$result['etc_settings'] = unserialize($result['etc_settings']);
			//unset($result['etc_settings']);
		}
		if($result['navermap_settings']) { // ���̹����� ����
			$result['navermap_settings'] = unserialize($result['navermap_settings']);
			//unset($result['navermap_settings']);
		}
		if($result['map_settings']) { // �൵ ����
			$result['map_settings'] = unserialize($result['map_settings']);
			//unset($result['map_settings']);
		}
		return (ereg(',', $selection)||$selection=='*') ? $result : $result[$selection];
	}

	//����Ʈ �⺻������ �Է�
	function set_config_info($field, $content) {
		// ��뿩�� �߰�
		$used = $this->getParam('used');
		if(!empty($used)) $addSet = ", use_$field='$used'";
		$values = $this->change_query_string(array($field=>"$content"));
		$nums = $this->queryR("select count(*) from $this->config_table");
		if($nums) $result = $this->query("update $this->config_table set $values $addSet");
		else $result = $this->query("insert into $this->config_table set return $values $addSet");

		return $result;
	}

	// �⺻ȯ�� ���� ����
	function save() {
		global $config_info;

		switch($_POST['kind']) {
			case 'siteconfig':
				$_vals['subject'] = $_POST['subject'];
				$_vals['site_name'] = $_POST['site_name'];
				$_vals['email'] = $_POST['email'];
				$_vals['bookmark'] = $_POST['bookmark'];
				$_vals['frame_use'] = $_POST['frame_use'];
				$_vals['meta'] = $_POST['meta'];

				// �൵
				$value = $this->get_config_info('map_settings');
				$value['use_nhn_map'] = $_POST['use_nhn_map'];
				$value['zipcode'] = $_POST['zipcode'];
				$value['addrs1'] = $_POST['addrs1'];
				$value['addrs2'] = $_POST['addrs2'];
				if($_POST['use_nhn_map']=='yes') {
					$value['mapx'] = $_POST['mapx'];
					$value['mapy'] = $_POST['mapy'];
				}
				else {
					if($_POST['on_map_attach']) {
						$attach = new attachment('map');
						if($value['map_attach']) $attach->del($value['map_attach']);
						$value['map_attach'] = $attach->save($_POST['on_map_attach']);
					}
				}
				$_vals['map_settings'] = serialize($value);
				break;

			case 'agreement':
			case 'mem_privacy':
				$_vals[$_POST['kind']] = parent::trans_wysiwyg($_POST['content']);
				break;
		}
		$values = $this->change_query_string($_vals);
		$num = $this->queryRows("select subject from $this->config_table");
		if($num) $this->query("update $this->config_table set $values");
		else $this->query("insert $this->config_table set $values");
	}

	// ���̹����� API ���� ����
	function save_mapkey() {
		$value = $this->get_config_info('navermap_settings');
		if($_POST['url']) {
			if($_POST['url']!=$_POST['mapurl']) {
				unset($value['map_keys'][$_POST['url']]);
				$value['map_keys'] = array_diff($value['map_keys'], array('', null));
			}
		}
		$domain = parse_url($_POST['mapurl']);
		$mapurl = str_replace('www.', '', $domain['host']).$domain['path'];

		$value['map_keys'][$mapurl] = $_POST['mapkey'];

		$_vals['navermap_settings'] = serialize($value);
		$values = $this->change_query_string($_vals);
		$num = $this->queryRows("select subject from $this->config_table");
		if($num) $this->query("update $this->config_table set $values");
		else $this->query("insert $this->config_table set $values");
	}

	// ���̹� ����Ű ����
	function del_mapkey() {
		$value = $this->get_config_info('navermap_settings');
		unset($value['map_keys'][$_POST['url']]); // ����
		$value['map_keys'] = array_diff($value['map_keys'], array('', null));

		$_val['navermap_settings'] = serialize($value);
		$values = $this->change_query_string($_val);
		$this->query("update $this->config_table set $values");
	}

	// ���̹� ����Ű ��ȯ
	function get_mapkey($kind='nhn') {
		global $config_info;
		switch($kind) {
			case 'nhn':
				$rows = $this->get_config_info('navermap_settings');
				$domain = parse_url($config_info['domain']);
				$map_url = str_replace('www.', '', $domain['host']).$domain['path'];
				$map_key = $rows['map_keys'][$map_url];
				break;
		}
		return $map_key;
	}

	// �ϳ��� �⺻������ ����
	function get_config_field($field) {
		return $this->queryR("select $field from $this->config_table");
	}
}
?>