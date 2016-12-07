<?php
## ��ũ�� ��Ƽ�Խ��� ���� Ŭ����
class rankup_board extends rankup_util {
	var $version = "v2.1 r100618"; // �Խ��� ���� ����
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $bconfig_table = "rankup_board_config"; // �Խ��� �� ȯ�漳�� ���̺�(���� ����, �з�(serialize),)
	var $setting_table = "rankup_etcconfig_setting"; // �Խ��� etc ����
	var $division_table = "rankup_board_division"; // �Խù� ���ҹ�ȣ ���� ���̺�
	var $category_table = "rankup_board_category"; // �Խ��� ī�װ�
	var $hit_best_table = "rankup_board_hit_best"; // ��ȸ�� ����Ʈ ���̺�(�Խ��Ǻ� 10������ ����)
	var $comment_best_table = "rankup_board_comment_best"; // ��ۼ� ����Ʈ ���̺�(�Խ��Ǻ� 10������ ����)
	var $weekly_best_table = "rankup_board_weekly_best"; // �ְ� ����Ʈ ���̺�
	var $new_article_table = "rankup_board_new_article"; // �ű� �Խù�(�Խ��Ǻ� 10������ ����)
	var $weekly_cbest_table = "rankup_board_weekly_cbest"; // �ְ� ��� ����Ʈ ���̺�
	var $report_table = "rankup_article_report"; // �ҷ� �Խù� �Ű� ���̺�
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $division_num = 100000; // 10���� ���� ����
	var $notice_sno = -2000000000; // -20��
	var $optimizer = true; // ���̺� ����ȭ ��� Ȱ��ȭ ����
	var $use_main_board = true; // ���ΰԽ��� ��뿩��
	var $use_board_menu = true; // �Խ��� �޴� ��뿩�� - 2011.10.06 added
	var $display_subject = true; // �Խ������� ��� ���� - 2011.10.06 added
	var $thumbnail_width = 200; // ����� ���λ�����
	var $thumbnail_height = null; // ����� ���λ�����(null : ���� ������ ����)
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $board_prefix = "rankup_board_";
	var $comment_prefix = "rankup_board_comment_";
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $sconfig_table = "rankup_siteconfig"; // �ַ�� �⺻ȯ�漳�� ���̺�(������,����Ʈ,��޼���)
	var $member_table = "rankup_member"; // ȸ�� ���̺�(�⺻����)
	var $member_extend_table = "rankup_member_extend"; // ȸ�� Ȯ�����̺�(�г���,�ҷ�ȸ��,���,����Ʈ����)
	var $admin_session_id = "admin_session_id"; // ������ ���� ID
	var $member_session_id = "niceId";	// ȸ�� ���� ID
	var $member_uid_field = "uid"; // ȸ�� ���̵� �ʵ��
	var $member_passwd_field = "passwd"; // ȸ�� ��й�ȣ �ʵ��
	var $member_name_field = "name"; // ȸ�� �̸� �ʵ��
	var $editor_name = "wysiwyg"; // ������ ������ ������
	var $index_name = "board"; // �Խ��� �ε��������� ��ġ�ϴ� ������
	var $etc_file_name = "include"; // �Խ��� ETC ������ ��ġ�ϴ� ������
	var $include_js_class = false; // rankup_basic::include_js_class() ��뿩�� - 2009.10.08 added
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $member_badness = "no"; // �ҷ� ȸ�� ����
	var $member_level = 7; // ȸ�� ���(7: ��ȸ��, 5: ��ȸ��, 1: ���)
	var $member_id = ""; // �α��� ���� ȸ�� ���̵�
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $confirm_used = true; // ���Թ����ڵ� ��뿩�� - 2010.06.17 added
	var $granted_messages = array( // 2010.08.12 added
		'list_level' => "�˼��մϴ�. �� �Խ����� '%s' �̻� �̿��� �����մϴ�.",
		'read_level' => "�˼��մϴ�. �� �Խ����� '%s' �̻� ��ȸ�� �����մϴ�.",
		'write_level' => "�˼��մϴ�. �� �Խ����� '%s' �̻� �۾��Ⱑ �����մϴ�.",
		'delete_level' => "�˼��մϴ�. �� �Խ����� '%s' �̻� ������ �����մϴ�.",
		'comment_level' => "�˼��մϴ�. �� �Խ����� '%s' �̻� ��۾��Ⱑ �����մϴ�.",
		'reply_level' => "�˼��մϴ�. �̰Խ����� '%s' �̻� �亯�� ���Ⱑ �����մϴ�.",
		'notice_level' => "�˼��մϴ�. �� �Խ����� '%s' �̻� ������ ���Ⱑ �����մϴ�.",
		'secret_level' => "�˼��մϴ�. �� �Խ����� '%s' �̻� ��б� ��ȸ�� �����մϴ�."
	);
	var $deny_nonmember = "\\n����, ȸ������ �α����Ͻñ� �ٶ��ϴ�."; // ȸ�� ����üũ�� ��ȸ���� �޽��� - 2010.08.12 added
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $index_url = "";
	var $wysiwyg_url = "";
	var $wysiwyg_dir = "";
	var $base_url = "";
	var $base_dir = "";
	var $board_url = "";
	var $board_dir = "";
	var $skin_url = "";
	var $skin_dir = "";
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $board_extension = false;
	var $board_configs = null;
	var $board_table = null;
	var $board_comment_table = null;
	var $board_name = null;
	var $board_id = null;
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $spermission = null;
	var $slayout = null;
	var $sfunction = null;
	var $soption = null;
	var $sattach = null;
	var $spoint = null;
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $wm_settings = array();
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// �Խ��� Ŭ���� �ʱ�ȭ
	function rankup_board($board_id='') {
		parent::rankup_util();
		$this->base_url = rankup_basic::base_url();
		$this->base_dir = rankup_basic::base_dir();
		$this->board_url = $this->base_url."rankup_module/rankup_board/";
		$this->board_dir = $this->base_dir."rankup_module/rankup_board/";
		// �ַ�Ǻ� ȯ�� ���� ����
		include $this->base_dir."Libs/_php/board_setting.inc.php";
		$this->index_url = $this->base_url.$this->index_name; // �ε��� ��� ����
		$this->etc_file_dir = $this->base_dir.$this->index_name."/".$this->etc_file_name."/"; //�Խ��� ETC FILE ��μ���
		$this->wysiwyg_url = $this->base_url.$this->editor_name."/"; // ������ ��� ����
		$this->wysiwyg_dir = $this->base_dir.$this->editor_name."/"; // ������ ��� ����
		$this->check_config_tables(); // �⺻ȯ�� ���̺� üũ
		$this->get_wm_settings(); //���͸�ũ ����
		if(!empty($board_id)) {
			$check_table = $this->queryR("show tables like '$this->board_prefix$board_id'");
			if(empty($check_table)) $this->popup_msg_js("��û�Ͻ� '$board_id' �Խ����� �������� �ʽ��ϴ�.", "BACK");
			$this->board_configs = $this->get_board_config($board_id);
			if(!$this->is_admin() && $this->board_configs['uval']=="no") { // 2009.08.31 fixed
				$this->popup_msg_js("��û�Ͻ� '$board_id' �Խ����� �������� �ʽ��ϴ�.", "BACK");
			}
			$this->check_iptables(); // ���� ������ ���� üũ
			$this->skin_url = $this->board_url."skin/board/".$this->board_configs['skin']."/"; // img �±� ���������
			$this->skin_dir = $this->board_dir."skin/board/".$this->get_skin_dir()."/"; // skin ���� �ε��
			$this->board_table = $this->board_prefix.$this->board_configs['id'];
			$this->board_comment_table = $this->comment_prefix.$this->board_configs['id'];
			$this->board_name = $this->board_configs['name'];
			$this->board_id = $this->board_configs['id'];
			$this->add_referers('id='.$this->board_id); // ������ �Խù����� ��ġ - 2010.05.31 fixed
			$this->spermission = unserialize($this->board_configs['spermission']); // ���Ѽ���
			$this->slayout = unserialize($this->board_configs['slayout']); // �⺻ ���̾ƿ�
			$this->sfunction = unserialize($this->board_configs['sfunction']); // ������ɼ���
			$this->soption = unserialize($this->board_configs['soption']); // ���û���
			$this->sattach = unserialize($this->board_configs['sattach']); // ����÷��
			$this->spoint = unserialize($this->board_configs['spoint']); // ����Ʈ
			// ����÷�� ��� ���� �ӽ� ����(��Ű �̿�) üũ
			if($this->sattach['use_attach']=="on") $this->clear_junk_files();
		}
		$this->member_level = $this->check_member_level(); // ȸ�� ���� üũ
		// ���������� / �޴������� ���̾ƿ� ���� �ε� - 2009.08.31 added
		$this->main_layout = $this->get_main_layout($_GET['pcno']);
	}

	// ���۷� ���� �߰� - 2010.05.31 added
	function add_referers($param='') {
		if(empty($param)) return;
		list($url, $query) = explode('?', $_SERVER['HTTP_REFERER']);
		parse_str($query, $queries);
		parse_str($param, $params);
		$referers = @http_build_query(array_merge($queries, $params));
		$_SERVER['HTTP_REFERER'] = $url.'?'.$referers;
	}

	// ȸ�� ��� üũ
	function check_member_level() {
		$this->member_id = $_SESSION[$this->member_session_id];
		if($this->use_extend_level) return extend_level($this); // 2011.10.01 added
		else {
			$fields = array();
			$table_check = $this->queryR("show tables like '".$this->member_extend_table."'");
			if($table_check) $field_datas = $this->queryFetchRows("desc ".$this->member_extend_table);
			else $field_datas = false;
			if($this->check_resource($field_datas)) foreach($field_datas as $rows) $fields[] = $rows['Field'];
			$this->board_extension = in_array("nickname", $fields) && in_array("badness", $fields) && in_array("point", $fields) && in_array("level", $fields);
			// ����Ʈ �������
			if(!empty($this->member_id)) {
				if($this->board_extension===true) {
					$this->smpoint = @unserialize($this->queryR("select smpoint from $this->sconfig_table"));
					$member_infos = $this->queryFetch("select nickname, badness, level, point from $this->member_extend_table where $this->member_uid_field='$this->member_id'");
					$this->member_badness = $member_infos['badness']; // �ҷ�ȸ��
					$this->member_point = $member_infos['point']; // ȸ�� ����Ʈ
					$this->member_name = $member_infos['nickname']; // ȸ���̸� - �г���
					return $member_infos['level'];
				}
				else {
					$member_infos = $this->queryFetch("select $this->member_name_field from $this->member_table where $this->member_uid_field='$this->member_id'");
					$this->member_name = $member_infos[$this->member_name_field]; // ȸ���̸�
				}
			}
		}
		return empty($this->member_id) ? 7 : 5; // 7 ��ȸ��, 5 ȸ��
	}

	// ȸ�� ��й�ȣ ����
	function get_member_passwd($uid) {
		return $this->queryR("select $this->member_passwd_field from $this->member_table where $this->member_uid_field='$uid'");
	}

	// ȸ�� �α��� ���� ���� - �ʿ�� ����
	function is_member() {
		return !empty($_SESSION[$this->member_session_id]);
	}

	// ������ �α��� ���� - 2009.09.18 added
	function is_administrator() {
		return !empty($_SESSION[$this->admin_session_id]);
	}

	// ��� �α��� ���� ���� - �ʿ�� ����
	function is_admin() {
		return (!empty($_SESSION[$this->admin_session_id]) || $this->member_level==1);
	}

	//�ٷλ���/������ �Ǵ�
	function is_del() {
		//��� �α����ϰ�� �ٷ� ����
		if($this->sfunction['use_articledel'] == "now" || $this->is_admin()) return true;
		else return false;
	}

	// �Խ��� ȯ�� ���̺� üũ
	function check_config_tables() {
		@include "scheme/rankup_board_scheme.inc.html";
		foreach($_BOARD_CONFIG_TABLES as $table_name=>$create_query) {
			$check_table = $this->queryR("show tables like '$table_name'");
			if($check_table===$table_name) continue;
			$this->query($create_query);
		}
		return true;
	}

	//���̺��� ������� ����
	function check_etc_tables() {
		$check_table = $this->queryR("show tables like '$this->setting_table'");
		if($check_table === $this->setting_table) return false;
		$this->query("CREATE TABLE `rankup_etcconfig_setting` (
		`item_name` varchar(30) NOT NULL default '',
		`item_value` text NOT NULL,
		UNIQUE KEY `item_name` (`item_name`)) TYPE=MyISAM");
	}

	// ���͸�ũ ������ȯ
	function get_wm_settings() {
		$this->check_etc_tables();
		$this->wm_settings = array();
		$datas = $this->query("select * from $this->setting_table where item_name='thumb_configs'");
		while($rows = $this->fetch($datas)) {
			$this->wm_settings = unserialize($rows['item_value']);
		}
	}
	// �Խ��� ȯ�� ���� ���� - ����
	function get_board_config($board, $fields='*') { // $board = { id  |  no }
		$addWhere = !is_numeric($board) ? is_array($board) ? "id in(".@implode(",", $board).")" : "id='$board'" : "no=$board";
		if(is_array($board)) return $this->queryFetchRows("select $fields from $this->bconfig_table where $addWhere");
		else {
			$result = $this->queryFetch("select $fields from $this->bconfig_table where $addWhere");
			return ($fields=='*' || count(explode(",", $fields))>1) ? $result : array_pop($result);
		}
	}

	// ȸ����� �ؽ�Ʈ ��ȯ - 2010.08.12 added
	function get_granted_level_text($branch) {
		$level = $this->spermission[$branch];
		if($this->board_extension===true || $this->use_extend_level===true) {
			$levels = unserialize($this->queryR("select smlevel from $this->sconfig_table"));
			if(!is_array($level_texts)) $levels = array(7=>"��ȸ��", 6=>"��ȸ��", 5=>"��ȸ��", 4=>"���ȸ��", 3=>"Ư��ȸ��", 2=>"�ο��", 1=>"���", "join_level"=>"6");
		}
		else {
			$levels = array(7=>"��ȸ��", 5=>"ȸ��", 1=>"���");
		}
		return $levels[$level];
	}

	// ���� �ؽ�Ʈ ��ȯ - 2010.08.12 added
	function get_granted_messages($branch) {
		$login_message = $this->member_id ? "" : $this->deny_nonmember;
		$message = sprintf($this->granted_messages[$branch].$login_message, $this->get_granted_level_text($branch));
		return $message;
	}

	// �޴��� ������ ��ȯ - 2009.08.31 added
	function get_main_layout($category='') {
		if($category=='main') {
			$datas = $this->queryFetch("select smlayout from $this->sconfig_table");
			$datas = @unserialize($datas['smlayout']);
		}
		else {
			if(!$category) $category = $this->queryR("select no from $this->category_table where pno=0 order by rank limit 1");
			$datas = $this->queryFetch("select no, mbno, mskin, lskin, mbnum, sprint, mval from $this->category_table where no=$category");
			$datas['sprint'] = @unserialize($datas['sprint']);
		}
		return $datas;
	}

	// �Խ��� ���� ���� - 2009.08.31 modified
	function get_setting($category, $xml=true) {
		// �޴��� ������ �߰�
		if($xml==true) {
			$main_datas = $this->get_main_layout($category); // 2009.08.31 modified
			if($category!=="main" && $main_datas['mbno']) $main_datas['mbname'] = $this->queryR("select name from $this->bconfig_table where no=$main_datas[mbno]");
		}
		$addWhere = $category=="main" ? '' : " and c.pcno=$category";
		$board_datas = $this->queryFetchRows("select c.no, c.id, c.name, c.cno, c.pcno, c.anum, c.uval, c.mval, c.pcmval, c.smlayout, c.spcmlayout, c.rank from $this->category_table as m1, $this->category_table as m2, $this->bconfig_table as c where (m1.no=c.pcno and m1.pval='yes' and m2.no=c.cno and m2.pval='yes')$addWhere order by m1.rank, m2.pno, m2.rank, m1.pno, c.rank");
		return $xml===true ? $this->formalize_setting_xml_data($board_datas, $main_datas) : $board_datas;
	}


##########################################################################
## ���ø� ������ ���� �� ���μ���
##########################################################################
	// ���ø� ������ �ε�
	function get_board_template($tpl_name, $type="board") {
		$cache_file = $this->board_dir."skin/$type/".$tpl_name;
		if(!is_file($cache_file)) {
			echo "<b style='color:red;font-size:9pt;'>Fatal error: �ջ�� ��Ų�̰ų� ��Ģ�� �ùٸ��� �ʽ��ϴ�.</b>";
			exit;
		}
		return $cache_file;
	}

	// ��Ų ��� ����
	function get_skin_dir($name='') {
		if(empty($name)) $name = $this->board_configs['skin'];
		return array_shift(explode("/", $name));
	}

	// ���ø� �������� �ε�
	function get_template_item($tpl_file) {
		$tpl_fp = @fopen($tpl_file, 'r');
		if(!is_resource($tpl_fp)) {
			echo "<b style='color:red;font-size:9pt;'>Fatal error: �ջ�� ��Ų�̰ų� ��Ģ�� �ùٸ��� �ʽ��ϴ�. $tpl_file</b>";
			exit;
		}
		while(!feof($tpl_fp)) $tpl_buffer .= fgets($tpl_fp, 4096);
		if(empty($tpl_buffer)) {
			echo "<b style='color:red;font-size:9pt;'>Fatal error: �ջ�� ��Ų�̰ų� ��Ģ�� �ùٸ��� �ʽ��ϴ�.</b>";
			exit;
		}
		return $tpl_buffer;
	}

##########################################################################
## ����������� - ���������� �κ�
##########################################################################
	// �������ܵ� ������ ���� üũ
	function check_iptables() {
		if(in_array($_SERVER['REMOTE_ADDR'], explode(",",$this->board_configs['sblock']))) $this->popup_msg_js("������ �����Ǵ� ���ܸ�Ͽ� �����Ǿ� �־� �Խ����� �̿��Ͻ� ���� �����ϴ�.", "BACK");
		return true;
	}

