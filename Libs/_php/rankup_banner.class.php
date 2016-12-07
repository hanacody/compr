<?php
## ��ʸ� ó���ϱ� ���� Ŭ����
class rankup_banner extends rankup_util {
	var $version = "v1.5 r101220";
	var $table = "rankup_banner"; // ��ʸ� �����ϴ� ���̺�
	var $base_url; // ��� ���
	var $base_dir; // ���� ���
	var $positions = array(
		1 => array('ci_top' => '��ܷΰ�', 'width' => 165, 'height' => 80),
		2 => array('ci_bottom' => '�ϴܷΰ�', 'width' => 165, 'height' => 80),
		// 3�� ���ʹ� ��ʷ� ���
		// design/skin/skin.init.php ���Ͽ� ����
	);

	function rankup_banner() {
		global $_skin_init;
		parent::rankup_util();
		$this->base_url = rankup_basic::base_url();
		$this->base_dir = rankup_basic::base_dir();
		$this->check_table(); // ���̺� üũ

		// ��Ų ��������� ����
		if(is_array($_skin_init['banner_preset'])) $this->positions = $this->positions + $_skin_init['banner_preset'];
	}

	// ��� ���̺� üũ
	function check_table() {
		$check_table = $this->queryR("show tables like '$this->table'");
		if($check_table===$this->table) return true;
		return $this->query("
			CREATE TABLE `$this->table` (
			  `no` int(10) unsigned NOT NULL auto_increment,
			  `bind` tinyint(3) unsigned default NULL,
			  `rank` smallint(5) unsigned NOT NULL default '0',
			  `position` tinyint(3) unsigned NOT NULL default '0',
			  `type` enum('text','media') NOT NULL default 'media',
			  `address` varchar(255) default NULL,
			  `target` enum('_blank','_self') NOT NULL default '_blank',
			  `outline` enum('on','off') NOT NULL default 'on',
			  `width` smallint(5) unsigned default NULL,
			  `height` smallint(5) unsigned default NULL,
			  `width_type` enum('pixel','percent','auto','exact') default NULL,
			  `height_type` enum('pixel','percent','auto','exact') default NULL,
			  `attached` varchar(255) default NULL,
			  `content` text,
			  `use_date` enum('yes','no') default 'no',
			  `sdate` datetime default NULL,
			  `edate` datetime default NULL,
			  `wdate` datetime NOT NULL default '0000-00-00 00:00:00',
			  `view` enum('yes','no') NOT NULL default 'yes',
			  PRIMARY KEY  (`no`),
			  KEY `position` (`position`),
			  KEY `bind` (`bind`),
			  KEY `rank` (`rank`),
			  KEY `view` (`view`)
			) TYPE=MyISAM AUTO_INCREMENT=1;
		");
	}

	// ������ �����ġ ���� - �����޴� ��ܿ� ����
	function print_positions($logo_mode=false) {
		$count = 0;
		$max_cols = $logo_mode ? 2 : 7;
		foreach($this->positions as $key=>$vals) {
			if($logo_mode==false && $key<3) continue;
			else if($logo_mode && $key>2) break;
			$style = ($_GET['position']==$key) ? " style='font-weight:bolder;'" : '';

			// ���(�ΰ� or ���)�� ��Ÿ��
			if($logo_mode==true) $link_template = "<td><img src='".$this->base_url."Libs/_images/ic_arrow1.gif' align='absmiddle'> <a href='./index.html?position={:key:}'$style>{:val:}</a></td>";
			else $link_template = "<td><img src='".$this->base_url."Libs/_images/ic_arrow1.gif' align='absmiddle'> <a href='./index.html?position={:key:}'$style>{:val:}</a></td>";

			$_item .= str_replace(array("{:key:}", "{:val:}"), array($key, array_shift(array_values($vals))), $link_template);
			if(!(++$count%$max_cols)) {
				$items .= "<tr>$_item</tr>";
				unset($_item);
			}
		}
		if(isset($_item)) {
			$_tds = str_repeat("<td>&nbsp;</td>", $max_cols-($count%$max_cols));
			$items .= "<tr>$_item$_tds</tr>";
			unset($_item, $_tds);
		}
		return $items;
	}

	// ��������� ���� ���� - ���/���� ���� ����
	function print_sizes($position, $logo_mode=false) {
		foreach($this->positions as $key=>$rows) {
			if($logo_mode==false && $key<3) continue;
			else if($logo_mode==true && $key>2) break;
			$vals = array_values($rows);
			if($key==$position) {
				$_name = "<b>�� $vals[0]</b>";
				$_size = empty($vals[2]) ? "<b>���� $vals[1]px ����</b>" : "<b>$vals[1] �� $vals[2]</b>";
				$_apply = "<b>����</b>";
			}
			else {
				$_name = $vals[0];
				$_size = empty($vals[2]) ? "���� $vals[1]px ����" : "$vals[1] �� $vals[2]";
				$_apply = "����";
			}
			$sizes .= "
			<tr$bgcolor>
				<td>$_name</td><td>$_size</td><td><a onClick=\"classObj.apply_media_size($vals[1],$vals[2])\">$_apply</a></td>
			</tr>";
			$bgcolor = empty($bgcolor) ? "  bgcolor='#F3F5F8'" : '';
		}
		return $sizes;
	}

	// ����÷�� - �̸�����
	function post_attached($local_file, $logo_mode=false) {
		if(empty($local_file['tmp_name'])) return false;

		$ext = array_pop(explode(".", strtolower($local_file['name'])));
		if(!empty($ext)) $ext = ".$ext";

		$temp_name = $logo_mode ? "_new_logo" : "_new_banner";
		$remote_file = $this->base_dir."RAD/PEG/$temp_name$ext";
		if(is_file($remote_file)) @unlink($remote_file); // �ӽ����� ����

		move_uploaded_file($local_file['tmp_name'], $remote_file); // ����
		$infos = getimagesize($remote_file);
		$infos[2] = $this->get_extension($remote_file); // Ȯ���ڸ�
		return array("name"=>"RAD/PEG/$temp_name$ext", "infos"=>$infos);
	}

	// ��ʵ��/����
	function regist_banner($datas, $DML="insert") { // $DML : { 'insert'  or  'update' }
		//���� �Է°� ����
		$_val['position'] = $datas['position']; // �����ġ
		$_val['type'] = $datas['banner_type']; // ������� { media or text }
		$_val['use_date'] = $datas['period']; // ������� ����

		// �̵������ ��Ͻ�
		if($datas['banner_type']=="media") {
			$_val['width'] = $datas['width']; // ���λ�����
			$_val['height'] = $datas['height']; // ���λ�����
			$_val['width_type'] = $datas['width_type']; // ���λ����� ����
			$_val['height_type'] = $datas['height_type']; // ���λ����� ����

			if(empty($datas['address']) || $datas['address']=="http://") $_val['address'] = $_val['target'] = null;
			else {
				$_val['address'] = $datas['address']; // ��ũ��ų �ּ�
				$_val['target'] = $datas['popup_banner']=="on" ? "_blank" : "_self";
			}
			if(!empty($datas['on_attached'])) {
				$local_file = $this->base_dir."RAD/PEG/".$datas['on_attached'];
				if(is_file($local_file)) {
					// ÷������ ���̹�
					$_ext = array_pop(explode(".", $datas['on_attached']));
					if(!empty($_ext)) $_ext = ".$_ext";
					$name_head = ($datas['position']<3) ? "logo_" : "banner_";
					$_val['attached'] = $name_head.$this->uniqueTimeStamp().$_ext;
					rename($local_file, $this->base_dir."RAD/PEG/".$_val['attached']); // �ӽ�÷������ �̸� ����
					if(array_pop(explode(".", strtolower($datas['on_attached'])))=="swf") {
						$_val['address'] = null;
						$_val['target'] = null;
					}
				}
			}
			$_val['outline'] = ($datas['mediaOutlineChecker']=='on') ? 'on' : 'off'; // 2010.12.20 added
			$_val['content'] = null;
		}
		// �����Է½�
		else {
			$_val['width'] = 0;
			$_val['height'] = 0;
			$_val['width_type'] = 'auto';
			$_val['height_type'] = 'auto';
			$_val['address'] = null;
			$_val['target'] = null;
			$_val['attached'] = null;
			$_val['content'] = $datas['content']; // ����
		}
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
				$_val['wdate'] = date("Y-m-d H:i:s"); // �����
				break;
			case "update":
				if(empty($datas['no'])) return false;
				// ���� ���� ����
				$prev_infos = $this->queryFetch("select type, target, attached from $this->table where no=$datas[no] and attached is not null");
				if(!empty($prev_infos['type']) && ($prev_infos['type']!=$_val['type'] || !empty($_val['attached']))) {
					if(is_file($this->base_dir."RAD/PEG/".$prev_infos['attached'])) @unlink($this->base_dir."RAD/PEG/".$prev_infos['attached']);
				}
				// ÷�������� ���� ��� ��â/��â ���� - 2008.06.11 �����Ƚ�
				if($_val['target']!=null && !empty($prev_infos['target']) && $datas['banner_type']=="media") $_val['target'] = $datas['popup_banner']=="on" ? "_blank" : "_self";
				$_val['bind'] = empty($datas['bind']) ? null : $datas['bind']; // ���ε� ��ȣ
				$addWhere = " where no=$datas[no]";
				break;
		}
		$values = $this->change_query_string($_val);
		$result = $this->query("$DML $this->table set $values$addWhere");
		return $result ? $DML=="insert" ? mysql_insert_id() : $datas['no'] : false;
	}

