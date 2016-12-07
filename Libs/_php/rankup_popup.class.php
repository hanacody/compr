<?php
## �˾��� ó���ϱ� ���� Ŭ���� 
class rankup_popup extends rankup_util {
	var $table = "rankup_popup"; // �˾��� �����ϴ� ���̺�	
	var $base_url; // ��� ���
	var $base_dir; // ���� ���
	var $version = "v1.0 r080609";
	function rankup_popup() {
		parent::rankup_util();
		$this->base_url = rankup_basic::base_url();
		$this->base_dir = rankup_basic::base_dir();
		$this->check_table(); // ���̺� üũ
	}

	// �˾� ���̺� üũ
	function check_table() {
		$check_table = $this->queryR("show tables like '$this->table'");
		if($check_table===$this->table) return true;
		return $this->query("
			CREATE TABLE `$this->table` (
			  `no` int(11) unsigned NOT NULL auto_increment,
			  `rank` smallint(5) unsigned NOT NULL default '0',
			  `type` enum('text','skin1','skin2','skin3') NOT NULL default 'text',
			  `title` varchar(30) NOT NULL default '',
			  `width` smallint(5) unsigned default NULL,
			  `height` smallint(5) unsigned default NULL,
			  `width_type` enum('pixel','exact') default NULL,
			  `height_type` enum('pixel','exact') default NULL,
			  `content` mediumtext NOT NULL,
			  `use_date` enum('yes','no') NOT NULL default 'no',
			  `sdate` datetime default NULL,
			  `edate` datetime default NULL,
			  `wdate` datetime NOT NULL default '0000-00-00 00:00:00',
			  `view` enum('yes','no') NOT NULL default 'yes',
			  PRIMARY KEY  (`no`),
			  KEY `rank` (`rank`),
			  KEY `view` (`view`)
			) TYPE=MyISAM AUTO_INCREMENT=1;
		");
	}

	// �˾����/����
	function regist_popup($datas, $DML="insert") { // $mode : { 'insert'  or  'update' }
		//���� �Է°� ����
		$_val['type'] = $datas['popup_type']; // �˾���Ų
		$_val['title'] = addslashes($datas['title']); // �˾�����
		$_val['use_date'] = $datas['period']; // ������� ����

		// �̵������ ��Ͻ�
		if($datas['popup_type']=="text") {
			$_val['width_type'] = $datas['width_type']; // ���λ����� ����
			$_val['height_type'] = $datas['height_type']; // ���λ����� ����
			$_val['width'] = $datas['width_type']=="exact" ? 0 : $datas['width']; // ���λ�����
			$_val['height'] = $datas['height_type']=="exact" ? 0 : $datas['height']; // ���λ�����
		}
		// �����Է½�
		else {
			$_val['width'] = 0;
			$_val['height'] = 0;
			$_val['width_type'] = 'exact';
			$_val['height_type'] = 'exact';
		}
		$_val['content'] = addslashes($datas['content']); // ����

		// ������� ������
		if($datas['period']=="yes") {
			$period_mins = array("����"=>"00", "��"=>"30");
			if($datas['period_sdate'] && $datas['period_shour'] && $datas['period_sminute']) {
				$_val['sdate'] = "$datas[period_sdate] $datas[period_shour]:".$period_mins[$datas['period_sminute']].":00"; // ���� ������
			}
			if($datas['period_edate'] && $datas['period_ehour'] && $datas['period_eminute']) {
				$_val['edate'] = "$datas[period_edate] $datas[period_ehour]:".$period_mins[$datas['period_eminute']].":00"; // ���� ������
			}
		}
		// ���� ���� / �Է�
		switch(strtolower($DML)) {
			case "insert":
				$_val['rank'] = 1; // ����
				$_val['wdate'] = date("Y-m-d H:i:s"); // �����
				break;
			case "update":
				if(empty($datas['no'])) return false;
				$addWhere = " where no=$datas[no]";
				break;
		}
		$values = $this->change_query_string($_val);
		$result = $this->query("$DML $this->table set $values$addWhere");
		return $result ? $DML=="insert" ? mysql_insert_id() : $datas['no'] : false;
	}

	// �˾�����
	function delete_popup($datas) {
		// ���ڵ� ����
		$this->query("delete from $this->table where no in(".str_replace("__", ",", $datas).")");
		$result = mysql_affected_rows() ? true : false;

		// ���̺� ����ȭ
		if($result) $this->query("optimize table $this->table");
		return $result;
	}

	// �˾����� XML�� �ۺ��� - ��������������� ���
	function get_xml_formalize($datas) {
		foreach($datas as $rows) {
			$items .= "
			<item type='$rows[type]' no='$rows[no]'>
				<title><![CDATA[".$rows['title']."]]></title>
				<top><![CDATA[0]]></top>
				<left><![CDATA[0]]></left>
				<width type='$rows[width_type]'><![CDATA[".$rows['width']."]]></width>
				<height type='$rows[height_type]'><![CDATA[".$rows['height']."]]></height>
				<content><![CDATA[".$rows['content']."]]></content>
			</item>";
		}
		return $items;
	}

	// �˾����� HTML�� �ۺ��� - ���������������� ���
	function get_html_formalize($datas, $single_mode=false) {

		$views = array("yes"=>"use", "no"=>"unused");
		$targets = array("_self"=>"selfwin", "_blank"=>"newwin");

		// ���۹�ư ���� - ����
		$control_buttons[0] = "<img src='./img/order_high.gif' onClick=\"classObj.set_direction(this, 'up')\" style='margin:1px'><img src='./img/order_low.gif' onClick=\"classObj.set_direction(this, 'down')\" style='margin:1px'>"; // ���� | �Ʒ���
		$control_buttons[1] = "<img src='./img/bt_{:view:}.gif' value='{:view:}' onClick=\"classObj.ajax_process('view')\">"; // ��� | �̻��
		$control_buttons[2] = "<a onClick=\"classObj.preview_popup()\"><img src='./img/bt_preview.gif'></a><a onClick=\"classObj.ajax_process('modify')\"><img src='./img/bt_edit_s.gif' style='margin-left:2px'></a><a onClick=classObj.ajax_process('delete')><img src='./img/bt_del_s.gif' style='margin-left:2px'></a>"; // �̸����� | ���� | ����

		// ���ø� popupBody - ����
		$template['popupBody'] = "
		<tr>
			<td>
				<table name='popupItem' id='popupItem' width='100%' cellpadding='0' cellspacing='0' border='0' bgcolor='white'>
					<tbody>{:popupItem:}
					</tbody>
				</table>
			</td>
		</tr>";
		// ���ø� popupItem - ����
		$template['popupItem'] = "
							<table id='item' width='100%' cellpadding='4' cellspacing='0' border='0'>
							<colgroup align='center'><col span='2' width='40'><col></colgroup>
							<tr>
								<th><input type='checkbox' name='chk_no[]' value='{:no:}'><input type='hidden' name='no[]' value='{:no:}'></th>
								<th>$control_buttons[0]</th>
								<th>
									<table id='itemInfo' width='100%' height='145' cellpadding='2' cellspacing='0' border='0' style='table-layout:fixed'>
									<tr height='30'>
										<th nowrap style='padding:0px;'>
											<table style='float:left;' cellpadding='0' cellspacing='0'>
											<tr><th style='padding:0px 0px 0px 4px;' id='controlButtons'>$control_buttons[1]</th></tr>
											</table><div id='settingsInfo'>{:popup_infos:}</div></th>
										<th width='180' nowrap style='text-align:right;padding:0px'>$control_buttons[2]</th>
									</tr>
									<tr height='100%' valign='top'>
										<th colspan='2'>{:popup_contents:}</th>
									</tr>
									</table>
								</th>
							</tr>
							</table>";
		// �̱۸�尡 �ƴѰ��
		if($single_mode==false) {
			$template['popupItem'] = "
					<tr>
						<td>$template[popupItem]
						</td>
					</tr>";
		}

		// ������ ���� ��ȯ�� �ڵ�
		$size_types = array("pixel"=>"px", "exact"=>"%");
		$period_mins = array("00"=>"����", "30"=>"��");

		foreach($datas as $rows) {
			// ���� ���� ����
			if($rows['use_date']=="no") $sdate = $edate = '';
			else {
				$sdate = $rows['sdate'] ? date("Y.m.d H�� ".$period_mins[date("i", strtotime($rows['sdate']))]."����", strtotime($rows['sdate'])) : '';
				$edate = $rows['edate'] ? date("Y.m.d H�� ".$period_mins[date("i", strtotime($rows['sdate']))]."����", strtotime($rows['edate'])) : '';
			}
			$_hidden = ($rows['type']=="text") ? '' : " style='display:none'";
			$_width = $_height = $__width = $__height = "100%";
			$___width = $___height = $_comment = '';
			if($rows['width_type']=="exact") {
				$_hidden = " style='display:none'";
				$_comment = ($rows['height_type']=="exact") ? "�ڵ�����" : "�������� ".$rows['height'].$size_types[$rows['height_type']];
			}
			else {
				$_width = $rows['width'].$size_types[$rows['width_type']];
				$__width = $rows['width'];
				$___width = " width='{$rows['width']}'";
			}
			if($rows['height_type']=="exact") {
				$_hidden = " style='display:none'";
				$_comment = ($rows['width_type']=="exact") ? "�ڵ�����" : "�������� ".$rows['width'].$size_types[$rows['width_type']];
			}
			else {
				$_height = $rows['height'].$size_types[$rows['height_type']];
				$__height = $rows['height'];
				$___height = " height='{$rows['height']}'";
			}

			// �˾� ��� ���� - �˾������� javascript ���� span �±� ������� ������ ����ϹǷ� ������ ���� ���!
			$popup_infos = "<span id='width' type='$rows[width_type]'$_hidden>$_width</span><font$_hidden>��</font><span id='height' type='$rows[height_type]'$_hidden style='margin-right:10px;'>$_height</span><span id='type' type='$rows[type]' value='$media_kind' style='padding-right:15px;'>$_comment</span><span id='use_date' value='$rows[use_date]'><span id='sdate'>$sdate</span> <span id='edate'>$edate</span></span>";

			// ��ũ��Ʈ �ڵ������� �����ϱ� ���� �ڵ� ��ȯ
			if($single_mode===true) $rows['content'] = str_replace("\\", "\\\\", $rows['content']); // stripslashes �����Ƚ� - 2008.06.09
			$text_content = "<span id='text_data' width='$__width' height='$__height' style='display:none'>".str_replace(array("<", ">"), array("{:_lt:}", "{:_gt:}"), $rows['content'])."</span><iframe id='textContent$rows[no]' frameborder='0' style='width:100%;height:0px;'></iframe>";
			$popup_contents = "
			<table border='0' cellpadding='1' cellspacing='1' bgcolor='#cacaca'>
			<tr>
				<th bgcolor='#ffffff' style='padding:1px;'>
					<table border='0' cellpadding='3' cellspacing='0' bgcolor='#f1f1f1'>
					<tr>
						<th nowrap style='font-weight:bolder;color:black;padding:5px 3px 0px 4px;'><label style='font-family:dotum;font-size:9pt;'>$rows[title]</label></th>
					</tr>
					<tr>
						<th style='padding:3px;'>
							<table$___width$___height cellpadding='0' cellspacing='0'>
							<tr valign='top'>
								<th style='padding:0px'><span id='text'></span></th>
							</tr>
							</table>
						</th>
					</tr>
					<tr>
						<th background='/rankup_module/rankup_popup/img/dp_background.gif' style='padding:0px'>
							<table border='0' cellspacing='0' cellpadding='0' align='right'>
							<tr>
								<th style='padding:0px'><input type='checkbox' disabled></th>
								<th style='padding:0px'><label style='padding:2 5 0 0px;height:14px;font-size:9pt;cursor:pointer' onFocus='this.blur()' disabled>�����Ϸ� �׸�����</label></th>
								<th style='padding:3px 3px 4px 0px;'><img src='/rankup_module/rankup_popup/img/dp_bclose.gif' border='0'></th>
							</tr>
							</table>
						</th>
					</tr>
					</table>
				</th>
			</tr>
			</table>$text_content";

			$popupItem = str_replace(array("{:no:}", "{:popup_infos:}", "{:popup_contents:}", "{:view:}"), array($rows['no'], $popup_infos, $popup_contents, $views[$rows['view']]), $template['popupItem']);
			$popupBody .= str_replace("{:popupItem:}", $popupItem, $template['popupBody']);
		}
		return $single_mode==true ? $popupItem : $popupBody;
	}

	// �˾����� - 2009.01.20 fixed
	function get_popup($datas, $xml_mode=true) {
		if($datas['no']) $addWhere .= "=$datas[no]";
		if($datas['view']) $addWhere .= " and view='$datas[view]'"; // ��¿���
		if($datas['check_date']=="yes") $addWhere .= " and (use_date='no' or (use_date='yes' and (sdate is NULL or sdate<=now()) and (edate is NULL or edate>=now())))";

		$popups = $this->queryFetchRows("select no,rank,type,title,width,height,width_type,height_type,content,use_date,sdate,edate,view from $this->table where no$addWhere order by rank");
		return $xml_mode ? $this->get_xml_formalize($popups) : $popups;
	}

	// �˾� ����Ʈ ���� - �����ڿ��� ���
	function get_popup_list() {
		// �˾����� ����
		$datas = $this->get_popup(array("position"=>$position), false);
		return $this->get_html_formalize($datas, false);
	}

	// �˾������� ���� :: ��뿩��/��������
	function set_popup($kind='') {
		switch($kind) {
			case "view": // ���/�̻�� �� ���� - ������ : GET
				$nos = str_replace("__", ", ", $_GET['data']);
				return $this->query("update $this->table set view='$_GET[val]' where no in($nos)");
				break;

			default: // ���� �� ���� - ������ : POST
				if(!count($_POST['no'])) return false;
				foreach($_POST['no'] as $rank => $no) {
					$_val['rank'] = $rank+1;
					$values = $this->change_query_string($_val);
					$this->query("update $this->table set $values where no=$no");
				}
		}
	}
}
?>