	// ����¡ ��� - $_GET �� ���� - 2009.06.22 added
	function print_paging($total_records='', $division=array(15, 10), $key='page') {
		if(empty($total_records)) return '&nbsp;';

		if(is_array($division)) list($limits, $grouping) = $division;
		else {
			$limits = $this->slayout['article_rows'];
			if(!$grouping) $grouping = 10;
		}

		$first_page = 1;
		$last_page = ceil($total_records/$limits);
		//if($last_page<=$first_page) return '&nbsp;';
		// ����¡ ����
		$pattern = array(
			'format' => "%d", // ������ ���� �������
			'space' => " | " // ������ �� ������
		);
		$icons = array(
			'first' => "<img src='{$this->skin_url}bt_prev_last.gif' align=''>",
			'previous' => "<img src='{$this->skin_url}bt_prev.gif' align='' hspace='3'>",
			'next' => "<img src='{$this->skin_url}bt_next.gif' align=''>",
			'last' => "<img src='{$this->skin_url}bt_next_last.gif' align='' hspace='3'>"
		);

		// ������ ���� ����
		$open_page = $_GET[$key];
		if(empty($open_page)) $open_page = 1;
		$now_grouping = ceil($open_page/$grouping);
		$last_grouping = ceil($last_page/$grouping);
		$min_page = ($now_grouping-1)*$grouping+1;
		$max_page = ($now_grouping*$grouping >= $last_page) ? $last_page : $now_grouping*$grouping;
		$prev_page = ($min_page==$first_page) ? 0 : $min_page-1;
		$next_page = ($max_page==$last_page) ? $last_page : $max_page+1;

		// ����¡ ����
		$pages = array();
		$contents = array($icons['first'], $icons['previous'], '', $icons['next'], $icons['last']);
		if($now_grouping!=$first_page) $contents[0] = "<a href='".$this->parameters("$key=1")."'>$icons[first]</a>";
		if($now_grouping!=$last_grouping) $contents[4] = "<a href='".$this->parameters("$key=$last_page")."'>$icons[last]</a>";
		foreach(range($prev_page, $next_page) as $page) {
			$params = $this->parameters("$key=$page");
			$num = sprintf($pattern['format'], $page);
			switch($page) {
				case $prev_page: if($now_grouping!=$first_page) $contents[1] = "<a href='$params'>$icons[previous]</a>"; break;
				case $open_page: $pages[] = "<font class='on'>$num</font>"; break;
				case $next_page:
					if($now_grouping!=$last_grouping) $contents[3] = "<a href='$params'>$icons[next]</a>";
					else $pages[] = "<a href='$params'>$num</a>";
					break;
				default: $pages[] = "<a href='$params'>$num</a>";
			}
		}
		$contents[2] = "<span class='num'>".@join($pattern['space'], $pages)."</span>";
		ksort($contents);
		return @join('', $contents);
	}

	// ���� ���� üũ
	function check_granted($branch) {
		// member_level = 1 : ��� -> �������� ���´�.
		return ((!empty($this->member_level) && $this->spermission[$branch]>=$this->member_level) || $this->member_level===1 || $this->is_admin());
	}

	// ���� ������ ����
	function get_main_contents($pcno='') {
		$this->main_layout = $this->get_main_layout($pcno); // 2009.09.21 fixed
		if($pcno==="main") unset($pcno);
		else {
			if(preg_match('/[^0-9]/', $pcno)) $this->popup_msg_js("�߸� �� ��û�Դϴ�.", "BACK");
			if(!empty($pcno)) $addWhere = " no=$pcno and";
			else {
				$category_infos = $this->queryFetchObject("select no, mbno, mval, mskin, mbnum, sprint from $this->category_table where pno=0 and uval='yes' and pval='yes' and dval='no' order by rank limit 0,1");
				if(empty($category_infos->no)) $this->popup_msg_js("�Խ����� �������� �ʽ��ϴ�.", "BACK");
				else $pcno = $category_infos->no;
			}
		}
		// ���������� ������ �ƴѰ�� - ��, �޴� ���������� ������ ���
		if(!empty($pcno)) {
			// �޴� ������ �ε�
			if(!is_object($category_infos)) $category_infos = $this->queryFetchObject("select mbno, mval, mskin, mbnum, sprint, content from $this->category_table where$addWhere uval='yes' and pval='yes' and dval='no'");
			$category_infos->sprint = @unserialize($category_infos->sprint); // ��¼���

			// ī�װ� ������ Ÿ��Ʋ�� �ݿ��ϱ� ���� �߰� - 2009.03.06
			$this->subject = $category_infos->content;

			// ������������ ������� ���� ��� ���� �Խ����� �ѷ���
			if($category_infos->mval=="no") {
				if(empty($category_infos->mbno)) $this->popup_msg_js("������������ �����Ǿ� ���� �ʽ��ϴ�.", "BACK"); // ���ΰԽ����� �������� �ʾ��� ���
				$board_id = $this->queryR("select id from $this->bconfig_table where no=$category_infos->mbno");
				$this->rankup_board($board_id);
				return $this->get_board_articles(array("id"=>$board_id));
			}
		}
		// ���������� ���� - 2009.08.31 modified
		else if($this->check_resource($this->main_layout)) {
			$category_infos->mskin = $this->main_layout['mskin'];
			$category_infos->mbnum = $this->main_layout['mbnum'];
			$category_infos->sprint = $this->main_layout['sprint'];
		}
		// �Խ��� ������� ���� - 2009.04.14 fixed
		$addWhere = empty($pcno) ? "c.mval='yes' and c.uval='yes'" : "c.pcno=$pcno and c.pcmval='yes' and c.uval='yes'"; // 2010.06.21 fixed
		$board_datas = $this->queryFetchRows("select c.no, c.id, c.name, c.smlayout, c.spcmlayout, c.sfunction, c.spermission, c.soption from $this->bconfig_table as c LEFT OUTER JOIN $this->category_table as m1 ON m1.no=c.pcno and m1.pval='yes' LEFT OUTER JOIN $this->category_table as m2 ON m2.no=c.cno and m2.pval='yes' where $addWhere order by m1.rank, m2.pno, m2.rank, m1.pno, c.rank");
		if($this->check_resource($board_datas)) {
			$column_count = 0;
			foreach($board_datas as $board_infos) {
				// �÷� ��Ÿ�� ����
				$_main_article_contents .= $this->get_main_articles($pcno, $board_infos, $category_infos, $column_count);
				if(!(++$column_count%$category_infos->mbnum)) {
					if($main_article_contents) $main_article_contents .= "<tr><td height='15'></td></tr>"; // 2009.11.13 fixed
					$main_article_contents .= "
					<tr valign='top'>
						$_main_article_contents
					</tr>";
					unset($_main_article_contents);
				}
			}
			if(isset($_main_article_contents)) {
				$_tds = str_repeat("<td>&nbsp;</td>", $category_infos->mbnum-($column_count%$category_infos->mbnum));
				if($main_article_contents) $main_article_contents .= "<tr><td height='15'></td></tr>"; // 2009.11.13 fixed
				$main_article_contents .= "
				<tr valign='top'>
					$_main_article_contents$_tds
				</tr>";
				unset($_main_article_contents);
			}
		}
		// ��Ų URL
		$this->skin_url = $this->board_url."skin/main/".$category_infos->mskin."/";
		ob_start();
		include $this->get_board_template($this->get_skin_dir($category_infos->mskin)."/".(empty($pcno)?"main":"menu")."_page.tpl.html", "main");
		return ob_get_clean();
	}

	// ������������ ����� �Խ��� ����
	function get_main_articles($pcno, $board_infos, $category_infos, $column=0) {
		$board_table = $this->board_prefix.$board_infos['id'];
		$slayout = empty($pcno) ? unserialize($board_infos['smlayout']) : unserialize($board_infos['spcmlayout']);
		if(!is_array($slayout) || !count($slayout)) return '';
		$skin_url = $this->board_url."skin/main/".$category_infos->mskin."/";
		$skin_dir = $this->board_dir."skin/main/".$this->get_skin_dir($category_infos->mskin)."/";
		switch($slayout['print_style']) {
			case "both":
				$image_width = $slayout['image_width'];
				$image_height = $slayout['image_height'];
				$attach_dir = $this->board_dir."attach/".$board_infos['id']."/";
				$attach_url = $this->board_url."attach/".$board_infos['id']."/";
			case "text":
				$tpl_buffer = $this->get_template_item($skin_dir."__text.tpl");
				break;
			case "image":
				$tpl_buffer = $this->get_template_item($skin_dir."__image.tpl");
				$image_width = $slayout['image_width'];
				$image_height = $slayout['image_height'];
				$attach_dir = $this->board_dir."attach/".$board_infos['id']."/";
				$attach_url = $this->board_url."attach/".$board_infos['id']."/";
				$column_count = 0;
				break;
		}
		// dno ó���ϴ� �κ� �߰� @#########
		$board_datas = $this->queryFetchRows("select no, uid, subject, cnum, attach, sval, dval, wdate from $board_table where dno>=1 and sno>$this->notice_sno and dval='no' order by sno, gno limit $slayout[article_rows]");
		if($this->check_resource($board_datas)) {

			// �Խ��� ��ũ ����
			parse_str($_SERVER['QUERY_STRING'], $query_infos);
			unset($query_infos['no'], $query_infos['pcno']); // �Խù� ��ȣ/�����޴� ��ȣ�� ����
			$query_infos['id'] = $board_infos['id']; // �Խ��� id �缳��
			$board_links = http_build_query($query_infos); // php5 �̻�, rankup_basic.class.php �� ���ǵ�

			$this->spermission = @unserialize($board_infos['spermission']); // ��б� ó���� ����
			$this->soption = @unserialize($board_infos['soption']); // ������ ó���� ���� - 2009.01.19 fixed
			foreach($board_datas as $rows) {
				$article_link = "href=\"$this->index_url/index.html?$board_links&no=$rows[no]\"";
				// ��б��� ���
				if($rows['sval']=="yes" && !$this->is_seeable($rows['no']) && !$this->check_granted("secret_level") && (empty($rows['uid']) || (!empty($rows['uid']) && $this->member_id!==$rows['uid']))) {
					$article_link .= " onClick=\"rankup_board.scanf_passwd($rows[no], this, 'article_view', '$board_infos[id]'); return false;\"";
				}

				// ������ ó�� - ÷������ / NEW ������
				$attach_icon = ($this->sattach['use_attach']=="on" && $this->soption['use_attach_icon']=="on" && !empty($rows['attach'])) ? "<img src='".$this->board_url."icon/icon_file.gif' align='absmillde'> " : '';
				$new_icon = ($this->soption['use_new_icon']=="on" && date("Y-m-d H:i:s", strtotime("-{$this->soption['recent_time']} hour"))<=$rows['wdate']) ? " <img src='".$this->board_url."icon/icon_new.gif' align='absmillde'>" : '';
				$secret_icon = $rows['sval']=="yes" ? " <img src='".$this->board_url."icon/icon_secret.gif' align='absmiddle'> " : '';

				// �Խù� ����
				if($rows['dval']=="yes") {
					$subject = "<strike>������ �Խù� �Դϴ�.</strike>";
					$article_link = "href=\"$this->index_url/index.html?$board_links&no=$rows[no]\" style=\"color:#cdcdcd\"";
					if(!$this->is_admin()) $article_link .= " onClick=\"alert('������ �Խù��� ��ȸ�� �� �����ϴ�.'+SPACE); return false;\"";
				}
				else $subject = $this->str_cut($rows['subject'], $slayout['subject_length'], '');

				$subject = "$secret_icon<a $article_link>".$subject."</a>$new_icon"; // 2009.01.19 fixed
				$cnum = $rows['cnum']>0 ? "[{$rows[cnum]}]" : '';
				switch($slayout['print_style']) {
					// ȥ����
					case "both":
						if(count(unserialize($rows['attach'])) && empty($thumbnail)) {
							list($thumbnail_file, $_width, $_height, $_type) = $this->get_thumbnail_image($rows['attach'], $attach_dir);
							if(empty($thumbnail_file)) {
								$thumbnail = "
								<table width='$image_width' height='$image_height' cellspacing='0' cellpadding='0' bgcolor='white'>
								<tr align='center'><td><a $article_link onFocus='blur()'><img src='{$this->board_url}img/no_thumb.gif'></a></td></tr>
								</table>";
							}
							else {
								$thumbnail = "<a $article_link><img src='$attach_url$thumbnail_file' width='$image_width' height='$image_height' align='absmiddle'></a>";
							}
							$thumb_subject = "<a $article_link>$rows[subject]</a>$new_icon";
							continue;
						}
					// �ؽ�Ʈ��
					case "text":
						$main_article_contents .= str_replace(
							array("{:board_skin:}", "{:thumbnail:}", "{:subject:}", "{:cnum:}"),
							array($skin_url, $thumbnail, $subject, $cnum),
							$tpl_buffer);
						break;
					// �̹�����
					case "image":
						list($thumbnail_file, $_width, $_height, $_type) = $this->get_thumbnail_image($rows['attach'], $attach_dir);
						if(empty($thumbnail_file)) {
							$thumbnail = "
							<table width='$image_width' height='$image_height' cellspacing='0' cellpadding='0' bgcolor='white'>
							<tr align='center'><td><a $article_link onFocus='blur()'><img src='{$this->board_url}img/no_thumb.gif'></a></td></tr>
							</table>";
						}
						else if(is_file($attach_dir.$thumbnail_file)) $thumbnail = "<a $article_link><img src='$attach_url$thumbnail_file' width='$image_width' height='$image_height' align='absmiddle'></a>";
						$_main_article_contents .= str_replace(
							array("{:board_skin:}", "{:thumbnail:}", "{:subject:}", "{:cnum:}"),
							array($skin_url, $thumbnail, $subject, $cnum),
							$tpl_buffer);
						if(!(++$column_count%$slayout['article_rows'])) {
							$main_article_contents .= "<tr>$_main_article_contents</tr>";
							unset($_main_article_contents);
						}
						break;
				}
			} // @eo  foreach($board_datas as $rows)

			// �� ä��� - �̹������� ��쿡�� ���� ��
			if(isset($_main_article_contents)) {
				$_tds = str_repeat("<td><table width='$image_width' height='$image_height' cellspacing='0' cellpadding='0' bgcolor='white'><tr align='center'><td>&nbsp;</td></tr></table></td>", $slayout['article_rows']-($column_count%$slayout['article_rows']));
				$main_article_contents .= "<tr>$_main_article_contents$_tds</tr>";
				unset($_main_article_contents);
			}
		}

		// �Խ��� �̸��� ��ũ ����
		$board_infos['name'] = "<a href='$this->index_url/index.html?id=$board_infos[id]' class='main_board_title'>$board_infos[name]</a>";

		// �÷� ��Ÿ�� ���� - ���ٿ� �Խ����� 1��/2�� ����� ��� td ��Ÿ�� ����
		$column_style = $category_infos->mbnum==2 ? !($column%2) ? " style='padding-right:7px;'" : " style='padding-left:7px;'" : " colspan='2'";
		// ��Ų URL
		$this->skin_url = $this->board_url."skin/main/".$category_infos->mskin."/";
		ob_start();
		include $this->get_board_template($this->get_skin_dir($category_infos->mskin)."/article_list.tpl.html", "main");
		return ob_get_clean();
	}

	// BEST �Խù� ���� - �����ƾ - 2009.10.28 fixed
	function _get_formalize_articles($rows, $string=true) {
		$board_table = $this->board_prefix.$rows['bid'];
		$board_datas = $this->queryFetch("select b.uid, b.subject, b.cno, b.cnum, b.sval, b.dval, c.sfunction, c.scategory, c.spermission from $board_table as b, $this->bconfig_table as c where c.id='$rows[bid]' and c.uval='yes' and b.no=$rows[ano]");
		if(!empty($board_datas['cno'])) {
			$sfunction = @unserialize($board_datas['sfunction']);
			if($sfunction['use_category']=="on") {
				$scategory = @unserialize($board_datas['scategory']);
				$category = $scategory[$board_datas['cno']]['name'];
			}
		}
		$category = empty($category) ? '' : "[$category] ";
		$cnum = $board_datas['cnum']>0 ? " <span class='num'>[{$board_datas[cnum]}]</span>" : '';

		// �Խù� ��ũ ����
		parse_str($_SERVER['QUERY_STRING'], $query_infos);
		unset($query_infos['no'], $query_infos['pcno']); // �Խù� ��ȣ/�����޴� ��ȣ�� ����
		$query_infos['id'] = $rows['bid']; // �Խ��� id �缳��
		$board_links = http_build_query($query_infos); // php5 �̻�, rankup_basic.class.php �� ���ǵ�
		$article_link = "href=\"$this->index_url/index.html?$board_links&no=$rows[ano]\"";

		// �۷ι� ���� ���� - 2009.10.28 added
		$keep_spermission = $this->spermission;

		// ��б��� ���
		$this->spermission = @unserialize($board_datas['spermission']);
		if($board_datas['sval']=="yes" && !$this->is_seeable($rows['ano']) && !$this->check_granted("secret_level") && (empty($board_datas['uid']) || (!empty($board_datas['uid']) && $this->member_id!==$board_datas['uid']))) {
			$article_link .= " onClick=\"rankup_board.scanf_passwd($rows[ano], this, 'article_view', '$rows[bid]'); return false;\"";
		}
		$secret_icon = $board_datas['sval']=="yes" ? " <img src='".$this->board_url."icon/icon_secret.gif' align='absmiddle'> " : '';

		// �Խù� ����
		if($board_datas['dval']=="yes") {
			$subject = "<strike>������ �Խù� �Դϴ�.</strike>";
			$article_link = "href=\"$this->index_url/index.html?$board_links&no=$rows[ano]\" style=\"color:#cdcdcd\"";
			if(!$this->is_admin()) $article_link .= " onClick=\"alert('������ �Խù��� ��ȸ�� �� �����ϴ�.'+SPACE); return false;\"";
		}
		else $subject= $board_datas['subject'];
		$subject = "$category$secret_icon<a $article_link>$subject</a>";

		// �۷ι� ���� ���� - 2009.10.28 added
		$this->spermission = $keep_spermission;

		return ($string===true) ? $subject.$cnum : array($subject, $cnum);
	}

	// BEST �Խù�
	function get_best_articles($mode, $pcno=false) { // pcno : ������������ ��� ���� �޴��߿��� ����Ʈ�� �̴´�.
		$addWhere = $pcno!==false? "b.pcno=$pcno" : "b.bid='$this->board_id'";
		switch($mode) {
			// �ְ� ����Ʈ
			case "weekly_best":
				$limit = $this->main_layout['sprint']['wbest_num']; // 2009.08.31 added
				$best_datas = $this->queryFetchRows("select b.bid, b.adno, b.ano, sum(b.hnum) as hnums from $this->weekly_best_table as b, $this->bconfig_table as c where b.pcno=$pcno and b.bid=c.id and c.uval='yes' group by b.bid, b.ano order by hnums desc limit 0, $limit");
				if($this->check_resource($best_datas)) foreach($best_datas as $rows) $result .= "<tr><td nowrap><img src='".$this->skin_url."icon_arrow.gif'>".$this->_get_formalize_articles($rows)."</td></tr>";
				if(empty($result)) $result = "<tr><td nowrap><img src='".$this->skin_url."icon_arrow.gif'>��ϵ� ����Ʈ �Խù��� �����ϴ�.</td></tr>";
				break;

			// ��ȸ�� ����Ʈ
			case "hit_best":
				if($pcno!==false) $limit = $this->main_layout['sprint']['hcbest_num']; // ���������� - 2009.08.31 added
				else $limit = $this->soption['hit_best_num']; // �Խ��Ǹ��/��������
				$best_datas = $this->queryFetchRows("select b.bid, b.adno, b.ano, b.ahnum from $this->hit_best_table as b, $this->bconfig_table as c where $addWhere and b.bid=c.id and c.uval='yes' group by b.bid, b.ano order by b.ahnum desc limit 0, $limit");
				if($this->check_resource($best_datas)) foreach($best_datas as $rows) $result .= "<tr><td nowrap style='overflow:hidden;text-overflow:ellipsis;'><img src='".$this->skin_url."icon_arrow.gif'>".$this->_get_formalize_articles($rows)."</td></tr>";
				if(empty($result)) $result = "<tr><td nowrap><img src='".$this->skin_url."icon_arrow.gif'>��ϵ� ����Ʈ �Խù��� �����ϴ�.</td></tr>";
				break;

			// ��� ����Ʈ
			case "comment_best":
				$limit = $this->main_layout['sprint']['hcbest_num']; // 2009.08.31 added
				$best_datas = $this->queryFetchRows("select b.bid, b.adno, b.ano, b.acnum from $this->comment_best_table as b, $this->bconfig_table as c where $addWhere and b.bid=c.id and c.uval='yes' order by b.acnum desc limit 0, $limit");
				if($this->check_resource($best_datas)) foreach($best_datas as $rows) $result .= "<tr><td nowrap style='overflow:hidden;text-overflow:ellipsis;'><img src='".$this->skin_url."icon_arrow.gif'>".$this->_get_formalize_articles($rows)."</td></tr>";
				if(empty($result)) $result = "<tr><td nowrap><img src='".$this->skin_url."icon_arrow.gif'>��ϵ� ����Ʈ �Խù��� �����ϴ�.</td></tr>";
				break;

			// �ְ� ��ۼ� ����Ʈ
			case "weekly_cbest":
				if($this->board_extension!==true) return '';
				$limit = $this->main_layout['sprint']['wcbest_num']; // 2009.08.31 modified
				$best_datas = $this->queryFetchRows("select b.uid, sum(b.cnum) as cnums, m.name from $this->weekly_cbest_table as b, $this->member_table as m, $this->bconfig_table as c where m.uid=b.uid and b.bid=c.id and c.uval='yes' group by b.uid order by cnums desc limit 0, $limit");
				if($this->check_resource($best_datas)) {
					$count = 0;
					foreach($best_datas as $rows) {
						$result .= "<tr><td nowrap><img src='".$this->base_url."images/rank_num".sprintf("%02d", ++$count).".gif'> $rows[name]</td><td align='center'>$rows[cnums]</td></tr>";
					}
				}
				break;
		}
		return $result;
	}