	// ��ʻ���
	function delete_banner($datas) {
		// ÷������ ����
		$prev_datas = $this->queryFetchRows("select attached from $this->table where no in(".str_replace("__", ",", $datas).") and attached is not null");
		if(is_array($prev_datas) && count($prev_datas)) {
			foreach($prev_datas as $rows) {
				if(is_file($this->base_dir."RAD/PEG/".$rows['attached'])) @unlink($this->base_dir."RAD/PEG/".$rows['attached']);
			}
		}
		// ���ڵ� ����
		$this->query("delete from $this->table where no in(".str_replace("__", ",", $datas).")");
		$result = mysql_affected_rows() ? true : false;

		// ���̺� ����ȭ
		if($result) $this->query("optimize table $this->table");
		return $result;
	}

	// ��ġ���� Ű�� ��ȯ :: get_position_key('ci_top') -> '1'
	function get_position_key($string_key) {
		foreach($this->positions as $key => $vals) if(array_key_exists($string_key, $vals)) return $key;
		return false;
	}

	// ������� XML�� �ۺ��� - �̻��
	function get_xml_formalize($datas) {
		foreach($datas as $rows) {
			$address = str_replace(array("&"), array("&amp;"), $rows['address']);
			if($old_position !== $rows['position']) {
				$old_position = $rows['position'];
				if(!empty($_items)) {
					$_positions .= "<postion value='".$this->positions[$rows['position']]."'>".$_items."</postion>";
					unset($_items);
				}
			}
			$_items .= "
			<item type='$rows[type]' no='$rows[no]' bind='$rows[bind]' rank='$rows[rank]' view='$rows[view]'>
				<address target='$rows[target]'><![CDATA[$address]]></address>
				<width type='$rows[width_type]'><![CDATA[".$rows['width']."]]></width>
				<height type='$rows[height_type]'><![CDATA[".$rows['height']."]]></height>
				<attached><![CDATA[".$rows['attached']."]]></attached>
				<content><![CDATA[".$rows['content']."]]></content>
			</item>";
		}
		if(isset($_items)) {
			$_positions .= "<postion value='".$this->positions[$old_position]."'>".$_items."</postion>";
			unset($_items);
		}
		return "<banner>".$_positions."</banner>";
	}

