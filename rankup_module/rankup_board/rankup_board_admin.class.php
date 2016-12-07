<?php
## ��ũ�� ��Ƽ�Խ��� ������ Ŭ����
class rankup_board_admin extends rankup_board {
	var $version = "v2.1 r090623"; // �Խ��� ���� ����
	function rankup_board_admin($board_id='') {
		parent::rankup_board($board_id);
	}

	// ���͸�ũ ����
	function set_wm_settings($datas) {
		unset($datas['x'], $datas['y'], $datas['mode']);
		if($this->chkRes($datas)) {
			$_val['item_value'] = serialize($datas);
			$values = $this->change_query_string($_val);
			if(!isset($this->wm_settings['use_watermark'])) $this->query("insert $this->setting_table set item_name='thumb_configs', $values");
			else $this->query("update $this->setting_table set $values where item_name='thumb_configs'");
			// ���͸�ũ ����
			if($datas['on_watermark']) {
				foreach(glob($this->base_dir.'rankup_module/rankup_board/watermark/watermark.*') as $old_wmark) unlink($old_wmark);
				$new_wmark = $this->base_dir.'rankup_module/rankup_board/watermark/'.$datas['on_watermark'];
				rename($new_wmark, str_replace('_junk_.', '', $new_wmark));
			}
		}
		return true;
	}

	// �Խ��� ����
	function create_board($board_id) {
		if(empty($board_id)) return false;
		// �Խ���/��� ���̺� ����
		@include "scheme/rankup_board_scheme.inc.html";
		foreach($_BOARD_TABLES as $scheme_name=>$create_query) {
			$table_name = str_replace("scheme", $board_id, $scheme_name);
			$check_table = $this->queryR("show tables like '$table_name'");
			if($check_table!==$table_name) $this->query(str_replace("{:board_id:}", $board_id, $create_query));
		}
		// ���̺� ���� ���� �߰�
		$check_board = $this->queryR("select bid from $this->division_table where bid='$board_id'");
		if($check_board!==$board_id) $this->query("insert $this->division_table set bid='$board_id'");

		// ÷�������� ������ ���� ����
		$path_info = pathinfo(__FILE__);
		$attach_dir = $path_info['dirname']."/attach/".$board_id;
		if(!is_dir($attach_dir)) {
			mkdir($attach_dir);
			@chmod($attach_dir, 0777);
		}
		return true;
	}

	// �Խ��� ���̵� �ߺ� üũ
	function verify_board($board_id) {
		$result = $this->queryR("show tables like '$this->board_prefix$board_id'");
		if($board_id!='best' && empty($result)) { // 2009.08.03 fixed
			$script_code = "
			$('board_id').value = '$board_id';
			alert('\'$board_id\' �Խ��� ���̵�� ����Ͻ� �� �ֽ��ϴ�.'+SPACE);
			var board_name = $('board_name');
			board_name.select();
			board_name.focus();";
		}
		else {
			$script_code = "
			$('board_id').value = '';
			alert('�Է��Ͻ� �Խ��� ���̵�� �̹� ����� �̰ų� �ٸ� ���̺��� �ߺ��Ǿ� ����Ͻ� �� �����ϴ�.'+SPACE);
			var boardId = $('boardId');
			boardId.select();
			boardId.focus();";
		}
		return $script_code;
	}

	// �Խ��� ����
	function delete_board($board_id, $cno='') {
		if(empty($board_id)) return false;
		// �Խ���/��� ���̺� ��Ű��
		@include "scheme/rankup_board_scheme.inc.html";
		foreach($_BOARD_TABLES as $scheme_name=>$create_query) {
			$table_name = str_replace("scheme", $board_id, $scheme_name);
			//���̺��� ���� ���� -- ������ ��ŵ �Ѵ�. ������ �Ʒ� ������ ���� �ִ� ��찡 �߻��Ѵ�.
			$table_exists = $this->queryR("show tables like '$table_name'");
			if($table_exists) $this->query("drop table $table_name");
		}
		// ���̺� ���� ���� ����
		$this->query("delete from $this->division_table where bid='$board_id'");
		if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->division_table");

		// ȯ�� ���̺��� �Խ��� ���� ����
		$previous_board_no = $this->queryR("select no from $this->bconfig_table where id='$board_id'");
		$this->query("delete from $this->bconfig_table where id='$board_id'");
		if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->bconfig_table");

		// ī�װ� bval, mbno �� ����
		if(!empty($cno)) {
			// �ش� ī�װ��� �Խ����� �������� ���� ��츦 ó��
			$board_no = $this->queryR("select no from $this->bconfig_table where cno=$cno order by rank");
			if(empty($board_no)) $this->query("update $this->category_table set bval='no', mbno=NULL where no=$cno");
			else $this->query("update $this->category_table set mbno=$board_no where no=$cno and mbno=$previous_board_no"); // mbno ����
		}

		// �ְ� ����Ʈ ����
		$this->query("delete from $this->weekly_best_table where bid='$board_id'");
		if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->weekly_best_table");
		// ��ȸ�� ����Ʈ ����
		$this->query("delete from $this->hit_best_table where bid='$board_id'");
		if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->hit_best_table");
		// ��ۼ� ����Ʈ ����
		$this->query("delete from $this->comment_best_table where bid='$board_id'");
		if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->comment_best_table");
		if($this->board_extension===true) {
			// �ְ� ��ۼ� ����Ʈ ����
			$this->query("delete from $this->weekly_cbest_table where bid='$board_id'");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->weekly_cbest_table");
			// �Ű�� ���� ���� ����
			$this->query("delete from $this->report_table where bid='$board_id'");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->report_table");
		}
		// �ű� �Խù� ����
		$this->query("delete from $this->new_article_table where bid='$board_id'");
		if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->new_article_table");

		// ÷������ ����
		$path_info = pathinfo(__FILE__);
		$attach_dir = $path_info['dirname']."/attach/".$board_id;
		$this->remove_directory($attach_dir); // rankup_util.class.php �� ����
		return true;
	}