	// �ű� �Խù�
	function get_new_articles($category_infos, $pcno='') {
		$limit = $this->main_layout['sprint']['narticle_num']; // 2009.08.31 added
		$f_addWhere = !in_array($pcno, array('', null)) ? "n.pcno=$pcno and" : '';
		$new_datas = $this->queryFetchRows("select n.bid, n.ano from $this->new_article_table as n, $this->bconfig_table as c where $f_addWhere n.bid=c.id and c.uval='yes' order by n.no desc limit 0, $limit");
		if($this->check_resource($new_datas)) {
			$skin_url = $this->board_url."skin/main/".$category_infos->mskin."/";
			$skin_dir = $this->board_dir."skin/main/".$this->get_skin_dir($category_infos->mskin)."/";
			$tpl_buffer = $this->get_template_item($skin_dir."__text.tpl");
			foreach($new_datas as $rows) {
				list($subject, $cnum) = $this->_get_formalize_articles($rows, false);
				$result .= str_replace(
					array("{:board_skin:}", "{:subject:}", "{:cnum:}"),
					array($skin_url, $subject, $cnum),
					$tpl_buffer);
			}
		}
		if(empty($result)) $result = "<tr><td nowrap>��ϵ� �Խù��� �����ϴ�.</td></tr>";
		return $result;
	}


##########################################################################
## ����������� - �Խ��� �κ�
##########################################################################
	// �Խ��� ���� ������Ʈ
	function update_board($datas) {
		switch($datas['cmd']) {
			// �Խ��� ���� �缳��
			case "set_direction":
				foreach($_POST['rank'] as $rank => $bno) {
					$_val['rank'] = $rank+1;
					$values = $this->change_query_string($_val);
					$this->query("update $this->bconfig_table set $values where no=$bno");
				}
				return true;
				break;

			// �Խ��� ��뿩�� ���� - 2009.08.28 added
			case "set_used":
				$this->query("update $this->bconfig_table set uval='$datas[use]' where id='$datas[id]'");
				return true;
				break;

			// �Խù� �� �缳�� - �Խù� ���/������ ���
			case "set_anum":
				if($datas['plus_mode']===true) return $this->query("update $this->bconfig_table set anum=anum+1 where id='$this->board_id'");
				else return $this->query("update $this->bconfig_table set anum=if(anum=0, 0, anum-1) where id='$this->board_id'");
				break;

			// ���������� ���� �缳��
			case "set_layout":
				// ���������� ����
				if($datas['category']=="main") {
					if($this->check_resource($datas['subject_length'])) {
						foreach(array_keys($datas['subject_length']) as $bno) {
							$_val['mval'] = @in_array($bno, $datas['bno']) ? "yes" : "no";		// ��¿���
							$_val['smlayout'] = serialize(array(
								"subject_length" => $datas['subject_length'][$bno],				// �������
								"article_rows" => $datas['article_rows'][$bno],						// �Խù���
								"image_width" => $datas['image_width'][$bno],						// �̹��� ����ũ��
								"image_height" => $datas['image_height'][$bno],					// �̹��� ����ũ��
								"print_style" => $datas['print_style'][$bno]							// �������
							));
							$values = $this->change_query_string($_val);
							$this->query("update $this->bconfig_table set $values where no=$bno");
						}
					}
					// ���������� ȯ�� ����
					$_xVal['smlayout'] = serialize(array(
						"mskin" => $datas['main_skin'],				// ���������� ��Ų
						"mbnum" => $datas['mbnum'],					// ���ٿ� ����� �Խ��� ��
						"sprint" => array(
							"narticle" => $datas['narticle'],				// �ű� �Խù�
							"narticle_num" => $datas['narticle_num']	// �ű� �Խù� ��°��� - 2009.08.28 added
						)
					));
					$values = $this->change_query_string($_xVal);
					$this->query("update $this->sconfig_table set $values");
				}
				// �޴��� ���������� ����
				else {
					// ���������� ��뿩�� - pcmval, mbno --> $this->category_table �� ����
					if($datas['pcmval']=="yes") {
						foreach(array_keys($datas['subject_length']) as $bno) {
							$_val['pcmval'] = @in_array($bno, $datas['bno']) ? "yes" : "no";	// ��¿���
							$_val['spcmlayout'] = serialize(array(
								"subject_length" => $datas['subject_length'][$bno],				// �������
								"article_rows" => $datas['article_rows'][$bno],						// �Խù���
								"image_width" => $datas['image_width'][$bno],						// �̹��� ����ũ��
								"image_height" => $datas['image_height'][$bno],					// �̹��� ����ũ��
								"print_style" => $datas['print_style'][$bno]							// �������
							));
							$values = $this->change_query_string($_val);
							$this->query("update $this->bconfig_table set $values where no=$bno");
						}
						$_xVal['lskin'] = $datas['left_skin'];			// ���� ��Ų
						$_xVal['mskin'] = $datas['main_skin'];		// ���������� ��Ų
						$_xVal['mbnum'] = $datas['mbnum'];		// ���ٿ� ����� �Խ��� ��
						$_xVal['sprint'] = serialize(array(
							"wbest" => $datas['wbest'],				// �̹��� ����Ʈ
							"hcbest" => $datas['hcbest'],				// ��ȸ��/��ۼ� ����Ʈ
							"narticle" => $datas['narticle'],			// �ű� �Խù�
							// ��°��� - 2009.08.28 added
							"wbest_num" => $datas['wbest_num'],		// �̹��� ����Ʈ
							"hcbest_num" => $datas['hcbest_num'],	// ��ȸ��/��ۼ� ����Ʈ
							"narticle_num" => $datas['narticle_num']	// �ű� �Խù�
						));
					}
					else {
						$_xVal['lskin'] = $datas['left_skin'];			// ���� ��Ų
						$_xVal['mbno'] = $datas['mbno'];				// ���ΰԽ���
					}
					$_xVal['mval'] = $datas['pcmval'];
					$values = $this->change_query_string($_xVal);
					$this->query("update $this->category_table set $values where no=$datas[category]");
				}
				return true;
				break;

			// �Խ��� �з� �缳��
			case "set_category":
				// �������� ��
				$scategory = $this->get_board_config($datas['bno'], "scategory");
				$scategory = @unserialize($scategory);
				if(!is_array($scategory)) $scategory = array();
				$_scategory = $this->sort_scategory($scategory);
				$rank = 1;
				$rank += $this->check_resource($_scategory) ? max(array_keys($_scategory)) : 0;
				$datas['canum'] = in_array($datas['cno'], array_keys($scategory)) ? $scategory[$datas['cno']]['anum'] : 0;
				if(empty($datas['cname'])) unset($scategory[$datas['cno']]); // �з� ����
				else { // ���/����
					$scategory[$datas['cno']] = array(
						"name" => $datas['cname'],						// �з���
						"anum" => $datas['canum'],						// ��ϵ� �Խù� ��
						"rank" => $rank										// ���� - 2009.09.18 added
					);
				}
				$_val['scategory'] = serialize($scategory);
				break;

			// ���� �缳��
			case "set_permission":
				$_val['spermission'] = serialize(array(
					"list_level" => $datas['list_level'],					// ����Ʈ ���� ����
					"read_level" => $datas['read_level'],				// �Խù� �б� ����
					"write_level" => $datas['write_level'],				// �Խù� ���� ����
					"delete_level" => $datas['delete_level'],			// �Խù� ���� ����
					"comment_level" => $datas['comment_level'],	// �ڸ�Ʈ ���� ����
					"reply_level" => $datas['reply_level'],				// �亯�� ���� ����
					"notice_level" => $datas['notice_level'],			// ������ ���� ����
					"secret_level" => $datas['secret_level']			// ��б� �б� ����
				));
				break;

			// ����Ʈ �缳��
			case "set_point":
				// �Խ��� �� ����
				$_val['spoint'] = serialize(array(
					"write_point" => @in_array("write", array_values($datas['event'])) ? $datas['minus']['write']=="on" ? -(abs($datas['write_point'])) : abs($datas['write_point']) : '', // �Խù� ����
					"read_point" => @in_array("read", array_values($datas['event'])) ? $datas['minus']['read']=="on" ? -(abs($datas['read_point'])) : abs($datas['read_point']) : '', // �Խù� �б�
					"comment_point" => @in_array("comment", array_values($datas['event'])) ? $datas['minus']['comment']=="on" ? -(abs($datas['comment_point'])) : abs($datas['comment_point']) : '', // �ڸ�Ʈ ����
					"reply_point" => @in_array("reply", array_values($datas['event'])) ? $datas['minus']['reply']=="on" ? -(abs($datas['reply_point'])) : abs($datas['reply_point']) : '', // �亯�� ����
					"vote_point" => @in_array("vote", array_values($datas['event'])) ? $datas['minus']['vote']=="on" ? -(abs($datas['vote_point'])) : abs($datas['vote_point']) : '', // �Խù� ��õ
					"upload_point" => @in_array("upload", array_values($datas['event'])) ? $datas['minus']['upload']=="on" ? -(abs($datas['upload_point'])) : abs($datas['upload_point']) : '', // ���� ���ε�
					"download_point" => @in_array("download", array_values($datas['event'])) ? $datas['minus']['download']=="on" ? -(abs($datas['download_point'])) : abs($datas['download_point']) : '' // ���� �ٿ�ε�
				));
				break;

			// �޴��� �Խ��� �̵�
			case "move_board":
				$datas['bno'] = explode("__", $datas['datas']);
				$_val['pcno'] = $datas['pcno'];
				$_val['cno'] = $datas['cno'];
				if(empty($datas['cno'])) $_val['cno'] = $datas['pcno']; // ����ī�װ��� �����ϰ� ����

				$rank = $this->queryR("select rank from $this->bconfig_table where cno=$_val[cno] order by rank desc limit 1");
				$_val['rank'] = $rank+1; // ������ ��� �����ϰ� ����

				$values = $this->change_query_string($_val);
				$addWhere = count($datas['bno'])>1 ? "no in (".@implode(",", $datas['bno']).")" : "no=$datas[datas]";
				$this->query("update $this->bconfig_table set $values where $addWhere");

				// �̵��ϴ� �� ī�װ�(�޴�)�� bval �� no �ΰ�� ó��
				$menu_infos = $this->queryFetch("select bval, mbno, mskin, lskin from $this->category_table where no=$_val[cno]");
				if($menu_infos['bval']=="no") {
					$_mbno = count($datas['bno'])>1 ? array_shift($datas['bno']) : $datas['datas'];
					$this->query("update $this->category_table set bval='yes', mbno=$_mbno where no=$_val[cno] and bval='no'");
				}

				// �ش� ī�װ��� �Խ����� �������� ���� ��� bval, mbno �ʱ�ȭ
				$board_no = $this->queryR("select no from $this->bconfig_table where cno=$datas[prev_cno] order by rank");
				if(empty($board_no)) $this->query("update $this->category_table set bval='no', mbno=NULL where no=$datas[prev_cno]");
				else {
					$addWhere = count($datas['bno'])>1 ? " and mbno in (".@implode(",", $datas['bno']).")" : " and mbno=$datas[datas]";
					$this->query("update $this->category_table set mbno=$board_no where no=$datas[prev_cno]$addWhere"); // mbno ����
				}
				return true;
				break;

			// �з��� �Խù� �̵�
			case "move_articles":
				$cnos = explode("__", $datas['datas']);
				$board_datas = $this->get_board_config($datas['bno'], "id, scategory");
				// ��ϵ� �Խù� �� �ʱ�ȭ
				$scategory = @unserialize($board_datas['scategory']);
				foreach($scategory as $key=>$val) if(in_array($key, $cnos)) $scategory[$key]['anum'] = 0;

				@include "scheme/rankup_board_scheme.inc.html";
				$board_table_name = str_replace("scheme", $board_datas['id'], array_shift(array_keys($_BOARD_TABLES)));
				$this->query("update $board_table_name set cno=$datas[cno] where cno in(".@implode(",", $cnos).")");

				// �з��� �Խù� �� ��ī��Ʈ
				$scategory[$datas['cno']]['anum'] = $this->queryR("select count(cno) from $board_table_name where cno=$datas[cno]");
				$_val['scategory'] = serialize($scategory);
				break;
		}
		$values = $this->change_query_string($_val);
		$addWhere = is_array($datas['bno']) ? "no in (".@implode(",", $datas['bno']).")" : "no=$datas[bno]";
		return $this->query("update $this->bconfig_table set $values where $addWhere");
	}

	// ���� �޴� ���
	function print_left_menu($skin='') {
		$pcno = $this->board_configs['pcno'] ? $this->board_configs['pcno'] : $_GET['pcno'];
		if(in_array($pcno, array('', "main"))) unset($pcno);
		if($this->board_extension===true && empty($pcno)) return false;
		else if(empty($pcno)) {
			$pcno = $this->queryR("select no from $this->category_table where pno=0 and dval='no' order by rank limit 0,1");
			if(empty($pcno)) $this->popup_msg_js("�Խ����� �������� �ʽ��ϴ�.", "BACK");
		}
		if(empty($pcno)) return false; // pcno �� ���� ��� �����޴� ��Ȱ��ȭ

		$category_infos = $this->queryFetchObject("select no, content, mbno, mval, lskin, mbnum from $this->category_table where no=$pcno and uval='yes' and dval='no'");
		if(empty($category_infos->lskin)) $this->popup_msg_js("�Խ��� ���� �޴��� ��Ų�� �������� �ʾҽ��ϴ�.", "BACK");
		$left_skin_url = $this->board_url."skin/left/".$category_infos->lskin."/";
		$left_skin_dir = $this->board_dir."skin/left/".$this->get_skin_dir($category_infos->lskin)."/";

		$board_datas = $this->queryFetchRows("select c.no, c.id, c.name, c.cno, c.pcno, c.soption from $this->category_table as m1, $this->category_table as m2, $this->bconfig_table as c where c.pcno=$pcno and c.uval='yes' and (m1.no=c.pcno and m1.pval='yes' and m2.no=c.cno and m2.pval='yes') order by m1.rank, m2.pno, m2.rank, m1.pno, c.rank");
		if($this->check_resource($board_datas)) {
			$tpl_item_buffer = $this->get_template_item($left_skin_dir."__item.tpl");
			$left_menu_contents = '';
			$sub_menu_division = str_replace("{:left_menu_skin:}", $left_skin_url, $this->get_template_item($left_skin_dir."__division.tpl"));
			foreach($board_datas as $board_infos) {
				// new icon - 2010.06.23 added
				$new_icon = '';
				$soptions = @unserialize($board_infos['soption']);
				if($soptions['use_new_icon']=="on") {
					$_new_articles = $this->queryR("select count(no) from ".$this->board_prefix.$board_infos['id']." where wdate>=date_sub(curdate(), interval ".$soptions['recent_time']." hour)");
					if($_new_articles) $new_icon = " <img src='".$this->board_url."icon/icon_new.gif' align='absmillde'>";
				}
				$board_name = "<a href='$this->index_url/index.html?id=$board_infos[id]'>$board_infos[name]</a>".$new_icon;
				// �޴��� �ٲ�� ����
				if($old_menu_cno != $board_infos['cno']) {
					if(!empty($left_menu_contents)) $left_menu_contents .= $sub_menu_division;
					if($old_menu_cno) {
						if($board_infos['cno']!=$board_infos['pcno']) $menu_name = $this->queryR("select content from $this->category_table where no=$old_menu_cno");
						ob_start();
						include $this->get_board_template($this->get_skin_dir($category_infos->lskin)."/menu_item.tpl.html", "left");
						$left_menu_contents .= ob_get_clean();
						unset($board_item_contents, $menu_name);
					}
					$old_menu_cno = $board_infos['cno'];
				}
				$board_item_contents .= str_replace(
					array("{:left_menu_skin:}", "{:board_name:}"),
					array($left_skin_url, $board_name),
					$tpl_item_buffer);
			}
			if(isset($board_item_contents)) {
				if(!empty($left_menu_contents)) $left_menu_contents .= $sub_menu_division;
				if($old_menu_cno && $board_infos['cno']!=$board_infos['pcno']) $menu_name = $this->queryR("select content from $this->category_table where no=$old_menu_cno");
				ob_start();
				include $this->get_board_template($this->get_skin_dir($category_infos->lskin)."/menu_item.tpl.html", "left");
				$left_menu_contents .= ob_get_clean();
				unset($board_item_contents);
			}
		}
		// �޴� ��ũ ����
		$category_name = "<a href='$this->index_url/index.html?pcno=$category_infos->no'>$category_infos->content</a>";
		ob_start();
		include $this->get_board_template($this->get_skin_dir($category_infos->lskin)."/left_menu.tpl.html", "left");
		return ob_get_clean();
	}

	// �Խ��� �з� ���� - ������ - 2009.09.18 added
	function sort_scategory($categories) {
		$scategory = array();
		foreach($categories as $cno=>$rows) {
			if(!$rows['name']) continue;
			$scategory[$rows['rank']] = array(
				'cno' => $cno,
				'anum' => $rows['anum'],
				'name' => $rows['name']
			);
		}
		ksort($scategory);
		return $scategory;
	}

	// ����� �̹��� ����
	function get_thumbnail_image($attach, $attach_dir) {
		$thumbnail_file = '';
		$attaches = @unserialize($attach);
		if(!$this->check_resource($attaches)) return false;
		foreach($attaches as $file) {
			if(!is_file($attach_dir.$file['sname'])) continue;
			list($image_width, $image_height, $image_type) = @getimagesize($attach_dir.$file['sname']);
			if(!$image_width || !$image_height || !in_array($image_type, array(1,2,3,6))) continue; // jpg, png �� �����
			$thumbnail_file = is_file($attach_dir."thumb_".$file['sname']) ? "thumb_".$file['sname'] : $file['sname'];
			break;
		}
		return array($thumbnail_file, $image_width, $image_height, $image_type);
	}