	// ������� HTML�� �ۺ��� - Ȩ���������� ��� - 2010.12.20 modified
	function get_html_formalize($datas, $single_mode=false, $logo_mode=false) {
		$views = array("yes"=>"use", "no"=>"unused");
		$targets = array("_self"=>"selfwin", "_blank"=>"newwin");

		// ���۹�ư ���� - ����
		$control_buttons[0] = "<img src='./img/order_high.gif' onClick=\"classObj.set_direction(this, 'up')\" align='absmiddle' vspace='1'><img src='./img/order_low.gif' onClick=\"classObj.set_direction(this, 'down')\" align='absmiddle' vspace='1'>"; // ���� | �Ʒ���
		$control_buttons[1] = "<img src='./img/bt_{:view:}.gif' value='{:view:}' onClick=\"classObj.ajax_process('view')\" align='absmiddle' hspace='1'>"; // ��� | �̻��
		$control_buttons[2] = "<img src='./img/bt_{:target:}.gif' value='{:target:}' onClick=\"classObj.ajax_process('target')\" align='absmiddle' vspace='1'>"; // ��â | ��â
		$control_buttons[3] = "<a onClick=\"classObj.preview_banner()\"><img src='./img/bt_preview.gif' align='absmiddle' hspace='1'></a><a onClick=\"classObj.ajax_process('modify')\"><img src='./img/bt_edit_s.gif' align='absmiddle' hspace='1'></a><a onClick=classObj.ajax_process('delete')><img src='./img/bt_del_s.gif' align='absmiddle' hspace='1'></a>"; // �̸����� | ���� | ����
		$control_buttons[4] = "<div><img src='./img/order_high.gif' onClick=\"classObj.set_bind_direction(this,'up')\" align='absmiddle' hspace='1'><img src='./img/order_low.gif' onClick=\"classObj.set_bind_direction(this,'down')\" align='absmiddle' hspace='1'><img src='./img/bt_bind_x.gif' onClick=\"classObj.resolve_item(this)\" align='absmiddle' hspace='1'></div>"; // ���ε� ���� | �Ʒ��� | ����
		$control_buttons[5] = "<a onClick=\"classObj.ajax_process('outline')\"><img src='./img/bt_outline.gif' align='absmiddle' hspace='1'></a>"; // �׵θ�ǥ�� - 2010.12.20 added

		// ���ø� bannerBody - ����
		$template['bannerBody'] = "
		<tr>
			<td class='{:bind_class:}'>
				<span id='bindToolBox'>{:bind_tools:}</span>
				<table name='bannerItem' id='bannerItem' width='100%' cellpadding='0' cellspacing='0' border='0' bgcolor='white'>
					<tbody>{:bannerItem:}
					</tbody>
				</table>
			</td>
		</tr>";
		// ���ø� bannerItem - ����
		$template['bannerItem'] = "
		<table id='item' width='100%' cellpadding='4' cellspacing='0' border='0'>
		<colgroup align='center'><col{:col_span:} width='30'><col></colgroup>
		<tr>
			<th><input type='checkbox' name='chk_no[]' value='{:no:}'><input type='hidden' name='bind_no[]' value='{:bind:}'><input type='hidden' name='no[]' value='{:no:}'></th>
			{:banner_rank:}
			<th>
				<table id='itemInfo' width='100%' height='145' cellpadding='2' cellspacing='0' border='0' style='table-layout:fixed'>
				<tr height='35'>
					<th nowrap id='controlButtons' style='border-bottom:1px #f4f4f4 solid'>
						$control_buttons[1]{:banner_target:}{:outline_button:}
						<div id='settingsInfo'>{:banner_infos:}</div>
					</th>
					<th width='173' nowrap style='text-align:right;padding:0;border-bottom:1px #f4f4f4 solid'>
						$control_buttons[3]
					</th>
				</tr>
				<tr height='100%' valign='top'>
					<th colspan='2' style='padding-top:8px'>{:banner_contents:}</th>
				</tr>
				</table>
			</th>
		</tr>
		</table>";
		// �̱۸�尡 �ƴѰ��
		if($single_mode==false) {
			$template['bannerItem'] = "
			<tr>
				<td>$template[bannerItem]
				</td>
			</tr>";
		}
		// �ΰ� ����� ��쿡�� �������� ��ư�� ����
		$banner_rank = $logo_mode ? '' : array(" span='2'", "<th>$control_buttons[0]</th>");
		$template['bannerItem'] = str_replace(array("{:col_span:}", "{:banner_rank:}"), $banner_rank, $template['bannerItem']);

		// ������ ���� ��ȯ�� �ڵ�
		$size_types = array("pixel"=>"px", "percent"=>"%", "exact"=>"px");
		$period_mins = array("00"=>"����", "30"=>"��");

		foreach($datas as $rows) {
			// ���� ���� ����
			if($rows['use_date']=="no") $sdate = $edate = '';
			else {
				$sdate = $rows['sdate'] ? date("Y.m.d H�� ".$period_mins[date("i", strtotime($rows['sdate']))]."����", strtotime($rows['sdate'])) : '';
				$edate = $rows['edate'] ? date("Y.m.d H�� ".$period_mins[date("i", strtotime($rows['sdate']))]."����", strtotime($rows['edate'])) : '';
			}
			if($rows['type']=="media") {
				$infos = @getimagesize($this->base_dir."RAD/PEG/".$rows['attached']);
				$infos[2] = strtoupper($this->get_extension($rows['attached']));
				$media_kind = $infos[2]=="SWF" ? "flash" : "image";
				$_hidden = '';
			}
			else {
				$_hidden = " style='display:none'";
			}

			// ��� ��� ���� - ��ʼ����� javascript ���� span �±� ������� ������ ����ϹǷ� ������ ���� ���!
			$banner_infos = "<span id='width' type='$rows[width_type]'$_hidden>$rows[width]".$size_types[$rows['width_type']]."</span><font$_hidden>��</font><span id='height' type='$rows[height_type]'$_hidden>$rows[height]".$size_types[$rows['height_type']]."</span><span id='type' type='$rows[type]' value='$media_kind' outline='$rows[outline]'></span><div style='font-size:8pt'><span id='use_date' value='$rows[use_date]'><span id='sdate'>$sdate</span> <span id='edate'>$edate</span></span></div>";

			// ��� ������
			switch($rows['type']) {
				case "media":
					$_width = $rows['width'].$size_types[$rows['width_type']];
					$_height = $rows['height'].$size_types[$rows['height_type']];
					$_outline = ($rows['outline']=='on') ? " class='banner_outline'" : '';
					switch($media_kind) {
						case "flash":
							$media_content = "<span id='media_data' value='flash|".$this->base_url."RAD/PEG/$rows[attached]|$_width|$_height'></span>";
							$banner_contents = "<span id='media' width='$infos[0]' height='$infos[1]' extension='$infos[2]' address='$rows[address]' target='$rows[target]'$_outline>$media_content</span>";
							break;
						case "image":
							$media_content = "<img src='".$this->base_url."RAD/PEG/$rows[attached]' width='$_width' height='$_height' border='0' align='absmiddle'>";
							$banner_contents = "<span id='media' width='$infos[0]' height='$infos[1]' extension='$infos[2]' address='$rows[address]' target='$rows[target]'$_outline>$media_content</span>";
							break;
					}
					break;
				case "text":
					// ��ũ��Ʈ �ڵ������� �����ϱ� ���� �ڵ� ��ȯ
					if($single_mode===true) $rows['content'] = str_replace("\\", "\\\\", $rows['content']); // stripslashes �����Ƚ� - 2008.06.09
					$text_content = "<span id='text_data' style='display:none'>".str_replace(array("<", ">"), array("{:_lt:}", "{:_gt:}"), $rows['content'])."</span><iframe id='textContent$rows[no]' frameborder='0' style='width:100%;height:0px;'></iframe>";
					$banner_contents = "<span id='text'></span>$text_content";
					break;
			}
			// ���ε� ���� �ٲ� ���
			if(($rows['bind']=='' && isset($bannerItem)) || $old_bind != $rows['bind']) {
				if($old_bind) {
					$bind_class = "bindCell";
					$bind_tools = $control_buttons[4];
				}
				else {
					$bind_class = "normalCell";
					$bind_tools = '';
				}
				$old_bind = $rows['bind'];
				if(!empty($bannerItem)) {
					$bannerBody .= str_replace(array("{:bind_class:}", "{:bind_tools:}", "{:bannerItem:}"), array($bind_class, $bind_tools, $bannerItem), $template['bannerBody']);
					unset($bannerItem);
				}
			}
			// �׵θ�ǥ�� - 2010.12.20 added
			$outline_button = ($rows['type']=='media') ? $control_buttons[5] : '';

			// â���
			$banner_target = empty($targets[$rows['target']]) ? '' : str_replace("{:target:}", $targets[$rows['target']], $control_buttons[2]);
			$bannerItem .= str_replace(array("{:no:}", "{:bind:}", "{:banner_infos:}", "{:banner_contents:}", "{:view:}", "{:banner_target:}", "{:outline_button:}"), array($rows['no'], $rows['bind'], $banner_infos, $banner_contents, $views[$rows['view']], $banner_target, $outline_button), $template['bannerItem']);
		}
		if(isset($bannerItem)) {
			if($old_bind) {
				$bind_class = "bindCell";
				$bind_tools = $control_buttons[4];
			}
			else {
				$bind_class = "normalCell";
				$bind_tools = '';
			}
			$bannerBody .= str_replace(array("{:bind_class:}", "{:bind_tools:}", "{:bannerItem:}"), array($bind_class, $bind_tools, $bannerItem), $template['bannerBody']);
			//unset($bannerItem); -- �̱۸�� �������� �ּ�
		}
		return $single_mode==true ? $bannerItem : $bannerBody;
	}

