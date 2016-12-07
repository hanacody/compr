<?php
// ��� ��Ʈ���� ���� Ŭ����
class rankup_db {
	var $db_conn;   // ���� ��� Ŀ�ؼ�
	var $conn_class;
	var $insert_id = 0;
	var $query_rows = 0;
	function rankup_db() { // Ŀ�ؼ� 1ȸ�� ���� - 2012.04.17 modified
		global $_db_connection, $_db_instance;
		if(!$_db_connection) $_db_connection = new rankup_connection;
		if(!$_db_instance) $_db_instance = $_db_connection->connection();
		$this->conn_class = $_db_connection;
		$this->db_conn = $_db_instance;
		if(!$this->db_conn) die("������ ���̽� ���ٿ� ���� �Ͽ����ϴ�.");
	}

	// ���� ����Ʈ ��ȯ - 2009.09.25 added
	function get_query_point($on_page, $list_row) {
		if(!$on_page) $on_page = 1;
		$qPoint = $on_page>1 ? ($on_page-1)*$list_row : 0;
		return $qPoint;
	}

	function query($query,$debug=0) { // $debug �� 0 �̸� ���� �����޽����� ������ ���·� ����Ѵ�.
		$mysql_result = @mysql_query($query, $this->db_conn);
		if(!$mysql_result) {
			if($debug==0) $this->db_error($query);
			else return $mysql_result;
		}
		else {
			// 2011.12.15 added
			$this->insert_id = (stristr($query, 'INSERT')!==false) ? @mysql_insert_id($this->db_conn) : 0;
			$this->query_rows = (stristr($query, 'SELECT')!==false) ? @mysql_num_rows($mysql_result) : @mysql_affected_rows($this->db_conn);
			return $mysql_result;
		}
	}

	// count �� ���� �Ѱ��� ������ �̾ƿö� ����ϴ� ���� ����  �޼ҵ�
	function queryR($query) {
		$result = $this->query($query);
		$row = @array_shift($this->fetch($result));
		@mysql_free_result($result);
		return empty($row) ? 0 : $row;
	}

	// �� ���� ���ڵ� �� ��ȯ - 2009.09.25 added
	function queryRows($query) {
		$result = $this->query($query);
		$rows = mysql_num_rows($result);
		@mysql_free_result($result);
		return $rows;
	}

	// ȸ�� �˻��� ���� index �� �̿��� where  �˻��� �Ҷ� ����ϴ� �޼ҵ�
	function queryFetch($query) {
		$result = $this->query($query);
		$row = $this->fetch($result);
		@mysql_free_result($result);
		return $row;
	}

	// ����Ʈ�� �����ִ� �Լ�
	// 0 : ������ (LIMIT ����)
	// 1 : ���������� ������ ����
	// 2 : ������ ����
	// 3 : ���� ��������
	//$lpage : �������� �̸�. page=2....page2=1....page3=1 ���·� ���� ����Ʈ ������ �ʿ�
	function queryFetchRows($query, $num_per_page='', $page_per_block='', $lpage='') {
		if($num_per_page && $page_per_block) {
			if(empty($lpage)) $lpage = "page";
			$page = $this->getParam($lpage);
			if(empty($page)) $page = 1;
			$result = $this->query($query);
			$total_record = @mysql_num_rows($result);
			$start = ($page*$num_per_page) - $num_per_page; // LIMIT ���
			$query_limit = " LIMIT $start, $num_per_page";
			$query .= $query_limit;
		}
		$total_row = array();
		$result = $this->query($query);
		$no = $total_record - (($page-1)*$num_per_page); // no ���ϱ�
		while($row=$this->fetch($result)) {
			$row['list_no'] = $no--; // row �迭�� list_no �߰�
			array_push($total_row, $row); // total_row �� row �迭 �߰�
		}
		@mysql_free_result($result);
		return $total_row;
	}

	// ���̻� ����Ʈ ������ ���� queryFetchRows �޽�带 ������� ����
	function fetch($resource, $result_type=MYSQL_ASSOC) {
		$record = mysql_fetch_array($resource, $result_type);
		rankup_util::stripslashes($record);
		return $record;
	}