	//���ۼ��� ȸ������ ��ȯ
	function get_writer_infos($datas) {
		global $config_info;
		if(is_object($datas)) { //�����͸� ȣ���ϴ� ��ġ�� ���� �����ؼ� ���
			$uid = $datas->uid;
			$unick = $datas->unick;
		} else {
			$uid= $datas['uid'];
			$unick = $datas['unick'];
		}
		if(empty($uid)) {		//��ȸ���ΰ��
			$return_data = $unick;
		} else if($uid == "_admin_") {		//��ȸ���ΰ��
			$return_data = $unick;
		} else {
			if($this->sfunction['use_writer'] == "uid") $return_data = $uid ? $uid : $unick;
			else $return_data = $unick;
		}
		return $return_data;
	}

	// ����Ʈ ���� - ���� - 2009.09.18 modified
	function formalize_board_articles($datas, $view_page=false) { // view_page : �������� ���� ����
		if($this->sfunction['use_category']=="on") {
			$categories = @unserialize($this->board_configs['scategory']);
			if($this->check_resource($categories)) {
				$scategory = $this->sort_scategory($categories); // 2009.09.18 added
				$scategories = "<option value=''>��ü�з�</option>";
				foreach($scategory as $rank=>$val) {
					$_selected = $datas['scategory']==$val['cno'] ? " selected" : '';
					$scategories .= "<option value='$val[cno]'$_selected>$val[name]</option>";
				}
			}
			else $scategories = "<option value=''>�з�����</option>";
			$article_column = "<select name='scategory' onChange=\"rankup_board.search_category(this)\">".$scategories."</select>";
			if($this->board_configs['style']!='webzin') {
				$article_column = "<td class='board_title' width='120' background='".$this->skin_url."title_bg.gif' align='left'>$article_column</td>";
			}
		}
		if(!$this->board_configs['style']) $this->board_configs['style'] = "normal";
		if(in_array($this->board_configs['style'], array('gallery', 'webzin'))) { // 2009.06.22 fixed
			$sgallery = unserialize($this->board_configs['sgallery']);
			$gallery_cell_width = $sgallery['thumb_nums']>1 ? 100/$sgallery['thumb_nums'] : 3;
			$board_category_view = $this->sfunction['use_category']=="on" ? "block" : "none";
			$attach_dir = $this->board_dir."attach/".$this->board_id."/";
			$attach_url = $this->board_url."attach/".$this->board_id."/";
			$column_count = 0;
		}

		// ���������� ���� ��� �ϴܿ� ���û��� ��ư ����
		if($view_page===false && $this->check_granted("delete_level")) {
			$sdelete_button = "<a onClick=\"rankup_board.check_all($('sAllButton').src.indexOf('bt_sall.gif')!=-1, $('sAllButton'))\"><img id='sAllButton' src='".$this->board_url."img/bt_sall.gif' align=''></a> <a onClick=\"rankup_board.articles_delete()\"><img src='".$this->board_url."img/bt_sdel.gif' align=''></a>";
		}

		// ���ø� �������� �ε�
		if($this->board_configs['style'] == "mantoman") $tpl_buffer = $this->get_template_item($this->skin_dir."__normal.tpl");
		else $tpl_buffer = $this->get_template_item($this->skin_dir."__".$this->board_configs['style'].".tpl");

		if(empty($datas['page'])) $datas['page'] = 1;
		$spos = $datas['page']>1 ? ($datas['page']-1)*$this->slayout['article_rows'] : 0;

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
		// �˻�� ������ ���
		if(!empty($datas['skey'])) {
			switch($datas['smode']) {
				case "both": // ����+���� �˻�
					$addWhere .= " and (subject like '%$datas[skey]%' or content like '%$datas[skey]%')";
					break;
				case "subject": // ���� �˻�
					$addWhere .= " and (subject like '%$datas[skey]%')";
					break;
				case "author": // �ۼ��� �˻�
					$addWhere .= " and (unick like '%$datas[skey]%')";
					break;
			}
		}
		// �з��� ������ ���
		if($this->sfunction['use_category']=="on" && $this->check_resource($categories) && !empty($datas['scategory'])) $addWhere .= " and cno=$datas[scategory]";

		// ���� ���� ������ ���
		if(!empty($datas['asort'])) {
			if(empty($datas['skey'])) { // dno �߰� @###################
				//
			}
		}
		switch($datas['asort']) {
			case "hit": $orderBy = " hnum desc"; break;
			case "hot": $orderBy = " cnum desc"; break;
			//case "vote": $orderBy = " vnum desc"; break;
			case "good": $orderBy = " gnum desc"; break;
			case "bad": $orderBy = " bnum desc"; break;
			case "recent": unset($datas['asort']); // $orderBy = " wdate desc"; break; // ���Ĺ�� �ʱ�ȭ �������� ����
			default: $orderBy = " sno, gno";
		}

		// ���������� �϶����� ����������� �Խù��� ���� -- �����ڰ� �ƴѰ�츸 ����
		if($this->board_configs['style']=="gallery" && !$this->is_admin()) $addWhere .= " and dval='no'";

		// �Խ��� ��ũ ����
		parse_str($_SERVER['QUERY_STRING'], $query_infos);
		$query_infos['id'] = $this->board_id; // �Խ��� id �缳��
		unset($query_infos['pcno']); // �޴� ��ȣ ����
		$_SERVER['QUERY_STRING'] = http_build_query($query_infos); // 2009.10.14 fixed
		$_SERVER['REQUEST_URI'] = "$this->index_url/index.html?".http_build_query($query_infos); // ����¡ �⺻����
		unset($query_infos['no']); // �Խù� ��ȣ ����
		$board_links = http_build_query($query_infos);

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
		$total_articles = $this->queryR("select count(no) from $this->board_table where no$addWhere");
		$paging_button = $this->print_paging($total_articles, array($this->slayout['article_rows'], 10), 'page');
		$anum = $total_articles-($spos);

		$board_datas = $this->queryFetchRows("select no,sno,gno,pno,subject,cno,uid,unick,content,attach,cnum,hnum,gnum,bnum,wdate,sval,dval from $this->board_table where no$addWhere order by $orderBy limit $spos, ".$this->slayout['article_rows']);
		if($this->check_resource($board_datas)) {
			foreach($board_datas as $rows) {
				$rows['unick'] = $this->get_writer_infos($rows);
				$_category = $this->sfunction['use_category']=="on" ? $categories[$rows['cno']]['name'] : '';
				$article_link = "href=\"./index.html?$board_links&no=$rows[no]\""; // $this->index_url - 2009.09.09 without

				//��б��ϰ�� ���ʵ���ڱ��� ������ ������´�
				if($rows['pno']>0 && $rows['sval']=="yes") $read_writer = $this->queryR("select uid from $this->board_table where sno = $rows[sno] and pno = 0 and uid='$this->member_id'");
				else $read_writer = null;

				// ��б��� ���
				if($rows['sval']=="yes" && !$this->is_seeable($rows['no']) && !$this->check_granted("secret_level") && (empty($rows['uid']) || (!empty($rows['uid']) && $this->member_id!==$rows['uid']))) {
					if($read_writer == null || $read_writer !== $this->member_id) {
						$article_link .= " onClick=\"rankup_board.scanf_passwd($rows[no], this, 'article_view'); return false;\"";
					}
				}

				// ������ ó�� - ÷������ / NEW ������
				$attach_icon = ($this->sattach['use_attach']=="on" && $this->soption['use_attach_icon']=="on" && !empty($rows['attach'])) ? "<img src='".$this->board_url."icon/icon_file.gif' align='absmillde'> " : '';
				$new_icon = ($this->soption['use_new_icon']=="on" && date("Y-m-d H:i:s", strtotime("-{$this->soption['recent_time']} hour"))<=$rows['wdate']) ? " <img src='".$this->board_url."icon/icon_new.gif' align='absmillde'>" : '';
				$secret_icon = $rows['sval']=="yes" ? " <img src='".$this->board_url."icon/icon_secret.gif' align='absmiddle'> " : '';

				// �Խù� �ۼ� �Ͻ�
				list($_date, $_time) = explode(" ", $rows['wdate']);
				$rows['wdate'] = ($_date == date("Y-m-d")) ? $_time : $_date;

				// �Խù� ����
				if($rows['dval']=="yes") {
					$subject = "<strike>������ �Խù� �Դϴ�.</strike>";
					$article_link = "href=\"./index.html?$board_links&no=$rows[no]\" style=\"color:#cdcdcd\""; // $this->index_url - 2009.09.09 without
					if(!$this->is_admin()) $article_link .= " onClick=\"alert('������ �Խù��� ��ȸ�� �� �����ϴ�.'+SPACE); return false;\"";
				}
				else {
					$subject= $this->str_cut($rows['subject'], $this->slayout['subject_length'], ($this->slayout['use_condense']=="on" ? "..." : ''));
					// �˻��� ���� ���� - str_cut ���� �˻��� ��ü�� �߷� ������ ���� ����
					if(!empty($datas['skey']) && in_array($datas['smode'], array("both", "subject"))) {
						$subject = str_replace($datas['skey'], "<font color='#FF6600'>$datas[skey]</font>", $subject);
					}
				}

				// ÷������ �� ������� ������ ���Ϸ� ����
				$thumbnail = '';
				list($thumbnail_file, $_width, $_height, $_type) = $this->get_thumbnail_image($rows['attach'], $attach_dir);
				if(!empty($thumbnail_file)) {
					// ���� ��� ���
					if($_width>$sgallery['thumb_width']) {
						$_height = $_height / ($_width / $sgallery['thumb_width']);
						$_width = $sgallery['thumb_width'];
					}
					// ���� ��� ���
					if($_height>$sgallery['thumb_height']) {
						$_width = $_width / ($_height / $sgallery['thumb_height']);
						$_height = $sgallery['thumb_height'];
					}
					$thumbnail_file = $attach_url.$thumbnail_file;
					// ����� �̹��� ����
					$thumbnail = "<a $article_link><img src='$thumbnail_file' width='$_width' height='$_height' align='absmiddle'></a>";
				}
				else if(in_array($this->board_configs['style'], array('gallery', 'webzin'))) {
					$thumbnail_file = $this->board_url."img/no_thumb.gif";
					$_width = $sgallery['thumb_width'];
					$_height = $sgallery['thumb_height'];
					// ����� �̹��� ����
					$thumbnail = "
					<div style='width:{$_width}px;height:{$_height}px;background-color:white;'>
						<table width='100%' height='100%' cellspacing='0' cellpadding='0'>
						<tr align='center'><td><a $article_link onFocus='blur()'><img src='$thumbnail_file'></a></td></tr>
						</table>
					</div>";
				}

				// �Ϲ��� / �������� ���
				if(in_array($this->board_configs['style'], array('normal','mantoman', 'webzin'))) {
					$_anum = $anum;

					// ���� ���� �ִ� �Խù��� ���
					if($datas['no'] == $rows['no']) $_anum = "<b style='color:black'>��</b>";

					// ����� ��� - �Խù� ���� ���ʿ� �����߰�
					if(empty($datas['asort'])) { // ���Ĺ���� ������ ��� ���� �鿩���� ���� ����
						if($rows['pno']==0) $reply_icon = '';
						else {
							$reply_icon = $this->soption['use_reply_icon']=="on" ? " <img src='".$this->board_url."icon/icon_re.gif' align='absmillde'>" : "��";
							$reply_icon = "<span style='margin-left:".(10*$rows['pno']).";margin-right:4px;'>$reply_icon</span>";
						}
					}
					// ��ۼ�
					$cnum = $rows['cnum']>0 ? " [{$rows[cnum]}] " : '';

					// ���������� ���� ��� ��ȣ ���� üũ�ڽ��� ����
					if($view_page===false && $this->check_granted("delete_level")) $_anum = "<input onFocus='blur()' type='checkbox' name='ano[]' value='$rows[no]' class='scheckbox'>";

					// �Ϲ����� ��� - ����� ����
					if(in_array($this->board_configs['style'], array('normal', 'mantoman'))) {
						// �Խ��� �з�
						if(!empty($_category)) $_category = "<td align='left'>$_category</td>";
						else if($this->sfunction['use_category']=="on") $_category = "<td align='left'>&nbsp;</td>";

						// �Խù� ���� ����
						$subject = "$reply_icon$secret_icon$attach_icon<a $article_link>".$subject."</a>$cnum$new_icon";

						// ������ ó��
						if($rows['sno']<=$this->notice_sno) {
							$_anum = "<b>����</b>";
							$subject = "<b>$subject</b>";
							if(!empty($_category)) $_category = "<td>&nbsp;</td>";
							// �������� ������쿡�� ���� ����
							if(empty($datas['asort'])) $notice_class = " class='notice'";
						}
						else $notice_class = '';
						// ��õ��
						if($this->sfunction['use_vote']=="on") {
							$_gnum = "<td>$rows[gnum]</td>";
							if($this->sfunction['use_only_good']!="on") $_bnum = "<td>$rows[bnum]</td>";
						}
						$article_contents .= str_replace(
							array("{:board_skin:}", "{:notice_class:}", "{:anum:}", "{:category:}", "{:subject:}", "{:author:}", "{:gnum:}", "{:bnum:}", "{:hnum:}", "{:wdate:}"),
							array($this->skin_url, $notice_class, $_anum, $_category, $subject, $rows['unick'], $_gnum, $_bnum, $rows['hnum'], $rows['wdate']),
							$tpl_buffer);
					}
					else { // ������

						// �Խù� ���� ����
						$subject = "$secret_icon$attach_icon<a $article_link>".$subject."</a>$cnum$new_icon";

						// ������ ó��
						if($rows['sno']<=$this->notice_sno) {
							$thumbnail = $content = $_category = $wz_article = '';
							$_anum = "<b>����</b>";
							$subject = "<b>$subject</b>";
							$content_class = " style='display:none'";

							// �������� ������쿡�� ���� ����
							if(empty($datas['asort'])) $notice_class = " class='notice'";
						}
						else {
							$wz_article = " class='wz_article'";
							$notice_class = $content_class = $_gnum = $_bnum = '';
							$_category = str_replace('&nbsp;', '', $_category);
							if($_category) $_category = "[$_category]";

							if($rows['dval']=="yes") { // ������ ���
								$thumbnail = '';
								$content_class = " style='display:none'";
							}
							else $content = "<a $article_link style='color:#999999'>".$this->str_cut(strip_tags($rows['content']), 200, '...')."</a>";

							// ��õ��
							if($rows['dval']!="yes" && $this->sfunction['use_vote']=="on") {
								$_gnum = "��õ: $rows[gnum]";
								if($this->sfunction['use_only_good']!="on") list($_gnum, $_bnum) = array("<nobr>[ $_gnum,", "�ݴ�: $rows[bnum] ]</nobr>");
								else $_gnum = "<nobr>[ $_gnum ]</nobr>";
							}
						}
						$article_contents .= str_replace(
							array("{:board_skin:}", "{:notice_class:}", "{:wz_article:}",  "{:anum:}", "{:thumbnail:}", "{:reply_icon:}", "{:category:}", "{:subject:}", "{:content:}", "{:content_class:}", "{:author:}", "{:gnum:}", "{:bnum:}", "{:hnum:}", "{:wdate:}"),
							array($this->skin_url, $notice_class, $wz_article, $_anum, $thumbnail, $reply_icon, $_category, $subject, $content, $content_class, $rows['unick'], $_gnum, $_bnum, $rows['hnum'], $rows['wdate']),
							$tpl_buffer);
					}
				}
				// ���������� ���
				else {
					if(empty($_category)) $_category = "<span style='font-size:1px;'>&nbsp;</span>";

					// ��ۼ�
					$cnum = $rows['cnum']>0 ? " <span style='font-family:Arial;font-size:6pt;color:#acacac;'>{$rows[cnum]}</span> " : '';

					// ���������� ���� ��� ��ȣ ���� üũ�ڽ��� ����
					if($view_page===false && $this->check_granted("delete_level")) $scheckbox = "<input onFocus='blur()' type='checkbox' name='ano[]' value='$rows[no]' class='scheckbox2'> ";

					// �Խù� ���� ����
					$subject = "$secret_icon<a $article_link>".$subject."</a>$cnum";
					$_article_contents .= str_replace(
						array("{:board_skin:}", "{:gallery_width:}", "{:category:}", "{:thumbnail:}", "{:subject:}", "{:author:}", "{:hnum:}", "{:wdate:}"),
						array($this->skin_url, $sgallery['thumb_width']+10, $_category, $thumbnail, $subject, $rows['unick'], $rows['hnum'], $scheckbox.$rows['wdate'].$new_icon),
						$tpl_buffer);

					if(!(++$column_count%$sgallery['thumb_nums'])) {
						$article_contents .= "
						<tr>$_article_contents</tr>";
						unset($_article_contents);
					}
				}
				$anum--;

			} // @eo  foreach($board_datas as $rows)
		}
		// �������� �� ä���
		if(isset($_article_contents)) {
			$_tds = str_repeat("<td>&nbsp;</td>", $sgallery['thumb_nums']-($column_count%$sgallery['thumb_nums']));
			$article_contents .= "
			<tr>$_article_contents$_tds</tr>";
			unset($_article_contents);
		}
		return array($article_column, $article_contents, $paging_button, $gallery_cell_width, $board_category_view, $scategories, $sdelete_button);
	}

	// �ش� �Խ��� �Խù� ��� ����
	function get_board_articles($datas) {
		// ���� ����üũ
		if(!$this->check_granted("list_level")) $this->popup_msg_js($this->get_granted_messages('list_level'), "BACK");

		list($article_column, $article_contents, $paging_button, $gallery_cell_width, $board_category_view, $scategories, $sdelete_button) = $this->formalize_board_articles($datas);
		// �Խ��Ǹ��� Ÿ��Ʋ�� �ݿ��ϱ� ���� �߰� - 2009.03.06
		$this->subject = $this->board_name;

		// ��ũ ����
		parse_str($_SERVER['QUERY_STRING'], $query_infos);
		unset($query_infos['no'], $query_infos['pcno']); // �Խù� ��ȣ, ���� ī�װ� ��ȣ�� ����
		$query_infos['id'] = $datas['id']; // �Խ��� ���̵� ����
		$board_links = http_build_query($query_infos); // php5 �̻�, rankup_basic.class.php �� ���ǵ�

		// ���� ��� ��ũ ����
		unset($query_infos['asort']);
		$sort_button_link = http_build_query($query_infos);

		ob_start();
		include $this->get_board_template($this->get_skin_dir()."/article_list.tpl.html");
		return ob_get_clean();
	}