	// �������
	function get_banner($datas, $xml_mode=true) {
		if($datas['no']) $addWhere .= "=$datas[no]";
		if($datas['position']) $addWhere = is_numeric($datas['position']) ? " and position=$datas[position]" : " and position=".$this->get_position_key($datas['position']); // �����ġ
		if($datas['view']) $addWhere .= " and view='$datas[view]'"; // ��¿���
		if($datas['check_date']=="yes") $addWhere .= " and (sdate is NULL or sdate<=now()) and (edate is NULL or edate>=now())";

		$banners = $this->queryFetchRows("select no,bind,rank,position,type,address,target,width,height,width_type,height_type,attached,content,use_date,sdate,edate,view,outline from $this->table where no$addWhere order by position, rank, bind"); // 'outline' - 2010.12.20 added
		return $xml_mode ? rankup_banner::get_xml_formalize($banners) : $banners;
	}

	// ��� ����Ʈ ���� - �����ڿ��� ���
	function get_banner_list($position='', $logo_mode=false) {
		// ������� ����
		if(empty($position)) $position = array_shift(array_keys($this->positions));
		$datas = $this->get_banner(array("position"=>$position), false);
		return $this->get_html_formalize($datas, false, $logo_mode);
	}

	// ��ʼ����� ���� :: ��»���/����/���ε�����
	function set_banner($kind='') {
		switch($kind) {
			case "view": // ���/����� �� ���� - ������ : GET
				$nos = str_replace("__", ", ", $_GET['data']);
				return $this->query("update $this->table set view='$_GET[val]' where no in($nos)");
				break;

			case "target": // ��â��� �� ���� - ������ : GET
				$nos = str_replace("__", ", ", $_GET['data']);
				return $this->query("update $this->table set target='$_GET[val]' where no in($nos)");
				break;

			case "outline": // ����׵θ� ���� ���� - 2010.12.20 added
				$nos = str_replace("__", ", ", $_GET['data']);
				return $this->query("update $this->table set outline='$_GET[val]' where no in($nos)");
				break;

			default: // ����/���ε� �� ���� - ������ : POST
				if(!count($_POST['no'])) return false;
				foreach($_POST['no'] as $rank => $no) {
					$_val['bind'] = empty($_POST['bind_no'][$rank]) ? NULL : $_POST['bind_no'][$rank];
					$_val['rank'] = $rank+1;
					$values = $this->change_query_string($_val);
					$this->query("update $this->table set $values where no=$no");
				}
		}
	}