	// �Խ��� ���� ����
	function regist_board($datas) {
		$DML = $datas['no']==='' ? "insert" : "update"; // 2009.08.31 modified

		$_val['name'] = str_replace("\"", "&quot;", $datas['board_name']); // �Խ��� �̸�
		$_val['skin'] = $datas['board_skin'];							// ��Ų ���� �̸�
		$_val['style'] = $datas['board_style'];						// �Խ��� ��Ÿ�� - �Խ��� or ������

		// �Խ��� �̻��� ����������/�޴����ο��� �Բ� �ݿ�
		if($datas['board_use']=="on") $_val['uval'] = "yes";
		else $_val['uval'] = $_val['mval'] = $_val['pcmval'] = "no";

		$_val['slayout'] = serialize(array(
			"board_width" => $datas['board_width'],				// �Խ��� ����ũ��
			"subject_length" => $datas['subject_length'],		// ��� ������� ����
			"use_condense" => $datas['use_condense'],			// �� ���ӱ�ȣ ���
			"article_rows" => $datas['article_rows']				// �������� �Խù� ��
		));
		$_val['scontent'] = $this->wysiwyg_result_func(stripslashes($datas['board_content']));	// �Խù� �⺻ ����
		$_val['sfunction'] = serialize(array(
			"use_category" => $datas['use_category'],			// �з� ���
			"use_duplicate_hit" => $datas['use_duplicate_hit'],	// �ߺ���ȸ ���
			"use_comment" => $datas['use_comment'],			// ��� ���
			"use_reply" => $datas['use_reply'],						// ��� ���
			"use_vote" => $datas['use_vote'],						// ��õ/�ݴ� ���
			"use_only_good" => $datas['use_only_good'],		// ��õ��ɸ� ���
			"use_report" => $datas['use_report'],					// �Ű� ���
			"use_secret" => $datas['use_secret'],					// ��б� ���
			"use_print" => $datas['use_print'],					// �μ� ���
			"use_writer" => $datas['use_writer'],				// ���ۼ���/�г���/���̵� ����
			"use_snssend" => $datas['use_snssend'],			// sns �ۺ����� ��뿩��
			"use_articledel" => $datas['use_articledel'],		// �Խù��ٷλ���
			"use_watermark" => $datas['use_watermark'],		// ���͸�ũ ��뿩��
			"sheader_file" => $datas['board_header_file'], //�Խ��� ��ܿ� ����� ����
			"sfooter_file" => $datas['board_footer_file'] // �Խ��� �ϴܿ� ����� ����
		));
		$_val['soption'] = serialize(array(
			"use_hit_best" => $datas['use_hit_best'],				// ��ȸ�� BEST ���
			"hit_best_num" => $datas['hit_best_num'],			// ��ȸ�� BEST ��� ���� - 2009.08.31 added
			"use_new_icon" => $datas['use_new_icon'],			// new ������ ���
			"recent_time" => $datas['recent_time'],				// �ֱ� �Խù��� ������ �ð�
			"use_attach_icon" => $datas['use_attach_icon'],	// ÷������ ������ ���
			"use_reply_icon" => $datas['use_reply_icon'],		// ��� ������ ���
			"use_near_article" => $datas['use_near_article'],	// ������/������ ���
			"use_detail_list" => $datas['use_detail_list'],			// �������� ��� ���
		));
		$_val['sattach'] = serialize(array(
			"use_attach" => $datas['use_attach'],					// ÷������ ���
			"use_detail_attach" => $datas['use_detail_attach'],	// ÷������ ���
			"attach_nums" => $datas['attach_nums'],				// ÷������ ����
			"attach_size" => $datas['attach_size'],				// ÷������ �ִ� ũ��
			"attach_extension" => $datas['attach_extension']	// ÷������ Ȯ����
		));
		$_val['sgallery'] = serialize(array(
			"thumb_width" => $datas['thumb_width'],				// ��� �ִ� ����ũ��
			"thumb_height" => $datas['thumb_height'],			// ��� �ִ� ����ũ��
			"picture_width" => $datas['picture_width'],			// �̹��� �ִ� ����ũ��
			"thumb_nums" => $datas['thumb_nums']				// �ٴ� �̹��� ��
		));
		$_val['sfilter'] = $datas['board_filter'];						// �ܾ� ����
		$_val['sblock'] = $datas['ip_block'];							// �����Ǻ�

		switch(strtolower($DML)) {
			// �ű� �Խ��� ����
			case "insert":
				$_val['id'] = $datas['board_id'];						// �Խ��� ���̵�
				$_val['cno'] = $datas['cno'];							// ī�װ� ��ȣ
				$_val['pcno'] = empty($datas['pcno']) ? $datas['cno'] : $datas['pcno']; // ���� ī�װ� ��ȣ
				$_val['rank'] = $datas['rank'];							// ����

				$_val['scategory'] = serialize(array());				// �Խ��� ī�װ� �⺻�� �Է�
				$_val['spermission'] = serialize(array(				// �Խ��� ���� �⺻�� �Է�
					"list_level" => 7,										// ����Ʈ ���� ���� : (7: ��ȸ��)
					"read_level" => 7,										// �Խù� �б� ���� : (7: ��ȸ��)
					"write_level" => 5,									// �Խù� ���� ���� : (5: ȸ��)
					"comment_level" => 5,								// �ڸ�Ʈ ���� ���� : (5: ȸ��)
					"reply_level" => 5,									// �亯�� ���� ���� : (5: ȸ��)
					"delete_level" => 1,									// �Խù� ���� ���� : (1: ���)
					"notice_level" => 1,									// ������ ���� ���� : (1: ���)
					"secret_level" => 1									// ��б� �б� ���� : (1: ���)
				));
				$_val['spoint'] = serialize(array());					// �Խ��� ����Ʈ �⺻�� �Է�

				$_val['smlayout'] = serialize(array(					// ���������� �⺻�� �Է�
					"subject_length" => 40,								// �������
					"article_rows" => 5,									// �Խù���
					"image_width" => '',									// �̹��� ����ũ��
					"image_height" => '',									// �̹��� ����ũ��
					"print_style" => "text"								// �������
				));
				$_val['spcmlayout'] = serialize(array(				// �޴����������� �⺻�� �Է�
					"subject_length" => 40,								// �������
					"article_rows" => 5,									// �Խù���
					"image_width" => '',									// �̹��� ����ũ��
					"image_height" => '',									// �̹��� ����ũ��
					"print_style" => "text"								// �������
				));

				// ������ �Խ����� �������̺� ���
				$this->create_board($datas['board_id']);
				break;

			// �Խ��� ���� ������Ʈ - 2009.10.06 fixed
			case "update":
				if($datas['no']) $addWhere = " where no=$datas[no]";
				else if($datas['board_id']) $addWhere = " where id='$datas[board_id]'";
				break;
		}
		$values = $this->change_query_string($_val);
		$result = $this->query("$DML $this->bconfig_table set $values$addWhere");
		if($DML=="insert" && mysql_affected_rows()) {
			$board_no = mysql_insert_id();
			$this->query("update $this->category_table set bval='yes' where no=$datas[cno]"); // �Խ��� �����Ѵٰ� ���
			$this->query("update $this->category_table set mbno=$board_no where no=$_val[pcno] and mbno is NULL"); // ���ΰԽ��� ����
		}
		return $result;
	}