	// �Խù� ������ �ε�
	function get_article($datas) {
		global $config_info;
		//SNS ����� ����ϱ� ���� ��Ŭ���
		include_once $this->base_dir."Libs/_php/rankup_sns.class.php";
		$rankup_sns = new rankup_sns;
		// �Խù� ������
		$board_infos = $this->queryFetchObject("select * from $this->board_table where no=$datas[no]");

		//���� ���ۿ� ����� �޸���� -- 2009.11.27
		if($board_infos->pno > 0 && $board_infos->sval=="yes") $read_writer = $this->queryR("select uid from $this->board_table where sno = $board_infos->sno and pno = 0 and uid='$this->member_id'");
		else $read_writer = null;

		// ���� ����üũ
		if(!$this->is_member() || ($this->is_member() && $board_infos->uid!==$this->member_id && $this->member_id !== $read_writer)) {
			if(!$this->is_seeable($datas['no']) && !$this->check_granted("read_level")) $this->popup_msg_js($this->get_granted_messages('read_level'), "BACK");
		}
		if(empty($board_infos->no)) $this->popup_msg_js("��û�Ͻ� �Խù��� ���� ���� �ʽ��ϴ�.", "BACK");
		if($board_infos->dval=="yes" && !$this->is_admin()) $this->popup_msg_js("��û�Ͻ� �Խù��� ������ �Խù��Դϴ�.", "BACK");
		if($board_infos->sval=="yes" && !$this->is_seeable($datas['no'])) { // 2009.09.17 fixed
			if(!$this->member_id || ($this->member_id && $board_infos->uid!==$this->member_id && $this->member_id !== $read_writer)) {
				if(!$this->check_granted("secret_level")) $this->popup_msg_js($this->get_granted_messages('secret_level'), "BACK");
			}
		}

		// �Խù� ������ Ÿ��Ʋ�� �ݿ��ϱ� ���� �߰� - 2009.03.06 added
		$this->subject = $board_infos->subject;

		//������ �̹��� ũ�� �ڵ�����
		$sgallery = unserialize($this->board_configs['sgallery']);
		$view_imgae_width = $sgallery['picture_width'] < $this->slayout['board_width'] ? $sgallery['picture_width'] : $this->slayout['board_width']-40; //���������� ũ�� �Խ��� ũ�⿡�� -40 ����
		$board_infos->content = $this->prefix_contents($board_infos->content, $view_imgae_width);

		// �ٸ� �Խù��̸� �ӽ��㰡 ���� ����
		if($_SESSION[$_COOKIE['rbUser']]!==$this->board_id.$datas['no']) unset($_SESSION[$_COOKIE['rbUser']]);

		/*// �Խù� �ۼ� �ð� ����
		list($_date, $_time) = explode(" ", $board_infos->wdate);
		$board_infos->wdate = ($_date==date("Y-m-d")) ? $_time : $_date;
		*/
		$board_infos->wdate = array_shift(explode(" ", $board_infos->wdate));

		// ��ȸ�� ���� - �����ڰ� �ƴѰ�쿡�� ó��
		if($this->sfunction['use_duplicate_hit']=="on") $this->increase_readcount($board_infos, true);
		else if(!$this->is_admin()) $this->increase_readcount($board_infos);

		// ���������� / ȸ������ ���� ��ư �߰�
		$board_infos->author = $board_infos->unick;
		$board_infos->unick = $this->get_writer_infos($board_infos);
		if($this->board_extension===true && !empty($board_infos->uid) && $this->member_id!==$board_infos->uid) {
			// �ۼ������� ��ư
			$board_infos->unick .= " <a onClick=\"rankup_board.get_author_info('$board_infos->uid')\"><img src='".$this->board_url."img/icon_info.gif' align='absmiddle' style='margin-top:-3px;' alt='�ۼ�������'></a>";
			// ���������� ��ư
			if($this->is_member()) $board_infos->unick .= " <a onClick=\"rankup_board.send_message('$_author', '$board_infos->uid')\"><img src='".$this->board_url."img/icon_msg.gif' align='absmiddle' style='margin-top:-3px;' alt='����������'></a>";
		}

		// ������ / ������ �ε� - �˻����ǿ� ���� �۾� ���� ó�� @#######
		if($this->soption['use_near_article']=="on") {
			/*// ����/�˻������� ���� ���
			if(!in_array($datas['asort'], array('', 'recent'))) {
				// ������/������ ã��
				if(empty($datas['page'])) $datas['page'] = 1;
				$spos = $datas['page']>1 ? ($datas['page']-1)*$this->slayout['article_rows'] : 0;
				// �˻�� ������ ���
				if(!empty($datas['skey'])) {
					switch($datas['smode']) {
						case "both": // ����+���� �˻�
							$addWhere .= " and (subject like '%$datas[skey]%' or content like '%$datas[skey]%')";
							break;
						case "subject": // ���� �˻�
							$addWhere .= " and (subject like '%$datas[skey]%')";
							break;
						case "author": // �ۼ��� �˻�
							$addWhere .= " and (unick like '%$datas[skey]%')";
							break;
					}
				}
				// �з��� ������ ���
				if($this->sfunction['use_category']=="on" && $this->check_resource($categories) && !empty($datas['scategory'])) $addWhere .= " and cno=$datas[scategory]";
				// ���� ���� ������ ���
				switch($datas['asort']) {
					case "hit": $orderBy = " hnum desc"; break;
					case "hot": $orderBy = " cnum desc"; break;
					case "vote": $orderBy = " vnum desc"; break;
					case "recent": unset($datas['asort']); // $orderBy = " wdate desc"; break; // ���Ĺ�� �ʱ�ȭ �������� ����
					default: $orderBy = " sno, gno";
				}
				$this->queryFetchRows("select no, uid, subject, cnum, sval, dval from $this->board_table where no=$board_infos->nano");
			}
			*/
			if($board_infos->nano>0) {
				$next_article_infos = $this->queryFetch("select no, uid, subject, cnum, sval, dval from $this->board_table where no=$board_infos->nano");
				$next_article = $this->get_near_article($board_infos, $next_article_infos);
			}
			if($board_infos->pano>0) {
				$previous_article_infos = $this->queryFetch("select no, uid, subject, cnum, sval, dval from $this->board_table where no=$board_infos->pano");
				$previous_article = $this->get_near_article($board_infos, $previous_article_infos);
			}
		}
		// ÷������ ��뿩��
		if($this->sattach['use_attach']=="on") {
			$board_infos->attach = unserialize($board_infos->attach);
			if($this->check_resource($board_infos->attach)) {
				$attach_dir = $this->board_dir."attach/".$this->board_id."/";
				foreach($board_infos->attach as $key=>$file) {
					$file_size = array_pop($this->get_file_size($attach_dir.$file['sname']));
					if(!empty($attach_files)) $attach_files .= ", ";
					$attach_files .= "<span class='colA'><a href='$this->index_url/index.html?cmd=download&id=$this->board_id&ano=$datas[no]&fid=$key&fname=".urlencode($file['oname'])."'>$file[oname]</a></span><span class='colB'>($file_size)</span><span class='colC'>Download: $file[dnum]</span>";
				}
			}
		}
		/*// �˻��� ���� ����
		if(!empty($datas['skey']) && in_array($datas['smode'], array("both", "subject"))) {
			$board_infos->subject = str_replace($datas['skey'], "<font color='#FF6600'>$datas[skey]</font>", $board_infos->subject);
		}*/

		// �Խ��� ��ũ ����
		parse_str($_SERVER['QUERY_STRING'], $query_infos);
		unset($query_infos['no']); // �Խù� ��ȣ�� ����
		$board_links = http_build_query($query_infos); // php5 �̻�, rankup_basic.class.php �� ���ǵ�

		// �� ������ �� �Խù� ��� ���
		if($this->soption['use_detail_list']=="on") {
			list($article_column, $article_contents, $paging_button, $gallery_cell_width, $board_category_view, $scategories) = $this->formalize_board_articles($datas, true);
		}
		ob_start();
		include $this->get_board_template($this->get_skin_dir()."/article_view.tpl.html");
		return ob_get_clean();
	}

	// ������/������ ��ũ ����
	function get_near_article($board_infos, $near_infos) {
		// �Խ��� ��ũ ����
		parse_str($_SERVER['QUERY_STRING'], $query_infos);
		unset($query_infos['no']); // �Խù� ��ȣ�� ����
		$board_links = http_build_query($query_infos); // php5 �̻�, rankup_basic.class.php �� ���ǵ�

		$near_link = "href=\"$this->index_url/index.html?$board_links&no=$near_infos[no]\"";
		$near_subject = $near_infos['subject'];
		if($near_infos['sval']=="yes") {
			$near_secret_icon = "<img src='".$this->board_url."icon/icon_secret.gif' align='absmiddle'> ";
			if(!$this->check_granted("secret_level") && (empty($near_infos['uid']) || (!empty($near_infos['uid']) && $near_infos['uid']!=$this->member_id))) {
				$near_link = "href=\"$this->index_url/index.html?$board_links&no=$near_infos[no]\" onClick=\"rankup_board.scanf_passwd($near_infos[no], this, 'article_view'); return false;\"";
			}
		}
		if($near_infos['dval']=="yes") {
			$near_link = "href=\"$this->index_url/index.html?$board_links&no=$near_infos[no]\" style=\"color:#cdcdcd\"";
			if(!$this->is_admin()) $near_link .= " onClick=\"alert('������ �Խù��� ��ȸ�� �� �����ϴ�.'+SPACE); return false;\"";
			$near_subject = "<strike>������ �Խù��Դϴ�.</strike>";
		}
		$near_cnum =  $near_infos['cnum']>0 ? " <span class=\"pre_next_num\">[".$near_infos['cnum']."]</span>" : '';
		return $near_article = $near_secret_icon."<a $near_link>$near_subject</a>$near_cnum";
	}

	// �Խù��� ��庰(�Խù����/��������/�����/������ ǥ��) ������ ����
	function get_board_contents($datas, $simple_mode=false) {
		// �ҷ�ȸ������ üũ - �������� ������ �ּ� ����
		//if($this->member_badness=="yes") $this->popup_msg_js("������ �ҷ�ȸ������ �Խ��� ������ ���ܵǾ����ϴ�.", $this->base_url."main/index.html");

		$this->simple_mode = $simple_mode; // 2009.09.09 added
		if($this->simple_mode==true) {
			$this->slayout['board_width'] = 750;
			//$this->board_configs['style'] = 'normal';
			$this->soption['use_hit_best'] = '';
			$this->soption['use_near_article'] = '';
			$this->soption['use_detail_list'] = '';

			// �������������� �α��� �� �Խù� �ۼ��� ȸ���α��� ���� ���̱� - 2009.09.21 added
			if($this->is_administrator() && $datas['mode']=='write' && !$datas['no']) {
				$_SESSION[$this->member_session_id] = '';
				$this->member_name = $this->member_id = '';
				$this->member_level = 7;
			}
		}

		switch($datas['mode']) {
			// ���/����������
			case "write":
				// �ҷ�ȸ������ üũ
				if(!$this->is_admin() && $this->member_badness=="yes") $this->popup_msg_js("������ �ҷ�ȸ������ �Խù��� �ۼ��Ͻ� �� �����ϴ�.", $this->base_url."main/index.html");

				// ���ٱ��� üũ
				if(!empty($datas['no']) && !$this->is_seeable($datas['no'])) $this->popup_msg_js("�Խù� ���� ������ �����ϴ�.", "BACK");
				else {
					/// ��۱���
					if(!empty($datas['pano'])) {
						if($this->sfunction['use_reply']=="no") $this->popup_msg_js("'".$this->board_name."' ���� ����� ����� �� �����ϴ�.", "BACK");
						else if(!$this->check_granted("reply_level")) $this->popup_msg_js($this->get_granted_messages('reply_level'), "BACK");
					}
					else if(empty($datas['no']) && !$this->check_granted("write_level")) $this->popup_msg_js($this->get_granted_messages("write_level"), "BACK");
				}
				// ��ũ���� üũ
				$this->clear_junk_files(true);

				// ����/����� ��� - �Խù� ���� �ε�
				$board_infos->unick = $this->member_name;
				$board_infos->content = $this->board_configs['scontent']; // ���� �⺻�� // 2009.05.19 fixed
				if(!empty($datas['no']) || !empty($datas['pano'])) {
					$ano = !empty($datas['no']) ? $datas['no'] : $datas['pano'];
					$board_infos = $this->queryFetchObject("select no,subject,cno,uid,unick,content,sval,nval,dval from $this->board_table where no=$ano");
					if(!empty($datas['no']) && $board_infos->dval=="yes") $this->popup_msg_js("������ �Խù��Դϴ�.", "BACK");
					if(!empty($datas['pano'])) { // ��� �ۼ���
						// �����ۿ� ����� �ۼ��� ���
						if(!$this->is_replyable($board_infos)) $this->popup_msg_js("������ �Խù����� ����� ����� �� �����ϴ�.", "BACK");
						$board_infos->unick = $this->member_name; // �ۼ��� �� ����
						$board_infos->subject = "[re] ".$board_infos->subject;
						$board_infos->content = "<br><div disabled style='width:100%;padding:8px;border:#dedede 1px dotted;background-color:#f7f7f7;margin-top:5px;'>".$board_infos->content."</div>";
						// ��� �Է����·� ����
						$board_infos->pano = $board_infos->no;
						unset($board_infos->no);
					}
				}
				if($this->sfunction['use_category']) {
					$categories = @unserialize($this->board_configs['scategory']);
					if($this->check_resource($categories)) {
						$scategory = $this->sort_scategory($categories); // 2009.09.18 added
						$scategories = "<option value=''>�з�����</option>";
						foreach($scategory as $rank=>$val) {
							$_selected = $board_infos->cno==$val['cno'] ? " selected" : '';
							$scategories .= "<option value='$val[cno]'$_selected>$val[name]</option>";
						}
					}
					else $scategories = "<option value=''>�з�����</option>";
					$category = "<select name='category' required hname='�з�'>".$scategories."</select>";
				}

				// ���� �ܾ� ���͸�
				$antifilter = !empty($this->board_configs['sfilter']) ? " antifilter=\"".$this->board_configs['sfilter']."\"" : '';

				// ÷������ ���͸�
				if(!empty($this->sattach['attach_extension'])) $filefilter = " filter=\"".str_replace(" ", '', $this->sattach['attach_extension'])."\"";
				ob_start();
				include $this->get_board_template($this->get_skin_dir()."/article_regist.tpl.html");
				return ob_get_clean();
				break;

			// �Խù����/��������
			default:
				if(empty($datas['no'])) $contents = $this->get_board_articles($datas);
				else $contents = $this->get_article($datas);
		}
		return $contents;
	}

	// ��ȸ�� ���� ó��
	function increase_readcount($board_infos, $force_mode=false) {
		// �������� ������� ����
		// �Խù� �ۼ��� �����ǿ� ���� �����ǰ� ���� ��� ����
		// ��Ű�� ������ ��� ����
		$board_cookies = $_COOKIE[$this->board_id];
		// �� || $_SERVER['REMOTE_ADDR']==$board_infos->uip ���� ����
		if($force_mode===false && ($board_cookies[$board_infos->no]==true || ($this->is_member() && $this->member_id==$board_infos->uid))) return false;
		$result = $this->query("update $this->board_table set hnum=hnum+1 where no=$board_infos->no");
		//�������̸� �������� �����Ѵ�.
		if($board_infos->nval == "yes") {
			// �Խù� ��Ű ����
			setcookie("$this->board_id"."[$board_infos->no]", true, strtotime(date("Y-m-d 00:00:00")." +1 day"), "/"); // ������ ����
			return $result;
		}
		if($result) {
			// ��ȸ�� ����Ʈ ���̺� �Է� - �Խ��Ǻ� 10�� ����
			$best_datas = $this->queryFetchRows("select no, ano, ahnum from $this->hit_best_table where bid='$this->board_id' order by ahnum desc limit 10");
			$best_rows = end($best_datas); // ������ ���ڵ�

			if($best_rows['ahnum']<$board_infos->hnum+1 || count($best_datas)<10) {
				$this->query("update $this->hit_best_table set ahnum=".($board_infos->hnum+1)." where ano=$board_infos->no and bid='$this->board_id'");
				if(!mysql_affected_rows()) {
					if(count($best_datas)>=10) {
						$this->query("delete from $this->hit_best_table where no=$best_rows[no]");
						if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->hit_best_table");
					}
					$_val['pcno'] = $this->board_configs['pcno'];
					$_val['bid'] = $this->board_id;
					$_val['adno'] = $board_infos->dno;
					$_val['ano'] = $board_infos->no;
					$_val['ahnum'] = $board_infos->hnum+1;
					$values = $this->change_query_string($_val);
					$this->query("insert $this->hit_best_table set $values");
				}
			}

			// �ְ� ����Ʈ ���̺� �Է� - ��ü 7��ġ�� ����
			// �������� ���� �����ʹ� ����
			$this->query("delete from $this->weekly_best_table where wdate<date_sub(curdate(), interval 7 day)");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->weekly_best_table");

			// �ְ� ����Ʈ ���̺� ��ȸ�� ����
			$this->query("update $this->weekly_best_table set hnum=hnum+1 where wdate=curdate() and pcno=".$this->board_configs['pcno']." and ano=$board_infos->no and bid='$this->board_id'");
			if(!mysql_affected_rows()) {
				$_xVal['pcno'] = $this->board_configs['pcno'];
				$_xVal['bid'] = $this->board_id;
				$_xVal['adno'] = $board_infos->dno;
				$_xVal['ano'] = $board_infos->no;
				$_xVal['hnum'] = 1;
				$_xVal['wdate'] = date("Y-m-d");
				$values = $this->change_query_string($_xVal);
				$this->query("insert $this->weekly_best_table set $values");
			}
			// �Խù� ��Ű ����
			setcookie("$this->board_id"."[$board_infos->no]", true, strtotime(date("Y-m-d 00:00:00")." +1 day"), "/"); // ������ ����
		}
		return $result;
	}

	// ���� ����� ��ü�� ��ȯ
	function queryFetchObject($query) {
		$result = $this->query($query);
		$rows = @mysql_fetch_object($result);
		$this->stripslashes($rows); // ������ ���Ͻ� stripslashes ���� - 2008.06.09 �߰�
		@mysql_free_result($result);
		return $rows;
	}

	// ������ �Ѱܹ��� �� ����
	function wysiwyg_result_func($content='') { // $content : stripslashes() �� ��
		$rbUser = $this->get_discern_name();
		preg_match_all('/<img\s+.*?src="([^"]+)"[^>]*>/is', $content, $imgs);
		foreach($imgs[1] as $key=>$img) {
			// �̹��� ���� PEG ������ �̵�
			if(strpos($img, $this->wysiwyg_url."PEG_temp/")!==false) {
				$_name = basename($img);
				$tmp_file = $this->wysiwyg_dir."PEG_temp/".$_name;
				$save_file = $this->wysiwyg_dir."PEG/".$_name;
				if(is_file($tmp_file)) {
					@copy($tmp_file, $save_file);
					@unlink($tmp_file);
				}
			}
			// �̹����� �ɸ� PEG_temp �� PEG �� ���� �� ������ ���� - 2008.06.09
			$_info = parse_url($img); // scheme : http  or  https
			$_info['scheme'] = empty($_info['scheme']) ? '' : $_info['scheme']."://";
			$_img = str_replace($img, str_replace(array($_info['scheme'].$_SERVER['HTTP_HOST'], "/PEG_temp/", "_junk_.{$rbUser}."), array('', "/PEG/", ''), $img), $imgs[1][$key]);
			if(strpos(strtolower($_img), "border")===false) $_img = eregi_replace(" src", " border=\"0\" src", $_img); // border='0' �߰�
			$content = str_replace($imgs[1][$key], $_img, $content);
		}

		// ��ũ���� ��ũ ����
		$content = str_replace("_junk_.{$rbUser}.", '', $content);
		return $content;
	}