	// ��� ��� - �����������
	function print_banner($position, $freesize=false, $logo_mode=false) {
		if(!is_numeric($position)) $position = $this->get_position_key($position);
		$banner_condition = array("position"=>$position, "view"=>"yes", "check_date"=>"yes");
		$datas = $this->get_banner($banner_condition, false);
		if(!count($datas) || !is_array($datas)) return false;

		// �ΰ� ��� �϶� - �� ���� ó��
		if($logo_mode==true) {
			foreach($datas as $rows) $logo_items[count($logo_items)] = $rows;
			shuffle($logo_items);
			$banner_items[0] = array_pop($logo_items);
		}
		else {
			foreach($datas as $rows) {
				if($old_bind != $rows['bind'] && isset($bind_item)) {
					shuffle($bind_item);
					$banner_items[count($banner_items)] = array_pop($bind_item);
					unset($bind_item);
				}
				if(empty($rows['bind'])) $banner_items[count($banner_items)] = $rows;
				else $bind_item[count($bind_item)] = $rows;
				$old_bind = $rows['bind'];
			}
			// �ܷ���� �߰�
			if(isset($bind_item)) {
				shuffle($bind_item);
				$banner_items[count($banner_items)] = array_pop($bind_item);
				unset($bind_item);
			}
		}

		// Formalize
		foreach($banner_items as $rows) {
			$size_types = array("pixel"=>"px", "percent"=>"%", "exact"=>"px");
			switch($rows['type']) {
				case "media": // �̵��(�̹���/�÷���)��� ���
					if(is_file($this->base_dir."RAD/PEG/".$rows['attached'])) {
						$attached = "http://".$_SERVER['HTTP_HOST'].$this->base_url."RAD/PEG/".$rows['attached'];
						// ���������� ������ - percent ������ ��쿡�� �����
						if($freesize==true) {
							if(empty($freesize[0])) $freesize[0] = "100%";
							if(empty($freesize[1])) $freesize[1] = "100%";
							$width = $rows['width_type']=="percent" ? $freesize[0] : $rows['width'].$size_types[$rows['width_type']];
							$height = $rows['height_type']=="percent" ? $freesize[1] : $rows['height'].$size_types[$rows['height_type']];
						}
						else {
							// percent ������ ��� �⺻ ������� ��ü
							$width = $rows['width_type']=="percent" ? $this->positions[$position]['width'] : $rows['width'].$size_types[$rows['width_type']];
							$height = $rows['height_type']=="percent" ? $this->positions[$position]['height'] : $rows['height'].$size_types[$rows['height_type']];
						}
						if(array_pop(explode(".", $rows['attached']))=="swf") {
							// 6th argument 'outline' - 2010.12.20 added
							$_item = "
							<script type='text/javascript'>
							flashDraw('b_$position', '$attached', '$width', '$height', 'opaque', '$rows[outline]');
							</script>";
						}
						else {
							/// IE 6.x png pix
							if(parent::ie_version()=='6.0') {
								$_infos = getimagesize($this->base_dir."RAD/PEG/".$rows['attached']);
								if($_infos[2]==3) $png24 = " class='png24'";
							}
							$_outline = ($rows['outline']=='on') ? ' class="banner_outline"' : ''; // 2010.12.20 added
							$_item = "<img src='$attached' width='$width' height='$height'$png24 border='0'$_outline>";
							if(!empty($rows['address'])) $_item = "<a href='$rows[address]' target='$rows[target]'>$_item</a>";
						}
					}
					break;
				// �����Է� ���
				case "text": $_item = $rows['content']; break;
			}
			$items[count($items)] = "
			<tr>
				<td>$_item</td>
			</tr>
			";
		}
		$items = @implode("<tr height='6'><td style='padding:0px;font-size:0px;line-height:0;'>&nbsp;</td></tr>", $items);
		// A �±� ���� {:domain:} ���� - 2008.06.14 �߰�
		preg_match_all('/(a [^<]*href=["|\']?([^ "\']*)["|\']?[^>].*>)/i', $items, $atags);
		foreach($atags[2] as $key=>$atag) {
			$_info['scheme'] = ($_SERVER['HTTPS']=="on") ? "https://" : "http://";
			$_atag = str_replace($atag, str_replace("{:domain:}", $_info['scheme'].$_SERVER['HTTP_HOST'].$this->base_url, $atag), $atags[1][$key]);
			$items = str_replace($atags[1][$key], $_atag, $items);
		}
		return ($freesize==true) ? "<table width='100%' cellspacing='0' cellpadding='0' border='0'>$items</table>" : "<table cellspacing='0' cellpadding='0' border='0'>$items</table>";
	}