	// ���º� �Խ��� �������� ����
	function board_type_config_setting($datas) {

		// �Խ��� �̻��� ����������/�޴����ο��� �Բ� �ݿ�
		if($datas['board_use']=="on") $_val['uval'] = "yes";
		else $_val['uval'] = $_val['mval'] = $_val['pcmval'] = "no";

		$_val['slayout'] = serialize(array(
			"board_width" => $datas['board_width'],				// �Խ��� ����ũ��
			"subject_length" => $datas['subject_length'],		// ��� ������� ����
			"use_condense" => $datas['use_condense'],			// �� ���ӱ�ȣ ���
			"article_rows" => $datas['article_rows']				// �������� �Խù� ��
		));
		$_val['scontent'] = $this->wysiwyg_result_func(stripslashes($datas['board_content'])); // �Խù� �⺻ ����
		$_val['sfunction'] = serialize(array(
			"use_category" => $datas['use_category'],			// �з� ���
			"use_duplicate_hit" => $datas['use_duplicate_hit'],	// �ߺ���ȸ ���
			"use_comment" => $datas['use_comment'],			// ��� ���
			"use_reply" => $datas['use_reply'],						// ��� ���
			"use_vote" => $datas['use_vote'],						// ��õ/�ݴ� ���
			"use_only_good" => $datas['use_only_good'],		// ��õ��ɸ� ���
			"use_report" => $datas['use_report'],					// �Ű� ���
			"use_secret" => $datas['use_secret'],					// ��б� ���
			"use_print" => $datas['use_print'],					// �μ� ���
			"use_writer" => $datas['use_writer'],				// ���ۼ���/�г���/���̵� ����
			"use_snssend" => $datas['use_snssend'],			// sns �ۺ����� ��뿩��
			"use_articledel" => $datas['use_articledel'],		// �Խù��ٷλ���
			"use_watermark" => $datas['use_watermark'],		// ���͸�ũ ��뿩��
			"sheader_file" => $datas['board_header_file'], //�Խ��� ��ܿ� ����� ����
			"sfooter_file" => $datas['board_footer_file'] // �Խ��� �ϴܿ� ����� ����
		));
		$_val['soption'] = serialize(array(
			"use_hit_best" => $datas['use_hit_best'],				// ��ȸ�� BEST ���
			"hit_best_num" => $datas['hit_best_num'],			// ��ȸ�� BEST ��� ���� - 2009.08.31 added
			"use_new_icon" => $datas['use_new_icon'],			// new ������ ���
			"recent_time" => $datas['recent_time'],				// �ֱ� �Խù��� ������ �ð�
			"use_attach_icon" => $datas['use_attach_icon'],	// ÷������ ������ ���
			"use_reply_icon" => $datas['use_reply_icon'],		// ��� ������ ���
			"use_near_article" => $datas['use_near_article'],	// ������/������ ���
			"use_detail_list" => $datas['use_detail_list'],			// �������� ��� ���
		));
		$_val['sattach'] = serialize(array(
			"use_attach" => $datas['use_attach'],					// ÷������ ���
			"use_detail_attach" => $datas['use_detail_attach'],	// ÷������ ���
			"attach_nums" => $datas['attach_nums'],				// ÷������ ����
			"attach_size" => $datas['attach_size'],				// ÷������ �ִ� ũ��
			"attach_extension" => $datas['attach_extension']	// ÷������ Ȯ����
		));
		$_val['sgallery'] = serialize(array(
			"thumb_width" => $datas['thumb_width'],				// ��� �ִ� ����ũ��
			"thumb_height" => $datas['thumb_height'],			// ��� �ִ� ����ũ��
			"picture_width" => $datas['picture_width'],			// �̹��� �ִ� ����ũ��
			"thumb_nums" => $datas['thumb_nums']				// �ٴ� �̹��� ��
		));
		$_val['sfilter'] = $datas['board_filter'];						// �ܾ� ����
		$_val['sblock'] = $datas['ip_block'];							// �����Ǻ�
		$addWhere = " where style='$datas[board_style]'"; //Ÿ�Ժ��θ� ����
		$values = $this->change_query_string($_val);
		$result = $this->query("update $this->bconfig_table set $values$addWhere");
		return $result;
	}