	// ���� 1 : ��ü ����
	// ���� 2 : ���� ������ ��
	// ���� 3 : ���������� ������ �Խù� ��
	// ���� 4 : ����¡ �׷쿡 ������ ��
	// ���� 5 : �̵���
	// ���� 6 : �� ���������� �������� ������ ���� �Լ��� ����� ��� ��, ������ ������ �����ϱ� ���� ����.
	function paging($total, $num_per_page, $page_per_block, $url='', $lpage='') {
		$total_result = '';
		$page = $this->getParam('page');
		if(empty($lpage)) $lpage = "page";
		if(empty($page)) $page = 1;
		if(empty($url)) $url = $_SERVER['PHP_SELF']."?";

		// ����¡�� ������ ���� ���, �ش� �������� �����ϱ� ���� ������ ���� ��
		$total_page = ceil($total/$num_per_page);		// ��ü ��������
		$block = ceil($page/$page_per_block);				// ���� ������ ��
		$start_page = ($block-1)*$page_per_block+1;	// ���� ������ ���� ���� ������
		$last_page = $start_page+$page_per_block-1;	// ������ ������
		if($last_page > $total_page) $last_page = $total_page;

		// �̹��� ���
		$img_dir = rankup_basic::base_url()."Libs/_images/";

		// ���� 10�� ����
		if($block > 1) {
			$prev_group_num = $start_page-$page_per_block;
			$total_result .= " <a href='$url&$lpage=$prev_group_num'><img src=".$img_dir."icon_first.gif border=0></a> </a> ";
		}
		// ���� ������
		if($page > 1) {
			$prev_page_num = $page-1;
			$total_result .= " <a href='$url&$lpage=$prev_page_num'><img src=".$img_dir."icon_back.gif border=0></a> ";
		}

		// ������ ����Ʈ [1] [2] [3] [4]
		$i=0;
		while($i+$start_page <= $last_page ) {
			$move_page = $i+$start_page;
			$total_result .= ($page == $move_page) ? "<b>$move_page </b>" : " <a onfocus=blur() href='$url&$lpage=$move_page'>[$move_page]</a> ";
			$i++;
		}
		// ���� ������
		if($page < $total_page) {
			$next_page_num = $page+1;
			$total_result .= " <a href='$url&$lpage=$next_page_num'><img src=".$img_dir."icon_next.gif border=0></a>";
		}
		// ���� 10�� ����
		if($total_page > $last_page) {
			$next_group_num = $last_page+1;
			$total_result .= " <a href='$url&$lpage=$next_group_num'><img src=".$img_dir."icon_last.gif border=0></a>";
		}
		return $total_result;
	}

	// ���� �����Ŀ� ���� �߻��� ������ ������ ���·� ���
	function db_error($query) {
		$error_msg = "ERROR CODE ".mysql_errno()." : ".mysql_error();
		$error_msg = addslashes($error_msg);
		echo "
		<table border=0 cellpadding=3 cellspacing=1>
			<tbody>
			<tr>
				<td colspan=2 align=center bgcolor=#cccccc><b><font size=2>�������� ������ �����ϴ�.</b></font></td>
			</tr>
			<tr>
				<td bgcolor=#F5F5F5><font size=2>������</font></td>
				<td><font size=2>$query</font></td>
			</tr>
			<tr>
				<td colspan=2 height=1 bgcolor=#CCCCCC></td></tr>
			<tr>
				<td bgcolor=#F5F5F5><font size=2>�����޼���</font></td>
				<td><font size=2>$error_msg</font></td>
			</tr>
			<tr>
				<td colspan=2 height=1 bgcolor=#CCCCCC></td>
			</tr>
			<tr>
				<td bgcolor=#F5F5F5><font size=2>������</font></td>
				<td><font size=2>$_SERVER[PHP_SELF]</font></td>
			</tr>
			<tr>
				<td colspan=2 height=1 bgcolor=#CCCCCC></td>
			</tr>
			</tbody>
		</table>";
		exit;
	}
}
?>