	// ���� �߼ۿ� ��� ����
	function get_email_banner($position, $logo_mode=false) {
		if(!is_numeric($position)) $position = $this->get_position_key($position);
		$banner_condition = array("position"=>$position, "view"=>"yes", "check_date"=>"yes");
		$datas = $this->get_banner($banner_condition, false);
		if(!count($datas) || !is_array($datas)) return false;

		// �ΰ� ��� �϶� - �� ���� ó��
		if($logo_mode==true) {
			foreach($datas as $rows) $logo_items[count($logo_items)] = $rows;
			shuffle($logo_items);
			$banner_items[0] = array_pop($logo_items);
		}
		else {
			foreach($datas as $rows) {
				if($old_bind != $rows['bind'] && isset($bind_item)) {
					shuffle($bind_item);
					$banner_items[count($banner_items)] = array_pop($bind_item);
					unset($bind_item);
				}
				if(empty($rows['bind'])) $banner_items[count($banner_items)] = $rows;
				else $bind_item[count($bind_item)] = $rows;
				$old_bind = $rows['bind'];
			}
			// �ܷ���� �߰�
			if(isset($bind_item)) {
				shuffle($bind_item);
				$banner_items[count($banner_items)] = array_pop($bind_item);
				unset($bind_item);
			}
		}

		// Formalize
		foreach($banner_items as $rows) {
			$size_types = array("pixel"=>"px", "percent"=>"%", "exact"=>"px");
			switch($rows['type']) {
				case "media": // �̵��(�̹���/�÷���)��� ���
					if(is_file($this->base_dir."RAD/PEG/".$rows['attached'])) {
						$attached = "http://".$_SERVER['HTTP_HOST'].$this->base_url."RAD/PEG/".$rows['attached'];
						// percent ������ ��� �⺻ ������� ��ü
						$width = $rows['width_type']=="percent" ? $this->positions[$position]['width'] : $rows['width'].$size_types[$rows['width_type']];
						$height = $rows['height_type']=="percent" ? $this->positions[$position]['height'] : $rows['height'].$size_types[$rows['height_type']];

						if(array_pop(explode(".", $rows['attached']))=="swf") $_item = "<embed id='b_$position' src='$attached' width='$width' height='$height'></embed>";
						else {
							$_item = "<img src='$attached' width='$width' height='$height' border='0'>";
							if(!empty($rows['address'])) $_item = "<a href='$rows[address]' target='$rows[target]'>$_item</a>";
						}
					}
					break;
				// �����Է� ���
				case "text": $_item = $rows['content']; break;
			}
			$items[count($items)] = "
			<tr>
				<td>$_item</td>
			</tr>
			";
		}
		$items = @implode("<tr height='8'><td style='padding:0px;font-size:1px;line-height:0'>&nbsp;</td></tr>", $items);
		// A �±� ���� {:domain:} ���� - 2008.06.14 �߰�
		preg_match_all('/(a [^<]*href=["|\']?([^ "\']*)["|\']?[^>].*>)/i', $items, $atags);
		foreach($atags[2] as $key=>$atag) {
			$_info['scheme'] = ($_SERVER['HTTPS']=="on") ? "https://" : "http://";
			$_atag = str_replace($atag, str_replace("{:domain:}", $_info['scheme'].$_SERVER['HTTP_HOST'].$this->base_url, $atag), $atags[1][$key]);
			$items = str_replace($atags[1][$key], $_atag, $items);
		}
		return "<table cellspacing='0' cellpadding='0' border='0'>$items</table>";
	}
}
?>