	// �Խ��� ������ XML ���·� ��ȯ
	function formalize_board_xml_data($datas, $smpoint=null) {
		// ȸ������/�α��� ����Ʈ
		if($smpoint!==null) $smpoint = @unserialize($smpoint);

		// �Խ��Ǻ� ����Ʈ ����
		foreach($datas as $rows) {
			$slayout = @unserialize($rows['slayout']);
			$sfunction = @unserialize($rows['sfunction']);
			$soption = @unserialize($rows['soption']);

			unset($scategories);
			$categories = @unserialize($rows['scategory']);
			if($this->check_resource($categories)) { // 2009.09.18 modified
				$scategory = $this->sort_scategory($categories);
				foreach($scategory as $rank=>$vals) $scategories .= "<category no='$vals[cno]' anum='$vals[anum]'><![CDATA[{$vals[name]}]]></category>";
			}

			$spermission = @unserialize($rows['spermission']);
			$spoint = @unserialize($rows['spoint']);

			$sattach = @unserialize($rows['sattach']);
			$sgallery = @unserialize($rows['sgallery']);

			$xml_data .= "
			<item no='$rows[no]'>
				<!-- basic -->
				<board_id>$rows[id]</board_id>
				<board_name><![CDATA[{$rows[name]}]]></board_name>
				<cno>$rows[cno]</cno>
				<pcno>$rows[pcno]</pcno>
				<anum>$rows[anum]</anum>
				<rank>$rows[rank]</rank>
				<board_skin>$rows[skin]</board_skin>
				<board_style>$rows[style]</board_style>
				<board_use>$rows[uval]</board_use>
				<!-- layout -->
				<board_width>$slayout[board_width]</board_width>
				<subject_length>$slayout[subject_length]</subject_length>
				<use_condense>$slayout[use_condense]</use_condense>
				<article_rows>$slayout[article_rows]</article_rows>
				<board_content><![CDATA[{$rows[scontent]}]]></board_content>
				<!-- function -->
				<use_category>$sfunction[use_category]</use_category>
				<use_duplicate_hit>$sfunction[use_duplicate_hit]</use_duplicate_hit>
				<use_comment>$sfunction[use_comment]</use_comment>
				<use_reply>$sfunction[use_reply]</use_reply>
				<use_vote>$sfunction[use_vote]</use_vote>
				<use_only_good>$sfunction[use_only_good]</use_only_good>
				<use_report>$sfunction[use_report]</use_report>
				<use_secret>$sfunction[use_secret]</use_secret>
				<use_print>$sfunction[use_print]</use_print>
				<use_writer>$sfunction[use_writer]</use_writer>
				<use_snssend>$sfunction[use_snssend]</use_snssend>
				<use_articledel>$sfunction[use_articledel]</use_articledel>
				<use_watermark>$sfunction[use_watermark]</use_watermark>
				<board_header_file><![CDATA[{$sfunction[sheader_file]}]]></board_header_file>
				<board_footer_file><![CDATA[{$sfunction[sfooter_file]}]]></board_footer_file>
				<!-- option -->
				<use_hit_best>$soption[use_hit_best]</use_hit_best>
				<hit_best_num>$soption[hit_best_num]</hit_best_num>
				<use_new_icon>$soption[use_new_icon]</use_new_icon>
				<recent_time>$soption[recent_time]</recent_time>
				<use_attach_icon>$soption[use_attach_icon]</use_attach_icon>
				<use_reply_icon>$soption[use_reply_icon]</use_reply_icon>
				<use_near_article>$soption[use_near_article]</use_near_article>
				<use_detail_list>$soption[use_detail_list]</use_detail_list>
				<!-- category -->
				<categories>
					$scategories
				</categories>
				<!-- permission -->
				<permission>
					<list_level>$spermission[list_level]</list_level>
					<read_level>$spermission[read_level]</read_level>
					<write_level>$spermission[write_level]</write_level>
					<delete_level>$spermission[delete_level]</delete_level>
					<comment_level>$spermission[comment_level]</comment_level>
					<reply_level>$spermission[reply_level]</reply_level>
					<notice_level>$spermission[notice_level]</notice_level>
					<secret_level>$spermission[secret_level]</secret_level>
				</permission>
				<!-- point -->
				<point>
					<join_point><![CDATA[{$smpoint[join_point]}]]></join_point>
					<login_point><![CDATA[{$smpoint[login_point]}]]></login_point>
					<write_point><![CDATA[{$spoint[write_point]}]]></write_point>
					<read_point><![CDATA[{$spoint[read_point]}]]></read_point>
					<comment_point><![CDATA[{$spoint[comment_point]}]]></comment_point>
					<reply_point><![CDATA[{$spoint[reply_point]}]]></reply_point>
					<vote_point><![CDATA[{$spoint[vote_point]}]]></vote_point>
					<upload_point><![CDATA[{$spoint[upload_point]}]]></upload_point>
					<download_point><![CDATA[{$spoint[download_point]}]]></download_point>
				</point>
				<!-- attach -->
				<use_attach>$sattach[use_attach]</use_attach>
				<use_detail_attach>$sattach[use_detail_attach]</use_detail_attach>
				<attach_nums>$sattach[attach_nums]</attach_nums>
				<attach_size>$sattach[attach_size]</attach_size>
				<attach_extension>$sattach[attach_extension]</attach_extension>
				<!-- gallery -->
				<thumb_width>$sgallery[thumb_width]</thumb_width>
				<thumb_height>$sgallery[thumb_height]</thumb_height>
				<picture_width>$sgallery[picture_width]</picture_width>
				<thumb_nums>$sgallery[thumb_nums]</thumb_nums>
				<!-- etc -->
				<board_filter><![CDATA[{$rows[sfilter]}]]></board_filter>
				<ip_block><![CDATA[{$rows[sblock]}]]></ip_block>
			</item>";
		}
		$xml_data = "
		<board>$xml_data
		</board>";
		return $xml_data;
	}

	// �Խ��� ����Ʈ ����
	function get_board($cno, $xml=true) {
		$smpoint = $this->board_extension===true ? $this->queryR("select smpoint from $this->sconfig_table") : null;
		$datas = $this->queryFetchRows("select * from $this->bconfig_table where cno=$cno order by rank");
		return $xml===true ? $this->formalize_board_xml_data($datas, $smpoint) : $datas;
	}

	// �Խ��� ������ XML ���·� ��ȯ
	function formalize_setting_xml_data($board_datas, $main_datas=null) {
		foreach($board_datas as $rows) {
			$smlayout = unserialize($rows['smlayout']);
			$spcmlayout = unserialize($rows['spcmlayout']);
			$xml_board_item .= "
			<item no='$rows[no]' anum='$rows[anum]'>
				<board_id>$rows[id]</board_id>
				<board_name><![CDATA[".str_replace("&", "&amp;", $rows['name'])."]]></board_name>
				<cno>$rows[cno]</cno>
				<pcno>$rows[pcno]</pcno>
				<uval>$rows[uval]</uval>
				<mval>$rows[mval]</mval>
				<pcmval>$rows[pcmval]</pcmval>
				<smlayout>
					<subject_length>$smlayout[subject_length]</subject_length>
					<article_rows>$smlayout[article_rows]</article_rows>
					<print_style>$smlayout[print_style]</print_style>
					<image_width>$smlayout[image_width]</image_width>
					<image_height>$smlayout[image_height]</image_height>
				</smlayout>
				<spcmlayout>
					<subject_length>$spcmlayout[subject_length]</subject_length>
					<article_rows>$spcmlayout[article_rows]</article_rows>
					<print_style>$spcmlayout[print_style]</print_style>
					<image_width>$spcmlayout[image_width]</image_width>
					<image_height>$spcmlayout[image_height]</image_height>
				</spcmlayout>
			</item>";
		}
		if($main_datas!=null) { // 2009.08.28 modified
			$sprint = $main_datas['sprint'];
			$xml_main_item = "
			<main no='$main_datas[no]'>
				<mboard no='$main_datas[mbno]'>".str_replace("&", "&amp;", $main_datas['mbname'])."</mboard>
				<lskin>$main_datas[lskin]</lskin>
				<mskin>$main_datas[mskin]</mskin>
				<mbnum>$main_datas[mbnum]</mbnum>
				<mval>$main_datas[mval]</mval>
				<wbest>$sprint[wbest]</wbest>
				<hcbest>$sprint[hcbest]</hcbest>
				<narticle>$sprint[narticle]</narticle>
				<wbest_num>$sprint[wbest_num]</wbest_num>
				<hcbest_num>$sprint[hcbest_num]</hcbest_num>
				<narticle_num>$sprint[narticle_num]</narticle_num>
			</main>";
		}
		$xml_data = "
		<setting>$xml_main_item$xml_board_item
		</setting>";
		return $xml_data;
	}


##########################################################################
## ī�װ� �κ� : �ε��� : ī�װ�, category
##########################################################################