	// ���� �̹��� ������ ���� - 2010.05.24 added
	function prefix_contents($content='', $prefix_size=685) {
		preg_match_all('/(<img\s+.*?src="([^"]+)"[^>]*)>/is', $content, $images); //�̹�������
		preg_match_all('/(<table id=community_image\s+.*?width=[\'"]?+([0-9%]{1,})[\'"]?+[^>]*)>/is', $content, $tables);//�̹����� ���δ� ���̺�����
		foreach($images[0] as $key=>$image) {
			list($width, $height) = @getimagesize($_SERVER[DOCUMENT_ROOT].$images[2][$key]);
			if($width > $prefix_size) {
				$_dimensions = 'width:'.$prefix_size.'px;'; //�߰��� ��Ÿ�� �Ӽ�
				if(stristr($tables[0][$key], "community_image") !== false) {
					list($new_table, $table_width) = array($tables[0][$key], $tables[2][$key]);
					$new_table = str_ireplace(array('width', 'height'), array('_width', '_height'), $new_table);
					if(stristr($tables[0][$key], 'style')!==false) {
						preg_match_all('/style="([^"]+)"[^"]/is', $new_table, $table_styles);
						$new_table = str_replace($table_styles[1][0], $_dimensions.$table_styles[1][0], $new_table);
					}
					else $new_table = str_replace(array('/>', '>'), ' style="'.$_dimensions.'">', $new_table);
					$content = str_replace($tables[0][$key], $new_table, $content);
				}
				list($new_image, $image_url) = array($image, $images[2][$key]);
				$new_image = str_ireplace(array('width', 'height'), array('_width', '_height'), $new_image); // width & height reset
				if(stristr($image, 'style')!==false) {
					preg_match_all('/style="([^"]+)"[^"]/is', $new_image, $styles);
					$new_image = str_replace($styles[1][0], $_dimensions.$styles[1][0], $new_image);
				}
				else $new_image = str_replace(array('/>', '>'), ' style="'.$_dimensions.'">', $new_image);
				$content = str_replace($image, $new_image, $content);
			}
		}
		return $content;
	}

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// �Խù� �Է�
	function regist_article($datas) {
		// ���ٱ��� üũ
		if(empty($datas['no'])) {
			if(!$this->check_granted("write_level")) $this->popup_msg_js($this->get_granted_messages("write_level"), "BACK");
			else if(!empty($datas['pano'])) {
				if($this->sfunction['use_reply']=="no") $this->popup_msg_js("'".$this->board_name."' ���� ����� ����� �� �����ϴ�.", "BACK");
				else if(!$this->check_granted("reply_level")) $this->popup_msg_js($this->get_granted_messages("reply_level"), "BACK");
			}
			if($datas['is_notice']=="on" && !$this->check_granted("notice_level")) $this->popup_msg_js($this->get_granted_messages("notice_level"), "BACK");
		}
		else { // ��������� ���
			$before_datas = $this->queryFetch("select no, cno, sno, nano, pano, uid, upass, attach, sval, nval from $this->board_table where no=$datas[no]");
			// �������� üũ ( ������/ȸ�����̵�/��ȸ��-��й�ȣ üũ )
			if(!$this->is_admin() && (
				(!empty($before_datas['uid']) && ($this->member_id!=$before_datas['uid'] && $datas['passwd']!==$this->get_member_passwd($before_datas['uid'])))
				|| (empty($before_datas['uid']) && $datas['passwd']!=$before_datas['upass']))
			) $this->popup_msg_js("�Խù� ���� ������ �����ϴ�.", "BACK");

			// ������ �������� üũ
			if($before_datas['nval']=="yes" && !$this->check_granted("notice_level")) $this->popup_msg_js("������ ���� ������ �����ϴ�.", "BACK");

			// �̿��ϴ� �������� ������ �ʿ䰡 �ִ��� üũ
			$change_near_articles = (($before_datas['nval']=="yes" && empty($datas['is_notice'])) || ($before_datas['nval']=="no" && $datas['is_notice']=="on"));
		}

		// �����ڵ尡 ���� �ʴ� ��� - 2010.06.17 added
		if(!$this->is_admin() && empty($this->member_id) && $this->confirm_used && !$this->check_confirm_code($_POST['keystring'])) {
			$this->popup_msg_js("�Է��Ͻ� ���Թ��� �ڵ尡 ��ġ���� �ʽ��ϴ�.", "BACK");
		}

		if(empty($datas['pano'])) {
			// ������ �϶�
			if($datas['is_notice']=="on") {
				$_sno = $this->queryR("select min(sno) from $this->board_table");
				$near_article = $this->queryFetch("select no, dno, sno, nano, pano from $this->board_table where sno=$_sno");
				if(is_array($near_article)) $next_sno = $near_article['sno']>$this->notice_sno ? $this->notice_sno-1 : $near_article['sno']-1;
				else $next_sno = $this->notice_sno-1;

				$_val['nval'] = "yes";
				$_val['pano'] = 0;
				$_val['gno'] = 0;
				$_val['pno'] = 0;
			}
			else {
				$near_article = $this->queryFetch("select no, dno, sno, nano, pano from $this->board_table where sno>$this->notice_sno order by sno limit 1");
				if(empty($near_article)) $near_article = $this->queryFetch("select no, dno, sno, nano, pano from $this->board_table order by sno desc limit 1");
				if(is_array($near_article)) $next_sno = !empty($near_article['sno']) ? $near_article['sno']>$this->notice_sno ? $near_article['sno']-1 : -1 : -1;
				else $next_sno = -1;

				$_val['nval'] = "no";
				if($before_datas['nval']=="yes") { // �������� ������ ���
					$_val['gno'] = 0;
					$_val['pno'] = 0;
				}
			}
			// �̿��ϴ� �� ����
			if(empty($datas['no']) || $change_near_articles) {
				if(empty($near_article['no'])) $near_article['no'] = 0;
				if($next_sno<$near_article['sno']) {
					$_val['nano'] = $near_article['no'];
					$_val['pano'] = $near_article['pano'];
				}
				else {
					$_val['nano'] = $near_article['nano'];
					$_val['pano'] = $near_article['no'];
				}
				if($_val['nano']==null) $_val['nano'] = 0;
			}
		}
		// ����� ���
		else {
			$near_article = $this->queryFetch("select no, cno, sno, gno, pno, nano, pano, uid, sval, nval from $this->board_table where no=$datas[pano]");

			$child_articles = $this->queryFetchRows("select no from $this->board_table where sno=$near_article[sno] and gno>$near_article[gno]");
			foreach($child_articles as $child_rows) $this->query("update $this->board_table set gno=gno+1 where no=$child_rows[no]");

			$next_sno = $near_article['sno'];
			$_val['nano'] = $near_article['nano'];
			$_val['pano'] = $near_article['no'];
			$_val['gno'] = $near_article['gno']+1;
			$_val['pno'] = $near_article['pno']+1;
		}

		// ���� �׸� ����
		$_val['subject'] = str_replace("\"", "&quot;", stripslashes($datas['subject']));
		$_val['content'] = $this->wysiwyg_result_func(stripslashes($datas['content']));
		$_val['uip'] = $_SERVER['REMOTE_ADDR'];
		$_val['sval'] = $datas['is_secret']=="on" ? "yes" : "no";
		// �з��� ����� ��쿡�� ī�װ� �� ����
		if($this->sfunction['use_category']=="on") $_val['cno'] = $datas['category'];

		// �űԵ��
		if(empty($datas['no'])) {
			$DML = "insert";
			$_val['sno'] = $next_sno;
			$_val['dno'] = $this->increase_division($near_article['dno']); // ����� ����  cf. decrease_division();
			//�α��ξ��̵� ������ ��� ���̵� �Է� �α������� ���� ������ �α����ϰ�� _admin_ �� �Է�
			if($_SESSION[$this->member_session_id]) {
				$_val['uid'] = $this->member_badness!=="yes" ? $_SESSION[$this->member_session_id] : '';
			} else if($this->is_admin()) {
				$_val['uid'] = "_admin_";
			} else {
				$_val['uid'] = "";
			}
			$_val['upass'] = $datas['passwd'];
			if($this->member_name) $_val['unick'] = $this->member_name; // 2009.08.28 modified
			else $_val['unick'] = str_replace("\"", "&quot;", stripslashes($datas['nickname']));
			$_val['wdate'] = date("Y-m-d H:i:s");
		}
		else {
			$DML = "update";
			// �Խù� ������ �ٲ�� ��Ȳ�� ���
			$_val['sno'] = $change_near_articles ? $next_sno : $before_datas['sno'];
			$_val['mdate'] = date("Y-m-d H:i:s");
			$addWhere = " where no=$datas[no]";
		}

		// ÷�������� ������ ���
		if($this->sattach['use_attach']=="on" && !empty($datas['on_attached'])) {
			// ������ : ���ϴ��� ������(,) ���ϸ� ������(:)
			// ���� ÷������ ����
			$attached = empty($before_datas['attach']) ? array() : @unserialize($before_datas['attach']);
			$attach_files = explode(',', $datas['on_attached']);
			$rbUser = $this->get_discern_name(); //
			$attach_dir = $this->board_dir."attach/".$this->board_id."/";
			foreach($attach_files as $file) {
				$_file = explode(":", $file);
				$_file[2] = str_replace("_junk_.$rbUser.", '', $_file[1]);
				if(!is_file($attach_dir.$_file[1])) continue;
				// �ӽ� ÷������ �̸� ����
				rename($attach_dir.$_file[1], $attach_dir.$_file[2]);
				$attached[] = array("oname"=>$_file[0], "sname"=>$_file[2], "dnum"=>0);

				// ���� ÷���ϴ� ���� ��� ���� - ����Ʈ ����� ���
				$sattached_files[count($sattached_files)] = $_file[2];

				// ����� ������ �ִٸ� �̸� ����
				$file_infos = explode(".", $_file[1]);
				$file_infos[2] = "thumb_".$file_infos[2];
				$thumb_file = @implode(".", $file_infos);
				if(!is_file($attach_dir.$thumb_file)) continue;
				rename($attach_dir.$thumb_file, $attach_dir.str_replace("_junk_.$rbUser.", '', $thumb_file));
			}
			$_val['attach'] = serialize($attached);
		}

		$values = $this->change_query_string($_val);
		$this->query("$DML $this->board_table set $values$addWhere");

		// �̿��ϴ� �Խù�(����/���� ��) ����
		if($DML=="insert") {
			$article_no = mysql_insert_id();
			$_datas = array("no"=>$article_no, "sno"=>$_val['sno'], "pano"=>0, "nano"=>$near_article['no']);
			$this->change_near_article($_datas, $near_article);
			$this->update_board(array("cmd"=>"set_anum", "plus_mode"=>true)); // �Խù� �� ����

			// �ֱ� �Խù��� ��� ##
			if($article_no) {
				// �ֱ� �Խù��� �����޴�(pcno) �� 5������ ����
				$new_datas = $this->queryFetchRows("select no from $this->new_article_table where pcno=".$this->board_configs['pcno']." order by no");
				$new_rows = current($new_datas);
				if(count($new_datas)>=5) { // 1��°(������� ���� ������) ���ڵ� ����
					$this->query("delete from $this->new_article_table where no=$new_rows[no]");
					if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->new_article_table");
				}
				$_xVal['pcno'] = $this->board_configs['pcno'];
				$_xVal['bid'] = $this->board_id;
				$_xVal['adno'] = $_val['dno'];
				$_xVal['ano'] = $article_no;
				$_xVal['awdate'] = $_val['wdate'];
				$values = $this->change_query_string($_xVal);
				$this->query("insert $this->new_article_table set $values");
			}
		}
		else {
			if($this->board_extension===true) {
				// ������ �Ű�� ���� ���̺��� ����
				$_yVal['asubject'] = $_val['subject'];
				$_yVal['aunick'] = $_val['unick'];
				$values = $this->change_query_string($_yVal);
				$this->query("update $this->report_table set $values where bid='$this->board_id' and ano=$datas[no]");
			}
			if($change_near_articles) {
				$before_datas['sno'] = $_val['sno'];
				$this->change_near_article($before_datas, $near_article);
			}
		}
		// �з���� ������� ��� ī�װ� anum ����
		if($this->sfunction['use_category']=="on") $this->change_category_anum($_val['cno'], $before_datas['cno']);
		return true;
	}

	// �̿��ϴ� �Խù� ��ȣ ����
	function change_near_article($datas, $near_datas=false) {
		if($datas['pano']!=0) $this->query("update $this->board_table set nano=$datas[nano] where nano=$datas[no]"); // ������ ����
		if($datas['nano']!=0) $this->query("update $this->board_table set pano=$datas[pano] where pano=$datas[no]"); // ������ ����
		// sno ���� �� �����ϰ� �Ǵ� �Խù� ������ �Ѿ�� ��� - ������/������ ����
		if(is_array($near_datas)) {
			if($datas['sno']<$near_datas['sno']) {
				// �̿��ϴ� �Խù��� �������� ������ ��� - �ش�Խù��� �������� ���Խù��� ����
				if($near_datas['pano']!=0) $this->query("update $this->board_table set nano=$datas[no] where no=$near_datas[pano]");
				// �̿��ϴ� �Խù��� �������� ���Խù��� ����
				$this->query("update $this->board_table set pano=$datas[no] where no=$near_datas[no]");
			}
			else {
				// �̿��ϴ� �Խù��� �������� ������ ��� - �ش�Խù��� �������� ���Խù��� ����
				if($near_datas['nano']!=0) $this->query("update $this->board_table set pano=$datas[no] where no=$near_datas[nano]");
				// �̿��ϴ� �Խù��� �������� ���Խù��� ����
				$this->query("update $this->board_table set nano=$datas[no] where no=$near_datas[no]");
			}
		}
	}

	// ���� �� ���� - �Խù� ��Ͻ� ���
	function increase_division($dno) {
		if(empty($dno)) $dno = 1;
		$banum = $this->queryR("select banum from $this->division_table where bid='$this->board_id' and division=$dno");
		if($banum+1>$this->division_num) {
			$_val['bid'] = $this->board_id;
			$_val['division'] = $dno+1;
			$_val['banum'] = 1;
			$this->query("insert $this->division_table set $values");
			$division = $_val['division'];
		}
		else {
			$this->query("update $this->division_table set banum=banum+1 where bid='$this->board_id' and division=$dno");
			$division = $dno;
		}
		return $division;
	}

	// �Խù� ���û���
	function delete_articles($datas) {
		// �������� �������� üũ
		$_referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($_referer_infos['query'], $referer_infos);
		if(!empty($datas['anos']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id']) {
			if($this->check_granted("delete_level")) {
				$board_datas = $this->queryFetchRows("select no, cno, dno, pano, nano, attach, dval from $this->board_table where no in(".str_replace("__", ",", $datas['anos']).")");
				foreach($board_datas as $board_infos) $this->delete_article($board_infos, $this->is_del());
				return "alert('�Խù��� �����Ǿ����ϴ�.'+SPACE); document.location.href='./index.html?".str_replace('&', "&amp;", $_referer_infos['query'])."';"; // $this->index_url - 2009.09.09 without
			}
			else return "alert('�Խù� ���������� �����ϴ�.'+SPACE);";
		}
		else $this->popup_msg_js("�������� ������ �ƴմϴ�.", "BACK");
	}

	// �Խù� ���� - 2009.09.09 modified
	function delete_article($board_infos, $real_mode=false) { // real_mode : ��������
		// ������� �Խù��� �����ϴ� ��� - ���������� ������� �ʵ��� ���� ����
		if($board_infos['dval']== "no" || $real_mode == true) {
			// �ְ� ����Ʈ ����
			$this->query("delete from $this->weekly_best_table where bid='$this->board_id' and ano=$board_infos[no]");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->weekly_best_table");
			// ��ȸ�� ����Ʈ ����
			$this->query("delete from $this->hit_best_table where bid='$this->board_id' and ano=$board_infos[no]");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->hit_best_table");
			// ��ۼ� ����Ʈ ����
			$this->query("delete from $this->comment_best_table where bid='$this->board_id' and ano=$board_infos[no]");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->comment_best_table");
			// �ű� �Խù� ����
			$this->query("delete from $this->new_article_table where bid='$this->board_id' and ano=$board_infos[no]");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->new_article_table");
			if($this->board_extension===true) {
				// �ְ� ��ۼ� ����Ʈ ����
				$this->query("delete from $this->weekly_cbest_table where bid='$this->board_id' and ano=$board_infos[no]");
				if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->weekly_cbest_table");
				// �Ű�� ���� ���� ����
				$this->query("delete from $this->report_table where bid='$this->board_id' and ano=$board_infos[no]");
				if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->report_table");
			}
			// �ӽû��� - ������ ���·θ� ����
			$this->query("update $this->board_table set dval='yes' where no=$board_infos[no]");
		}
		// ����������
		if($real_mode==true) {
			// �̿��ϴ� �Խù� �翬��
			// ÷�� ���� ����
			// �Խù� �� �ٿ�ī��Ʈ
			// BEST ���̺��� �Խù� ����
			// new_article ���̺��� �Խù� ����
			// ȸ�� �Խù� ���� ���̺��� �Խù� �� �ٿ�ī��Ʈ - Ŀ�´�Ƽ�ַ�ǿ����� ����
			$this->change_near_article($board_infos);
			$attaches = unserialize($board_infos['attach']);
			if($this->check_resource($attaches)) {
				$attach_dir = $this->board_dir."attach/".$this->board_id."/";
				foreach($attaches as $file) {
					if(!is_file($attach_dir.$file['sname'])) continue;
					@unlink($attach_dir.$file['sname']); // ���ϻ���
					// ����� ����
					$thumb_file = "thumb_".$file['sname'];
					if(is_file($attach_dir.$thumb_file)) @unlink($attach_dir.$thumb_file);
				}
			}
			// �Խù� ����
			$this->query("delete from $this->board_table where no=$board_infos[no]");
			if(mysql_affected_rows()) {
				if($this->optimizer) $this->query("optimize table $this->board_table");

				// �Խ��� ȯ�� ���̺� �ش� �Խ����� �Խù� �� �ٿ�ī��Ʈ
				if($board_infos['cno']) $this->change_category_anum($board_infos['cno'], '', true); // �з� ������Ʈ - ����
				$this->update_board(array("cmd"=>"set_anum", "plus_mode"=>false)); // �Խù� �� ����

				// ���� ���̺� �ش� �Խ����� �Խù� �� �ٿ�ī��Ʈ
				$this->query("update $this->division_table set banum=if(banum=0, 0, banum-1) where bid='$this->board_id' and division=$board_infos[dno]");

				// ��� ����
				$this->query("delete from $this->board_comment_table where ano=$board_infos[no]");
				if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->board_comment_table");
			}
		}
	}

	// �з��� �Խù��� ���� - �Խù� ���/����/������ ���
	function change_category_anum($cno, $before_cno, $delete_mode=false) {
		if($this->board_configs!==null) $scategory = @unserialize($this->board_configs['scategory']);
		else {
			$scategory = $this->get_board_config($this->board_id, "scategory");
			$scategory = @unserialize($scategory);
		}
		if($cno===$before_cno) return true;
		if($delete_mode===true) $scategory[$cno]['anum'] -= 1;
		else {
			$scategory[$cno]['anum'] += 1;
			if(!empty($before_cno) && $cno!=$before_cno) $scategory[$before_cno]['anum'] -= 1;
		}
		$_val['scategory'] = serialize($scategory);
		$values = $this->change_query_string($_val);
		$this->query("update $this->bconfig_table set $values where id='$this->board_id'");
		return true;
	}

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// �Խù� ��ũ���� ����� �� �ִ��� ���� - ȸ���� ���� / ���α��� �ȵ�
	function is_scrapable($board_infos) {
		return ($this->board_extension===true && $board_infos->sval=="no" && $board_infos->dval=="no" && (!$this->is_member() || ($this->is_member() && $this->member_id!=$board_infos->uid)));
	}

	// �Խù� ��õ����� ����� �� �ִ��� ���� - ȸ���� ���� / ���α��� �ȵ�
	function is_votable($board_infos, $check_bad=false) { // $check_bad ���ڰ� �߰� - 2008.12.31
		if($check_bad==true) return ($this->sfunction['use_only_good']!="on");
		return ($this->sfunction['use_vote']=="on" && $board_infos->nval=="no" && $board_infos->sval=="no" && (!$this->is_member() || ($this->is_member() && $this->member_id!=$board_infos->uid)));
	}

	// �Խù� �Ű� ����� ����� �� �ִ��� ���� - ȸ���� ���� / ���α��� �ȵ�
	function is_reportable($board_infos) {
		return ($this->sfunction['use_report']=="on" && $this->board_extension===true && (!$this->is_member() || ($this->is_member() && $this->member_id!=$board_infos->uid)));
	}

	// �Խù�  ����Ʈ ����� ����� �� �ִ��� ����
	function is_printable() {
		return $this->sfunction['use_print']=="on";
	}

	// �Խù��� ���� �� �ִ� ������ �����ϴ��� üũ ( is_readable )
	function is_seeable($ano) {
		return ($_SESSION[$_COOKIE['rbUser']]===$this->board_id.$ano || $this->is_admin());
	}

	// �Խù��� �ۼ��� �� �ִ��� ����
	function is_registable() {
		return $this->check_granted("write_level");
	}

	// ����� �ۼ��� �� �ִ��� ����
	function is_replyable($board_infos) {
		if($board_infos->dval=="yes") return false;
		return ($board_infos->nval=="no" && $this->sfunction['use_reply']=="on" && $this->check_granted("reply_level"));
	}

	// �Խù��� ������ �� �ִ��� ����
	function is_modifiable($board_infos) {
		return $board_infos->dval=="no";
	}

	//�Խù� ��ϱ��� ��� ���� Ȯ��
	function check_admin_registable() {
		if($this->spermission["write_level"] == 1 && $this->member_level == 1 || $this->is_admin()) return true;
		else if($this->spermission["write_level"] == 1 && $this->member_level != 1) return false;
		else return true;
	}

	// ��й�ȣ �Է¹����� ����
	function is_scanpass($board_infos) { // member_level = 7 : ��ȸ��
		if($this->is_member() && $this->member_badness==="yes" && $this->is_admin()) return true;
		else return ((!empty($board_infos->no) && !$this->is_admin() && ($this->member_level==7 || (!empty($board_infos->uid) && $board_infos->uid!==$this->member_id) || $this->is_member() && empty($board_infos->uid))) || (empty($board_infos->no) && $this->member_level==7));
	}

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// �Խù� ��õ/�ݴ� - 2008.12.30 ����
	function vote_article($datas) {
		// �������� �������� üũ
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano']) {
			if(!$this->is_member()) return "alert('�α��� �� �ٽ� �õ��Ͻñ� �ٶ��ϴ�.'+SPACE);";

			// ��õ������ �ִ��� üũ
			$vote_infos = $this->queryFetch("select FIND_IN_SET('$this->member_id', voter) as is_voted, voter from $this->board_table where no=$datas[ano]");
			if(!empty($vote_infos['is_voted'])) return "alert('�̹� ��ǥ�Ͻ� �Խù��Դϴ�.'+SPACE);";
			else {
				if($datas['key']!==$this->get_discern_name()) return "rankup_board.article_vote('$datas[kind]', '".$this->get_discern_name()."');";
				// ��ũ�� ó��
				$_val['voter'] = empty($vote_infos['voter']) ? $this->member_id : $vote_infos['voter'].",".$this->member_id;
				$values = $this->change_query_string($_val);
				$apply_field = ($datas['kind']=="good") ? "gnum=gnum+1" : "bnum=bnum+1";
				$this->query("update $this->board_table set $values, $apply_field where no=$datas[ano]");
				return mysql_affected_rows() ? "alert('���������� ��ǥ�Ǿ����ϴ�.'+SPACE);" : "alert('�Խù� ��ǥ�� �����Ͽ����ϴ�.'+SPACE);";
			}
		}
		else $this->popup_msg_js("�������� ������ �ƴմϴ�.", "BACK");
	}

