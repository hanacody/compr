<?php
// 디비 컨트롤을 위한 클래스
class rankup_db {
	var $db_conn;   // 사용될 디비 커넥션
	var $conn_class;
	var $insert_id = 0;
	var $query_rows = 0;
	function rankup_db() { // 커넥션 1회로 제한 - 2012.04.17 modified
		global $_db_connection, $_db_instance;
		if(!$_db_connection) $_db_connection = new rankup_connection;
		if(!$_db_instance) $_db_instance = $_db_connection->connection();
		$this->conn_class = $_db_connection;
		$this->db_conn = $_db_instance;
		if(!$this->db_conn) die("데이터 베이스 접근에 실패 하였습니다.");
	}

	// 쿼리 포인트 반환 - 2009.09.25 added
	function get_query_point($on_page, $list_row) {
		if(!$on_page) $on_page = 1;
		$qPoint = $on_page>1 ? ($on_page-1)*$list_row : 0;
		return $qPoint;
	}

	function query($query,$debug=0) { // $debug 가 0 이면 쿼리 에러메시지를 일정한 형태로 출력한다.
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

	// count 와 같이 한개의 값만을 뽑아올때 사용하는 쿼리 응용  메소드
	function queryR($query) {
		$result = $this->query($query);
		$row = @array_shift($this->fetch($result));
		@mysql_free_result($result);
		return empty($row) ? 0 : $row;
	}

	// 총 쿼리 레코드 수 반환 - 2009.09.25 added
	function queryRows($query) {
		$result = $this->query($query);
		$rows = mysql_num_rows($result);
		@mysql_free_result($result);
		return $rows;
	}

	// 회원 검색과 같은 index 를 이용한 where  검색을 할때 사용하는 메소드
	function queryFetch($query) {
		$result = $this->query($query);
		$row = $this->fetch($result);
		@mysql_free_result($result);
		return $row;
	}

	// 리스트를 보여주는 함수
	// 0 : 쿼리문 (LIMIT 제외)
	// 1 : 한페이지에 보여줄 갯수
	// 2 : 페이지 묶음
	// 3 : 현제 페이지수
	//$lpage : 페이지의 이름. page=2....page2=1....page3=1 형태로 다중 리스트 구현시 필요
	function queryFetchRows($query, $num_per_page='', $page_per_block='', $lpage='') {
		if($num_per_page && $page_per_block) {
			if(empty($lpage)) $lpage = "page";
			$page = $this->getParam($lpage);
			if(empty($page)) $page = 1;
			$result = $this->query($query);
			$total_record = @mysql_num_rows($result);
			$start = ($page*$num_per_page) - $num_per_page; // LIMIT 계산
			$query_limit = " LIMIT $start, $num_per_page";
			$query .= $query_limit;
		}
		$total_row = array();
		$result = $this->query($query);
		$no = $total_record - (($page-1)*$num_per_page); // no 구하기
		while($row=$this->fetch($result)) {
			$row['list_no'] = $no--; // row 배열에 list_no 추가
			array_push($total_row, $row); // total_row 에 row 배열 추가
		}
		@mysql_free_result($result);
		return $total_row;
	}

	// 더이상 리스트 구현을 위해 queryFetchRows 메쏘드를 사용하지 않음
	function fetch($resource, $result_type=MYSQL_ASSOC) {
		$record = mysql_fetch_array($resource, $result_type);
		rankup_util::stripslashes($record);
		return $record;
	}

	// 인자 1 : 전체 갯수
	// 인자 2 : 현재 페이지 수
	// 인자 3 : 한페이지에 보여줄 게시물 수
	// 인자 4 : 페이징 그룹에 보여줄 수
	// 인자 5 : 이동할
	// 인자 6 : 한 페이지에서 여러개의 페이지 분할 함수를 사용할 경우 각, 페이지 분할을 구별하기 위한 인자.
	function paging($total, $num_per_page, $page_per_block, $url='', $lpage='') {
		$total_result = '';
		$page = $this->getParam('page');
		if(empty($lpage)) $lpage = "page";
		if(empty($page)) $page = 1;
		if(empty($url)) $url = $_SERVER['PHP_SELF']."?";

		// 페이징을 여러개 돌릴 경우, 해당 페이지를 구별하기 위한 페이지 변수 값
		$total_page = ceil($total/$num_per_page);		// 전체 페이지수
		$block = ceil($page/$page_per_block);				// 현재 페이지 블럭
		$start_page = ($block-1)*$page_per_block+1;	// 현재 페이지 블럭의 시작 페이지
		$last_page = $start_page+$page_per_block-1;	// 마지막 페이지
		if($last_page > $total_page) $last_page = $total_page;

		// 이미지 경로
		$img_dir = rankup_basic::base_url()."Libs/_images/";

		// 이전 10개 보기
		if($block > 1) {
			$prev_group_num = $start_page-$page_per_block;
			$total_result .= " <a href='$url&$lpage=$prev_group_num'><img src=".$img_dir."icon_first.gif border=0></a> </a> ";
		}
		// 이전 페이지
		if($page > 1) {
			$prev_page_num = $page-1;
			$total_result .= " <a href='$url&$lpage=$prev_page_num'><img src=".$img_dir."icon_back.gif border=0></a> ";
		}

		// 페이지 리스트 [1] [2] [3] [4]
		$i=0;
		while($i+$start_page <= $last_page ) {
			$move_page = $i+$start_page;
			$total_result .= ($page == $move_page) ? "<b>$move_page </b>" : " <a onfocus=blur() href='$url&$lpage=$move_page'>[$move_page]</a> ";
			$i++;
		}
		// 다음 페이지
		if($page < $total_page) {
			$next_page_num = $page+1;
			$total_result .= " <a href='$url&$lpage=$next_page_num'><img src=".$img_dir."icon_next.gif border=0></a>";
		}
		// 다음 10개 보기
		if($total_page > $last_page) {
			$next_group_num = $last_page+1;
			$total_result .= " <a href='$url&$lpage=$next_group_num'><img src=".$img_dir."icon_last.gif border=0></a>";
		}
		return $total_result;
	}

	// 쿼리 실행후에 에러 발생시 에러를 일정한 형태로 출력
	function db_error($query) {
		$error_msg = "ERROR CODE ".mysql_errno()." : ".mysql_error();
		$error_msg = addslashes($error_msg);
		echo "
		<table border=0 cellpadding=3 cellspacing=1>
			<tbody>
			<tr>
				<td colspan=2 align=center bgcolor=#cccccc><b><font size=2>쿼리에서 에러가 났습니다.</b></font></td>
			</tr>
			<tr>
				<td bgcolor=#F5F5F5><font size=2>쿼리문</font></td>
				<td><font size=2>$query</font></td>
			</tr>
			<tr>
				<td colspan=2 height=1 bgcolor=#CCCCCC></td></tr>
			<tr>
				<td bgcolor=#F5F5F5><font size=2>에러메세지</font></td>
				<td><font size=2>$error_msg</font></td>
			</tr>
			<tr>
				<td colspan=2 height=1 bgcolor=#CCCCCC></td>
			</tr>
			<tr>
				<td bgcolor=#F5F5F5><font size=2>페이지</font></td>
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