	// �޴�(ī�װ�) �Է� - �޴� ����/������ ���
	function regist_category($datas) {
		$DML = in_array($datas['no'], array('', "undefined")) ? "insert" : "update";
		switch($DML) {
			case "insert":
				$_val['pno'] = $datas['pno'];
				$_val['mskin'] = $this->get_board_skins("main", "gray"); // Ư�� ������ ��Ų�� �ϳ��� �޾ƿ�
				$_val['lskin'] = $this->get_board_skins("left", "gray"); // Ư�� ������ ��Ų�� �ϳ��� �޾ƿ�
				// 2009.08.28 added
				$_val['mbnum'] = 2;					// ���ٿ� ����� �Խ��� ��
				$_val['sprint'] = serialize(array(
					"wbest" => 'yes',					// �̹��� ����Ʈ
					"hcbest" => 'yes',					// ��ȸ��/��ۼ� ����Ʈ
					"narticle" => 'yes',				// �ű� �Խù�
					"wbest_num" => 5,				// �̹��� ����Ʈ
					"hcbest_num" => 5,				// ��ȸ��/��ۼ� ����Ʈ
					"narticle_num" => 5				// �ű� �Խù�
				));
				if(!empty($datas['pno']) && $datas['cval']=="no") $this->update_category(array("cval"=>"yes"), $datas['pno']);
				break;
			case "update":
				$addWhere = " where no=$datas[no]";
				break;
		}
		$_val['content'] = str_replace("\"", "&quot;", stripslashes($datas['content']));
		$values = $this->change_query_string($_val);
		$this->query("$DML $this->category_table set $values$addWhere");
		return $DML=="insert" ? mysql_insert_id() : $datas['no'];
	}

	// �޴�(ī�װ�) ���� ������Ʈ - ���ΰԽ��� ��� ������ ���
	function update_category($datas, $no) {
		$values = $this->change_query_string($datas);
		return $this->query("update $this->category_table set $values where no=$no");
	}

	// �޴�(ī�װ�) �缳��
	function reset_category() {
		$nos = $this->getParam("no");
		$views = $this->getParam("view");
		for($i=0; $i<count($nos); $i++) {
			$no = $nos[$i];
			$_val['rank'] = $i+1;
			$_val['pval'] = ($views[$no]=="on") ? "yes" : "no";
			$values = $this->change_query_string($_val);
			$result[$i] = $this->query("update $this->category_table set $values where no=$no");
			// ī�װ� ��뿩�ο� ���� ���ΰԽ���/�޴������� ��¿��� �ʱ�ȭ - 2009.07.22 fixed
			if($_val['pval']=='no') $this->query("update $this->bconfig_table set mval='no', pcmval='no' where cno=$no or pcno=$no"); // 2010.07.05 fixed
		}
		return (array_sum($result)==count($nos));
	}

	// �޴�(ī�װ�) ����
	function delete_category($datas) {
		$nos = str_replace("__", ",", $datas['no']);

		// ī�װ��� ���Ե� �Խ��� ����
		$boards = $this->queryFetchRows("select id from $this->bconfig_table where (cno in($nos) or (cno!=pcno and pcno in($nos)))");
		if($this->check_resource($boards)) foreach($boards as $board) $this->delete_board($board['id']);

		// ī�װ� ���� ����
		$result = $this->query("delete from $this->category_table where (no in($nos) or (no!=pno and pno in($nos)))");
		if(mysql_affected_rows() && $datas['pno'] && $datas['items']==1) $this->update_category(array("cval"=>"no"), $datas['pno']);

		if($this->optimizer) $this->query("optimize table $this->bconfig_table, $this->category_table");
		return $result;
	}

	// �޴�(ī�װ�) ��� - ���ΰԽ��� �������� ���
	function print_category() {
		$datas = $this->get_category(array("pno"=>0), false, false);
		if(!$this->check_resource($datas)) return '';
		$count = 1;
		$max_cols = 7;
		$bolder = " style='font-weight:bolder'";
		$_style = in_array($_GET['category'], array('', "main")) ? $bolder : '';
		// ���������� ���� ��ũ �߰�
		if($this->use_main_board) $_categories = "<td$_style><img src='./img/ic_arrow1.gif' align='absmiddle'> <a href='./main.html?category=main'>����������</a></td>";
		foreach($datas as $rows) {
			$_style = $_GET['category']==$rows['no'] ? $bolder : '';
			$_categories .= "<td$_style><img src='./img/ic_arrow1.gif' align='absmiddle'> <a href='./main.html?category=$rows[no]' cno='$rows[no]'>$rows[content]</a></td>";
			if(!(++$count%$max_cols)) {
				$categories .= "<tr>$_categories</tr>";
				unset($_categories);
			}
		}
		if(isset($_categories)) {
			$_tds = str_repeat("<td>&nbsp;</td>", $max_cols-($count%$max_cols));
			$categories .= "<tr>$_categories$_tds</tr>";
		}
		return $categories;
	}

	// �޴�(ī�װ�) ������ XML ���·� ��ȯ
	function formalize_category_xml_data($datas) {
		foreach($datas as $rows) {
			$xml_data .= "
			<item no='$rows[no]'>
				<pno>$rows[pno]</pno>
				<mbno><![CDATA[{$rows[mbno]}]]></mbno>
				<content><![CDATA[{$rows[content]}]]></content>
				<mval>$rows[mval]</mval>
				<bval>$rows[bval]</bval>
				<cval>$rows[cval]</cval>
				<uval>$rows[uval]</uval>
				<pval>$rows[pval]</pval>
			</item>";
		}
		$xml_data = "
		<category>$xml_data
		</category>";
		return $xml_data;
	}

	// �޴�(ī�װ�) ����
	function get_category($datas, $all=true, $xml=true) {
		$addWhere = ($all===true) ? '' : " and pval='yes' and uval='yes' and dval='no'";
		$datas = $this->queryFetchRows("select * from $this->category_table where pno=$datas[pno] $addWhere order by rank");
		return $xml===true ? $this->formalize_category_xml_data($datas) : $datas;
	}

	// ���������� ī�װ� ����
	function get_main_category() {
		if($this->board_extension===true) return "main";
		else $category = $this->queryR("select no from $this->category_table where pno=0 and uval='yes' and dval='no' order by rank limit 0,1");
		if(empty($category)) $this->popup_msg_js("�Խ����� �������� �ʽ��ϴ�. �Խ����� �����Ǿ� �ִ��� Ȯ���Ͻñ� �ٶ��ϴ�.", "BACK");
		else return $category;
	}

	// ȸ�� ���/����Ʈ ����
	function get_member_level_points($option=false, $in7th=true) { // $in7th : 7��� ���� ���� - ȸ�� ���/����Ʈ �����ÿ��� ���
		// rankup_board ���̺��� ȸ�� ��� ����
		if($this->board_extension===true || $this->use_extend_level===true) {
			if($this->use_extend_level) list($levels, $sm_infos) = extend_level_point($this); // 2011.10.01 added
			else {
				$sm_infos = $this->queryFetch("select smlevel, smpoint from $this->sconfig_table");
				$levels = unserialize($sm_infos['smlevel']);
			}
			if(!$this->check_resource($levels)) $levels = array(7=>"��ȸ��", 6=>"��ȸ��", 5=>"��ȸ��", 4=>"���ȸ��", 3=>"Ư��ȸ��", 2=>"�ο��", 1=>"���", "join_level"=>"6");
		}
		else $levels = array(7=>"��ȸ��", 5=>"ȸ��", 1=>"���");
		if($option===true) {
			foreach($levels as $key=>$val) {
				if($key==="join_level" || $in7th===false && $key==7) continue;
				$options .= "<option value='$key'>$val</option>";
			}
			return ($in7th===true) ? $options : array($levels, $options, unserialize($sm_infos['smpoint']));
		}
		return $levels;
	}