	// �Խù��� ������ �ִ��� ��й�ȣ üũ
	function view_article($datas) {
		// �������� �������� üũ
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($referer_infos['pcno']) || (!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']))) {
			$board_infos = $this->queryFetch("select no, sno, gno, pno, uid, upass, sval from $this->board_table where no=$datas[ano]");

			// ��б� ���� ��ġ
			$secret_article_view = false;
			if($board_infos['pno']>0 && $board_infos['sval']=="yes") {
				$parent_ano = $this->queryR("select pano from $this->board_table where sno='$board_infos[sno]' and pno='$board_infos[pno]' order by gno limit 0, 1");
				$prev_article_infos = $this->queryFetch("select uid, upass from $this->board_table where no=$parent_ano");
				if(empty($prev_article_infos['uid'])) {
					if($datas['passwd']===$prev_article_infos['upass']) $secret_article_view = true;
				}
				else if($datas['passwd']===$this->get_member_passwd($prev_article_infos['uid'])) {
					$secret_article_view = true;
				}
			}

			if($secret_article_view===true || (empty($board_infos['uid']) && $board_infos['upass']==$datas['passwd']) ||
				(!empty($board_infos['uid']) && $datas['passwd']===$this->get_member_passwd($board_infos['uid']))) {
				unset($referer_infos['pcno']);
				$referer_infos['no'] = $datas['ano'];
				$referer_infos['id'] = $datas['id'];
				$referers = http_build_query($referer_infos); // php5 �̻�, rankup_basic.class.php �� ���ǵ�
				// 1ȸ�� �������� �Խù��� ������ �� �ֵ��� ó��
				$_SESSION[$_COOKIE['rbUser']] = $this->board_id.$datas['ano'];
				return "$this->index_url/index.html?".str_replace('&', "&amp;", $referers);
			}
			else return "alert('��й�ȣ�� ��ġ���� �ʽ��ϴ�.'+SPACE);";
		}
		else $this->popup_msg_js("�������� ������ �ƴմϴ�.", "BACK");
	}

	// �Խù� �ۼ������� ��й�ȣ üũ
	function verify_author($datas) {
		// �������� �������� üũ
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano']) {
			$board_infos = $this->queryFetch("select no, dno, cno, uid, upass, nano, pano, attach, dval from $this->board_table where no=$datas[ano]");
			if($this->is_admin() ||
				($datas['cmd']=="delete_article" && $this->check_granted("delete_level")) ||
				(empty($board_infos['uid']) && $board_infos['upass']===$datas['passwd']) ||
				(!empty($board_infos['uid']) && ($board_infos['uid']===$this->member_id || $datas['passwd']===$this->get_member_passwd($board_infos['uid'])))) {
				switch($datas['cmd']) {
					// �Խù� ���� ó��
					case "delete_article":
						if($datas['passwd']==="undefined") return "rankup_board.article_delete('', 'article_delete');";
						$this->delete_article($board_infos, $this->is_del()); // �Խù� dval='yes' �θ� ����
						unset($referer_infos['no']);
						$referers = http_build_query($referer_infos);
						return "alert('���������� �����Ǿ����ϴ�.'+SPACE); document.location.href=\"./index.html?".str_replace('&', "&amp;", $referers)."\";"; // $this->index_url - 2009.09.09 without
						break;

					// �Խù� ����
					case "modify_article":
						// 1ȸ�� �������� �Խù��� ������ �� �ֵ��� ó��
						$referer_infos['mode'] = "write";
						$referer_infos['no'] = $datas['ano'];
						$referers = http_build_query($referer_infos); // php5 �̻�, rankup_basic.class.php �� ���ǵ�
						$_SESSION[$_COOKIE['rbUser']] = $this->board_id.$datas['ano']; // �ӽ� ���� ����
						return "document.location.href=\"./index.html?".str_replace('&', "&amp;", $referers)."\";"; // $this->index_url - 2009.09.09 without
						break;
				}
			}
			else {
				// ������ �Լ��� ����
				$command = $datas['cmd']=="delete_article" ? "article_delete" : "article_modify";
				if(in_array($datas['passwd'], array('', "undefined"))) return "rankup_board.scanf_passwd($datas[ano], click_obj, '$command');";
				else return "alert('��й�ȣ�� ��ġ���� �ʽ��ϴ�.'+SPACE); var scanf_passwd = $('div_scanf_passwd').getElementsByTagName('input')[0]; scanf_passwd.select(); scanf_passwd.focus();";
			}
		}
		else $this->popup_msg_js("�������� ������ �ƴմϴ�.", "BACK");
	}

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// �ƽ�Ű �ڵ� ���� : 2008.10.22 �߰�
	function ASCII_code_filtering($string) {
		$pattern = '/[^A-Za-z0-9\.,\&\(\)_\-\s\x{0080}-\x{ffef}]+/u';
		//$pattern = '/^[\s\x{0000}-\x{007F}]+/u'; // PHP 4.3.x ���� ���ϱ��� ������ ����ȭ - 2008.12.03
		if($this->check_unicode($string)) return preg_replace($pattern, "", $string);
		else return iconv("UTF-8", "CP949", preg_replace($pattern, "", iconv("CP949", "UTF-8", $string)));
	}

	// ��� �ε� : 2008.10.22 ����
	function get_comment_articles($datas) {
		if($this->sfunction['use_comment']!="on") return false;
		$comment_datas = $this->queryFetchRows("select no, pno, cnum, remove, unick, uip, icon, content, wdate from $this->board_comment_table where ano=$datas[ano] order by no");
		foreach($comment_datas as $rows) {
			list($date, $time) = explode(" ", $rows['wdate']);
			$wdate = $date==date("Y-m-d") ? $time : $date;
			if($this->is_admin()) $uip = $rows['uip'];
			if($rows['pno']<1) $reply_icon = '';
			else $reply_icon = "<img src='".$this->board_url."icon/icon_re.gif' align='absmillde' />";
			$result .= "
			<item no='$rows[no]' pno='$rows[pno]' remove='$rows[remove]'>
				<reply_icon><![CDATA[".$reply_icon."]]></reply_icon>
				<icon>$rows[icon]</icon>
				<nickname><![CDATA[".$rows['unick']."]]></nickname>
				<uip><![CDATA[".$uip."]]></uip>
				<content><![CDATA[".nl2br($rows['content'])."]]></content>
				<wdate>$wdate</wdate>
			</item>";
		}
		return $result;
	}
	// ��� ���
	function regist_comment($datas) {
		if(empty($datas['no']) || empty($datas['nickname'])) return false;

		// �ҷ�ȸ������ üũ - 2009.08.12 added
		if($this->member_badness=="yes") $this->popup_msg_js("������ �ҷ�ȸ������ ����ۼ��� ���ѵǾ����ϴ�.", $this->base_url."main/index.html");

		// ���� ���� üũ
		if(!$this->check_granted('comment_level')) $this->popup_msg_js($this->get_granted_messages("comment_level"), "BACK");

		// �����ڵ尡 ���� �ʴ� ��� - 2010.06.17 added
		if(!$this->is_admin() && empty($this->member_id) && $this->confirm_used && !$this->check_confirm_code($_POST['keystring'])) {
			$this->popup_msg_js("�Է��Ͻ� ���Թ��� �ڵ尡 ��ġ���� �ʽ��ϴ�.", "VOID");
		}

		$_val['ano'] = $datas['no'];
		$_val['pno'] = $datas['pno'];
		$_val['uip'] = $_SERVER['REMOTE_ADDR'];
		$_val['uid'] = $this->member_id;
		$_val['unick'] = str_replace("\"", "&quot;", stripslashes($datas['nickname']));
		$_val['upasswd'] = $datas['passwd'];
		$_val['icon'] = $datas['icon'];
		$_val['content'] = stripslashes($datas['content']);
		$_val['wdate'] = date("Y-m-d H:i:s");
		$values = $this->change_query_string($_val);
		$this->query("insert $this->board_comment_table set $values");
		$comment_no = mysql_insert_id();
		//���ۼ��� ���̵�/�г���
		$writer_info = $this->get_writer_infos($_val, true);
		if($comment_no) {
			// ����Ʈ ����
			if($point_check===true) $this->apply_point($this->member_id, "comment", $datas['no'], $comment_no);
			// �� ��ۿ� ���
			if($this->board_extension===true && $this->is_member()) {
				$_wVal['uid'] = $this->member_id;
				$_wVal['pcno'] = $this->board_configs['pcno'];
				$_wVal['bid'] = $this->board_id;
				$_wVal['ano'] = $datas['no'];
				$_wVal['cno'] = $comment_no;
				$_wVal['wdate'] = $_val['wdate'];
				$values = $this->change_query_string($_wVal);
				$this->query("insert $this->my_comment_table set $values");
			}

			$this->query("update $this->board_table set cnum=cnum+1 where no=$datas[no]");
			//����� ����ϰ�� �θ��ۼ� cnum����
			if($datas[pno]>0) $this->query("update $this->board_comment_table set cnum=cnum+1 where no=$datas[pno]");
			unset($_val, $values);

			// ��� ����Ʈ ���̺� �Է� - �Խ��Ǻ� 10�� ����
			// ������ ��� ��
			$article_datas = $this->queryFetch("select no, dno, cnum, nval from $this->board_table where no=$datas[no]");
			$best_datas = $this->queryFetchRows("select no, ano, acnum from $this->comment_best_table where bid='$this->board_id' order by acnum desc limit 10");
			$best_rows = end($best_datas); // ������ ���ڵ�
			//�ش� ���� ���������� �ƴϸ� �Է��Ѵ�.
			if($article_datas['nval'] != "yes") {
				if($best_rows['acnum']<$article_datas['cnum'] || count($best_datas)<10) {
					$this->query("update $this->comment_best_table set acnum=".$article_datas['cnum']." where ano=$datas[no] and bid='$this->board_id'");
					if(!mysql_affected_rows()) {
						if(count($best_datas)>=10) {
							$this->query("delete from $this->comment_best_table where no=$best_rows[no]");
							if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->comment_best_table");
						}
						$_val['pcno'] = $this->board_configs['pcno'];
						$_val['bid'] = $this->board_id;
						$_val['adno'] = $article_datas['dno'];
						$_val['ano'] = $article_datas['no'];
						$_val['acnum'] = $article_datas['cnum'];
						$values = $this->change_query_string($_val);
						$this->query("insert $this->comment_best_table set $values");
					}
				}
				if($this->is_member() && $this->board_extension===true) {
					// �ְ� ��� ����Ʈ ���̺� �Է� - ��ü 7��ġ�� ����
					// �������� ���� �����ʹ� ����
					$this->query("delete from $this->weekly_cbest_table where wdate<date_sub(curdate(), interval 7 day)");
					if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->weekly_cbest_table");
					// �ְ� ��� ����Ʈ ���̺� ��ȸ�� ����
					$this->query("update $this->weekly_cbest_table set cnum=cnum+1 where wdate=curdate() and ano=$article_datas[no] and uid='$this->member_id'");
					if(!mysql_affected_rows()) {
						$_xVal['bid'] = $this->board_id;
						$_xVal['uid'] = $this->member_id;
						$_xVal['ano'] = $article_datas['no'];
						$_xVal['cnum'] = 1;
						$_xVal['wdate'] = date("Y-m-d");
						$values = $this->change_query_string($_xVal);
						$this->query("insert $this->weekly_cbest_table set $values");
					}
				}
			}
			//��۴���� ��� ������� �߰�
			if($datas['pno']<1) $reply_icon = '';
			else $reply_icon = "<img src='".$this->board_url."icon/icon_re.gif' align='absmillde' />";
		}
		return $this->is_admin() ? array($comment_no, date("H:i:s"), $writer_info, $_SERVER['REMOTE_ADDR'], $datas['pno'], $reply_icon) : array($comment_no, date("H:i:s"), $writer_info, "", $datas['pno'], $reply_icon);
	}
	/*
	function regist_comment($datas) {
		if(empty($datas['no']) || empty($datas['nickname'])) return false;

		// ���� ���� üũ
		if(!$this->check_granted('comment_level')) $this->popup_msg_js($this->get_granted_messages("comment_level"), "BACK");

		// �����ڵ尡 ���� �ʴ� ��� - 2010.06.17 added
		if(!$this->is_admin() && empty($this->member_id) && $this->confirm_used && !$this->check_confirm_code($_POST['keystring'])) {
			$this->popup_msg_js("�Է��Ͻ� ���Թ��� �ڵ尡 ��ġ���� �ʽ��ϴ�.", "VOID");
		}

		$_val['ano'] = $datas['no'];
		$_val['uip'] = $_SERVER['REMOTE_ADDR'];
		$_val['uid'] = $this->member_id;
		$_val['unick'] = str_replace("\"", "&quot;", stripslashes($datas['nickname']));
		$_val['upasswd'] = $datas['passwd'];
		$_val['icon'] = $datas['icon'];
		$_val['content'] = stripslashes($datas['content']);
		$_val['wdate'] = date("Y-m-d H:i:s");
		$values = $this->change_query_string($_val);
		$this->query("insert $this->board_comment_table set $values");
		$comment_no = mysql_insert_id();
		//���ۼ��� ���̵�/�г���
		$writer_info = $this->get_writer_infos($_val);
		if($comment_no) {
			// ��� �� ����
			$this->query("update $this->board_table set cnum=cnum+1 where no=$datas[no]");
			unset($_val, $values);

			// ��� ����Ʈ ���̺� �Է� - �Խ��Ǻ� 10�� ����
			// ������ ��� ��
			$article_datas = $this->queryFetch("select no, dno, cnum, nval from $this->board_table where no=$datas[no]");
			$best_datas = $this->queryFetchRows("select no, ano, acnum from $this->comment_best_table where bid='$this->board_id' order by acnum desc limit 10");
			$best_rows = end($best_datas); // ������ ���ڵ�
			//������ ���������� �ƴϸ�
			if($article_datas['nval'] != "yes") {
				if($best_rows['acnum']<$article_datas['cnum'] || count($best_datas)<10) {
					$this->query("update $this->comment_best_table set acnum=".$article_datas['cnum']." where ano=$datas[no] and bid='$this->board_id'");
					if(!mysql_affected_rows()) {
						if(count($best_datas)>=10) {
							$this->query("delete from $this->comment_best_table where no=$best_rows[no]");
							if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->comment_best_table");
						}
						$_val['pcno'] = $this->board_configs['pcno'];
						$_val['bid'] = $this->board_id;
						$_val['adno'] = $article_datas['dno'];
						$_val['ano'] = $article_datas['no'];
						$_val['acnum'] = $article_datas['cnum'];
						$values = $this->change_query_string($_val);
						$this->query("insert $this->comment_best_table set $values");
					}
				}

				if($this->is_member() && $this->board_extension===true) {
					// �ְ� ��� ����Ʈ ���̺� �Է� - ��ü 7��ġ�� ����
					// �������� ���� �����ʹ� ����
					$this->query("delete from $this->weekly_cbest_table where wdate<date_sub(curdate(), interval 7 day)");
					if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->weekly_cbest_table");

					// �ְ� ��� ����Ʈ ���̺� ��ȸ�� ����
					$this->query("update $this->weekly_cbest_table set cnum=cnum+1 where wdate=curdate() and ano=$article_datas[no] and uid='$this->member_id'");
					if(!mysql_affected_rows()) {
						$_xVal['bid'] = $this->board_id;
						$_xVal['uid'] = $this->member_id;
						$_xVal['ano'] = $article_datas['no'];
						$_xVal['cnum'] = 1;
						$_xVal['wdate'] = date("Y-m-d");
						$values = $this->change_query_string($_xVal);
						$this->query("insert $this->weekly_cbest_table set $values");
					}
				}
			}
		}
		return $this->is_admin() ? array($comment_no, date("H:i:s"), $writer_info, $_SERVER['REMOTE_ADDR']) : array($comment_no, date("H:i:s"), $writer_info, "");
	}
*/
	// ��� ���� - Ajax - 2009.09.09 added
	function apply_comment($datas) {
		if(!$datas['no']) return false;
		$_val['icon'] = $datas['icon'];
		$_val['content'] = stripslashes($datas['comment']);
		$values = $this->change_query_string($_val);
		$this->query("update $this->board_comment_table set $values where no=$datas[no]");
	}

	// ��� ���� - 2009.09.09 added
	function modify_comment($datas) {
		// ��� �������� üũ
		// �α��� ȸ���� ��� �ڽ��� ����� �ڸ�Ʈ���� üũ - �ٷ� ����
		// �������� ��� �ٷ� ����
		// �׿��� ��쿡�� ��й�ȣ â ���
		// �������� �������� üũ
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano']) {

			$comment_infos = $this->queryFetch("select no, ano, uid, upasswd, wdate from $this->board_comment_table where no=$datas[no] and ano=$datas[ano]");
			if($this->is_admin() ||
				(empty($comment_infos['uid']) && $comment_infos['upasswd']===$datas['passwd']) ||
				(!empty($comment_infos['uid']) && ($comment_infos['uid']===$this->member_id || $datas['passwd']===$this->get_member_passwd($comment_infos['uid'])))) {

				if($datas['passwd']==="undefined") return "rankup_board.comment_modify($datas[no], 'comment_modify');";
				else return "rankup_board.comment_form($datas[no], click_obj); $('div_scanf_passwd').hide();"; // ������ �ε�
			}
			else {
				if(in_array($datas['passwd'], array('', "undefined"))) return "rankup_board.scanf_passwd($datas[no], click_obj, 'comment_modify');";
				else return "alert('��й�ȣ�� ��ġ���� �ʽ��ϴ�.'+SPACE); var scanf_passwd = $('div_scanf_passwd').getElementsByTagName('input')[0]; scanf_passwd.select(); scanf_passwd.focus();";
			}
		}
		else $this->popup_msg_js("�������� ������ �ƴմϴ�.", "BACK");
	}