	// �Խ��� ��Ų ����
	function get_board_skins($skin_dir="board", $color='') { // $color = "gray"
		$skin_dir = $this->board_dir."skin/$skin_dir/";
		if(is_dir($skin_dir) && $dh = opendir($skin_dir)) {
			while(($dir = readdir($dh)) !== false) {
				if(in_array($dir, array(".", "..")) || strtoupper(filetype($skin_dir.$dir))!="DIR") continue;
				$options .= "<optgroup style='background-color:black;color:white;' label='".strtolower($dir)."'></optgroup>";
				if($_dh = opendir($skin_dir.$dir)) {
					while(($_dir = readdir($_dh)) !== false) {
						if(in_array($_dir, array(".", "..")) || strtoupper(filetype($skin_dir.$dir."/".$_dir))!="DIR") continue;
						$options .= "<option value='$dir/$_dir'>{$dir}_$_dir</option>";
						if($color!=="" && $color===$_dir) return "$dir/$_dir";
					}
				}
			}
		}
		if(empty($options)) $options = "<option value=''>��Ų����</option>";
		return $options;
	}

	// �Խ��� ��� ���� - ������ - 2009.09.09 added
	function get_category_boards($datas, $is_comment=false) {
		$category_datas = $this->queryFetchRows("select * from $this->category_table where pno=0 and pval='yes' and uval='yes' and dval='no' order by rank");
		if($this->check_resource($category_datas)) {
			$max_cols = 4;
			$_width = " width='".floor(100/$max_cols)."%'";
			foreach($category_datas as $category_rows) {
				$board_datas = $this->queryFetchRows("select c.no, c.id, c.name, c.anum from $this->bconfig_table as c, $this->category_table as m2, $this->category_table m1 where c.pcno=$category_rows[no] and m1.no = c.pcno and m2.no=c.cno and m2.pval='yes' order by m1.rank, m2.pno, m2.rank, m1.pno, c.rank"); // �����ڰ� ������ ������� ���ĵȴ�.
				if($this->check_resource($board_datas)) {
					$count = 0;
					$board_options .= "<optgroup label=\"$category_rows[content]\" style='background-color:#343434;color:white'></optgroup>";
					foreach($board_datas as $board_rows) {
						if(empty($datas['id'])) $datas['id'] = $board_rows['id'];
						if($is_comment==false) $nums = "($board_rows[anum])";
						else {
							$nums = $this->queryR("select sum(cnum) from $this->board_prefix$board_rows[id]");
							$nums = "($nums)";
						}
						$bolder_style = ($datas['id']==$board_rows['id']) ? " style='font-weight:bolder'" : '';
						$board_item .= " <nobr><img src='./img/ic_dot1.gif' align='absmiddle' vspace='8'> <a href='?id=$board_rows[id]' class='bottom_cate'$bolder_style>$board_rows[name]</a>$nums</nobr>";
						$bg_style = ($datas['id']==$board_rows['id']) ? " style='background-color:#dedede;color:#999'" : '';
						$board_options .= "<option value='$board_rows[id]'$bg_style>$board_rows[name]</option>";
					}
					if(!empty($category_items)) {
						$category_items .= "
						<tr>
							<td height='1' colspan='2' style='border-bottom:1px #c9ddec solid;font-size:1px;padding:0;line-height:0'>&nbsp;</td>
						</tr>";
					}
					$category_items .= "
					<tr>
						<td width='100' style='padding-top:7px'><img src='./img/ic_arrow1.gif' align='absmiddle'> $category_rows[content]</td>
						<td style='padding-top:7px' bgcolor='white'>$board_item</td>
					</tr>";
					unset($board_item);
				}
			}
		}
		return array($category_items, $board_options, $datas['id']);
	}

	// �Խ��� �з� - ������ - 2009.09.09 added
	function get_board_categories($datas) {
		$categories = unserialize($this->board_configs['scategory']);
		if($this->check_resource($categories)) {
			$scategory = $this->sort_scategory($categories); // 2009.09.18 added
			foreach($scategory as $rank=>$rows) {
				$nodes .= "<item cno='$rows[cno]' anum='$rows[anum]'><![CDATA[{$rows[name]}]]></item>";
			}
		}
		return $nodes;
	}

	// �Խ��� �з� ���� - ������ - 2009.09.18 added
	function reset_category_rank($datas) {
		if($this->check_resource($datas['rank'])) {
			// ���� ����
			$ranks = array();
			foreach($datas['rank'] as $rank=>$cno) $ranks[$cno] = $rank+1;
			// ���� ����
			$scategory = array();
			$categories = $this->get_board_config($datas['bno'], "scategory");
			$categories = @unserialize($categories);
			foreach($categories as $cno=>$rows) {
				$scategory[$cno] = array(
					"name" => $rows['name'],
					"anum" => $rows['anum'],
					"rank" => $ranks[$cno]
				);
			}
			$_val['scategory'] = serialize($scategory);
			$values = $this->change_query_string($_val);
			$this->query("update $this->bconfig_table set $values where no=$datas[bno]");
		}
	}

	// �Խù� �̵� - ������ - 2009.09.09 added
	function move_articles($datas) {
		// �������� �������� üũ
		if(!empty($datas['anos']) && !empty($_SERVER['HTTP_REFERER'])) {
			// �Խù��̵� �� �����̵�, ��� �̵�
			// regist_article() �� ÷������ �̵�
			$this->point_locked = true;
			foreach(explode("__", $datas['anos']) as $ano) {
				$board_infos = $this->queryFetch("select * from $this->board_table where no=$ano");
				$this->move_article($board_infos, $datas['move_bid'], $datas['move_cno']); // ��� ����
			}
			return "alert('�Խù��� �̵��Ǿ����ϴ�.'+SPACE); document.location.reload();";
		}
		else $this->popup_msg_js("�������� ������ �ƴմϴ�.", "BACK");
	}

	// �Խ��� ���� ���� - 2009.09.09 added
	function change_board($board_id) {
		$this->board_configs = $this->get_board_config($board_id);
		$this->board_table = $this->board_prefix.$this->board_configs['id'];
		$this->board_comment_table = $this->comment_prefix.$this->board_configs['id'];
		$this->board_id = $this->board_configs['id'];
	}

	// �Խù� �̵� ó�� - ������ - 2009.09.09 added
	function move_article($datas, $move_bid, $move_cno='') {
		$board_id = $this->board_id;

		// ��� ������ �ε�
		$comment_datas = $this->queryFetchRows("select * from $this->board_comment_table where ano=$datas[no] order by no");

		// �Խ��� ���� ����
		$this->change_board($move_bid);

		// 1. �Խù� �̵� - 2012.05.15 modify
		$near_article = $this->queryFetch("select no, dno, sno, nano, pano from $this->board_table where sno>$this->notice_sno and wdate <= '$datas[wdate]' order by sno limit 1");
		if(empty($near_article)) $near_article = $this->queryFetch("select no, dno, sno, nano, pano from $this->board_table order by sno desc limit 1");
		if(is_array($near_article)) $next_sno = !empty($near_article['sno']) ? $near_article['sno']>$this->notice_sno ? $near_article['sno']-1 : -1 : -1;
		else $next_sno = -1;

		// �̿��ϴ� �� ����
		if(empty($near_article['no'])) $near_article['no'] = 0;
		if($next_sno<$near_article['sno']) list($_val['nano'], $_val['pano']) = array($near_article['no'], $near_article['pano']);
		else list($_val['nano'], $_val['pano']) = array($near_article['nano'], $near_article['no']);
		if($_val['nano']==null) $_val['nano'] = 0;

		// ���
		$_val['sno'] = $next_sno;
		$_val['cno'] = $move_cno ? $move_cno : null; // �з�
		$_val['dno'] = $this->increase_division($near_article['dno']); // ����� ����  cf. decrease_division();
		$_val['uip'] = $datas['uip'];
		$_val['uid'] = $datas['uid'];
		$_val['unick'] = $datas['unick'];
		$_val['upass'] = $datas['upass'];
		$_val['subject'] = $datas['subject'];
		$_val['content'] = $datas['content'];
		$_val['voter'] = $datas['voter'];
		$_val['sval'] = $datas['sval'];
		$_val['nval'] = 'no';
		$_val['dval'] = $datas['dval'];
		$_val['cnum'] = $datas['cnum'];
		$_val['dnum'] = $datas['dnum'];
		$_val['gnum'] = $datas['gnum'];
		$_val['bnum'] = $datas['bnum'];
		$_val['hnum'] = $datas['hnum'];
		$_val['wdate'] = $datas['wdate'];
		$_val['mdate'] = $datas['mdate'];
		if($datas['attach']) {
			$_val['attach'] = $datas['attach'];
			// ÷������(����� ����) �̵� �� ���� ����
			$attach_infos = unserialize($datas['attach']);
			if($this->check_resource($attach_infos)) {
				preg_match_all('/<img\s+.*?src="([^"]+)"[^>]*>/is', $_val['content'], $imgs);
				$attach_dir = $this->board_dir.'attach/';
				foreach($attach_infos as $attach) {
					$file = $attach_dir.$board_id.'/'.$attach['sname'];
					if(is_file($file)) { // ÷������ �̵�
						rename($file, $attach_dir.$this->board_id.'/'.$attach['sname']);
						// ����� �̵�
						$thumb_file = $attach_dir.$board_id.'/thumb_'.$attach['sname'];
						if(is_file($thumb_file)) rename($thumb_file, $attach_dir.$this->board_id.'/thumb_'.$attach['sname']);
					}
					// ������ �̹����� ���Ե� ��� ���� ����
					if(!$this->check_resource($imgs[1])) continue;
					foreach($imgs[1] as $key=>$img) {
						$comp = "/attach/$board_id/$attach[sname]";
						if(ereg($comp, $img)!==false) {
							$_img = str_replace($comp, "/attach/$this->board_id/$attach[sname]", $img);
							$_val['content'] = str_replace($img, $_img, $_val['content']);
							unset($imgs[1][$key]);
						}
					}
				}
			}
		}

		$values = $this->change_query_string($_val);
		$this->query("insert $this->board_table set $values$addWhere");
		$article_no = mysql_insert_id();

		//����ѱ� ���δ� sno �� ���� - 2012.05.15 add
		if($near_article[no]) $this->query("update $this->board_table set sno = sno-1 where no > $near_article[no] and no != $article_no");

		// �̿��ϴ� �Խù�(����/���� ��) ����
		$_datas = array("no"=>$article_no, "sno"=>$_val['sno'], "pano"=>0, "nano"=>$near_article['no']);
		$this->change_near_article($_datas, $near_article);
		$this->update_board(array("cmd"=>"set_anum", "plus_mode"=>true)); // �Խù� �� ����

		// �з���� ������� ��� ī�װ� anum ����
		if($move_cno) {
			$scategory = @unserialize($this->board_configs['scategory']);
			$scategory[$move_cno]['anum'] += 1;
			$_sVal['scategory'] = serialize($scategory);
			$values = $this->change_query_string($_sVal);
			$this->query("update $this->bconfig_table set $values where id='$this->board_id'");
		}

		// �� �Խù� ���� ����
		if($this->board_extension===true && $datas['uid']) {
			$_wVal['pcno'] = $this->board_configs['pcno'];
			$_wVal['bid'] = $this->board_id;
			$_wVal['ano'] = $article_no;
			$values = $this->change_query_string($_wVal);
			$this->query("update $this->my_article_table set $values where bid='$this->board_id' and ano=$datas[no]"); // 2010.06.30 fixed
		}

		// �ֱ� �Խù��� ��� - �����޴�(pcno) �� 5������ ����
		$new_datas = $this->queryFetchRows("select no from $this->new_article_table where pcno=".$this->board_configs['pcno']." order by no");
		$new_rows = current($new_datas);
		if(count($new_datas)>=5 && $this->is_demo===false) { // 1��°(������� ���� ������) ���ڵ� ����
			$this->query("delete from $this->new_article_table where no=$new_rows[no]");
			if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->new_article_table");
		}
		$_xVal['pcno'] = $this->board_configs['pcno'];
		$_xVal['bid'] = $this->board_id;
		$_xVal['adno'] = $_val['dno'];
		$_xVal['ano'] = $article_no;
		$_xVal['awdate'] = $_val['wdate'];
		$values = $this->change_query_string($_xVal);
		if($_val['dval'] == "no") $this->query("insert $this->new_article_table set $values");

		// ��ȸ�� ����Ʈ �� �ְ� ����Ʈ ����
		$_bVal['pcno'] = $this->board_configs['pcno'];
		$_bVal['bid'] = $this->board_id;
		$_bVal['adno'] = $_val['dno'];
		$_bVal['ano'] = $article_no;
		$values = $this->change_query_string($_bVal);
		if($_val['dval'] == "no") $this->query("update $this->hit_best_table set $values where bid='$board_id' and ano=$datas[no]");
		if($_val['dval'] == "no") $this->query("update $this->weekly_best_table set $values where bid='$board_id' and ano=$datas[no]");

		// 2. ��� �̵�
		if($this->check_resource($comment_datas)) {
			foreach($comment_datas as $crows) {
				$_yVal['ano'] = $article_no;
				$_yVal['uip'] = $crows['uip'];
				$_yVal['uid'] = $crows['uid'];
				$_yVal['unick'] = $crows['unick'];
				$_yVal['upasswd'] = $crows['upasswd'];
				$_yVal['icon'] = $crows['icon'];
				$_yVal['content'] = $crows['content'];
				$_yVal['wdate'] = $crows['wdate'];
				$values = $this->change_query_string($_yVal);
				$this->query("insert $this->board_comment_table set $values");
				$comment_no = mysql_insert_id();

				// �� ��� ����
				if($this->board_extension===true && $crows['uid']) {
					$_zVal['pcno'] = $this->board_configs['pcno'];
					$_zVal['bid'] = $this->board_id;
					$_zVal['ano'] = $article_no;
					$_zVal['cno'] = $comment_no;
					$values = $this->change_query_string($_zVal);
					$this->query("update $this->my_comment_table set $values where bid='$board_id' and no=$crows[no]");
				}
			}
			// ��� ����Ʈ ����
			$_aVal['pcno'] = $this->board_configs['pcno'];
			$_aVal['bid'] = $this->board_id;
			$_aVal['adno'] = $_val['dno'];
			$_aVal['ano'] = $article_no;
			$values = $this->change_query_string($_aVal);
			if($_val['dval'] == "no") $this->query("update $this->comment_best_table set $values where bid='$board_id' and ano=$datas[no]");

			// �ְ� ��� ����Ʈ ����
			if($this->board_extension===true) {
				$_cVal['bid'] = $this->board_id;
				$_cVal['ano'] = $article_no;
				$values = $this->change_query_string($_cVal);
				if($_val['dval'] == "no") $this->query("update $this->weekly_cbest_table set $values where bid='$board_id' and ano=$datas[no]");
			}
		}

		// �Խ��� ���� ����
		$this->change_board($board_id);
		$this->delete_article($datas, true);
		return true;
	}