// ��ۿ� ��� - 2011.08.16 added
   function comment_reply($datas) {
		//
		//
		// �������� ��� �ٷ� ���
		// �������� �������� üũ
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano']) {

			$comment_infos = $this->queryFetch("select no, ano, uid, upasswd, wdate from $this->board_comment_table where no=$datas[no] and ano=$datas[ano]");
			return "rankup_board.comment_reply_form($datas[no], click_obj);"; // ������ �ε�
		}
		else $this->popup_msg_js("�������� ������ �ƴմϴ�.", "BACK");
	}


	// ��� ����ó�� - �����ƾ - 2009.09.14 move-in
    function _delete_comment($datas) {
		//���-��ۿ� ���� ó�� ����
		if($datas[cnum]<=0) $this->query("delete from $this->board_comment_table where no=$datas[no] and ano=$datas[ano]");
		else $this->query("UPDATE $this->board_comment_table set content='������ �����Դϴ�.', remove='yes' where no=$datas[no] and ano=$datas[ano]");
		if($datas[pno]>0){
			$this->query("UPDATE $this->board_comment_table set cnum=cnum-1 where no=$datas[pno]");
			$parent_infos = $this->queryFetch("select cnum, remove from $this->board_comment_table where no=$datas[pno];");
			if($parent_infos[cnum]==0 && $parent_infos[remove]=="yes")
			$this->query("delete from $this->board_comment_table where no=$datas[pno]");
		}
		if(!mysql_affected_rows()) return false;
		$this->query("update $this->board_table set cnum=if(cnum=0, 0, cnum-1) where no=$datas[ano]");

		// ��� ����Ʈ ���̺��� ����
		// ���� �Խù��� �ڸ�Ʈ�� ������ comment_best_table ���� ����
		$cnums = $this->queryR("select cnum from $this->board_table where no=$datas[ano]");
		if($cnums>0) $this->query("update $this->comment_best_table set acnum=if(acnum=0, 0, acnum-1) where ano=$datas[ano] and bid='$this->board_id'");
		else {
			$this->query("delete from $this->comment_best_table where ano=$datas[ano]");
			if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->comment_best_table");
		}
		// �ְ� ��� ����Ʈ �ٿ�ī��Ʈ
		if(!empty($datas['uid']) && $this->board_extension===true) {
			$this->query("update $this->weekly_cbest_table set cnum=if(cnum=0, 0, cnum-1) where bid='$this->board_id' and uid='$datas[uid]' and ano=$datas[ano]");
			if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->weekly_cbest_table");

			// ȸ�� ��� ���̺����� ����
			$this->query("delete from $this->my_comment_table where bid='$this->board_id' and uid='$datas[uid]' and ano=$datas[ano]");
			if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->my_comment_table");
		}
		return true;
	}
	// ��� ����
    function delete_comment($datas) {
		// ��� �������� üũ
		// �α��� ȸ���� ��� �ڽ��� ����� �ڸ�Ʈ���� üũ - �ٷ� ����
		// �������� ��� �ٷ� ����
		// �׿��� ��쿡�� ��й�ȣ â ���
		// �������� �������� üũ
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano']) {
			$comment_infos = $this->queryFetch("select no, ano, pno, cnum, remove, uid, upasswd, wdate from $this->board_comment_table where no=$datas[no] and ano=$datas[ano]");
			if($this->is_admin() ||
				(empty($comment_infos['uid']) && $comment_infos['upasswd']===$datas['passwd']) ||
				(!empty($comment_infos['uid']) && ($comment_infos['uid']===$this->member_id || $datas['passwd']===$this->get_member_passwd($comment_infos['uid'])))) {
				$obj_name = ($comment_infos['pno']>0) ? "div_comment_reply_view_".$comment_infos['pno'] : "div_comment_articles";

				if($datas['passwd']==="undefined") return "rankup_board.comment_delete($datas[no], 'comment_delete');";
				if($this->_delete_comment($comment_infos)){
					return "alert('���������� �����Ǿ����ϴ�.'+SPACE); parent.rankup_board.comment_load(); var cnumObj = $('div_comment_nums').getElementsByTagName('span')[0]; cnumObj.innerHTML = parseInt(cnumObj.innerHTML, 10) - 1; parent.rankup_board.comment_reply_form('bottom', $('reply_bottom'));";
				}
			}
			else {
				if(in_array($datas['passwd'], array('', "undefined"))) return "rankup_board.scanf_passwd($datas[no], click_obj, 'comment_delete');";
				else return "alert('��й�ȣ�� ��ġ���� �ʽ��ϴ�.'+SPACE); var scanf_passwd = $('div_scanf_passwd').getElementsByTagName('input')[0]; scanf_passwd.select(); scanf_passwd.focus();";
			}
		}
		else $this->popup_msg_js("�������� ������ �ƴմϴ�.", "BACK");
	}

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// ��ũ���� �ĺ��ڵ�
	function get_discern_name() {
		$rbUser = $_COOKIE['rbUser'];
		if(empty($rbUser)) {
			$rbUser = base64_encode($this->uniqueTimeStamp());
			@setCookie("rbUser", $rbUser, time()+86400, "/"); // 1�� ¥�� ��Ű����
		}
		return $rbUser;
	}

	// �ӽ� ���ε� ���� ����
	function clear_junk_files($write_mode=false) {
		// ������� ������ ���� ���� : �������� 3�ð� ��
		$attach_dir = $this->board_dir."attach/".$this->board_id."/";
		if(is_dir($attach_dir) && $dh=opendir($attach_dir)) {
			// ��ũ���� �ĺ��ڵ�
			$rbUser = $this->get_discern_name();
			while(($file = readdir($dh)) !== false) {
				if(in_array($file, array(".", "..")) || strtoupper(filetype($attach_dir.$file))=="DIR") continue;
				$file_names = explode(".", $file);
				if($file_names[0]!="_junk_") continue; // junk ������ �ƴϸ� ��ŵ
				if($write_mode && $file_names[1]==$rbUser) @unlink($attach_dir.$file); // ȸ���� �÷ȴ� �ӽ� �����̸� ����
				else if(filectime($attach_dir.$file)<=strtotime("-3 hours")) @unlink($attach_dir.$file); // 3�ð��� ���� ���� ����
			}
			closedir($dh);
		}
	}

	// ����� �����
	function make_thumbnail($source_file, $dest_file, $width=null, $height=null) {
		ini_set("memory_limit", "80M"); // �޸� ������ ���� �ְ� ����

		list($image_width, $image_height, $image_type) = getimagesize($source_file);
		if($image_type==1) $source = imagecreatefromgif($source_file);
		else if($image_type==2) $source = imagecreatefromjpeg($source_file);
		else if($image_type==3) $source = imagecreatefrompng($source_file);
		else return;

		$thumb_width = $thumb_height = null;
		if($width!=null && $image_width>$width) {
			$thumb_width = $width;
			$thumb_height = $image_height / ($image_width/$width);
		}
		if($height!=null) {
			if($thumb_height!=null) {
				if($thumb_height>$height) {
					$thumb_width = $thumb_width / ($thumb_height/$height);
					$thumb_height = $height;
				}
			}
			else if($image_height>$height) {
				$thumb_height = $height;
				$thumb_width = $image_width / ($image_height/$height);
			}
		}
		if($thumb_width==null||$thumb_height==null) {
			if($source_file!=$dest_file) @copy($source_file, $dest_file);
		}
		else {
			$new_source = imagecreatetruecolor($thumb_width, $thumb_height);
			imagecopyresampled($new_source, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $image_width, $image_height);
			if($image_type==1) imagegif($new_source, $dest_file);
			else if($image_type==2) imagejpeg($new_source, $dest_file);
			else if($image_type==3) imagepng($new_source, $dest_file);
			imagedestroy($new_source);
		}
		return array_pop(explode("/", $dest_file));
	}

	// ���͸�ũ ���� - 2010.10.22 added
	function append_watermark($canvasImage) {
		if($this->wm_settings['use_watermark']!='yes') return false;
		if($this->sfunction['use_watermark']!='on') return false;
		$watermark_image = $this->base_dir."rankup_module/rankup_board/watermark/".$this->get_watermark_image($this->base_dir."rankup_module/rankup_board/watermark/");
		$this->watermark_image($canvasImage,$watermark_image,$this->wm_settings['watermark_location'],$this->wm_settings['watermark_transcolor'],$this->wm_settings['watermark_opacity'],$this->wm_settings['watermark_margin']);
	}

	// ÷������ - �̸�����
	function post_attached($local_file, $make_thumb=false) {
		if(empty($local_file['tmp_name'])) return false;

		$ext = array_pop(explode(".", strtolower($local_file['name'])));
		if(!empty($ext)) $ext = ".$ext";

		// ��ũ���� ���̹� : _junk_.userCookie.unique_time_stamp.extension
		$file_name = $this->uniqueTimeStamp();
		$junk_file_name = "_junk_.".$this->get_discern_name().".".$file_name.$ext;
		$remote_file = $this->board_dir."attach/".$this->board_id."/".$junk_file_name;

		move_uploaded_file($local_file['tmp_name'], $remote_file); // ��ũ���� ����

		// ���� ����
		$infos = getimagesize($remote_file);

		// ��������� ���̹� : _junk_.userCookie.tmb_.file_unique_time_stamp.extension
		if($make_thumb===true && in_array($infos[2], array(2,3))) { // jpg, png �����
			$junk_thumb_file_name = "_junk_.".$this->get_discern_name().".thumb_".$file_name.$ext;
			$remote_thumb_file = $this->board_dir."attach/".$this->board_id."/".$junk_thumb_file_name;

			// �������� - ����� ���� ���� - 2010.04.28 fixed
			if($this->board_configs['sgallery']) $sgallery = @unserialize($this->board_configs['sgallery']);
			if($sgallery['thumb_width'] && $sgallery['thumb_height']) list($thumb_width, $thumb_height) = array($sgallery['thumb_width'], $sgallery['thumb_height']);
			else list($thumb_width, $thumb_height) = array($this->thumbnail_width, $this->thumbnail_height);
			$this->make_thumbnail($remote_file, $remote_thumb_file, $thumbnail_width, $thumbnail_height);
		}

		//���͸�ũ ����
		$this->append_watermark($remote_file);

		$infos[2] = $this->get_extension($remote_file); // Ȯ���ڸ�
		$infos[4] = array_pop($this->get_file_size($remote_file));
		$board_url = ($this->base_url!=="/") ? str_replace($this->base_url, '', $this->board_url) : substr($this->board_url, 1);
		return array("name"=>$board_url."attach/".$this->board_id."/".$junk_file_name, "infos"=>$infos);
	}

	// ÷������ ����
	function delete_attach($datas) {
		if(in_array($datas['file'], array('', 'undefined'))) return false;
		// ���������� �ִ��� üũ
		if(!$this->check_granted("write_level")) return false;

		// ���� ������ ���ϻ��� ��û�� ������������ üũ
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		$is_junk = @array_shift(explode(".", $datas['file']))=="_junk_"; // ��ũ���� ����
		if(!empty($datas['ano']) && !$is_junk && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano'] && $referer_infos['mode']=="write") {
			// �ش� �Խù��� attach �ʵ� ���� ����
			$board_infos = $this->queryFetch("select no, uid, attach from $this->board_table where no=$datas[ano]");
			$attaches = unserialize($board_infos['attach']);
			if($this->check_resource($attaches)) {
				$file_name = basename($datas['file']);
				foreach($attaches as $key=>$val) {
					if($val['sname']!=$file_name) continue;
					unset($attaches[$key]);
					$_val['attach'] = serialize($attaches);
					$values = $this->change_query_string($_val);
					$this->query("update $this->board_table set $values where no=$datas[ano]"); // ���� ����
					if(is_file($this->base_dir.$datas['file'])) {
						@unlink($this->base_dir.$datas['file']); // ���ϻ���
						$file_infos = explode(".", $file_name);
						// ����� ����
						if($file_infos[0]=="_junk_") $file_infos[2] = "thumb_".$file_infos[2];
						else $file_infos[0] = "thumb_".$file_infos[0];
						$thumb_file = @implode(".", $file_infos);
						if(is_file($this->base_dir.$thumb_file)) @unlink($this->base_dir.$thumb_file);
					}
					break;
				}
			}
		}
		// �������� �ƴѰ�� �ۼ��ڰ� ÷���ߴ� ��ũ���ϸ� ����
		else if(file_exists($this->base_dir.$datas['file'])) {
			$file_names = explode(".", array_pop(explode("/", $datas['file'])));
			if($file_names[0]=="_junk_" && $file_names[1]==$this->get_discern_name()) {
				@unlink($this->base_dir.$datas['file']);
				// ����� ���� - 2009.01.13 fixed
				$file_names[2] = "thumb_".$file_names[2];
				$thumb_file = $this->board_dir."attach/".$this->board_id."/".implode(".", $file_names);
				if(is_file($thumb_file)) @unlink($thumb_file);
			}
		}
		return true;
	}

	// ÷������ ������ ����
	function load_attach($datas) {
		// �������� ��û������ üũ
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['mode']=="write") {
			$sgallery = unserialize($this->board_configs['sgallery']);
			if(!empty($datas['ano']) && $referer_infos['no']==$datas['ano']) {
				$attach_dir = $this->board_dir."attach/".$this->board_id."/";
				$board_url = ($this->base_url!=="/") ? str_replace($this->base_url, '', $this->board_url) : substr($this->board_url, 1);
				$attach_url = $board_url."attach/".$this->board_id."/";
				$attaches = $this->queryR("select attach from $this->board_table where no=$datas[ano]");
				$attaches = unserialize($attaches);
				if($this->check_resource($attaches)) {
					foreach($attaches as $key=>$val) {
						// ���� ������ �������� �ʴ� ������ ����
						if(!is_file($attach_dir.$val['sname'])) $infos = array();
						else $infos = getimagesize($attach_dir.$val['sname']);
						$infos[2] = $this->get_extension($attach_dir.$val['sname']); // Ȯ���ڸ�
						$infos[4] = array_pop($this->get_file_size($attach_dir.$val['sname']));
						$item .= "
						<item key='$key'>
							<fname><![CDATA[{$attach_url}{$val[sname]}]]></fname>
							<fwidth><![CDATA[{$infos[0]}]]></fwidth>
							<fheight><![CDATA[{$infos[1]}]]></fheight>
							<ftype>$infos[2]</ftype>
							<fsize>$infos[4]</fsize>
						</item>";
					}
				}
			}
			$item = "
			<attach max_width='".$sgallery['picture_width']."' max_nums='".$this->sattach['attach_nums']."' />$item";
			return $item;
		}
		return false;
	}

	// ÷������ �ٿ�ε�
	function download_attach($datas) {
		// �Խù� �󼼺��� ���� üũ
		if(!$this->check_granted("read_level")) $this->popup_msg_js("�Խù� �ٿ�ε� ������ �����ϴ�.", "VOID");

		// �������� ��û������ üũ
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano']) {
			$article_infos = $this->queryFetch("select uid, attach from $this->board_table where no=$datas[ano]");
			$attaches = unserialize($article_infos['attach']);
			if($this->check_resource($attaches)) {
				foreach($attaches as $key=>$val) {
					if($key!=$datas['fid'] || $val['oname']!=$datas['fname']) continue;
					$attach_dir = $this->board_dir."attach/".$this->board_id."/";
					if(is_file($attach_dir.$val['sname'])) {
						$this->down_file($attach_dir.$val['sname'], $val['oname']); // ���ϴٿ�ε�
						// ���ϴ� �ٿ�ε尡 �Ϸ�Ǹ� ó���� - ��, ���ϻ���� ������쿡�� ó����
						$attaches[$key]['dnum'] += 1; // �ٿ�ε� Ƚ�� ����
						$_val['attach'] = serialize($attaches);
						$values = $this->change_query_string($_val);
						$this->query("update $this->board_table set $values where no=$datas[ano]"); // ���� ����
					}
					break;
				}
			}
			// ���������� ������ �ٿ�ε� �Ǿ��ٸ� �̺κб��� ������� �ʴ´�.
			$this->popup_msg_js("��û�Ͻ� ����('$datas[fname]')�� �������� �ʽ��ϴ�.", "VOID");
		}
		else $this->popup_msg_js("�Խù� ������������ �ٿ�ε� �Ͻñ� �ٶ��ϴ�.", "BACK");
	}
}
?>