	// �Խ��� �ڸ�Ʈ ��� - 2009.09.09 added
	function get_board_comments($datas, $limit=15) {
		if(empty($datas['id'])) return false;
		if($this->board_table==null) $this->rankup_board($datas['id']); // ȯ�漳��

		if(empty($datas['page'])) $datas['page'] = 1;
		$stpos = $datas['page']>1 ? ($datas['page']-1)*$limit : 0;

		// �˻��Ⱓ
		if($datas['use_date']=="on") {
			if(!empty($datas['sdate'])) {
				if(!empty($datas['edate'])) $addWhere .= " and date_format(wdate, '%Y-%m-%d') between '$datas[sdate]' and '$datas[edate]'";
				else $addWhere .= " and date_format(wdate, '%Y-%m-%d')>='$datas[sdate]'";
			}
			else if(!empty($datas['edate'])) $addWhere .= " and date_format(wdate, '%Y-%m-%d')<='$datas[edate]'";
		}
		// �˻���
		if(!empty($datas['skey'])) {
			switch($datas['smode']) {
				case "content": // ���� �˻�
					$addWhere .= " and content like '%$datas[skey]%'";
					break;
				case "author": // �ۼ��� �˻�
					$addWhere .= " and unick like '%$datas[skey]%'";
					break;
				case "uid": // ���̵� �˻�
					$addWhere .= " and uid like '%$datas[skey]%'";
					break;
			}
		}
		$total_comments = $this->queryR("select count(no) from $this->board_comment_table where no$addWhere");
		$paging_button = $this->print_paging($total_comments, array($limit, 10), 'page', $this->board_url.'skin/board/basic/gray/');
		$comment_datas = $this->queryFetchRows("select no, ano, uid, unick, icon, content, date_format(wdate, '%Y-%m-%d') as wdate from $this->board_comment_table where no$addWhere order by no desc limit $stpos, $limit");
		if($this->check_resource($comment_datas)) {
			foreach($comment_datas as $rows) {
				$member_info_link = empty($rows['uid']) ? '' : "(<a href='{$this->base_url}rankup_module/rankup_member/member_detail.html?uid=$rows[uid]'>$rows[uid]</a>)";
				if(!empty($comment_contents)) {
					$comment_contents .= "
					<tr height='15'><td></td></tr>";
				}
				$rows['wdate'] = str_replace('-', '.', $rows['wdate']);
				$comment_contents .=
				"<tr>
					<td width='80%'>
						<input type='checkbox' name='no[]' value='$rows[no]' align='absmiddle'>
						<b>$rows[unick]$member_info_link</b>&nbsp; <span class='gray_s'>$rows[wdate]</span> &nbsp; <a onClick='rankup_board.comment_delete($rows[no])'><img src='./img/btn_delete_s.gif' align='absmiddle'></a></td>
					<td align='right' width='20%'><a href='./index.html?id=$datas[id]&no=$rows[ano]'><img src='./img/btn_view_sentence.gif'></a></td>
				</tr>
				<tr><td height='3'></td></tr>
				<tr>
					<td background='./img/dot_line_gray.gif' colspan='2' height='1'></td>
				</tr>
				<tr bgcolor='#f4f4f4'>
					<td colspan='2' height='30' class='content'><img src='".$this->board_url."icon/face_$rows[icon].gif' align='absmiddle' class='icon'>&nbsp; ".nl2br($rows['content'])."</td>
				</tr>
				<tr>
					<td background='./img/dot_line_gray.gif' colspan='2' height='1'></td>
				</tr>";
			}
		}
		if(empty($comment_contents)) {
			$comment_contents = "
			<tr disabled>
				<td colspan='2'><input type='checkbox'> ����� �������� �ʽ��ϴ�.</td>
			</tr>";
		}
		return array($total_comments, $comment_contents, $paging_button);
	}

	// ��� ���û��� - ������
	function delete_comments($datas) {
		// �������� �������� üũ
		if(!empty($datas['nos']) && !empty($_SERVER['HTTP_REFERER'])) {
			$comment_datas = $this->queryFetchRows("select no, ano, uid, wdate from $this->board_comment_table where no in(".str_replace("__", ",", $datas['nos']).")");
			foreach($comment_datas as $comment_infos) $this->_delete_comment($comment_infos); // ��� ���� ��ƾ�� ���� ȣ��
			return "alert('����� �����Ǿ����ϴ�.'+SPACE); document.location.reload();";
		}
		else $this->popup_msg_js("�������� ������ �ƴմϴ�.", "BACK");
	}

}
?>